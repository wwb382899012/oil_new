<?php

/**
 * Created by youyi000.
 * DateTime: 2017/4/12 15:19
 * Describe：
 */
class UserCreditService
{

    /**
     * 获取用户剩余可用额度
     * @param $userId
     * @return int|mixed
     */
    public static function getUserBalanceCreditAmount($userId)
    {
        $model=UserCredit::model()->findByPk($userId);
        if(empty($model->user_id))
            return 0;
        $amount=$model->credit_amount-$model->frozen_amount-$model->use_amount;
        $amount=$amount<0?0:$amount;
        return $amount;
    }

    /**
     * 使用业务员额度
     * @param $userId
     * @param $amount
     * @return bool|string
     */
    public static function useUserCreditAmount($userId,$amount)
    {
        /*$user=UserCredit::model()->findByPk($userId);
        if(empty($user->user_id))
            return "无用户可用额度信息";*/

        $rows=UserCredit::model()->updateByPk($userId
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
     * 冻结业务员额度
     * @param $userId
     * @param $amount
     * @return bool
     */
    public static function freezeUserCreditAmount($userId,$amount)
    {
        /*$user=UserCredit::model()->findByPk($userId);
        if(empty($user->user_id))
            return "无用户可用额度信息";*/

        $rows=UserCredit::model()->updateByPk($userId
            ,array("frozen_amount"=>new CDbExpression("frozen_amount+".$amount),"update_time"=>new CDbExpression("now()"))
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
     * 使冻结的额度生效为已使用
     * @param $userId
     * @param $amount
     * @return bool
     */
    public static function effectFrozenCreditAmount($userId,$amount)
    {
        $rows=UserCredit::model()->updateByPk($userId
            ,array(
                "frozen_amount"=>new CDbExpression("frozen_amount-".$amount),
                "use_amount"=>new CDbExpression("use_amount+".$amount),
                "update_time"=>new CDbExpression("now()")
            )
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
     * 解除冻结的用户额度
     * @param $userId
     * @param $amount
     * @return bool
     */
    public static function unFreezeUserCreditAmount($userId,$amount)
    {
        /*$user=UserCredit::model()->findByPk($userId);
        if(empty($user->user_id))
            return "无用户可用额度信息";*/

        $rows=UserCredit::model()->updateByPk($userId
            ,array("frozen_amount"=>new CDbExpression("frozen_amount-".$amount),"update_time"=>new CDbExpression("now()"))
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
     * 释放业务员的额度
     * @param $userId
     * @param $amount
     * @return bool
     */
    public static function releaseUserCreditAmount($userId,$amount)
    {
        /*$user=UserCredit::model()->findByPk($userId);
        if(empty($user->user_id))
            return "无用户可用额度信息";*/
        $rows=UserCredit::model()->updateByPk($userId
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