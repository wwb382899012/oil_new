<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/7/16 0016
 * Time: 17:58
 */

class MoneyPayOrder extends BaseBusinessActiveRecord
{
    const STATUS_NEW = 0; //未处理，付款中
    const STATUS_PAID = 2; //付款成功
//    const STATUS_PAY_FAILED = 3; //付款失败
    const STATUS_PAY_REJECT = 4; //付款拒绝

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_money_pay_order';
    }

    public function relations()
    {
        return array(
            "apply" => array(self::BELONGS_TO, "PayApplication", "apply_id"),
            "corp" => array(self::BELONGS_TO, "Corporation", "corporation_id"),
        );
    }

    /**
     * 根据付款申请编号查找付款指令记录
     * @param $applyId
     * @return CActiveRecord
     */
    public function findByApplyId($applyId)
    {
        return $this->find("apply_id=" . $applyId);
    }

    /**
     * 根据资金系统订单编号查找付款指令记录
     * @param $orderNum
     * @return CActiveRecord
     */
    public function findByOrderNum($orderNum)
    {
        return $this->find("order_num=" . $orderNum);
    }
}