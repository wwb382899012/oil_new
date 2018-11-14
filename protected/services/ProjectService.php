<?php

/**
 * Created by youyi000.
 * DateTime: 2016/7/8 10:46
 * Describe：
 */
class ProjectService {
    /**
     * 项目状态信息
     *  其中status 0表示未完成，1表示已完成，2表示已拒绝，3表示项目终止
     * @var array
     */
    public static $projectProgress = array(
        "1" => array("name" => "①项目发起", "status" => 0, "startStatus" => Project::STATUS_REJECT, "endStatus" => Project::STATUS_SUBMIT,),
        "2" => array("name" => "②商务确认", "status" => 0,  "startStatus" => Project::STATUS_SUBMIT, "endStatus" => Project::STATUS_CONTRACT_CHECKING,),
        "3" => array("name" => "③合同初审", "status" => 0,  "startStatus" => Project::STATUS_CONTRACT_CHECKING, "endStatus" => Project::STATUS_UP_DOWN_CONTRACT_STAMP,),
        "4" => array("name" => "④项目合同", "status" => 0,"startStatus" => Project::STATUS_UP_DOWN_CONTRACT_STAMP, "endStatus" => Project::STATUS_PAY_CONFIRM,),
        "5" => array("name" => "⑤项目结算", "status" => 0, "startStatus" => Project::STATUS_UP_PREPAY_CONFIRM, "endStatus" => Project::STATUS_SETTLE_CONFIRM,),
        "6" => array("name" => "⑥项目收放款", "status" => 0, "startStatus" => Project::STATUS_SETTLE_CONFIRM, "endStatus" => Project::STATUS_DONE,),
    );

    /**
     * 更新项目状态
     * @param $projectId
     * @param $status
     * @param null $oldStatus
     * @return int|string
     */
    public static function updateProjectStatus($projectId, $status, $oldStatus = null) {
        $obj = Project::model()->findByPk($projectId);
        if (empty($obj->project_id)) {
            return "当前项目不存在！";
        }
        if ($oldStatus !== null && $obj->status != $oldStatus) {
            return "当前项目原状态与条件状态不一致！";
        }
        if ($obj->status != $status) {
            $obj->old_status = $obj->status;
            $obj->status = $status;
            $obj->status_time = date("Y-m-d H:i:s");
            $obj->update_user_id = Utility::getNowUserId();
            $obj->update_time = date("Y-m-d H:i:s");
            $res = $obj->save();
            if ($res === true) {
                return 1;
            } else {
                return $res;
            }
        } else {
            return 1;
        }
    }

    /**
     * 获取项目的详细交易方案
     * @param $id
     * @return array
     */
    /*public static function getProjectDetail($id)
    {
        $sql="select * from t_project_detail where project_id=".$id."";
        $data= Utility::query($sql);
        if(Utility::isNotEmpty($data))
            return $data[0];
        else
            return null;
    }*/

    /**
     * 获取项目的当前状态
     * @param $id
     * @return array
     */
    public static function getProjectStatus($id) {
        $sql = "select project_id,status from t_project where project_id=" . $id . "";
        $data = Utility::query($sql);
        if (Utility::isNotEmpty($data)) {
            return $data[0]['status'];
        } else {
            return null;
        }
    }

    /**
     * 获取风控财务初审确切状态
     * @param $id
     * @return array
     */
    public static function getCheckStatus($id, $checkId = 0) {
        $sql = "select obj_id,role_id,status from t_check_detail where check_id=" . $checkId . " and obj_id=" . $id . " and role_id in (10,11)";
        $data = Utility::query($sql);
        $statusArr = array();
        if (Utility::isNotEmpty($data)) {
            foreach ($data as $key => $value) {
                $statusArr[$value['role_id']] = $value;
            }
        }

        return $statusArr;
    }

