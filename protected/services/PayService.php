<?php

/**
 * Created by youyi000.
 * DateTime: 2016/7/8 10:46
 * Describe：
 */
class PayService
{
    
    /**
     * 获取付款申请头部信息
     * @param $id
     * @return array
     */
    public function getPayHead($projectId)
    {
        $sql = "select {col} from t_project a
                left join t_partner p on a.up_partner_id=p.partner_id
                left join t_corporation c on a.corporation_id=c.corporation_id
                left join t_invoice_up i on a.project_id=i.project_id
                left join t_settlement s on a.project_id=s.project_id and s.type=2
                where a.project_id=".$projectId." order by a.project_id {limit}";
        $fields = "a.project_id,a.project_name,a.status,p.partner_id,p.name as partner_name,
                c.corporation_id,c.name as corporation_name,
                case when i.status=1 then '已收到'
                else '未收到'
                end as invoice_status,
                case when s.status=".Settlement::STATUS_DONE." then '是'
                else '否'
                end as settle_status";
        $data = $this->queryTablesByPage($sql, $fields);
        
        return $data;
    }


    /**
     * 获取上游的付款计划详情
     * @param $projectId
     * @return array
     */
    public static function getUpPayInfo($projectId)
    {
        $sql = "select a.plan_id,a.project_id,a.pay_days,a.pay_date,a.pay_type,a.type,a.period,
               a.rate,a.amount,a.status,sum(b.amount) as pay_amount,sum(c.amount) as actual_amount
               from t_project_pay_plan a
               left join t_pay_application b on a.plan_id=b.plan_id and b.status>0
               left join t_payment c on b.apply_id=c.apply_id 
               where a.project_id=".$projectId." group by a.plan_id order by a.period asc";
        $data = Utility::query($sql);
        $payments = array();
        if(Utility::isNotEmpty($data)){
          foreach ($data as $key => $value) {
            $value['paying_amount'] = $value['pay_amount'] - $value['actual_amount'];
            $value['balance']  = $value['amount'] - $value['pay_amount'];
            $payments['plan'][]   = $value;
            $payments['total']['total_amount']      += $value['amount'];
            $payments['total']['total_balance']     += $value['balance'];
            $payments['total']['total_actual_amount']  += $value['actual_amount']; 
            $payments['total']['total_paying_amount']  += $value['paying_amount']; 
          }
        }
        return $payments;
    }


    /**
     * 获取上游所有付款申请记录
     * @param $projectId
     * @return array
     */
    public static function getUpAllPay($projectId)
    {
        $sql="select distinct a.apply_id,a.pay_date,a.payee,a.bank,a.account,
              a.amount,a.is_invoice,a.status,a.remark,pr.period,
              l.check_status,l.remark as check_remark,p.id,IFNULL(p.status,0) as pay_status 
              from t_pay_application a 
              left join t_project_pay_plan pr on a.plan_id=pr.plan_id 
              left join t_check_log l on a.apply_id=l.obj_id 
              left join t_payment p on a.apply_id=p.apply_id 
              where a.project_id=".$projectId." order by a.pay_date ASC,pr.period asc";
        return Utility::query($sql);
    }

    public static function getActualPayDetail($apply_id) {
        $sql = "select p.apply_id, p.pay_date, p.corporation_id, p.account_id, p.amount, p.remark,
                c.name as corporation_name, a.bank_name, pa.id as attach_id, pa.type
                from t_payment p 
                left join t_corporation c on p.corporation_id = c.corporation_id 
                left join t_account a on p.account_id = a.account_id
                left join t_pay_attachment pa on pa.relation_id = p.apply_id and pa.status = 1 
                where p.apply_id=".$apply_id;
        return Utility::query($sql);
    }

    public static function getByProject($projectId){
        if(empty($projectId)){
            return array();
        }
        $sql="select * from ". self::tableName() . " where project_id=". $projectId;
        $data=Utility::query($sql);
        return $data;
    }

