<?php

/**
 * Created by youyi000.
 * DateTime: 2016/7/7 17:27
 * Describe：
 */
class ProjectBaseController extends AttachmentController {
    /**
     * 是否显示全览链接
     * @var int
     */
    public $isShowAllLink = 1;

    public $isCanAdd = 0;
    public $indexView = "/project/index";
    public $editView = "/project/edit";
    public $detailView = "/project/detail";

    public function pageInit() {
        parent::pageInit();
        $this->attachmentType = Attachment::C_PROJECT;
        $this->mainUrl = "/" . $this->getId() . "/";
        $this->filterActions = "";
        $this->rightCode = "project";
    }

    public function actionIndex() {
        $data = $this->getIndexData();
        $this->render($this->indexView, $data);
    }

    protected function getIndexData() {
        $attr = $_GET[search];
        $user = SystemUser::getUser(Utility::getNowUserId());
        $sql = "select {col} from t_project a left join t_system_user b on b.user_id = a.manager_user_id " . $this->getWhereSql($attr);

        $sql .= " and a.corporation_id in (" . $user['corp_ids'] . ") order by a.project_id desc {limit}";

        return $this->queryTablesByPage($sql, "a.*,b.name");
    }

    public function actionAdd() {
        if ($this->isCanAdd != 1) {
            $this->renderError("当前不允许添加");
        }

        $type=Mod::app()->request->getParam('type');

        $data = $this->getAddData($type);
        $this->pageTitle = empty($this->pageTitle) ? "项目列表＞发起项目" : $this->pageTitle;
        $this->render($this->editView, $data);
    }

    protected function getAddData($type=0) {
        $data['project_id'] = IDService::getProjectId();
        $data["type"]=$type;
        $data['purchase_currency'] = !empty($type) && in_array($type, array(ConstantMap::PROJECT_TYPE_SELF_IMPORT, ConstantMap::PROJECT_TYPE_IMPORT_BUY, ConstantMap::PROJECT_TYPE_IMPORT_CHANNEL)) ? ConstantMap::CURRENCY_DOLLAR : ConstantMap::CURRENCY_RMB;
        $data['contractGoodsUnitConvert'] = ConstantMap::CONTRACT_GOODS_UNIT_CONVERT;
        $data['contractGoodsUnitConvertValue'] = ConstantMap::CONTRACT_GOODS_UNIT_CONVERT_VALUE;
        // $goods = Goods::model()->findAllToArray('status = :status', array('status' => ConstantMap::STATUS_VALID));
        $goods = Goods::getActiveTreeTable();
        $attachments = Project::getAttachment($data['project_id'], ConstantMap::PROJECT_LAUNCH_DECISION_ATTACH_TYPE);

        return array('data' => $data, 'goods' => $goods, 'attachments' => $attachments);
    }