    /**
     * 获取上游的放款计划
     * @param $projectId
     * @param int $isBak 是否原数据
     * @return array
     */
    public static function getUpPayments($projectId, $isBak = 0) {
        if ($isBak == 1) {
            $tableName = "t_project_pay_plan_bak";
        } else {
            $tableName = "t_project_pay_plan";
        }
        $sql = "select * from " . $tableName . " where project_id=" . $projectId . " order by period asc";

        return Utility::query($sql);
    }

    /**
     * 获取上游的放款计划,统计总额
     * @param $projectId
     * @param int $isBak 是否原数据
     * @return array
     */
    public static function getUpPaymentsWithTotal($projectId, $isBak = 0) {
        $result = array();
        $upPayment = self::getUpPayments($projectId, $isBak);
        if (is_array($upPayment)) {
            $totalAmount = 0;
            foreach ($upPayment as $item) {
                $totalAmount += $item['amount'];
            }
            $result['total']['total_amount'] = $totalAmount;
            $result['plan'] = $upPayment;
        }

        return $result;
    }

    /**
     * 获取下游的还款计划
     * @param $projectId
     * @param int $type
     * @param int $isBak 是否原数据
     * @return array
     */
    public static function getDownReturnPlans($projectId, $type = - 1, $isBak = 0) {
        if ($isBak == 1) {
            $tableName = "t_return_plan_bak";
        } else {
            $tableName = "t_return_plan";
        }
        if ($type < 0) {
            $sql = "select * from " . $tableName . " where project_id=" . $projectId . " order by period asc";
        } else {
            $sql = "select * from " . $tableName . " where project_id=" . $projectId . " and type=" . $type . " order by period asc";
        }

        return Utility::query($sql);
    }

    /**
     * 获取下游的还款计划,统计总额
     * @param $projectId
     * @param int $type
     * @param int $isBak 是否原数据
     * @return array
     */
    public static function getDownReturnPlansWithTotal($projectId, $type = - 1, $isBak = 0) {
        $result = array();
        $downReturnPlans = self::getDownReturnPlans($projectId, $type, $isBak);
        if (is_array($downReturnPlans)) {
            $totalAmount = 0;
            foreach ($downReturnPlans as $item) {
                $totalAmount += $item['amount'];
            }
            $result['total']['total_amount'] = $totalAmount;
            $result['plan'] = $downReturnPlans;
        }

        return $result;
    }

    /**
     * @desc 获取项目详细信息
     * @param $projectId
     * @return array
     */
    public static function getProjectDetail($projectId) {
        $sql = "select a.project_id, a.project_code, a.project_name, a.corporation_id, a.manager_user_id, a.storehouse_id, a.type, a.status as status, a.status_time, a.remark, 
                b.up_partner_id, b.down_partner_id, b.buy_sell_type, b.price_type, b.purchase_currency, b.sell_currency, b.plan_describe, c.name as up_partner_name, p.name as agent_name, 
                d.name as down_partner_name, e.name as corporation_name, f.name as manager_name, g.name as storehouse_name, h.name as create_name, a.create_time, b.agent_id   
                from t_project a 
                left join t_project_base b on b.project_id = a.project_id 
                left join t_partner c on c.partner_id = b.up_partner_id
                left join t_partner d on d.partner_id = b.down_partner_id 
                left join t_partner p on p.partner_id = b.agent_id 
                left join t_corporation e on e.corporation_id = a.corporation_id 
                left join t_system_user f on f.user_id = a.manager_user_id 
                left join t_storehouse g on g.store_id = a.storehouse_id 
                left join t_system_user h on h.user_id = a.create_user_id  
                where a.project_id = " . $projectId;

        return Utility::query($sql);
    }

