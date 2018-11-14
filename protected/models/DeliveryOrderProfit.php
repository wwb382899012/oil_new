<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/11 11:47
 * Describe：
 */

class DeliveryOrderProfit extends BaseHasSubActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_delivery_order_profit';//表名
    }

    public function relations()
    {
        return array(
            
        );
    }

    public function beforeSave()
    {
        if ($this->isNewRecord)
        {
            if (empty($this->create_time))
                $this->create_time = new CDbExpression("now()");
             /*if (empty($this->create_user_id))
                $this->create_user_id= Utility::getNowUserId();*/
        }
        if ($this->update_time == $this->getOldAttribute("update_time"))
        {
            $this->update_time = new CDbExpression("now()");
            //$this->update_user_id = Utility::getNowUserId();
        }
        return parent::beforeSave(); // TODO: Change the autogenerated stub
    }
}