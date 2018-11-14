<?php
class StockBatchAttachment extends BaseActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public function tableName()
    {
        return 't_stock_in_batch_attachment';
    }
    
    public function relations()
    {
        return array(
            //"action" => array(self::BELONGS_TO, "Action", "action_id"),
            
        );
    }


}