<?php
class QuotaController  extends AttachmentController
{
    public function pageInit() {
        $this->rightCode="quota";
        $this->newUIPrefix = 'new_';
    }

    public function actionIndex()
    {
//        $attr = $_GET['search'];
        $attr = $this->getSearch();
        if(!is_array($attr) || !array_key_exists("b.status",$attr))
        {
            $attr["b.status"]="19";
        }
        $user = Utility::getNowUser();
        $query="";
        $projectType = 0;
        if (!empty($attr['project_type'])) {
            switch ($attr["project_type"]) {
                case ConstantMap::SELF_IMPORT_FIRST_SALE_LAST_BUY: //进口自营-先销后采
                    $query .= " and c.type = " . ConstantMap::PROJECT_TYPE_SELF_IMPORT . ' and e.buy_sell_type = ' . ConstantMap::FIRST_SALE_LAST_BUY;
                    break;
                case ConstantMap::SELF_IMPORT_FIRST_BUY_LAST_SALE: //进口自营-先采后销
                    $query .= " and c.type = " . ConstantMap::PROJECT_TYPE_SELF_IMPORT . ' and e.buy_sell_type = ' . ConstantMap::FIRST_BUY_LAST_SALE;
                    break;
                case ConstantMap::SELF_INTERNAL_TRADE_FIRST_SALE_LAST_BUY: //内贸自营-先销后采
                    $query .= " and c.type = " . ConstantMap::PROJECT_TYPE_SELF_INTERNAL_TRADE . ' and e.buy_sell_type = ' . ConstantMap::FIRST_SALE_LAST_BUY;
                    break;
                case ConstantMap::SELF_INTERNAL_TRADE_FIRST_BUY_LAST_SALE: //内贸自营-先采后销
                    $query .= " and c.type = " . ConstantMap::PROJECT_TYPE_SELF_INTERNAL_TRADE . ' and e.buy_sell_type = ' . ConstantMap::FIRST_BUY_LAST_SALE;
                    break;
                default:
                    $query .= " and c.type = " . $attr['project_type'];
                    break;
            }
            $projectType = $attr['project_type'];
            unset($attr['project_type']);
        }

        $fields  = "c.type as project_type, c.project_code, e.buy_sell_type as base_buy_sell_type, cg.project_id, b.contract_id, b.type as contract_type, cg.corporation_id, co.name as corp_name,
                    b.status as contract_status, b.num, u.name, su.name as create_name, b.is_main, b.create_time, cg.up_partner_id,cg.down_partner_id,up.name as up_partner_name, dp.name as down_partner_name,b.split_type,b.original_id";

        /*$sql1 = "select ".$fields." from t_check_detail a
                 left join t_contract_group cg on cg.contract_id=a.obj_id 
                 left join t_contract b on cg.contract_id=b.contract_id
                 left join t_project c on c.project_id=cg.project_id
                 left join t_project_base e on e.project_id=c.project_id
                 left join t_system_user u on u.user_id=c.manager_user_id 
                 left join t_corporation co on co.corporation_id=cg.corporation_id 
                 left join t_partner up on up.partner_id=cg.up_partner_id 
                 left join t_partner dp on dp.partner_id=cg.down_partner_id 
                 left join t_system_user su on su.user_id=b.create_user_id
                ". $this->getWhereSql($attr) . $query. " and a.business_id=2 and b.status>=".Contract::STATUS_RISK_CHECKED . ' group by b.contract_id order by b.contract_id desc';

        $sql = 'select {col} from ('.$sql1.') as dd where 1=1 {limit}';*/
        $sql = 'select {col} from t_contract_group cg 
                left join t_contract b on cg.contract_id=b.contract_id
                left join t_project c on c.project_id=cg.project_id
                left join t_project_base e on e.project_id=c.project_id
                left join t_system_user u on u.user_id=c.manager_user_id 
                left join t_corporation co on co.corporation_id=cg.corporation_id 
                left join t_partner up on up.partner_id=cg.up_partner_id 
                left join t_partner dp on dp.partner_id=cg.down_partner_id 
                left join t_system_user su on su.user_id=b.create_user_id '.$this->getWhereSql($attr).$query.' and '.AuthorizeService::getUserDataConditionString('cg').' and b.status >= '.Contract::STATUS_RISK_CHECKED.' order by b.contract_id desc {limit}';
        if (!empty($user['corp_ids'])) {
            $data=$this->queryTablesByPage($sql,$fields);
        } else {
            $data = array();
        }
        if (!empty($projectType)) {
            $attr['project_type'] = $projectType;
        }
        $data["search"]=$attr;
        $this->render("index", $data);
    }