    /**
     * 获取结算后的交易方案数据
     * @param $id
     * @return null
     */
    public static function getSettledDetail($id) {
        $sql = "select
                  a.*,
                  u.settle_id as up_settle_id,u.price as up_price,u.quantity as up_quantity,u.amount as up_amount,
                  d.settle_id as down_settle_id,d.price as down_price,d.quantity as down_quantity,d.amount as down_amount
                  ,ud.pay_date,dd.pay_date as return_date
                  ,pc.is_down_receive,pc.is_down_first,pc.is_contract,pc.is_bond 
                  ,pc.is_guarantee ,pc.is_goods ,pc.invoice_type,pc.invoice_remark,pc.extra,pc.check_status
                from t_project a
                  left join t_settlement u on a.project_id=u.project_id and u.type=1
                  left join t_settlement d on a.project_id=d.project_id and d.type=2
                  
                  left join t_project_detail ud on a.project_id=ud.project_id and ud.type=1
                  left join t_project_detail dd on a.project_id=dd.project_id and dd.type=2
                  
                  left join t_pay_condition pc on pc.project_id=a.project_id
                  
                where a.project_id=" . $id . "";
        $data = Utility::query($sql);
        if (Utility::isEmpty($data)) {
            return null;
        }
        $finance = $data[0];
        if (empty($finance["up_settle_id"]) || empty($finance["down_settle_id"])) {
            $detail = ProjectService::getDetail($id);
            $detail = $detail[0];
            if (empty($finance["up_settle_id"])) {
                $finance["up_price"] = $detail["up_price"];
                $finance["up_quantity"] = $detail["up_quantity"];
                $finance["up_amount"] = $detail["up_amount"];
            }
            if (empty($finance["down_settle_id"])) {
                $finance["down_price"] = $detail["down_price"];
                $finance["down_quantity"] = $detail["down_quantity"];
                $finance["down_amount"] = $detail["down_amount"];
            }
        }

        return $finance;

    }

    /**
     * 获取项目的所有合同信息
     * @param $projectId
     * @return array
     */
    public static function getAllContracts($projectId) {
        if (empty($id)) {
            return array();
        }
        $sql = "select contract_id,type,file_url from t_contract where project_id=" . $id . " and status>0  order by type asc";
        $data = Utility::query($sql);
        $attachments = array();
        foreach ($data as $v) {
            $attachments[$v["type"]] = $v;
        }

        return $attachments;
    }

    /**
     * 根据结算ID更新项目状态
     * @param $settleId
     * @param $status
     * @param null $oldStatus
     * @return int|string
     */
    public static function updateProjectStatusWithSettleID($settleId, $status, $oldStatus = null) {
        $settle = Settlement::model()->findByPk($settleId);
        ProjectService::updateProjectStatus($settle->project_id, $status, $oldStatus);
    }


    /**
     * 判断项目是否有下游保证金
     * @param $projectId
     * @return true|false
     */
    public static function isHaveDownFirstAmount($projectId) {
        $sql = "select * from t_return_plan where receive_type=1 and project_id=" . $projectId;
        $data = Utility::query($sql);
        if (Utility::isNotEmpty($data)) {
            return 1;
        }

        return 0;
    }

    /**
     * 根据付款计划ID获取对应付款计划信息
     * @param $projectId
     * @return true|false
     */
    public static function getPlanById($planId) {
        $sql = "select * from t_project_pay_plan where plan_id=" . $planId;

        return Utility::query($sql);
    }

    /**
     * 计算XIRR
     * @param $payments
     * @param $plans
     * @return float|mixed|string
     */
    public static function computeXIRR($payments, $plans) {
        $dArr = array();
        $vArr = array();
        foreach ($payments as $key => $value) {
            $dArr[] = $value['pay_date'];
            $vArr[] = - $value['amount'] / 100;
        }
        foreach ($plans as $key => $value) {
            $dArr[] = $value['return_date'];
            $vArr[] = $value['amount'] / 100;
        }
        $obj = new PHPExcel();
        $xirr = PHPExcel_Calculation_Financial::XIRR($vArr, $dArr, 0.1);
        $result = pow((1 + $xirr), 1 / 12) - 1;

        return $result;
    }


