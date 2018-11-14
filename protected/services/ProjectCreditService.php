<?php

/**
 * Created by youyi000.
 * DateTime: 2017/4/13 10:39
 * Describe：
 */
class ProjectCreditService
{

    /**
     * 释放项目额度的占用
     * @param $projectId
     * @param $amount
     * @param null $project 项目对象
     * @param string $remark 备注
     * @return bool
     * @throws Exception
     */
    public static function releaseCreditAmount($projectId,$amount,$project=null,$remark="")
    {
        if(empty($projectId))
            return false;

        if($amount<=0)
            return false;

        if(empty($project))
        {
            $project=Project::model()->findByPk($projectId);
        }

        $isInDbTrans=Utility::isInDbTrans();

        if(!$isInDbTrans)
        {
            $trans = Utility::beginTransaction();
        }

        try {

            $credit=ProjectCredit::model()->find("project_id=".$projectId);
            if(empty($credit->id))
            {
                return true;
            }
            //此处为了兼容原系统项目，如果不顾在，直接返回true ，update by youyi000 @2017-04-20
            //throw new Exception("项目".$projectId."额度占用信息不存在");


            $log=new ProjectCreditLog();
            $log->project_id=$projectId;
            $log->amount=$amount; //$amount后续会发生变化，在前面赋值
            $log->remark=$remark;

            $usedDetails=UserCreditUseDetail::model()->findAll(array(
                "condition"=>"project_id=".$projectId." and amount>amount_free",
                "order"=>"type desc"
            ));

            $freeAmount=0;

            foreach ($usedDetails as $detail)
            {

                $diffAmount=$detail->amount-$detail->amount_free;
                if($amount<$diffAmount)
                {
                    $diffAmount=$amount;
                }

                $rows=UserCreditUseDetail::model()->updateByPk($detail->detail_id
                    ,array(
                        "amount_free"=>new CDbExpression("amount_free+".$diffAmount),
                        "update_time"=>new CDbExpression("now()")
                    )
                    ,"amount_free>=".$detail->amount_free
                );
                if($rows!=1)
                    throw new Exception("释放用户".$detail->user_id."的占用额度失败");
                $res=UserCreditService::releaseUserCreditAmount($detail->user_id,$diffAmount);
                if(!$res)
                    throw new Exception("释放用户".$detail->user_id."的占用额度失败");

                $freeAmount+=$diffAmount;
                $amount=$amount-$diffAmount;
                if($amount<=0)
                    break;
            }

            $d=array(
                "other"=>array("key"=>"other_amount","key_free"=>"other_amount_free"),
                "user"=>array("key"=>"user_amount","key_free"=>"user_amount_free"),
                //"partner"=>array("key"=>"partner_amount","key_free"=>"partner_amount_free"),
            );

            $fields=array();

            if($freeAmount>0)
            {
                foreach ($d as $k=>$v)
                {
                    $diffAmount=$credit[$v["key"]]-$credit[$v["key_free"]];
                    if($freeAmount<$diffAmount)
                        $diffAmount=$freeAmount;
                    //$d[$k]["amount"]=$diffAmount;
                    $fields[$v["key_free"]]=new CDbExpression($v["key_free"]."+".$diffAmount);

                    $freeAmount-=$diffAmount;
                    if($freeAmount<=0)
                        break;
                }
            }

            if($amount>0)
            {
                $diffAmount=$credit->partner_amount-$credit->partner_amount_free;
                if($amount>$diffAmount)
                    throw  new Exception("释放项目".$projectId."的占用额度出错，超出项目总的占用额度");

                //$credit->partner_amount_free=$credit->partner_amount_free+$amount;

                $fields["partner_amount_free"]=new CDbExpression("partner_amount_free+".$amount);
                $res=CreditService::releasePartnerCreditAmount($project->down_partner_id,$amount);
                if(!$res)
                    throw new Exception("释放合作方".$project->down_partner_id."的占用额度失败");
            }
            $fields["update_time"]=new CDbExpression("now()");
            $rows=ProjectCredit::model()->updateByPk($credit->id
                ,$fields
                ,"other_amount_free=".$credit["other_amount_free"].
                " and user_amount_free=".$credit["user_amount_free"].
                " and partner_amount_free=".$credit["partner_amount_free"]
            );
            if($rows!=1)
            {
                throw  new Exception("更新项目".$projectId."的额度占用信息出错");
            }


            $log->save();

            if(!$isInDbTrans)
            {
                $trans->commit();
            }
            return true;
        } catch (Exception $e) {
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
     * 确认额度占用申请
     * @param $applyId
     * @throws Exception
     */
    public static function confirmCreditApply($applyId)
    {
        $items=ProjectCreditApplyDetail::model()->findAll("status<>".ProjectCreditApplyDetail::STATUS_CONFIRM." and apply_id=".$applyId);
        if(empty($items) || count($items)<1)
        {
            $apply=ProjectCreditApply::model()->findByPk($applyId);
            if(empty($apply->apply_id))
            {
                Mod::log("额度占用申请不存在，id：".$applyId,"error");
                throw new Exception("额度占用申请不存在，id：".$applyId);
            }

            $apply->confirm();
        }
    }

    /**
     * 拒绝额度占用申请
     * @param $applyId
     * @throws Exception
     */
    public static function rejectCreditApply($applyId)
    {
        $apply=ProjectCreditApply::model()->findByPk($applyId);
        if(empty($apply->apply_id))
        {
            Mod::log("额度占用申请不存在，id：".$applyId,"error");
            throw new Exception("额度占用申请不存在，id：".$applyId);
        }

        $apply->reject();
    }

    /**
     * 增加使用合作方额度
     * @param $projectId
     * @param $amount
     * @param null $project
     * @return bool
     * @throws Exception
     */
    public static function addPartnerCreditAmount($projectId,$amount,$project=null)
    {
        $isInDbTrans=Utility::isInDbTrans();

        if(!$isInDbTrans)
        {
            $trans = Utility::beginTransaction();
        }

        try
        {
            if(empty($project))
                $project=Project::model()->findByPk($projectId);
            CreditService::usePartnerCreditAmountForSettlement($project,$amount);
            $sql="update ".ProjectCredit::model()->tableName()." set partner_amount=partner_amount+".$amount."
             where project_id=".$projectId."";
            Utility::execute($sql);

            if(!$isInDbTrans)
            {
                $trans->commit();
            }
            return true;
        } catch (Exception $e) {
            if(!$isInDbTrans)
            {
                try { $trans->rollback(); }catch(Exception $ee){}
                return false;
            }
            else
                throw $e;
        }
    }
}