    /**
     * 检查必填参数
     * @param $params
     * @return bool|mixed|string
     */
    public static function checkParamsValid($params)
    {
        if (Utility::isNotEmpty($params)) {
            $requiredParams = array('corporation_id',
                                    'subject_id',
                                    'payee',
                                    'currency',
                                    'bank',
                                    'account_name',
                                    'amount',
                                    'account',
                );

            //必填参数校验
            if (Utility::checkRequiredParamsNoFilterInject($params, $requiredParams)) {
                return true;
            }
        }

        return BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR);
    }

    /**
     * 获取可以申请付款的合同的状态条件
     * @param string $tablePrefix
     * @return string
     */
    public static function getPayContractCondition($tablePrefix="")
    {
        $pre="";
        if(!empty($tablePrefix))
            $pre=$tablePrefix.".";
        $condition=$pre."status>=".Contract::STATUS_BUSINESS_CHECKED." and ".$pre."status<".Contract::STATUS_COMPLETED;
        return $condition;
    }

    /**
     * 提交付款申请进流程
     * @param $apply
     * @return bool
     * @throws Exception
     */
    public static function submitPayApplication($apply,$check_user=0)
    {
        if(empty($apply))
            return false;

        $isInDbTrans=Utility::isInDbTrans();

        if(!$isInDbTrans)
        {
            $trans = Utility::beginTransaction();
        }

        try {
            /*if(!$apply->isCanEdit())
            {
                throw new Exception("付款申请当前状态不允许提交");
            }*/
            if($apply->status<PayApplication::STATUS_SUBMIT)
            {
                $rows=$apply->updateByPk($apply->apply_id,array(
                    "status"=>PayApplication::STATUS_SUBMIT,
                    "status_time"=>new CDbExpression("now()"),
                    "update_time"=>new CDbExpression("now()"),
                    "update_user_id"=>Utility::getNowUserId()
                ),"status<".PayApplication::STATUS_SUBMIT);

                if($rows!=1)
                    throw new Exception("更新付款申请状态失败");
            }

            self::generateApplicationExtra($apply->apply_id,$apply);

            if($apply->type == PayApplication::TYPE_CONTRACT || $apply->type ==PayApplication::TYPE_SELL_CONTRACT)
            {
                //更新合同付款计划中的付款金额
                $details=PayApplicationDetail::model()->findAll("apply_id=".$apply->apply_id);
                if(is_array($details))
                {
                    foreach ($details as $d)
                    {
                        if(empty($d["plan_id"]))
                            continue;
                        PaymentPlanService::updatePaidAmount($d["plan_id"],$d["amount"]);
                        /*PaymentPlan::model()->updateByPk($d["plan_id"],array(
                            "amount_paid"=>new CDbExpression("amount_paid+".$d["amount"]),
                            "update_time"=>new CDbExpression("now()"),
                            "update_user_id"=>Utility::getNowUserId()
                            ));*/
                    }
                }
            }

            //根据付款申请id更新保理单状态
            if($apply->is_factoring) {
                $factor = Factor::model()->find('apply_id=:applyId', array('applyId' => $apply->apply_id));
                if ($factor->status < Factor::STATUS_SUBMIT) {
                    FactoringService::updateFactorStatusByPayApply($apply->apply_id);
                    TaskService::addTasks(Action::ACTION_FACTOR_AMOUNT_CONFIRM, $factor->factor_id, ActionService::getActionRoleIds(Action::ACTION_FACTOR_AMOUNT_CONFIRM), 0, $factor->corporation_id, array('code' => $factor->contract_code));
                }
            }
            if($apply->type == PayApplication::TYPE_CONTRACT || $apply->type ==PayApplication::TYPE_SELL_CONTRACT){
                $checkUserId=$apply->contract->manager_user_id;
            }else{
                $checkUserId=$check_user;//TODO
            }
            FlowService::startFlow(FlowService::BUSINESS_PAY_APPLICATION,$apply->apply_id,$checkUserId);
            TaskService::doneTask($apply->apply_id,Action::ACTION_PAY_APPLICATION_BACK);

            if(!$isInDbTrans)
            {
                $trans->commit();
            }
            return true;
        } catch (Exception $e) {
            Mod::log("PayApplication submit error: ".$e->getMessage(),"error");
            if(!$isInDbTrans)
            {
                try { $trans->rollback(); }catch(Exception $ee){}
                return false;
            }
            else
                throw $e;
        }



    }

    /**
     * 付款申请是否可以修改
     * @param $status
     * @return bool
     */
    public static function isCanEdit($status)
    {
        return ($status<PayApplication::STATUS_SUBMIT && $status>=PayApplication::STATUS_NEW || $status == PayApplication::STATUS_WITHDRAW || $status == self::STATUS_NOT_SAVE);
    }

    /**
     * 获取付款的可选合同信息
     * @param int $corpId
     * @param int $projectId
     * @return array
     */
    public static function getContracts($corpId=0,$projectId=0)
    {
        $sql="select a.contract_id,a.contract_code,a.type,a.project_id,b.project_code from t_contract a left join t_project b on a.project_id=b.project_id
                where ".AuthorizeService::getUserDataConditionString("a")."
                and ".self::getPayContractCondition("a")."
                ";
        if(!empty($corpId))
            $sql.=" and a.corporation_id=".$corpId."";
        if(!empty($projectId))
            $sql.=" and a.project_id=".$projectId."";
        $sql.=" order by a.contract_id desc";

        return Utility::query($sql);
    }

    /**
     * 获取付款的可选项目信息
     * @param int $corpId
     * @return array
     */
    public static function getProjects($corpId=0)
    {
        $sql="select a.project_id,a.project_code,a.type from t_project a
            where status>=".Project::STATUS_SUBMIT." and status<=".Project::STATUS_DONE." 
            and ".AuthorizeService::getUserDataConditionString("a");

        if(!empty($corpId))
            $sql.=" and a.corporation_id=".$corpId."";
        $sql.=" order by a.project_id desc";

        return Utility::query($sql);
    }

    /**
     * 生成付款申请的额外风险信息
     * @param $applyId
     * @param null $applyModel
     * @throws Exception
     */
    public static function generateApplicationExtra($applyId,$applyModel=null)
    {
        if(empty($applyModel))
            $applyModel=PayApplication::model()->with("details","contract","contract.payments")->findByPk($applyId);
        if(empty($applyModel))
            throw new Exception("付款申请不存在");

        $extra=array();
        foreach (Map::$v["pay_application_extra"] as $k=>$v)
        {
            $extra[$k]=0;
        }

        $model=PayApplication::model()->find("status>=".PayApplication::STATUS_SUBMIT." 
            and trim(payee)='".trim($applyModel->payee)."' and apply_id<>".$applyModel->apply_id);
        if(empty($model))
            $extra[3]=1;


        switch($applyModel->type)
        {
            case PayApplication::TYPE_CONTRACT:
            case PayApplication::TYPE_SELL_CONTRACT:
                if(is_array($applyModel->details))
                {
                    $plans=array();
                    foreach ($applyModel->contract->payments as $p)
                    {
                        $plans[$p["plan_id"]]=$p;
                    }

                    foreach ($applyModel->details as $v)
                    {
                        $p=$plans[$v["plan_id"]];
                        $balanceAmount=$p["amount"]-$p["amount_paid"];
                        if($balanceAmount<=0)
                        {
                            $extra["5"]=1;
                            $extra["4"]=1;
                        }
                        else if(($v["amount"]-$balanceAmount)/$balanceAmount>0.1)
                        {
                            $extra["4"]=1;
                        }
                    }
                }
                break;
            case PayApplication::TYPE_MULTI_CONTRACT:

                break;
            case PayApplication::TYPE_CORPORATION:
            case PayApplication::TYPE_CLAIM:
                $extra["1"]=1;
                $extra["2"]=1;
                $extra["4"]=1;
                $extra["5"]=1;
                break;
            case PayApplication::TYPE_PROJECT:
                $extra["2"]=1;
                break;
            default:

                break;
        }

        $obj=PayApplicationExtra::model()->find("apply_id=".$applyModel->apply_id);
        if(empty($obj))
        {
            $obj=new PayApplicationExtra();
            $obj->apply_id=$applyModel->apply_id;
        }

        $obj->content=json_encode($extra);
        $obj->save();

    }


    /**
     * 作废付款申请
     * @param $apply
     * @return bool
     * @throws Exception
     */
    public static function trashPayApplication($apply)
    {

        if(empty($apply))
            return false;

        $isInDbTrans=Utility::isInDbTrans();

        if(!$isInDbTrans)
        {
            $trans = Utility::beginTransaction();
        }

        try {

            if(!$apply->isCanTrash())
            {
                throw new Exception("付款申请当前状态不允许作废");
            }

            if($apply->is_factoring)
            {
                if(empty($apply->factor))
                    $factor=Factor::model()->find("apply_id=".$apply->apply_id);
                else
                    $factor=$apply->factor;
                if(!empty($factor) && $factor->isCanTrash())
                {
                    $rows=$factor->updateByPk($factor->factor_id,array(
                        "status"=>Factor::STATUS_TRASHED,
                        "status_time"=>new CDbExpression("now()"),
                        "update_time"=>new CDbExpression("now()"),
                        "update_user_id"=>Utility::getNowUserId()
                    ),"status<".Factor::STATUS_SUBMIT);

                    if($rows!=1)
                        throw new Exception("付款申请对应的保理信息作废失败");
                }
            }
            $rows=$apply->updateByPk($apply->apply_id,array(
                "status"=>PayApplication::STATUS_TRASHED,
                "status_time"=>new CDbExpression("now()"),
                "update_time"=>new CDbExpression("now()"),
                "update_user_id"=>Utility::getNowUserId()
            ),"status<".PayApplication::STATUS_SUBMIT);

            if($rows!=1)
                throw new Exception("付款申请作废失败");

            TaskService::doneTask($apply->apply_id,Action::ACTION_PAY_APPLICATION_BACK);

            if(!$isInDbTrans)
            {
                $trans->commit();
            }
            return true;
        } catch (Exception $e) {
            Mod::log("PayApplication trash error: ".$e->getMessage(),"error");
            if(!$isInDbTrans)
            {
                try { $trans->rollback(); }catch(Exception $ee){}
                return $e->getMessage();
                //return false;
            }
            else
                throw $e;
        }
    }

    /**
     * @desc 发送付款申请撤回提醒
     * @param int $checkDetailId
     * @param int $corporationId
     */
    public static function sendWithDrawReminder($checkDetailId, $corporationId=0)
    {
        if(Utility::checkQueryId($checkDetailId) && $checkDetailId>0) {
            $checkDetail = CheckDetail::model()->findByPk($checkDetailId);
            if (!empty($checkDetail)) {
                $msg = '审核编号:'.$checkDetail->check_id.'，付款申请编号:'.$checkDetail->obj_id.'，付款单已被撤回，请知悉。';
                $title = '石油系统提醒';
                $userIds=array();
                if(!empty($checkDetail->check_user_id))
                {
                    $user=SystemUser::getUser($checkDetail->check_user_id);
                    $userIds[] = $user['identity'];
                    AMQPService::publishEmail($checkDetail->check_user_id, $title, $msg);
                }
                else
                {
                    $users=UserService::getUserByRoleId($checkDetail->role_id, $corporationId);
                    foreach ($users as $u)
                    {
                        $userIds[] = $u['identity'];
                        AMQPService::publishEmail($u["user_id"], $title, $msg);
                    }
                }

                AMQPService::publishWinxinReminder($userIds, $msg);
            }
        }
    }

    // 撤销付款申请审核
    public static function withdrawPayApplication($applyId) {
        $isInDbTrans=Utility::isInDbTrans();
        if(!$isInDbTrans)
        {
            $trans = Utility::beginTransaction();
        }

        try {
            $apply = PayApplication::model()->with('details')->findByPk($applyId);
            if (empty($apply))
                throw new Exception("不存在对应的付款申请");

            if (!PayService::canWithdraw($applyId, $apply->create_user_id))
                throw new Exception('付款申请当前状态不允许撤回');

            // 更新审核状态
            $checkDetail = CheckDetail::model()->with('checkItem')->find('t.status = 0 and t.obj_id='.$applyId.' and t.business_id='.FlowService::BUSINESS_PAY_APPLICATION);
            if (!empty($checkDetail->detail_id)) {
                $checkDetail->updateByPk($checkDetail->detail_id, array(
                   'check_status' => -2,
                   'status' => 1
                ));

                $checkDetail->checkItem->updateByPk($checkDetail->check_id, array(
                    'node_id' => 0,
                    'next_node_id' => 0,
                    'status' => 1
                ));

                PayService::sendWithDrawReminder($checkDetail->detail_id, $apply->corporation_id);
            }


            // 已申请金额减去本次申请金额
            if (!empty($apply->details)) {
                foreach ($apply->details as $detail) {
                    $plan = PaymentPlan::model()->findByPk($detail->plan_id);
                    if (!empty($plan)) {
                        $plan->updateByPk($plan->plan_id, array(
                           'amount_paid' => ($plan->amount_paid - $detail->amount)
                        ));
                    }
                }
            }

            // 更新状态
            $res=$apply->updateByPk($apply->apply_id,array(
                "status"=>PayApplication::STATUS_WITHDRAW,
                "status_time"=>new CDbExpression("now()"),
                "update_time"=>new CDbExpression("now()"),
                "update_user_id"=>Utility::getNowUserId()
            ),"status>=".PayApplication::STATUS_SUBMIT);

            if($res!=1)
                throw new Exception("付款申请撤回失败");

            TaskService::doneTask($applyId,Action::ACTION_21);

            if(!$isInDbTrans)
            {
                $trans->commit();
            }
            return true;
        } catch (Exception $e) {
            Mod::log("PayApplication withdraw error: ".$e->getMessage(),"error");
            if(!$isInDbTrans)
            {
                try { $trans->rollback(); }catch(Exception $ee){}
                return $e->getMessage();
                //return false;
            }
            else
                throw $e;
        }
    }

    /**
     * 完成付款
     * @param $apply
     * @return bool
     */
    public static function donePaidPayApplication($apply)
    {
        if(empty($apply))
            return false;

        if($apply->category==PayApplication::CATEGORY_NORMAL)
            $apply->status=PayApplication::STATUS_DONE;
        else
            $apply->status=PayApplication::STATUS_PAID;

        $apply->status_time=new CDbExpression("now()");
        $res=$apply->save();


        if($apply->type == PayApplication::TYPE_CONTRACT || $apply->type ==PayApplication::TYPE_SELL_CONTRACT)
        {
            //更新合同付款计划中的实际付款金额
            $details=PayApplicationDetail::model()->findAll("apply_id=".$apply->apply_id);
            if(is_array($details))
            {
                foreach ($details as $d)
                {
                    if(empty($d["plan_id"]))
                        continue;
                    PaymentPlanService::updateActualPaidAmount($d["plan_id"],$d["amount"]);
                }
            }
        }
        
        return $res;
    }

    /**
     * 完成待认领付款
     * @param $apply
     * @return bool
     */
    public static function doneClaimPayApplication($apply)
    {
        if(empty($apply))
            return false;
        if($apply->category==PayApplication::CATEGORY_CLAIMING)
        {
            if(bccomp($apply->amount_claim,$apply->amount_paid)==0 && $apply->status!=PayApplication::STATUS_STOP)
            {
                $apply->status=PayApplication::STATUS_DONE;
                $apply->status_time=new CDbExpression("now()");
                $res=$apply->save();
                return $res;
            }
            else
                return false;
        }
        return false;
    }

    //更新付款申请已实付金额
    public static function updateAmountPaid($applyId, $amount)
    {
        if(empty($applyId) || empty($amount))
            return;

        $obj = PayApplication::model()->updateByPk($applyId,
            array(
                'amount_paid' => new CDbExpression("amount_paid+".$amount),
                'update_user_id'=> Utility::getNowUserId(),
                'update_time'=> new CDbExpression('now()')
                ) 
        );
    }

    //获取所有实付信息
    public static function getAllPayComfirmInfo($applyId)
    {
        $data = array();
        if(empty($applyId))
            return $data;
        $sql = "select c.*,a.account_id,a.bank_name,a.account_no,a.corporation_id as account_corp_id,t.id as file_id,t.name as file_name,
                co.corporation_id as corporation_id,co.name as corporation_name
                from t_payment c
                left join t_account a on c.account_id=a.account_id
                left join t_corporation co on a.corporation_id=co.corporation_id
                left join t_pay_attachment t on c.payment_id=t.base_id and t.type=11 and t.status = 1
                where c.apply_id=".$applyId." order by c.payment_id desc ";

        return Utility::query($sql);
    }

    /**
     * 获取收款人的银行帐号信息
     * @param $name
     * @return array|null
     */
    public static function getPayeeAccount($name)
    {
        $res=array();
        if(empty($name))
            return $res;

        $apply=PayApplication::model()->find(array(
            "condition"=>"payee='".$name."'",
            "order"=>"apply_id desc"
                                             ));

        if(!empty($apply))
        {
            $res["bank"]=$apply->bank;
            $res["account_name"]=$apply->account_name;
            $res["account"]=$apply->account;
        }
        return $res;
    }

    /**
     * @desc 获取合同实付金额
     * @param int $contractId
     * @return float
     */
    public static function getContractActualPaidAmount($contractId) {
        if (Utility::checkQueryId($contractId)) {
            $sql1 = 'select ifnull(sum(a.amount_cny), 0) sum_amount_cny from t_payment a left join t_pay_application b on b.apply_id = a.apply_id where b.contract_id=' . $contractId . ' and a.status>=' . Payment::STATUS_SUBMITED;
            $sql2 = 'select ifnull(sum(amount_cny), 0) sum_amount_cny from t_pay_claim where contract_id=' . $contractId . ' and status>=' . PayClaim::STATUS_SUBMITED;
            $sql = 'select sum(sum_amount_cny) as sum_amount_cny from (' . $sql1 . ' union ' . $sql2 . ') p';
            $res = Utility::query($sql);
            if (Utility::isNotEmpty($res)) {
                return $res[0]['sum_amount_cny'];
            }
        }

        return 0;
    }

    /**
     * @desc 获取付款申请实付金额
     * @param int $applyId
     * @return float
     */
    public static function getApplyActualPaidAmount($applyId) {
        if (Utility::checkQueryId($applyId)) {
            $payApply = PayApplication::model()->findByPk($applyId);
            if(!empty($payApply)) {
                $sql = 'select ifnull(sum(amount_cny), 0) sum_amount_cny from t_payment where apply_id = ' . $applyId.' and status >= ' . Payment::STATUS_SUBMITED;
                $res = Utility::query($sql);
                if (Utility::isNotEmpty($res)) {
                    return $res[0]['sum_amount_cny'];
                }
            }
        }

        return 0;
    }

    /**
     * @desc 获取止付金额
     * @param $applyId
     * @return int
     */
    public static function getStopPayAmount($applyId) {
        if (Utility::checkQueryId($applyId)) {
            $sql = "select (a.amount - a.amount_paid) as amount_stop from t_pay_application a left join t_pay_application_extra b on a.apply_id=b.apply_id where a.apply_id = $applyId ". ' and b.status ='.PayApplicationExtra::STATUS_PASS;
           $res = Utility::query($sql);
           if (Utility::isNotEmpty($res)) {
               return $res[0]['amount_stop'];
           }
        }

        return 0;
    }

    // 是否可撤销当前审核
    public static function canWithdraw($applyId, $applyUserId) {
        if ($applyUserId != Utility::getNowUserId())
            return false;

        if (Utility::checkQueryId($applyId)) {
            $sql = 'select a.* from t_check_detail a left join t_check_item ci on a.check_id = ci.check_id left join  t_flow_node n on n.node_id = ci.node_id where a.obj_id = '.$applyId.' and a.business_id='.FlowService::BUSINESS_PAY_APPLICATION.' and a.status=0 and n.previous_id=0 and a.create_user_id='. Utility::getNowUserId();
            $res = Utility::query($sql);
            if (Utility::isEmpty($res))
                return false;

            return true;
        }

        return false;
    }

    /**
     * 更新付款计划的已付金额（对应付款申请详情的金额）
     * @param $detailId
     * @param $amount
     * @return bool
     */
    public static function updatePaidAmount($detailId,$amount)
    {
        if(empty($detailId))
            return false;
        $rows=PayApplicationDetail::model()->updateByPk($detailId,array(
            "amount_paid"=>new CDbExpression($amount),
            "update_time"=>new CDbExpression("now()"),
            "update_user_id"=>Utility::getNowUserId()
        ));
        return $rows==1;
    }


    // 检查是否在进行止付
    public static function isPendingPayStop($applyId) {
        $sql = "select id,status from t_pay_application_extra a where a.apply_id = {$applyId} and a.status in (".PayApplicationExtra::STATUS_CHECKING . ',' .PayApplicationExtra::STATUS_PASS . ')';
        $res = Utility::query($sql);

        if (Utility::isEmpty($res))
            return false;

        return true;
    }

    //获取付款编号对应的商品名称
    public static function getGoodsName($apply_id,$category=1,$type=11){
        $result=array();
        if($category==PayApplication::CATEGORY_NORMAL){
            if($type==PayApplication::TYPE_MULTI_CONTRACT){//多合同
                $sql="
                       select
                               group_concat(distinct c.name) goods_name
                       from
                              t_pay_application_detail a
                              left join t_contract_goods b on a.contract_id=b.contract_id
                              left join t_goods c on b.goods_id=c.goods_id
                        where a.apply_id='{$apply_id}'
                    ";
                $result = Utility::query($sql);
            }else{//单合同
                $sql="
                       select
                               group_concat(distinct c.name) goods_name
                       from
                              t_pay_application a
                              left join t_contract_goods b on a.contract_id=b.contract_id
                              left join t_goods c on b.goods_id=c.goods_id
                        where a.apply_id='{$apply_id}'
                    ";
                $result = Utility::query($sql);
            }
        }else{//后补合同付款
            $result =  Utility::query("
                       select
                               group_concat(distinct c.name) goods_name
                       from
                              t_pay_claim a
                              left join t_contract_goods b on a.contract_id=b.contract_id
                              left join t_goods c on b.goods_id=c.goods_id
                        where a.apply_id='{$apply_id}'
                        ");
        }
        return empty($result)?'':$result[0]['goods_name'];
    }
}