    /**
     * 获取初审合同审核详细信息
     */
    public static function getContractCheckDetail($projectId) {
        $sql = "select a.*,p.project_id,p.project_name,t.type
                 from t_check_detail a
                 left join t_contract t on a.obj_id=t.contract_id and t.type>100 and t.type<=200
                 left join t_project p on t.project_id=p.project_id
                 left join t_check_item c on c.check_id=a.check_id
                 where p.project_id=" . $projectId . " and a.status>0 order by a.check_id desc ";
        $data = Utility::query($sql);

        $check = array();
        if (Utility::isNotEmpty($data)) {
            foreach ($data as $key => $value) {
                $check[$value['business_id']][$value['role_id']][$value['type']][] = $value;
                if ($check[$value['business_id']][$value['role_id']][$value['type']][0]['status'] == 1 && empty($check[$value['business_id']][$value['role_id']][$value['type']][0]['check_status'])) {
                    $check['status'] = 1;
                }
            }
        }

        return $check;
    }

    public static function getContractCheckLogs($projectId) {
        $sql = "select a.*,b.type,b.contract_id,c.role_id
                from t_check_log a,t_contract b,t_check_detail c
                where a.business_id=13 and a.obj_id=b.contract_id 
                 and a.detail_id=c.detail_id
                  and b.project_id=" . $projectId . "
                order by a.id asc";

        $data = Utility::query($sql);
        $logs = array();
        foreach ($data as $v) {
            $logs[$v["type"]][$v["role_id"]] = $v;
        }

        return $logs;

    }


    public static function getCheckContractInfo($projectId) {
        $sql = "select * from t_contract where check_status=1 and project_id=" . $projectId . " order by type asc,contract_id asc";
        $data = Utility::query($sql);
        $logs = array();
        foreach ($data as $v) {
            $logs[$v["type"]] = $v;
        }

        return $logs;

    }

