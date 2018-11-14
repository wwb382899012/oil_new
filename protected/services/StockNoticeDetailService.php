<?php

/**
 * Desc: 入库通知单明细服务
 * User: susiehuang
 * Date: 2017/10/10 0031
 * Time: 11:05
 */
class StockNoticeDetailService {
    /**
     * @desc 入库通知单明细参数校验
     * @param array $goodsItems
     * @return bool
     */
    public static function checkParamsValid($goodsItems) {
        if (Utility::isNotEmpty($goodsItems)) {
            $goodsArr = array();
            $invalid = false;
            foreach ($goodsItems as $key => $row) {
                $requiredParams = array('contract_id', 'project_id', 'goods_id');
                if(!empty($row['quantity'])) {
                    array_push($requiredParams, 'unit');
                }
                if(!empty($row['quantity_sub'])) {
                    array_push($requiredParams, 'unit_sub');
                }
                if($row['type'] == ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE) {
                    array_push($requiredParams, 'store_id');
                }
                //必填参数校验
                if (!Utility::checkRequiredParamsNoFilterInject($row, $requiredParams)) {
                    $invalid = true;
                    break;
                }

                array_push($goodsArr, $row['goods_id']);
            }

            if ($invalid) {
                return BusinessError::outputError(OilError::$TRANSACTION_REQUIRED_PARAMS_CHECK_ERROR);
            }

            if(count($goodsArr) != count(array_unique($goodsArr))) {
                return BusinessError::outputError(OilError::$STOCK_BATCH_GOODS_REPEAT);
            }
        } else {
            return BusinessError::outputError(OilError::$STOCK_BATCH_NOT_HAVE_DETAIL);
        }

        return true;
    }


    /**
     * @desc 保存交易明细
     * @param array $transactions
     * @param int $batch_id
     * @return array|int
     */
    public static function saveGoodsTransactions($transactions, $batch_id) {
        Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' pass transaction params are:' . json_encode($transactions) . ' batch_id is:' . $batch_id);
        if (Utility::isEmpty($transactions) || empty($batch_id)) {
            BusinessException::throw_exception(OilError::$PARAMS_PASS_ERROR);
        }

        $data = StockNoticeDetail::model()->findAll('batch_id = :batchId', array('batchId' => $batch_id));
        $p = array();
        if (Utility::isNotEmpty($data)) {
            foreach ($data as $v) {
                $p[$v["detail_id"]] = $v["detail_id"];
            }
        }

        foreach ($transactions as $row) {
            if (array_key_exists($row["detail_id"], $p)) {
                $obj = StockNoticeDetail::model()->findByPk($row["detail_id"]);
                unset($p[$row["detail_id"]]);
            } else {
                $obj = new StockNoticeDetail();
                //$obj->detail_id = IDService::getSerialNum('stock.batch.detail.id');
            }
            unset($row['detail_id']);
            $obj->batch_id = $batch_id;
            $obj->setAttributes($row, false);
            $obj->save();
        }
        if (count($p) > 0) {
            foreach ($p as $val) {
                StockNoticeDetail::model()->deleteByPk($val);
            }
        }
    }
}
