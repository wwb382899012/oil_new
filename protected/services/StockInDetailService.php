<?php

/**
 * Desc: 入库单明细服务
 * User: susiehuang
 * Date: 2017/10/10 0031
 * Time: 11:05
 */
class StockInDetailService {
    /**
     * @desc 入库单明细参数校验
     * @param array $goodsItems
     * @return bool
     */
    public static function checkParamsValid($goodsItems) {
        Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' pass params are:' . json_encode($goodsItems));
        if (Utility::isNotEmpty($goodsItems)) {
            $totalQuantity = 0;
            $invalid = false;
            foreach ($goodsItems as $key => $row) {
                $requiredParams = array('contract_id', 'project_id', 'goods_id', 'stock_in_id', 'unit');
                //必填参数校验
                if (!Utility::checkRequiredParamsNoFilterInject($row, $requiredParams)) {
                    $invalid = true;
                    break;
                }

                $totalQuantity += $row['quantity'];
            }

            if ($invalid) {
                return BusinessError::outputError(OilError::$TRANSACTION_REQUIRED_PARAMS_CHECK_ERROR);
            }

            if ($totalQuantity == 0) {
                return BusinessError::outputError(OilError::$TRANSACTION_TOTAL_QUANTITY_EMPTY);
            }
        } else {
            return BusinessError::outputError(OilError::$STOCK_IN_NOT_HAVE_DETAIL);
        }

        return true;
    }


    /**
     * @desc 保存交易明细
     * @param array $transactions
     * @param int $stock_in_id
     * @param int $store_id
     * @return array|int
     */
    public static function saveGoodsTransactions($transactions, $stock_in_id, $store_id = 0) {
        Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' pass transaction params are:' . json_encode($transactions) . ' stock_in_id is:' . $stock_in_id . ', store_id is:' . $store_id);
        if (Utility::isEmpty($transactions) || empty($stock_in_id)) {
            BusinessException::throw_exception(OilError::$PARAMS_PASS_ERROR);
        }

        $data = StockInDetail::model()->findAll('stock_in_id = :stockInId', array('stockInId' => $stock_in_id));
        $p = array();
        if (Utility::isNotEmpty($data)) {
            foreach ($data as $v) {
                $p[$v["stock_id"]] = $v["stock_id"];
            }
        }

        foreach ($transactions as $row) {
            if (array_key_exists($row["stock_id"], $p)) {
                $obj = StockInDetail::model()->findByPk($row["stock_id"]);
                unset($p[$row["stock_id"]]);
            } else {
                $obj = new StockInDetail();
                //$obj->stock_id = IDService::getSerialNum('stock.in.detail.id');
            }
            unset($row['store_id']);
            unset($row['stock_id']);
            if ($row['unit'] != $row['unit_sub'] && $row['unit'] != $row['stock_unit']) {
               $temp = $row['quantity'];
               $row['quantity'] = $row['quantity_sub'];
               $row['quantity_sub'] = $temp;
               $row['unit_rate'] = $row['quantity'] / $row['quantity_sub'];
            }
            $obj->stock_in_id = $stock_in_id;
            $obj->store_id = $store_id;
            $obj->quantity_actual = !empty($row['quantity']) ? $row['quantity'] : 0;
            $obj->quantity_actual_sub = !empty($row['quantity_sub']) ? $row['quantity_sub'] : 0;
            $obj->setAttributes($row, false);
            $obj->save();
        }
        if (count($p) > 0) {
            foreach ($p as $val) {
                StockInDetail::model()->deleteByPk($val);
            }
        }
    }
}