    /**
     * @desc 参数校验
     * @param array @params
     * @return bool|string
     */
    public static function checkParamsValid($params) {
        if (Utility::isNotEmpty($params)) {
            $requiredParams = array('type', 'corporation_id', 'price_type', 'purchase_currency', 'sell_currency', 'manager_user_id');
            if (in_array($params['type'], ConstantMap::$self_support_project_type)) {
                array_push($requiredParams, 'buy_sell_type');
                array_push($requiredParams, 'plan_describe');
            }
            if (in_array($params['type'], ConstantMap::$warehouse_receive_project_type)) {
                array_push($requiredParams, 'storehouse_id');
            }
            if (in_array($params['type'], array_merge(ConstantMap::$channel_buy_project_type, ConstantMap::$warehouse_receive_project_type)) || (in_array($params['type'], ConstantMap::$self_support_project_type) && $params['buy_sell_type'] == ConstantMap::FIRST_BUY_LAST_SALE)) {
                array_push($requiredParams, 'up_partner_id');
            }
            if (in_array($params['type'], array_merge(ConstantMap::$channel_buy_project_type, ConstantMap::$warehouse_receive_project_type)) || (in_array($params['type'], ConstantMap::$self_support_project_type) && $params['buy_sell_type'] == ConstantMap::FIRST_SALE_LAST_BUY)) {
                array_push($requiredParams, 'down_partner_id');
            }
            if(in_array($params['type'], ConstantMap::$buy_select_contract_type) || ($params['type'] == ConstantMap::PROJECT_TYPE_SELF_IMPORT && $params['buy_sell_type'] == ConstantMap::FIRST_BUY_LAST_SALE)) {
                array_push($requiredParams, 'agent_id');
            }
            
            //必填参数校验
            if (Utility::checkRequiredParamsNoFilterInject($params, $requiredParams)) {
                return true;
            }

            if(!empty($params['up_partner_id']) && !empty($params['down_partner_id']) && $params['up_partner_id'] == $params['down_partner_id']) {
                return BusinessError::outputError(OilError::$PARTNER_NOT_ALLOW_REPEAT);
            }
        }

        return BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR);
    }

    /**
     * @desc 检查项目是否存在
     * @param bigint $project_id
     * @return bool
     */
    public static function checkProjectExist($project_id) {
        $project = Project::model()->findByPk($project_id);
        if (empty($project->project_id)) {
            return false;
        }
        $projectBase = ProjectBase::model()->find('project_id = :projectId', array('projectId' => $project_id));
        if (empty($projectBase->base_id)) {
            return false;
        }
        $projectBaseGoods = ProjectBaseGoods::model()->find('project_id = :projectId and base_id = :baseId', array('projectId' => $project_id, 'baseId' => $projectBase->base_id));
        if (empty($projectBaseGoods->detail_id)) {
            return false;
        }

        return true;
    }

    /**
     * @desc 检查项目是否可以添加子合同
     * @param int $project_id
     * @return bool
     */
    public static function checkIsCanAddSubContract($project_id) {
        if (Utility::checkQueryId($project_id)) {
            $project = Project::model()->findByPk($project_id);
            if (empty($project)) {
                return false;
            }
            $mainContract = Contract::model()->find('project_id = :projectId and is_main = :isMain and status >= :status', array('projectId' => $project_id, 'isMain' => ConstantMap::CONTRACT_MAIN, 'status' =>Contract::STATUS_SUBMIT));
            if (!empty($mainContract) || ($project->create_user_id = -1 && $project->status >= Project::STATUS_SUBMIT)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 判断是否可以删除项目
     * @param $status
     * @return bool
     */
    public static function  checkIsCanDel($status)
    {
        if ($status < Project::STATUS_SUBMIT)
        {
            return true;
        }

        return false;
    }

    /**
     * 删除项目
     * @param $projectId
     * @param null $projectModel    项目的对象信息，可以不传，为空会根据$projectId读取
     * @return bool|mixed|string
     */
    public static function deleteProject($projectId,$projectModel=null)
    {
        if (!Utility::checkQueryId($projectId) || $projectId<1) {
            return BusinessError::outputError(OilError::$PARAMS_PASS_ERROR);
        }

        if(empty($projectModel))
            $projectModel=Project::model()->findByPk($projectId);

        if(empty($projectModel))
            return BusinessError::outputError(OilError::$PROJECT_NOT_EXIST, array('project_id' => $projectId));

        if(!self::checkIsCanDel($projectModel->status))
            return BusinessError::outputError(OilError::$PROJECT_NOT_ALLOW_DELETE);

        $res=$projectModel->delete();
        if(!$res)
            return BusinessError::outputError(OilError::$OPERATE_FAILED, array('reason' => $res));

        return true;

    }

    public static function getProjectDetailAsModel($project_id) {
        $base = ProjectBase::model()->with("project", "up_partner", "down_partner")->find(
            array(
                'condition'=>'t.project_id=:project_id',
                'params'=>array('project_id'=>$project_id)
                )
            );
        return $base;
    }

    public static function getContractDetailAsModel($project_id, $type) {
        $contract = Contract::model()->with('agent', 'agentDetail', 'agentDetail.goods', 'manager', 'extra', 'payments')->find(
            array(
                'condition'=>'t.project_id=:project_id and t.type=:type',
                'params'=>array(
                    'project_id'=>$project_id,
                    'type'=>$type
                    )
                )
            );
        return $contract;
    }

    // 是否仅需要上游数据
    public static function checkProjectUpPartnerOnly($project, $base) {
        // 进口自营, 内贸自营, 先采后销
        if(in_array($project->type,
            array(
                ConstantMap::PROJECT_TYPE_SELF_IMPORT,
                ConstantMap::PROJECT_TYPE_SELF_INTERNAL_TRADE)
            ) && $base->buy_sell_type == ConstantMap::FIRST_BUY_LAST_SALE) {
            return true;
        }
        return false;
    }

    // 是否仅需要下游数据
    public static function checkProjectDownPartnerOnly($project, $base) {
        // 进口自营, 内贸自营, 先销后采
        if(in_array($project->type,
            array(
                ConstantMap::PROJECT_TYPE_SELF_IMPORT,
                ConstantMap::PROJECT_TYPE_SELF_INTERNAL_TRADE)
            ) && $base->buy_sell_type == ConstantMap::FIRST_SALE_LAST_BUY) {
            return true;
        }
        return false;
    }


    // 是否需要代理商数据
    public static function checkAgentNeeded($project, $base, $contract) {
        // ((进口代采, 进口渠道) 或者 (进口自营^先采后销)) ^ 代理进口合同
        if((in_array($project->type, array(ConstantMap::PROJECT_TYPE_IMPORT_BUY, ConstantMap::PROJECT_TYPE_IMPORT_CHANNEL)) || ($project->type == ConstantMap::PROJECT_TYPE_SELF_IMPORT && $base->buy_sell_type == ConstantMap::FIRST_SALE_LAST_BUY)) && $contract->category == ConstantMap::BUY_SALE_CONTRACT_TYPE_AGENT_IMPORT ) {
            return true;
        }
        return false;
    }

    // 合同是否需要代理商数据
    public static function checkContractAgentNeeded($contract) {
        // ((进口代采, 进口渠道) 或者 (进口自营^先采后销)) ^ 代理进口合同
        if($contract->category == ConstantMap::BUY_SALE_CONTRACT_TYPE_AGENT_IMPORT ) {
            return true;
        }
        return false;
    }

    //获取合同\主项目信息
    /*public static  function getViewDetail($contract, $project) {
        $renderData = array();
        $renderData['is_main'] = $contract['is_main'];
        $renderData['project'] = $project->attributes;
        $renderData['upPartnerOnly'] = (!in_array(ConstantMap::SALE_TYPE, array_keys($contract['contracts']))) && (!in_array(ConstantMap::CONTRACT_CATEGORY_SUB_SALE, array_keys($contract['contracts'])));
        $renderData['downPartnerOnly'] = (!in_array(ConstantMap::BUY_TYPE, array_keys($contract['contracts']))) && (!in_array(ConstantMap::CONTRACT_CATEGORY_SUB_BUY, array_keys($contract['contracts'])));

        $map = Map::$v;

        if($renderData['is_main']) {
            //项目预算表
            $renderData['attachments'] = Project::getAttachment($project->project_id, ConstantMap::PROJECT_BUDGET_ATTACH_TYPE);
        }

        $needAgent = false;
        foreach($contract['contracts'] as $con) {
            $keyPre = 'sell';
            switch ($con['type']) {
                case ConstantMap::SALE_TYPE:
                    $keyPre = 'sell';
                    break;
                default:
                    $keyPre = 'buy';
                    break;
            }
            // 交易明细
            $renderData[$keyPre . 'Arr'] = ProjectBaseGoodsService::getGoodsInfos($con->goods, $con['type']);
            // 上下游条款
            $extra = json_decode($con->extra->content, true);
            $renderData[$keyPre . 'Extra'] = empty($extra)?array():$extra;
            // 首付款计划
            $renderData[$keyPre . 'Payments'] = PaymentPlanService::reversePaymentPlans($con->payments);
            $renderData[$keyPre . '_contract'] = $con->attributes;
            $renderData[$keyPre . '_partner'] = $con->partner->attributes;
            $renderData[$keyPre . '_manager'] = $sell_contract->manager->attributes;
            //交易主体
            $renderData['corporation'] = $con->corporation->attributes;
            if(!empty($cont->agent)) {
                $needAgent = true;
                $renderData['agent'] = $con->agent->attributes;
            }
            if(!$renderData['is_main'])
                $renderData['project']['buy_sell_desc'] = $map['buy_sell_desc_type'][$con->is_main][$con->type];
            else
                $renderData['project']['buy_sell_desc'] = $map['buy_sell_desc_type'][$renderData['is_main']];
        }
        return $renderData;
    }*/

    /**
     * @desc 检查是否可合同上传
     * @param int $project_id
     * @param int $moduleType
     * @return bool
     */
    public static function checkIsCanContractUpload($project_id, $moduleType = ConstantMap::ELECTRON_DOUBLE_SIGN_CONTRACT_MODULE) {
        if (Utility::checkQueryId($project_id) && Utility::checkQueryId($moduleType)) {
            $query = '';
            if ($moduleType != ConstantMap::FINAL_CONTRACT_MODULE) {
                $contractFileType = ConstantMap::FINAL_CONTRACT_FILE;
                $contractFileStatus = ContractFile::STATUS_CHECK_PASS;
                if ($moduleType == ConstantMap::PAPER_DOUBLE_SIGN_CONTRACT_MODULE) { //纸质双签
                    $contractFileType = ConstantMap::ELECTRON_SIGN_CONTRACT_FILE;
                    $contractFileStatus = ContractFile::STATUS_CHECKING;
                }
                $query .= ' and exists(select file_id from t_contract_file where type=' . $contractFileType . ' and status = ' . $contractFileStatus . ' and project_id = ' . $project_id . ')';
            }
            $sql = 'select project_id from t_project where project_id = ' . $project_id . ' and status >= ' . Project::STATUS_SUBMIT . ' 
                    and exists(select contract_id from t_contract where project_id = ' . $project_id . ' and status >= ' . Contract::STATUS_BUSINESS_CHECKED . ')'. $query;
            $data = Utility::query($sql);
            if (Utility::isNotEmpty($data)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 获取合同详情
     * @param $contractId
     * @return CActiveRecord|null
     */
    public static function getContractDetailModel($contractId)
    {
        /*$with=array("project", "project.base", "project.attachments",
                    "corporation","partner",
                    "partner.usedAmount","partner.contractAmount",
                    "contractGoods", "contractGoods.goods",
                    "extra","payments",
                    "agent", "agentDetail", "agentDetail.agentGoods",
                    "quotas", "quotas.quotaPartner", "quotas.quotaManager",
                    "creator", "project.storehouse");*/
        $with=array(
                    "corporation","partner",
                    "partner.usedAmount","partner.contractAmount",
                    "contractGoods", "contractGoods.goods",
                    "extra","payments",
                    "agent", "agentDetail", "agentDetail.agentGoods",
                    "quotas", "quotas.quotaPartner", "quotas.quotaManager",
                    "creator","originalContractGoods");
        $model=Contract::model()->with($with)->findByPk($contractId);
        if(empty($model))
            return null;
        if($model->is_main )
        {
            $relativeModel=Contract::model()->with($with)
                ->find("t.project_id=".$model->project_id." and t.is_main=1 and t.contract_id<>".$model->contract_id);
            if(!empty($relativeModel))
                $model['relative']=$relativeModel;
        }
        $splitWith = ['partner', 'contractGoods', 'contractGoods.goods'];
        $splitInfo = Contract::model()->with($splitWith)->findAll('t.split_type=1 AND t.original_id=' . $model->contract_id);
        if(!empty($splitInfo)){
            $model['split'] = $splitInfo;
        }
        return $model;
    }

    /**
     * 根据项目类型判断是否是渠道业务
     * @param $projectType
     * @return bool
     */
    public static function checkIsChannel($projectType)
    {
        return in_array($projectType,ConstantMap::$channel_buy_project_type);
    }

    /**
     * 显示合同详情
     * @param $contractModel
     * @param $page
     */
    /*public static function showContractDetail($contractModel,$page)
    {
        $pagePath = "";
        if (!empty($contractModel->relative))
            $pagePath = "/common/contractBasicInfo";
        else
            $pagePath = "/common/contractBasicInfoOneSideOnly";
        $page->renderPartial($pagePath, array("contract"=>$contractModel));

    }*/


    /**
    *   通过项目id获取check item
    */
    public static function getProjectCheckItem($project_id, $businessId) {
        $sql = "select check_id from t_check_item ci where ci.obj_id in (select contract_id from t_contract where t_contract.project_id = '{$project_id}' and is_main=1)
        ";
        $data = Utility::query($sql);
        if(!empty($data[0]) && isset($data[0]['check_id'])) {
            return $data[0]['check_id'];
        }
        return null;
    }

    /**
    *   通过项目id获取check item obj id
    */
    public static function getProjectCheckItemObj($project_id, $businessId) {
        $sql = "select obj_id from t_check_item ci where ci.obj_id in (select contract_id from t_contract where t_contract.project_id = '{$project_id}' and t_contract.is_main=1) ";
        $data = Utility::query($sql);
        if(!empty($data[0]) && isset($data[0]['obj_id'])) {
            return $data[0]['obj_id'];
        }
        return null;
    }


    /**
     * 判断当前用户是否有权限查看合同
     * @param $projectModel
     * @return bool
     */
    public static function isCanRead($projectModel)
    {
        $userId=Utility::getNowUserId();
        $res=AuthorizeService::checkUserCorpRight($projectModel->corporation_id,$userId);
        return ($res || $projectModel->manager_user_id==$userId || $projectModel->create_user_id==$userId);
    }

    /**
     * @desc 计算项目保理相关费用金额(按月统计)
     * @param int $projectId
     * @param  string $date
     * @return array
     */
    public static function computeProjectFactorRelatedFees($projectId, $date) {
        $res = array('factoring_interest' => 0, 'factoring_fee' => 0, 'factoring_fee2' => 0);
        if (Utility::checkQueryId($projectId)) {
            $factors = FactorDetail::model()->findAll('project_id = :projectId and status >= :status', array('projectId'=>$projectId, 'status' => FactorDetail::STATUS_SUBMIT));
            if(Utility::isNotEmpty($factors)) {
                foreach ($factors as $key => $row) {
                    $res['factoring_interest'] += FactoringService::calculatePeriodInterest($row->factor_id, null, $date);;
                    $res['factoring_fee'] += ($row['amount'] * ProfitService::$factoring_rate / 100);
                    $res['factoring_fee2'] += ($row['amount'] * ProfitService::$factoring_huoer_rate / 100);
                }
            }
        }
        return $res;
    }

    /**
     * 获取项目编码前缀
     * @param $corpId
     * @param $managerId
     * @param $type
     * @return string
     */
    public static function getProjectCodePrefix($corpId,$managerId,$type)
    {
        if (!Utility::checkQueryId($corpId) || !Utility::checkQueryId($managerId) || !Utility::checkQueryId($type))
        {
            return "";
        }

        $code = ConstantMap::PROJECT_CODE_START_STR;
        $str = Corporation::getCorporationCode($corpId);
        if (empty($str))
        {
           return "";
        }
        $code .= $str;

        $str = UserExtra::getUserCode($managerId);
        if (empty($str)) {
            $userInfo = SystemUser::getUser($managerId);
            $res['code'] = ConstantMap::INVALID;
            $res['msg'] = BusinessError::outputError(OilError::$BUSINESS_MANAGER_NO_CODE, array('name' => $userInfo['name']));

            return $res;
        }
        $code .= $str;

        $str = Map::$v['project_business_type'][$type]['code'];
        if (empty($str))
        {
            return "";
        }
        $code .= $str;

        return $code;


    }

    /**
     * 根据交易主体、负责人及类型更新项目编码
     * @param $projectCode
     * @param $corpId
     * @param $managerId
     * @param $type
     * @return bool|string
     */
    public static function updateProjectCode($projectCode,$corpId,$managerId,$type)
    {
        if(empty($projectCode))
            return false;
        $prefix=self::getProjectCodePrefix($corpId,$managerId,$type);
        if(empty($prefix))
           return false;

        $code=substr($projectCode,9);
        return $prefix.$code;
    }


}