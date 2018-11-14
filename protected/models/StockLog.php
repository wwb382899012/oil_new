<?php

/**
 * Desc: 库存出入库明细
 * User: susiehuang
 * Date: 2017/11/21 0013
 * Time: 16:10
 */
class StockLog extends BaseBusinessActiveRecord {

    const TYPE_OUT=1;//出库
    const TYPE_IN=2;//入库

    const METHOD_CONTRACT=1;//合同出入库
    const METHOD_RETURN=2;//还货出入库
    const METHOD_BORROW=3;//借货出入库
    const METHOD_STOCK_CHECK=4;//盘点出入库

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 't_stock_log';
    }

    public function relations() {
        return array(
            "stock" => array(self::BELONGS_TO, "Stock", "stock_id"),
            "goods" => array(self::BELONGS_TO, "Goods", "goods_id"),
        );
    }

    /**
     * @desc 添加出入库明细
     * @param array $stockArr
     */
    public static function addStockLog($stockArr) {
        if (Utility::isNotEmpty($stockArr)) {
            $requiredParams = array('stock_id', 'goods_id', 'type', 'relation_id');
            if (!Utility::checkRequiredParamsNoFilterInject($stockArr, $requiredParams)) {
                BusinessException::throw_exception(OilError::$PARAMS_PASS_ERROR);
            }
            $stockLog = new StockLog();
            $stockLog->stock_id = $stockArr['stock_id'];
            $stockLog->goods_id = $stockArr['goods_id'];
            $stockLog->type = $stockArr['type'];
            $stockLog->relation_id = $stockArr['relation_id'];
            $stockLog->method = array_key_exists('method', $stockArr) ? $stockArr['method'] : 0;
            $stockLog->price = array_key_exists('price', $stockArr) ? $stockArr['price'] : 0;
            $stockLog->quantity = array_key_exists('quantity', $stockArr) ? $stockArr['quantity'] : 0;
            $stockLog->quantity_balance = array_key_exists('quantity_balance', $stockArr) ? $stockArr['quantity_balance'] : 0;
            $stockLog->unit = array_key_exists('unit', $stockArr) ? $stockArr['unit'] : 0;
            $stockLog->amount = array_key_exists('amount', $stockArr) ? $stockArr['amount'] : 0;
            $stockLog->to_contract_id = array_key_exists('to_contract_id', $stockArr) ? $stockArr['to_contract_id'] : 0;
            $stockLog->create_user_id = Utility::getNowUserId();
            $stockLog->create_time = Utility::getDateTime();
            $stockLog->update_user_id = Utility::getNowUserId();
            $stockLog->update_time = Utility::getDateTime();
            $stockLog->save();
        }
    }
}