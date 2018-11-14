<?php

/**
 * Created by youyi000.
 * DateTime: 2017/4/11 15:19
 * Describe：
 *  企业额度相关的服务类
 */
class CreditService
{

    /**
     * 项目提交时占用各额度信息
     *      1、验证合作方额度
     *      2、验证占用的用户额度
     * @param $projectId
     * @param null $project 项目对象
     * @return bool
     * @throws Exception
     */
    public static function useCreditAmount($projectId,$project=null)
    {
        if(empty($project))
        {
            $project=Project::model()->with("down_detail")->findByPk($projectId);
        }
        if(empty($project->project_id))
                return false;

        $isInDbTrans=Utility::isInDbTrans();
        if(!$isInDbTrans)
            $trans=Utility::beginTransaction();
        try
        {

            $amount=$project->down_detail->amount;

            $credit=ProjectCredit::model()->find("project_id=".$projectId);
            if(empty($credit->id))
            {
                $credit=new ProjectCredit();
                $credit->project_id=$projectId;
            }
            else
            {
                $amount=$amount-($credit->user_amount-$credit->user_amount_free)
                    -($credit->other_amount-$credit->other_amount_free);
            }
            if($amount>0)
            {
                $res=CreditService::usePartnerCreditAmount($project->down_partner_id,$amount);
                if(!$res)
                    throw new Exception("合作方额度不足");
            }

            if($credit->user_amount-$credit->user_amount_free
                +$credit->other_amount-$credit->other_amount_free >0)
            {
                $apply=ProjectCreditApply::model()->findByPk($credit->effect_apply_id);
                if(empty($apply->apply_id))
                {
                    Mod::log("项目".$project->project_id."的额度占用申请".$credit->effect_apply_id."不存在","error");
                    throw new Exception("项目".$project->project_id."的额度占用申请".$credit->effect_apply_id."不存在");
                }

                $apply->effect($project->manager_user_id);
            }

            $credit->partner_amount+=$amount;

            $credit->save();
            if(!$isInDbTrans)
                $trans->commit();
            return true;
        }
        catch (Exception $e)
        {
            Mod::log("CreditService::useCreditAmount Error: ".$e->getMessage(),"error");
            if(!$isInDbTrans)
            {
                try{$trans->rollback();}catch(Exception $ee){}
                return false;
            }
            else
                throw $e;
        }

    }

    /**
     * 获取项目需要申请占用的额度信息
     * @param $projectId
     * @param null $projectData
     * @return int
     */
    public static function getProjectApplyAmount($projectId,$projectData=null)
    {
        if(empty($projectData))
        {
            $projectData=Project::model()->with("down_detail")->findByPk($projectId);
            if(empty($projectData->project_id))
                return -1001;

        }
        $partnerId=$projectData->down_partner_id;
        $model=PartnerCredit::model()->findByPk($partnerId);
        if(empty($model->partner_id))
            return -2001;
        if(empty($projectData->down_detail))
            $detail=ProjectDetail::model()->find("project_id=".$projectId." and type=2");
        else
            $detail=$projectData->down_detail;
        if(empty($detail->detail_id))
            return -1002;

        $projectCredit=ProjectCredit::model()->find("project_id=".$projectId);
        if (empty($projectCredit->project_id))
        {
            $projectCredit = new ProjectCredit();
            $projectCredit->project_id = $projectId;
            $projectCredit->save();
        }

        $amount=$detail->amount
            -($projectCredit->user_amount-$projectCredit->user_amount_free)
            -($projectCredit->other_amount-$projectCredit->other_amount_free)
            -($model->credit_amount-$model->use_amount);
        $amount=$amount<0?0:$amount;

        return $amount;
    }


    /**
     * 使用合作方额度
     * @param $partnerId
     * @param $amount
     * @return bool|string
     */
    public static function usePartnerCreditAmount($partnerId,$amount)
    {
        $rows=PartnerCredit::model()->updateByPk($partnerId
            ,array("use_amount"=>new CDbExpression("use_amount+".$amount),"update_time"=>new CDbExpression("now()"))
            ,"credit_amount-frozen_amount-use_amount>=".$amount
        );
        if($rows==1)
        {
            return true;
        }
        else
            return false;

    }

    /**
     * ！！！用于结算时占用合作额度，当前方法可以把合作方额度减成负值，暂时只能是结算时使用
     * @param $partnerId
     * @param $amount
     * @return bool
     */
    public static function usePartnerCreditAmountForSettlement($partnerId,$amount)
    {
        $rows=PartnerCredit::model()->updateByPk($partnerId
            ,array("use_amount"=>new CDbExpression("use_amount+".$amount),"update_time"=>new CDbExpression("now()"))
        );
        if($rows==1)
        {
            return true;
        }
        else
            return false;
    }

    /**
     * 冻结合作方额度
     * @param $partnerId
     * @param $amount
     * @return bool
     */
    public static function freezePartnerCreditAmount($partnerId,$amount)
    {
        $rows=PartnerCredit::model()->updateByPk($partnerId
            ,array("use_amount"=>new CDbExpression("frozen_amount+".$amount),"update_time"=>new CDbExpression("now()"))
            ,"credit_amount-frozen_amount-use_amount>=".$amount
        );
        if($rows==1)
        {
            return true;
        }
        else
            return false;

    }

    /**
     * 解除冻结的用户额度
     * @param $partnerId
     * @param $amount
     * @return bool
     */
    public static function unFreezePartnerCreditAmount($partnerId,$amount)
    {
       
        $rows=PartnerCredit::model()->updateByPk($partnerId
            ,array("use_amount"=>new CDbExpression("frozen_amount-".$amount),"update_time"=>new CDbExpression("now()"))
            ,"frozen_amount>=".$amount
        );
        if($rows==1)
        {
            return true;
        }
        else
            return false;

    }

    /**
     * 释放合作方的额度
     * @param $partnerId
     * @param $amount
     * @return bool
     */
    public static function releasePartnerCreditAmount($partnerId,$amount)
    {
        $rows=PartnerCredit::model()->updateByPk($partnerId
            ,array("use_amount"=>new CDbExpression("use_amount-".$amount),"update_time"=>new CDbExpression("now()"))
            ,"use_amount>=".$amount
        );
        if($rows==1)
        {
            return true;
        }
        else
            return false;
    }
}