    public function actionDetail() {
        $contract_id = $_GET['contract_id'];
        $is_main = $_GET['is_main'];
        if (!Utility::checkQueryId($contract_id))
        {
            $this->returnError("非法操作！", "/quota/");
        }
        $contract = ProjectService::getContractDetailModel($contract_id);
        if(empty($contract) || empty($contract->project)) {
            $this->renderError(BusinessError::outputError(OilError::$PROJECT_NOT_EXIST, array('project_id'=>$contract->project_id)), "/quota/");
        }
        $checkLogs = FlowService::getCheckLog($contract->contract_id,"2,3");
        $this->render('detail', array('contract'=>$contract, 'checkLogs'=>$checkLogs));
    }

    public function actionEdit() {
        $this->editDetail('edit');
    }

    public function actionAjaxEdit() {
        $this->layout = 'empty';
        $this->editDetail('ajaxEdit');
    }

    private function editDetail($view = 'edit') {
        $contract_id = $_GET['contract_id'];
        $is_main = $_GET['is_main'];
        if (!Utility::checkQueryId($contract_id))
        {
            $this->renderError(BusinessError::outputError(OilError::$NOT_INT_ERR, array('str'=>"合同ID")), "/quota/", true);
        }
        $contract = ProjectService::getContractDetailModel($contract_id);
        if(empty($contract) || empty($contract->project)) {
            $this->renderError(BusinessError::outputError(OilError::$PROJECT_NOT_EXIST, array('project_id'=>$contract->project_id)), "/quota/");
        }
        if(!$this->checkIsCanEdit($contract->status)) {
            $this->renderError('合同状态不允许填写额度', "/quota/", true);
        }
        $managers = UserService::getProjectManageUsers();
        list($buyContract, $sellContract) = $this->handleCompanyIds($contract);
        $upManagers = empty($buyContract)?$managers:array_merge($buyContract, $managers);
        $downManagers = empty($sellContract)?$managers:array_merge($sellContract, $managers);
        $this->render($view, array('contract'=>$contract, 'upManagers'=>$upManagers, 'downManagers'=>$downManagers));
    }

