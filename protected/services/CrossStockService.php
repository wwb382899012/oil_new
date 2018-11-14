<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/19 14:54
 * Describe：
 */

class CrossStockService
{

    /**
     * 出库存
     * @param $crossDetailId
     * @param $quantity
     * @return bool
     */
    public static function out($crossDetailId,$quantity)
    {
        $rows=CrossDetail::model()->updateByPk($crossDetailId
            ,array(
                 "quantity_balance"=>new CDbExpression("quantity_balance-".$quantity),
                 "quantity_out"=>new CDbExpression("quantity_out+".$quantity),
                 "update_time"=>new CDbExpression("now()")
             )
            ,"quantity-quantity_out-quantity_frozen>=".$quantity
        );
        if($rows==1)
        {
            return true;
        }
        else
            return false;
    }

    /**
     * 退款库
     * @param $crossDetailId
     * @param $quantity
     * @return bool
     */
    public static function refund($crossDetailId,$quantity)
    {
        $rows=CrossDetail::model()->updateByPk($crossDetailId,
             array(
                 "quantity_balance"=>new CDbExpression("quantity_balance+".$quantity),
                 "quantity_out"=>new CDbExpression("quantity_out-".$quantity),
                 "update_time"=>new CDbExpression("now()")
             )
            ,"quantity_out>=".$quantity
        );
        if($rows==1)
        {
            return true;
        }
        else
            return false;
    }

    /**
     * 冻结库存
     * @param $crossDetailId
     * @param $quantity
     * @return bool
     */
    public static function freeze($crossDetailId,$quantity)
    {
        $rows=CrossDetail::model()->updateByPk($crossDetailId
            ,array(
                 "quantity_balance"=>new CDbExpression("quantity_balance-".$quantity),
                 "quantity_frozen"=>new CDbExpression("quantity_frozen+".$quantity),
                 "update_time"=>new CDbExpression("now()")
             )
            ,"quantity-quantity_out-quantity_frozen>=".$quantity
        );
        if($rows==1)
        {
            return true;
        }
        else
            return false;
    }

    /**
     * 解冻库存
     * @param $crossDetailId
     * @param $quantity
     * @return bool
     */
    public static function unFreeze($crossDetailId,$quantity)
    {
        $rows=CrossDetail::model()->updateByPk($crossDetailId
            ,array(
                 "quantity_balance"=>new CDbExpression("quantity_balance+".$quantity),
                 "quantity_frozen"=>new CDbExpression("quantity_frozen-".$quantity),
                 "update_time"=>new CDbExpression("now()")
             )
            ,"quantity_frozen>=".$quantity
        );
        if($rows==1)
        {
            return true;
        }
        else
            return false;
    }


    /**
     * 增加库存，此方法只在实际有库存增加时才调用，比如盘点时有盘赢，如果退库，调用refund！！！
     * @param $crossDetailId
     * @param $quantity
     * @return bool
     */
    public static function add($crossDetailId,$quantity)
    {
        $rows=CrossDetail::model()->updateByPk(
            $crossDetailId,
            array(
                "quantity"=>new CDbExpression("quantity+".$quantity),
                "quantity_balance"=>new CDbExpression("quantity_balance+".$quantity),
                "update_time"=>new CDbExpression("now()")
            )
        );
        if($rows==1)
        {
            return true;
        }
        else
            return false;
    }


    /**
     * 减小库存，此方法只在实际有库存减小时才调用，比如盘点时有盘亏等场景；如果出库，调用out！！！
     * @param $crossDetailId
     * @param $quantity
     * @return bool
     */
    public static function reduce($crossDetailId,$quantity)
    {
        $rows=CrossDetail::model()->updateByPk($crossDetailId
            ,array(
                 "quantity_balance"=>new CDbExpression("quantity_balance-".$quantity),
                 "quantity"=>new CDbExpression("quantity-".$quantity),
                 "update_time"=>new CDbExpression("now()")
             )
            ,"quantity>=".$quantity
        );
        if($rows==1)
        {
            return true;
        }
        else
            return false;
    }

}