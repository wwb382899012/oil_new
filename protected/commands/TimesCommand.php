<?php
/**
* 工具命令行，提供常用的系统维护工具
*
* 
*/
class TimesCommand extends CConsoleCommand
{

    const BUSUNESS_ROLE_VALUE=11;//商务,商务主管
    const RISK_ROLE_VALUE=6;//风控
    const ENERGY_ACCOUNT_VALUE=14;//财务会计,能源会计
    //const ENERGY_ACCOUNT=12; //板块财务负责人
    const ENERGY_CASHIER_VALUE=8; //出纳，能源出纳
    const FACTOR_ACCOUNT_VALUE=20; //保理财务会计
    const FACTOR_MANAGER_VALUE=21; //保理财务板块负责人
    const FACTOR_CASHIER_VALUE=18; //保理出纳

    const BUSNIESS_ID=13;
    const FLOW_ID=22;
    public function actionPay()
    {
        set_time_limit(0);
        $sql="select 
                    a.*,c.name as corp_name,fs.name as subject_name,co.type as contract_type,co.category as contract_category,co.contract_code,su.name as create_name,
                    pt.id as pay_timeliness_id,p.create_time as pay_finish_time,p.create_user_id as pay_finish_user
              from 
                    t_pay_application a
                    left join t_pay_timeliness as pt on pt.apply_id=a.apply_id
                    left join t_corporation c on c.corporation_id=a.corporation_id
                    left join t_finance_subject fs on fs.subject_id=a.subject_id
                    left join t_contract co on co.contract_id=a.contract_id
                    left join t_system_user su on su.user_id=a.create_user_id
                    left join t_payment as p on p.apply_id=a.apply_id
                    
               group by a.apply_id order by a.apply_id"; // "." and ".TimesCommand::getUserDataConditionString("a")."
        $payList=Utility::query($sql);
        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try{
            if(Utility::isNotEmpty($payList)){
                
                foreach ($payList as $key=>$value){
                    //if($value['apply_id']=='2018012300002'){
                    $payTimeliness = PayTimeliness::model()->findByPk($value['pay_timeliness_id']);
                    if(empty($payTimeliness->id)){
                       
                        $payTimeliness= new PayTimeliness();
                    }
                    $payTimeliness->apply_id=$value['apply_id'];
                    $payTimeliness->subject_id=$value['subject_id'];
                    $payTimeliness->payee=$value['payee'];
                    //$payTimeliness->update_time=$payTimeliness->create_time=date("Y-m-d H:i:s");
                    $payTimeliness->apply_user_id=$value['create_user_id'];
                    //驳回次数
                    $backDetail=CheckDetail::model()->findAll(
                        array(
                            'condition'=>"obj_id=:obj_id and business_id=:busniess_id and check_status=-1",
                            'params'=>array('obj_id'=>$value['apply_id'],':busniess_id'=>TimesCommand::BUSNIESS_ID)
                        ));
                    $payTimeliness->reject_times=count($backDetail); //驳回次数
                    //最后一次申请时间 ：end_apply_time ,若没有驳回 ，则等于start_apply_time
                     $check_item=Utility::query("
                     select * from t_check_item where business_id=".TimesCommand::BUSNIESS_ID." and obj_id=".$value['apply_id']." order by check_id
                    "); 
                   
                    if(!empty($check_item)){
                       $payTimeliness->start_apply_time=$check_item[0]['create_time']; //付款申请 开始时间
					   $payTimeliness->apply_user_id =  $check_item[0]['create_user_id'];
                       $num=count($check_item)-1;
                       $payTimeliness->end_apply_time=$check_item[$num]['create_time']; //最后一次申请时间
                    }
                   
                    $payTimeliness->contract_check_value=(strtotime($payTimeliness->end_apply_time))-(strtotime($payTimeliness->start_apply_time));
                    //商务审核
                    $busniess_role=$this->getTimeValue($value['apply_id'],TimesCommand::BUSUNESS_ROLE_VALUE);
                    $payTimeliness->business_check_value=$busniess_role['timeValue'];
                    $payTimeliness->business_check_time=empty($busniess_role['lastTime'])?null:$busniess_role['lastTime'];
                    $payTimeliness->business_user_id=$busniess_role['check_user_id'];

                    //风控审核
                    $risk_role=$this->getTimeValue($value['apply_id'],TimesCommand::RISK_ROLE_VALUE);
                    $payTimeliness->risk_check_value=$risk_role['timeValue'];
                    $payTimeliness->risk_check_time=empty($risk_role['lastTime'])?null:$risk_role['lastTime'];
                    $payTimeliness->risk_user_id=$risk_role['check_user_id'];
                    //财务会计 审核
                    $energy_account_role=$this->getTimeValue($value['apply_id'],TimesCommand::ENERGY_ACCOUNT_VALUE);
                    $payTimeliness->energy_account_check_value=$energy_account_role['timeValue'];
                    $payTimeliness->energy_account_check_time=empty($energy_account_role['lastTime'])?null:$energy_account_role['lastTime'];
                    $payTimeliness->energy_account_user_id=$energy_account_role['check_user_id'];
                    //能源出纳
                    $energy_cashier_role=$this->getTimeValue($value['apply_id'],TimesCommand::ENERGY_CASHIER_VALUE);
                    $payTimeliness->energy_cashier_check_value=$energy_cashier_role['timeValue'];
                    $payTimeliness->energy_cashier_check_time=empty($energy_cashier_role['lastTime'])?null:$energy_cashier_role['lastTime'];
                    $payTimeliness->energy_cashier_user_id=$energy_cashier_role['check_user_id'];
                    //保理会计 审核
                    $factor_account_role=$this->getTimeValue($value['apply_id'],TimesCommand::FACTOR_ACCOUNT_VALUE);
                    $payTimeliness->factor_account_check_value=$factor_account_role['timeValue'];
                    $payTimeliness->factor_account_check_time=empty($factor_account_role['lastTime'])?null:$factor_account_role['lastTime'];
                    $payTimeliness->factor_account_user_id=$factor_account_role['check_user_id'];
                    //保理负责人 审核
                    $factor_manager_role=$this->getTimeValue($value['apply_id'],TimesCommand::FACTOR_MANAGER_VALUE);
                    $payTimeliness->factor_manager_check_value=$factor_manager_role['timeValue'];
                    $payTimeliness->factor_manager_check_time=empty($factor_manager_role['lastTime'])?null:$factor_manager_role['lastTime'];
                    $payTimeliness->factor_manager_user_id=$factor_manager_role['check_user_id'];
                    //保理出纳 审核
                    $factor_cashier_role=$this->getTimeValue($value['apply_id'],TimesCommand::FACTOR_CASHIER_VALUE);
                    $payTimeliness->factor_cashier_check_value=$factor_cashier_role['timeValue'];
                    $payTimeliness->factor_cashier_check_time=empty($factor_cashier_role['lastTime'])?null:$factor_cashier_role['lastTime'];
                    $payTimeliness->factor_cashier_user_id=$factor_cashier_role['check_user_id'];
                    //导出功能：保理管理=》保理对接
                    //出纳实付开始时间，即付款审核最后一步的结束时间；结束时间，即付款表t_payment生成时间
                   
                    $check_detail=Utility::query("select obj_id,update_time from t_check_detail where obj_id=".$value['apply_id']."
                                and business_id=".TimesCommand::BUSNIESS_ID."
                                and status=1 order by detail_id desc limit 1");
                    if(!empty($value['pay_finish_time'])){
                        //print_r($check_detail[0]['update_time']);
                        if(empty($check_detail[0]['update_time']))
                            $timeValue=0;
                            else {
                            $timeValue=(strtotime($value['pay_finish_time'])-strtotime($check_detail[0]['update_time']));//
                            $payTimeliness->energy_cashier_payment_value=$timeValue;
                            $payTimeliness->energy_cashier_payment_time=$value['pay_finish_time'];
                            $payTimeliness->energy_cashier_payment_user_id=$value['pay_finish_user'];
                            }
                    }
                    
                    $status=$payTimeliness->save();
                  }  
                //}
            }
        $trans->commit();
        //echo 'ok';//
        return true;
        } catch(Exception $e) {
            $trans->rollback();
            //echo $e->getMEssage();
            return false;
        }
       
    }
    public static function getUserDataConditionString($tablePrefix="",$userId=0)
    {
        if(empty($userId))
            $userId=Utility::getNowUserId();
            $user=SystemUser::getUser($userId);
            $pre="";
            if(!empty($tablePrefix))
                $pre=$tablePrefix.".";
                $condition=$pre."corporation_id in (" . $user['corp_ids'] . ")";
                return $condition;
    }
    /*
     * 获取每个角色的付款总时效
     * @param: 付款单号
     * @param: 角色id
     * @return: 总时效值、最后一次审核时间、审核人id
     * */
    public function getTimeValue($obj_id,$role_id){
        $data=CheckDetail::model()->findAll(
            array(
                'condition'=>"obj_id=:obj_id and business_id=:busniess_id and role_id=:role_id",
                'params'=>array('obj_id'=>$obj_id,':role_id'=>$role_id,':busniess_id'=>TimesCommand::BUSNIESS_ID)
            ));
        $timeValue=0; //总时效值
        $lastTime=''; //最后一次审核时间
        $check_user_id='';
        if(empty($data))
        return array(
            'timeValue'=>'',
            'lastTime'=>'',
            'check_user_id'=>''
        );
        foreach ($data as $value){
            $timeValue+=(strtotime($value['update_time'])-strtotime($value['create_time']));
            if($value['status']=="1"){//已经审核了，才算
            $lastTime=$value['update_time'];
            $check_user_id=$value['update_user_id'];
            }
        }
        return array(
            'timeValue'=>$timeValue,
            'lastTime'=>$lastTime,
            'check_user_id'=>$check_user_id
        );
    }

}