    public function actionSave() {
        $contract_id = $_POST['contract_id'];
        $is_main = $_POST['is_main'];
        if (!Utility::checkQueryId($contract_id))
        {
            $this->returnError("参数错误", "/quota/");
        }

        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        $saved = true;
        if($is_main) {
            // 主合同信息, 获取第一章主合同信息
            $contracts = Contract::model()->findAll(
                array(
                    'condition'=>'project_id = :project_id and is_main = 1',
                    'params'=>array('project_id'=>$_POST['project_id']),
                    'order'=>'t.type asc'
                    )
                );
            if(count($contracts) < 1) {
                $saved = $this->returnError("合同不存在", "/quota/");
            }
            $contract = $contracts[0];
            if(!$this->checkIsCanEdit($contract->status)) {
                $this->returnError('合同状态不允许填写额度', "/quota/");
            }
            try {
                foreach($contracts as $subContract) {
                    $thisSaved = $this->saveContractQuota($subContract);
                    $saved = $saved && $thisSaved;
                }
            } catch (Exception $e) {
                try {
                    $trans->rollback();
                } catch (Exception $ee) {
                }
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);

                $this->returnError('保存失败:'.$e->getMessage());
            }
        } else {
            // 子合同信息
            $contract = Contract::model()->findByPk($contract_id);
            if(!$this->checkIsCanEdit($contract->status)) {
                $this->returnError('合同状态不允许填写额度', "/quota/");
            }
            try {
                $saved = $this->saveContractQuota($contract);
            } catch (Exception $e) {
                try {
                    $trans->rollback();
                } catch (Exception $ee) {
                }
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);

                $this->returnError('保存失败:'.$e->getMessage());
            }
        }
        if ($saved) {
            FlowService::startFlowForCheck3($contract->contract_id, $contract->manager_user_id);
            //TaskService::addTasks(Action::ACTION_13, $contract->contract_id, ActionService::getActionRoleIds(Action::ACTION_13), 0, $contract->corporation_id);
            TaskService::doneTask($contract->contract_id, Action::ACTION_12);

            $trans->commit();
            $this->returnSuccess('提交成功');
        } else {
            $this->returnError('保存失败:'.$e->getMessage());
        }
    }

    public function checkIsCanEdit($status) {
        return $status == Contract::STATUS_BUSINESS_REJECT || $status == Contract::STATUS_RISK_CHECKED;
    }

    private function handleCompanyIds($contract) {
        $arrays = array('0'=>array(), '1'=>array());
        if(!empty($contract->relative)) {
            $relative = $contract->relative;
            $buyContract = ($relative->type == ConstantMap::BUY_TYPE || $relative->type == ConstantMap::CONTRACT_CATEGORY_SUB_BUY)?$relative:$contract;
            $sellContract = ($relative->type == ConstantMap::SALE_TYPE || $relative->type == ConstantMap::CONTRACT_CATEGORY_SUB_SALE)?$relative:$contract;
        } else {
            if($contract->type == ConstantMap::BUY_TYPE || $contract->type == ConstantMap::CONTRACT_CATEGORY_SUB_BUY) {
                $buyContract = $contract;
                $sellContract = null;
            } else {
                $sellContract = $contract;
                $buyContract = null;
            }
        }
        if(!empty($buyContract)) {
            if(!empty($buyContract->agent)){
                $arrays['0'][] = array(
                        'user_id'=>'-'.$buyContract->agent['partner_id'],
                        'user_name'=>'代理:'.$buyContract->agent['name'],
                        'name'=>'代理:'.$buyContract->agent['name']);
            }
            $arrays['0'][] = !empty($buyContract->partner)?array(
                    'user_id'=>'-'.$buyContract->partner['partner_id'],
                    'user_name'=>'上游:'.$buyContract->partner['name'],
                    'name'=>'上游:'.$buyContract->partner['name']):array();
        }
        if(!empty($sellContract)) {
            $arrays['1'][] = !empty($sellContract->partner)?array(
                    'user_id'=>'-'.$sellContract->partner['partner_id'],
                    'user_name'=>'下游:'.$sellContract->partner['name'],
                    'name'=>'下游:'.$sellContract->partner['name']):array();
        }
        return $arrays;
    }

    private function saveContractQuota($contract) {
        if(($contract->is_main && $contract->type == ConstantMap::BUY_TYPE) || (!$contract->is_main && $contract->type == ConstantMap::CONTRACT_CATEGORY_SUB_BUY)) {
            // 采购
            $quotas = $_POST['upQuotaItems'];
            $saved = $this->saveQuotas($contract, $quotas);
        } else if(($contract->is_main && $contract->type == ConstantMap::SALE_TYPE) || (!$contract->is_main && $contract->type == ConstantMap::CONTRACT_CATEGORY_SUB_SALE)) {
            // 销售
            $quotas = $_POST['downQuotaItems'];
            $saved = $this->saveQuotas($contract, $quotas);
        }
        return $saved;
    }

    private function saveQuotas($contract, $quotas) {
        $saved = true;
        if(is_array($quotas))
        foreach ($quotas as $quota) {
            if(!$saved) {
                break;
            }
            if(empty($quota['quota'])) {
                continue;
            }
            $quotaModel = new ProjectCreditDetail();
            $quotaModel->contract_id = $contract->contract_id;
            $quotaModel->project_id = $contract->project_id;
            if($quota['user_id'] < 0) {
                $quotaModel->to_user_id = 0;
                $quotaModel->corporation_id = trim($quota['user_id'], '-');
            } else {
                $quotaModel->to_user_id = $quota['user_id'];
            }
            $quotaModel->amount = $quota['quota'];
            $quotaModel->remark = $quota['remark'];
            $quotaModel->create_user_id = Utility::getNowUserId();
            $quotaModel->update_user_id = Utility::getNowUserId();
            $quotaModel->create_time = new CDbExpression('now()');
            $quotaModel->update_time = new CDbExpression('now()');
            $thisSaved = $quotaModel->save();
            $saved = $thisSaved && $saved;
        }
        return $saved;
    }

    private function getQuotas($contract) {
        if($contract['type'] == ConstantMap::CONTRACT_CATEGORY_SUB_SALE) {
            // 销售
            $quotas = $this->getQuota($contract['contract_id'], $contract['project_id']);
        } else {
            // 采购
            $quotas = $this->getQuota($contract['contract_id'], $contract['project_id']);
        }
        return $quotas;
    }

    private function getQuota($contract_id, $project_id) {
        $quotas = ProjectCreditDetail::model()->findAll(array(
            'condition'=>'contract_id=:contract_id and project_id=:project_id',
            'params'=>array(
                'contract_id'=>$contract_id,
                'project_id'=>$project_id
                )
            ));
        $arrays = array();
        foreach ($quotas as $quota) {
            $array = $quota->attributes;
            if(!empty($quota->to_user_id)) {
                $array['name'] = $quota->manager->name;
            } else {
                $array['name'] = $quota->partner->name;
            }
            $arrays[] = $array;
        }
        return $arrays;
    }
}