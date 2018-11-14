<?php

/**
 * Created by youyi000.
 * DateTime: 2017/8/22 11:30
 * Describe：
 */
class SubContractController extends ProjectBaseController
{
    public function pageInit() {
        parent::pageInit();
        $this->attachmentType = Attachment::C_PROJECT;
        $this->filterActions = '';
        $this->treeCode = "businessConfirm";
        $this->rightCode = 'subContract';
        $this->isShowAllLink = 0;
        $this->isCanAdd = 1;
        $this->editView = '/subContract/edit';
        $this->detailView = '/subContract/detail';
        $this->newUIPrefix = "new_";
    }

    public function getIndexData() {
        $attr = Mod::app()->request->getParam('search');
        $user = SystemUser::getUser(Utility::getNowUserId());
        $query = '';
        $projectType = 0;
        if (!empty($attr['project_type'])) {
            switch ($attr["project_type"]) {
                case ConstantMap::SELF_IMPORT_FIRST_SALE_LAST_BUY: //进口自营-先销后采
                    $query .= " and a.type = " . ConstantMap::PROJECT_TYPE_SELF_IMPORT . ' and c.buy_sell_type = ' . ConstantMap::FIRST_SALE_LAST_BUY;
                    break;
                case ConstantMap::SELF_IMPORT_FIRST_BUY_LAST_SALE: //进口自营-先采后销
                    $query .= " and a.type = " . ConstantMap::PROJECT_TYPE_SELF_IMPORT . ' and c.buy_sell_type = ' . ConstantMap::FIRST_BUY_LAST_SALE;
                    break;
                case ConstantMap::SELF_INTERNAL_TRADE_FIRST_SALE_LAST_BUY: //内贸自营-先销后采
                    $query .= " and a.type = " . ConstantMap::PROJECT_TYPE_SELF_INTERNAL_TRADE . ' and c.buy_sell_type = ' . ConstantMap::FIRST_SALE_LAST_BUY;
                    break;
                case ConstantMap::SELF_INTERNAL_TRADE_FIRST_BUY_LAST_SALE: //内贸自营-先采后销
                    $query .= " and a.type = " . ConstantMap::PROJECT_TYPE_SELF_INTERNAL_TRADE . ' and c.buy_sell_type = ' . ConstantMap::FIRST_BUY_LAST_SALE;
                    break;
                default:
                    $query .= " and a.type = " . $attr['project_type'];
                    break;
            }
            $projectType = $attr['project_type'];
            unset($attr['project_type']);
        }

        $sql = 'select {col} from t_project a 
                left join t_system_user b on b.user_id = a.manager_user_id 
                left join t_project_base c on c.project_id = a.project_id 
                left join t_corporation d on d.corporation_id = a.corporation_id 
                left join t_partner up on up.partner_id = c.up_partner_id 
                left join t_partner dp on dp.partner_id = c.down_partner_id 
                left join t_system_user su on su.user_id = a.create_user_id ' . $this->getWhereSql($attr) . $query . ' 
                and a.status >=' . Project::STATUS_SUBMIT . ' and a.corporation_id in (' . $user['corp_ids'] . ') 
                and (exists(select contract_id from t_contract where project_id = a.project_id and status >= ' . Contract::STATUS_SUBMIT . ' and is_main = ' . ConstantMap::CONTRACT_MAIN . ') or a.create_user_id = -1) 
                order by a.project_id desc {limit}'; //主合同商务确认提交或者是历史数据导入的项目可以添加子合同
        $fields = 'a.project_id, a.project_code, a.corporation_id, d.name as corp_name, a.type, a.status, a.status_time, b.name, c.buy_sell_type, 
                   c.up_partner_id, c.down_partner_id,c.goods_name, up.name as up_partner_name, dp.name as down_partner_name, su.name as creater_name, a.create_time';
        $data = $this->queryTablesByPage($sql, $fields);
        if (Utility::isNotEmpty($data['data']['rows'])) {
            $map = Map::$v;
            foreach ($data['data']['rows'] as $key => $row) {
                $type_desc = $map['project_type'][$row['type']];
                if (!empty($row["buy_sell_type"])) {
                    $type_desc .= '-' . $map['purchase_sale_order'][$row["buy_sell_type"]];
                }
                $data['data']['rows'][$key]['project_type_desc'] = $type_desc;
            }
        }
        if (!empty($projectType)) {
            $attr['project_type'] = $projectType;
        }
        $data['search'] = $attr;
        $this->pageTitle = '项目列表';

        return $data;
    }