    public function actionSaveAdd() {
        $params = Mod::app()->request->getParam('data');
        $paramsTrans = $params['goodsDetail'];
        unset($params['goodsDetail']);

        if(ContractService::isHaveSameGoods($paramsTrans))
            $this->returnError(BusinessError::outputError(OilError::$TRANSACTION_DETAIL_GOODS_NAME_REPEAT));

        if (in_array($params['type'], ConstantMap::$self_support_project_type)) { //自营
            $params['storehouse_id'] = 0;
            if ($params['buy_sell_type'] == ConstantMap::FIRST_BUY_LAST_SALE) {
                $params['down_partner_id'] = 0;
                $params['sell_currency'] = 1;
            } elseif ($params['buy_sell_type'] == ConstantMap::FIRST_SALE_LAST_BUY) {
                $params['up_partner_id'] = 0;
                $params['purchase_currency'] = 1;
            } else {
                $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
            }
        } else {
            $params['buy_sell_type'] = 0;
            $params['plan_describe'] = '';
            if (!in_array($params['type'], ConstantMap::$warehouse_receive_project_type)) {
                $params['storehouse_id'] = 0;
            }
        }


        if(!in_array($params['type'], ConstantMap::$import_buy_project_type) &&
            ($params['type']!=ConstantMap::PROJECT_TYPE_SELF_IMPORT || $params['buy_sell_type']!=ConstantMap::FIRST_BUY_LAST_SALE))
            $params['agent_id'] = 0;

        //项目发起参数校验
        $paramsCheckRes = ProjectService::checkParamsValid($params);
        if ($paramsCheckRes !== true) {
            $this->returnError($paramsCheckRes);
        }

        //必传附件校验
        $attachCheckRes = Utility::checkRequiredAttachments($this->attachmentType, $params['project_id'], 'project_id');
        if ($attachCheckRes !== true) {
            $this->returnError($attachCheckRes);
        }

        //交易明细参数校验
        $transParamsCheck = ProjectBaseGoodsService::checkParamsValid($paramsTrans);
        if ($transParamsCheck !== true) {
            $this->returnError($transParamsCheck);
        }

        if (!empty($params['project_id'])) {
            $project = Project::model()->findByPk($params['project_id']);
        }

        $isNew = 0;

        if (!empty($project->project_id)) {
            if (!$this->checkIsCanEdit($project->status)) {
                $this->returnError(BusinessError::outputError(OilError::$PROJECT_NOT_ALLOW_EDIT));
            }
        } else {
            $isNew = 1;
            $project = new Project();
            $project->project_id = $params["project_id"];
            $projectCodeInfo = CodeService::getProjectCode($params['corporation_id'], $params['manager_user_id'], $params['type']);
            if ($projectCodeInfo['code'] == ConstantMap::VALID) {
                $project->project_code = $projectCodeInfo['project_code'];
            } else {
                $this->returnError(BusinessError::outputError(OilError::$PROJECT_CODE_GENERATE_ERROR) . $projectCodeInfo['msg']);
            }
            $project->create_time = Utility::getDateTime();
            $project->create_user_id = Utility::getNowUserId();
            $project->status = Project::STATUS_NEW;
            $project->status_time = Utility::getDateTime();
        }

        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {
            unset($params["project_id"]);
            $project->setAttributes($params, false);
            $project->update_time = Utility::getDateTime();
            $project->update_user_id = Utility::getNowUserId();
            if(!$project->isNewRecord)
            {
                if($project->attributeIsModified("corporation_id")
                    || $project->attributeIsModified("manager_user_id")
                    || $project->attributeIsModified("type"))
                {
                    $pCode=ProjectService::updateProjectCode($project->project_code,$project->corporation_id,$project->manager_user_id,$project->type);
                    if($pCode!==false)
                        $project->project_code=$pCode;
                }

                if( $project->status != Project::STATUS_NEW)
                {
                    $project->status = Project::STATUS_NEW;
                    $project->status_time = Utility::getDateTime();
                }
            }
            $res = $project->save();
            if ($res === true) {
                $projectBase = ProjectBase::model()->find('project_id = :projectId', array('projectId' => $project->project_id));
                if (empty($projectBase->base_id)) {
                    $projectBase = new ProjectBase();
                    $projectBase->project_id = $project->project_id;
                    $projectBase->create_time = Utility::getDateTime();
                    $projectBase->create_user_id = Utility::getNowUserId();
                    $projectBase->status = 0;
                    $projectBase->status_time = Utility::getDateTime();
                }
                $projectBase->setAttributes($params, false);
                $projectBase->update_time = Utility::getDateTime();
                $projectBase->update_user_id = Utility::getNowUserId();
                $ret = $projectBase->save();
                if ($ret === true) {
                    ProjectBaseGoodsService::saveGoodsTransactions($paramsTrans, $project->project_id, $projectBase->base_id);
                } else {
                    throw new Exception($ret);
                }

                /*if($isNew==1)
                {
                    $taskParams=array(
                        'project_id'=>$project->project_id,
                        'title'=>"您发起了一个新项目，项目编号：".$project->project_code
                    );
                    TaskService::addTasks(Action::ACTION_9, $project->project_id, 0, $project->create_user_id, $project->corporation_id,$taskParams);
                }*/

                TaskService::doneTask($project->project_id,Action::ACTION_PROJECT_BACK);

                $trans->commit();

                $logRemark = ActionLog::getEditRemark($isNew, "项目");
                Utility::addActionLog(json_encode($project->oldAttributes), $logRemark, "Project", $project->project_id);
                $this->returnSuccess($project->project_id);
            } else {
                throw new Exception($res);
            }
        } catch (Exception $e) {
            try {
                $trans->rollback();
            } catch (Exception $ee) {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $ee->getMessage(), CLogger::LEVEL_ERROR);
            }

            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);

