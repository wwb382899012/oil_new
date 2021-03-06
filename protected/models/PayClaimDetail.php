<?php

/**
 * 付款流水认领detail
 */
class PayClaimDetail extends BaseActiveRecord
{
    const STATUS_SUBMITED=1;//已提交

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_pay_claim_detail';
    }

    public function relations()
    {
        return array(
            "paymentPlan" => array(self::BELONGS_TO, "PaymentPlan", "plan_id"),
        );
    }

    public function beforeSave() {
        if ($this->isNewRecord) {
            if (empty($this->create_time)) {
                $this->create_time = new CDbExpression("now()");
            }
            if (empty($this->create_user_id)) {
                $this->create_user_id = Utility::getNowUserId();
            }
        }
        if ($this->update_time == $this->getOldAttribute("update_time")) {
            $this->update_time = new CDbExpression("now()");
            $this->update_user_id = Utility::getNowUserId();
        }

        return parent::beforeSave(); // TODO: Change the autogenerated stub
    }


}