    public function getAddData($type = 0) {
        $project_id = Mod::app()->request->getParam('id');
        $type = Mod::app()->request->getParam('type');
        if (!Utility::checkQueryId($project_id) || !Utility::checkQueryId($type)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        /*if (!ProjectService::checkProjectExist($project_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PROJECT_NOT_EXIST, array('project_id' => $project_id)));
        }*/
        $project = Project::model()->findByPk($project_id);
        if (empty($project)) {
            $this->returnError(BusinessError::outputError(OilError::$PROJECT_NOT_EXIST, array('project_id' => $project_id)));
        }

        $pageTitle = $this->newUIPrefix ? '' : '商务确认＞';
        if ($type == ConstantMap::CONTRACT_CATEGORY_SUB_BUY) {
            $pageTitle .= '增加采购合同';
        } elseif ($type == ConstantMap::CONTRACT_CATEGORY_SUB_SALE) {
            $pageTitle .= '增加销售合同';
        } else {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
//        $goods = Goods::model()->findAllToArray(array("condition" => 'status = :status', "order" => "parent_id asc,order_index asc", "params" => array('status' => ConstantMap::STATUS_VALID)));
        //$goods = Goods::model()->findAllToArray('status = :status', array('status' => ConstantMap::STATUS_VALID));
        $goods = Goods::getActiveTreeTable();
        $project = Project::model()->findByPk($project_id);
        $projectInfo = $project->getAttributes(array('project_id', 'project_code', 'type', 'corporation_id', 'remark'));
        $data['project_id'] = $project_id;
        $data['corporation_id'] = $projectInfo['corporation_id'];
        $data['type'] = $type;
        $data['is_main'] = ConstantMap::CONTRACT_NOT_MAIN;
        $data['contract_default_delivery_term'] = ConstantMap::CONTRACT_DEFAULT_DELIVERY_TERM;
        //单位换算比
        $data['contractGoodsUnitConvert'] = ConstantMap::CONTRACT_GOODS_UNIT_CONVERT;
        $data['contractGoodsUnitConvertValue'] = ConstantMap::CONTRACT_GOODS_UNIT_CONVERT_VALUE;

        $this->pageTitle = $pageTitle;
        $attachments = Project::getAttachment($project_id);
        return array('goods' => $goods, 'project' => $projectInfo, 'data' => $data, 'attachments' => $attachments);
    }

    public function actionEdit() {
        $contract_id = Mod::app()->request->getParam("id");
        if (!Utility::checkQueryId($contract_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR), "/businessConfirm/");
        }

        $contract = Contract::model()->with('extra', 'goods', 'agentDetail', 'payments')->findByPk($contract_id);
        if (empty($contract->contract_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PROJECT_SUB_CONTRACT_NOT_EXIST, array('contract_id' => $contract_id)));
        }

        if (!$this->checkIsCanEdit($contract->status)) {
            $this->renderError(BusinessError::outputError(OilError::$SUB_CONTRACT_NOT_ALLOW_EDIT));
        }

//        $goods = Goods::model()->findAllToArray(array("condition" => 'status = :status', "order" => "goods_id desc", "params" => array('status' => ConstantMap::STATUS_VALID)));
        $goods = Goods::getActiveTreeTable();
        $contractInfo = $contract->getAttributes(true, array("contract_status", "start_date", "end_date", "old_status", "create_user_id", "create_time", "update_user_id", "update_time"));
        $contractInfo['delivery_term'] = empty($contractInfo['delivery_term']) && $contractInfo['delivery_mode'] == 0 ? date("Y-m-d", strtotime("+" . ConstantMap::CONTRACT_DEFAULT_DELIVERY_TERM . " day")) : $contractInfo['delivery_term'];
        $contractInfo['days'] = is_null($contractInfo['days']) ? ConstantMap::CONTRACT_DEFAULT_DAYS : $contractInfo['days'];
        $contractInfo['delivery_mode'] = empty($contractInfo['delivery_mode']) ? 0 : $contractInfo['delivery_mode'];
        $contractInfo['contract_default_delivery_term'] = ConstantMap::CONTRACT_DEFAULT_DELIVERY_TERM;

        $contractInfo['contractGoodsUnitConvert'] = ConstantMap::CONTRACT_GOODS_UNIT_CONVERT;
        $contractInfo['contractGoodsUnitConvertValue'] = ConstantMap::CONTRACT_GOODS_UNIT_CONVERT_VALUE;
        $project = Project::model()->findByPk($contract->project_id);
        $projectInfo = $project->getAttributes(array('project_id', 'project_code', 'type', 'corporation_id', 'remark'));
        $extra = ContractExtraService::reverseExtraData($contract->extra->items, $contractInfo['type'], $contractInfo['category']);
        $goodsItem = ContractGoodsService::reverseContractGoodsItems($contract->goods, $contract->agentDetail, $contractInfo['type'], $contractInfo['exchange_rate']);
        $paymentPlans = PaymentPlanService::reversePaymentPlans($contract->payments);
        $pageTitle = $this->newUIPrefix ? '' : '商务确认＞';
        if ($contractInfo['type'] == ConstantMap::CONTRACT_CATEGORY_SUB_BUY) {
            $pageTitle .= '修改采购合同';
        } elseif ($contractInfo['type'] == ConstantMap::CONTRACT_CATEGORY_SUB_SALE) {
            $pageTitle .= '修改销售合同';
        } else {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        $this->pageTitle = $pageTitle;
        $attachments = Project::getAttachment($contract->project_id);
        $contractInfo['goods_can_edit'] = 1;
        //如果是拆分后的合同  商品不能编辑
        if ($this->contractIsSplit($contractInfo['split_type'],$contractInfo['original_id'])) {
            $contractInfo['goods_can_edit'] = 0;
        }
        $this->render("edit", array('goods' => $goods, 'project' => $projectInfo, 'data' => $contractInfo, 'extra' => $extra, 'goodsItem' => $goodsItem, 'payments' => $paymentPlans, 'attachments' => $attachments));
    }

    public function actionSave() {
        $params = Mod::app()->request->getParam('data');
        Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' pass params are:' . json_encode($params));
        if (empty($params['project_id']) && empty($params['category']) && empty($params['contract_id'])) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        $goodsItems = $params['contractGoods'];
        $planParams = $params['paymentPlans'];
        $extraParams = $params['extra'];
        unset($params['contractGoods']);
        unset($params['paymentPlans']);
        unset($params['extra']);

        //项目是否存在
        /*if (!ProjectService::checkProjectExist($params['project_id'])) {
            $this->returnError(BusinessError::outputError(OilError::$PROJECT_NOT_EXIST, array('project_id' => $params['project_id'])));
        }*/
        $project = Project::model()->findByPk($params['project_id']);
        if (empty($project)) {
            $this->returnError(BusinessError::outputError(OilError::$PROJECT_NOT_EXIST, array('project_id' => $params['project_id'])));
        }

        if (ContractService::isHaveSameGoods($goodsItems)) {
            $this->returnError(BusinessError::outputError(OilError::$TRANSACTION_DETAIL_GOODS_NAME_REPEAT));
        }

        //商品交易明细&交易代理费参数拆分
        $goodsTransactionInfo = ContractGoodsService::formatTransactionData($goodsItems);
        $contractGoodsParams = $goodsTransactionInfo['contractGoods'];
        $contractAgentFeeParams = $goodsTransactionInfo['contractAgent'];

        if ($params['category'] != ConstantMap::BUY_SALE_CONTRACT_TYPE_AGENT_IMPORT) {
            unset($contractAgentFeeParams);
            $params['agent_id'] = 0;
            $params['agent_type'] = 0;
        }

        //参数校验
        if (empty($params['tempSaveOperate'])) { //保存操作
            //不允许添加子合同
            if (!ProjectService::checkIsCanAddSubContract($params['project_id'])) {
                $this->returnError(BusinessError::outputError(OilError::$PROJECT_NOT_ALLOW_ADD_SUB_CONTRACT));
            }

            //项目合同信息参数校验
            $paramsCheckRes = ContractService::checkParamsValid($params);
            if ($paramsCheckRes !== true) {
                $this->returnError($paramsCheckRes);
            }

            //项目合同补充信息参数校验
            $paramsCheckRes = ContractExtraService::checkParamsValid($extraParams, $params['type'], $params['category']);
            if ($paramsCheckRes !== true) {
                $this->returnError('合同条款中' . $paramsCheckRes);
            }

            //商品交易明细参数校验
            $paramsCheckRes = ContractGoodsService::checkParamsValid($params['type'], $params['price_type'], $contractGoodsParams, $params['exchange_rate']);
            if ($paramsCheckRes !== true) {
                $this->returnError($paramsCheckRes);
            }

            //交易代理费参数校验
            if ($params['category'] == ConstantMap::BUY_SALE_CONTRACT_TYPE_AGENT_IMPORT && $params['agent_id'] > 0) {
                $paramsCheckRes = ContractAgentDetailService::checkParamsValid($contractAgentFeeParams, $params['exchange_rate']);
                if ($paramsCheckRes !== true) {
                    $this->returnError($paramsCheckRes);
                }
            }

            //收付款计划参数校验
            $paramsCheckRes = PaymentPlanService::checkParamsValid($planParams, $params['amount'], $params['type']);
            if ($paramsCheckRes['code'] == ConstantMap::INVALID) {
                $this->returnError($paramsCheckRes['error_msg']);
            }
        }

        if (!empty($params['contract_id'])) {
            $contract = Contract::model()->findByPk($params['contract_id']);
        }

        $isNew = 0;
        if (empty($contract->contract_id)) {
            $isNew = 1;
            $contract = new Contract();
            $contract->num = ContractService::getSubContractNum($params['project_id'], $params['type']);
        } else {
            if (!$this->checkIsCanEdit($contract->status)) {
                $this->returnError(BusinessError::outputError(OilError::$SUB_CONTRACT_NOT_ALLOW_EDIT));
            }
        }

        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {
            //项目合同信息保存
            unset($params["contract_id"]);
            $contract->status = empty($params['tempSaveOperate']) ? Contract::STATUS_SAVED : Contract::STATUS_TEMP_SAVE;
            if ($contract->status != $contract->getOldAttribute("status")) {
                $contract->status_time = Utility::getDateTime();
            }
            $contract->setAttributes($params, false);
            if (empty($contract->delivery_term)) $contract->delivery_term = null;
            $contract->save();

            //合同补充信息保存
            $contractExtra = ContractExtra::model()->find('contract_id = :contractId and project_id = :projectId', array('contractId' => $contract->contract_id, 'projectId' => $params['project_id']));
            if (empty($contractExtra->contract_id)) {
                $contractExtra = new ContractExtra();
                $contractExtra->contract_id = $contract->contract_id;
                $contractExtra->project_id = $params['project_id'];
                $contractExtra->status = 0;
            }
            $contractExtra->content = json_encode($extraParams);
            $contractExtra->save();

            //商品交易明细&代理费保存
            if (!$this->contractIsSplit($contract->split_type, $contract->original_id)) {
                if (!empty($goodsItems) && Utility::isNotEmpty($goodsItems)) {
                    ContractGoodsService::saveContractGoodsAndAgentFee($goodsItems, $contract->contract_id, $params['tempSaveOperate']);
                }
            }

            //收付款计划保存
            if (!empty($planParams) && Utility::isNotEmpty($planParams)) {
                PaymentPlanService::savePaymentPlanItems($planParams, $params['project_id'], $contract->contract_id);
            }

            ContractService::generateContractGroup($contract);

            /*if ($isNew == 1) {
                TaskService::addTasks(Action::ACTION_10, $contract->contract_id, ActionService::getActionRoleIds(Action::ACTION_10), 0, $contract->corporation_id);
            }*/

            $trans->commit();

            $this->returnSuccess($contract->contract_id);
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
     * @desc 是否是拆分的子合同
     * @param $contractInfo
     * @return bool
     */
    private function contractIsSplit($split_type,$original_id){
        return $split_type == Contract::SPLIT_TYPE_SPLIT && $original_id > 0;
    }

    /**
     * 判断是否可以修改，子类需要修改该方法
     * @param $status
     * @return bool
     */
    public function checkIsCanEdit($status) {
        if ($status < Contract::STATUS_SUBMIT) {
            return true;
        } else {
            return false;
        }
    }
}