            $this->returnError(BusinessError::outputError(OilError::$PROJECT_SAVE_ADD_ERROR, array('reason' => $e->getMessage())));
        }
    }

    /**
     * 判断是否可以修改，子类需要修改该方法
     * @param $status
     * @return bool
     */
    public function checkIsCanEdit($status) {
        if ($status < Project::STATUS_SUBMIT) {
            return true;
        } else {
            return false;
        }
    }

    public function actionEdit() {
        $project_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($project_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        /*if (!ProjectService::checkProjectExist($project_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PROJECT_NOT_EXIST, array('project_id' => $project_id)));
        }*/

        $project = Project::model()->findByPk($project_id);
        if (empty($project)) {
            $this->returnError(BusinessError::outputError(OilError::$PROJECT_NOT_EXIST, array('project_id' => $project_id)));
        }

        $projectInfo = ProjectService::getProjectDetail($project_id);
        if (Utility::isEmpty($projectInfo)) {
            $this->renderError(BusinessError::outputError(OilError::$PROJECT_NOT_EXIST, array('project_id' => $project_id)));
        }

        if (!$this->checkIsCanEdit($projectInfo[0]['status'])) {
            $this->renderError(BusinessError::outputError(OilError::$PROJECT_NOT_ALLOW_EDIT));
        }

        $data = $this->getEditData($project_id);
        //单位换算比
        $projectInfo[0]['contractGoodsUnitConvert'] = ConstantMap::CONTRACT_GOODS_UNIT_CONVERT;
        $projectInfo[0]['contractGoodsUnitConvertValue'] = ConstantMap::CONTRACT_GOODS_UNIT_CONVERT_VALUE;
        $params = Utility::isNotEmpty($data) ? array_merge_recursive(array('data' => $projectInfo[0]), $data) : array();
        $this->pageTitle = '修改项目';
        
        $this->render($this->editView, $params);
    }

    protected function getEditData($project_id) {
        // $goods = Goods::model()->findAllToArray('status = :status', array('status' => ConstantMap::STATUS_VALID));
        $goods = Goods::getActiveTreeTable();
        $attachments = Project::getAttachment($project_id);
        $transactions = ProjectBaseGoodsService::getProjectTransactions($project_id);

        return array('goods' => $goods, 'attachments' => $attachments, 'transactions' => $transactions);
    }

    public function actionDetail() {
        $project_id = Mod::app()->request->getParam("id");
        if (!Utility::checkQueryId($project_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $project = Project::model()->findByPk($project_id);
        if (empty($project)) {
            $this->renderError(BusinessError::outputError(OilError::$PROJECT_NOT_EXIST, array('project_id' => $project_id)));
        }

        $projectInfo = ProjectService::getProjectDetail($project_id);
        if (Utility::isEmpty($projectInfo)) {
            $this->renderError(BusinessError::outputError(OilError::$PROJECT_NOT_EXIST, array('project_id' => $project_id)));
        }

        $data = $this->getDetailData($project_id);
        $params = Utility::isNotEmpty($data) ? array_merge_recursive(array('data' => $projectInfo[0]), $data) : array();
        $this->pageTitle = '项目列表＞项目详情';
        $this->render($this->detailView, $params);
    }

    protected function getDetailData($project_id) {
        // $goods = Goods::model()->findAllToArray('status = :status', array('status' => ConstantMap::STATUS_VALID));
        $goods = Goods::getActiveTreeTable();
        $attachments = Project::getAttachment($project_id);
        $transactions = ProjectBaseGoodsService::getProjectTransactions($project_id);

        $purchaseData = ContractService::getContractsByProjectId($project_id, ConstantMap::BUY_TYPE);
        $saleData = ContractService::getContractsByProjectId($project_id, ConstantMap::SALE_TYPE);
        $budgetData = Project::getAttachment($project_id, ConstantMap::PROJECT_BUDGET_ATTACH_TYPE);

        return array('goods' => $goods, 'attachments' => $attachments, 'transactions' => $transactions, 'purchaseData' => $purchaseData, 'saleData' => $saleData, 'budgetData' => $budgetData);
    }

    public function actionDel() {
        $project_id = Mod::app()->request->getParam("id");
        try {
            ProjectService::deleteProject($project_id);
            //TaskService::trashTask($project_id,Action::ACTION_2);
            Utility::addActionLog(null, "删除项目", "Project", $project_id);
            $this->returnSuccess();
        } catch (Exception $e) {
            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' project delete error:' . $e->getMessage(), CLogger::LEVEL_ERROR);

            $this->returnError(BusinessError::outputError(OilError::$OPERATE_FAILED, array('reason' => $e->getMessage())));
        }
    }

    public function actionSubmit() {
        $project_id = Mod::app()->request->getParam("id");
        if (!Utility::checkQueryId($project_id)) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        /*if (!ProjectService::checkProjectExist($project_id)) {
            $this->returnError(BusinessError::outputError(OilError::$PROJECT_NOT_EXIST, array('project_id' => $project_id)));
        }*/
        $project = Project::model()->findByPk($project_id);
        if (empty($project)) {
            $this->returnError(BusinessError::outputError(OilError::$PROJECT_NOT_EXIST, array('project_id' => $project_id)));
        }

        $obj = Project::model()->with('base',"contracts")->findbyPk($project_id);
        $oldStatus = $obj->status;

        if ($obj->base->buy_sell_type > 0) {
            $contractType = Map::$v['project_detail_type'][$obj->type . $obj->base->buy_sell_type];
        } else {
            $contractType = Map::$v['project_detail_type'][$obj->type];
        }

        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {
            $obj->update_time = Utility::getDateTime();
            $obj->update_user_id = Utility::getNowUserId();
            $obj->status = Project::STATUS_SUBMIT;
            $obj->status_time = Utility::getDateTime();
            $obj->save();

            if(is_array($obj->contracts))
            {
                foreach ($obj->contracts as $c)
                {
                    $c->updateByPk($c->contract_id,array("corporation_id"=>$obj->corporation_id));
                }
            }

            /*$taskParams=array(
                'project_id'=>$project_id, 'contract_id'=>0,
                'title'=>$obj->project_code." 项目合同待商务确认"
                );*/
            // TaskService::addTasks(Action::ACTION_10, $project_id, ActionService::getActionRoleIds(Action::ACTION_10), 0, $obj->corporation_id, $taskParams);
            if ($obj->base->buy_sell_type > 0) {
                $contractType = Map::$v['project_detail_type'][$obj->type . $obj->base->buy_sell_type];
            } else {
                $contractType = Map::$v['project_detail_type'][$obj->type];
            }
            TaskService::addTasks(Action::ACTION_10, $project_id, ActionService::getActionRoleIds(Action::ACTION_10), 0, $obj->corporation_id, array('projectCode'=>$obj->project_code,'contractType'=>$contractType, 'isMain'=>'','project_id'=>$project_id, 'contract_id'=>0));
            TaskService::doneTask($project_id, Action::ACTION_9);
            TaskService::doneTask($project_id,Action::ACTION_PROJECT_BACK);

            //根据项目信息初始化合同组信息
            ContractService::initContractGroupByProject($obj);
            $trans->commit();

            Utility::addActionLog(json_encode(array('oldStatus'=>$oldStatus)), "提交项目信息", "Project", $obj->project_id);
			$this->returnSuccess();
        } catch (Exception $e) {
            try {
                $trans->rollback();
            } catch (Exception $ee) {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $ee->getMessage(), CLogger::LEVEL_ERROR);
            }

            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);

            $this->returnError(BusinessError::outputError(OilError::$PROJECT_SAVE_ADD_ERROR, array('reason' => $e->getMessage())));
        }

        /*TaskService::addTasks(Action::ACTION_10, $project_id, ActionService::getActionRoleIds(Action::ACTION_10), 0, $obj->corporation_id);
        TaskService::doneTask($project_id, Action::ACTION_9);
        $this->returnSuccess();*/
    }
}