<?php 

/**
 * Created by vector.
 * DateTime: 2018/3/27 11:35
 * Describe：合同结算单商品结算附件
 */

class StockBatchSettlementAttachment extends BaseActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_stock_batch_settlement_attachment';
    }

}