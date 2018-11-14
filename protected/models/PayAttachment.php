<?php
class PayAttachment extends BaseBusinessActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return 't_pay_attachment';
    }

    public function relations()
    {
        return array(
        );
    }


}