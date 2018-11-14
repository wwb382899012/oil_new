<?php

/**
 * Created by youyi000.
 * DateTime: 2016/11/7 15:51
 * Describe：合作方额度
 */
class PartnerCredit extends BaseActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_partner_credit';
    }

    public function afterSave()
    {
        if($this->credit_amount!=$this->oldAttributes["credit_amount"])
        {
            $log=new PartnerFieldLog();
            $log->partner_id=$this->partner_id;
            $log->field_name="credit_amount";
            $log->old_value=$this->oldAttributes["credit_amount"];
            $log->new_value=$this->credit_amount;
            $log->create_user_id=$this->update_user_id;
            $log->create_time=new CDbExpression("now()");
            $log->save();
        }
    }


}