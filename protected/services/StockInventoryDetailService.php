<?php

/**
 * Desc: 库存盘点损耗分摊数据服务
 * User: susiehuang
 * Date: 2017/11/16 0009
 * Time: 17:03
 */
class StockInventoryDetailService {
    /**
     * @desc 损耗分摊明细参数校验
     * @param array $stockInventory
     * @param float $quantityDiff
     * @return bool | string
     */
    public static function checkStockInventoryDetailParamsValid($stockInventory, $quantityDiff) {
        Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' pass params are:' . json_encode($stockInventory), ' and quantity_diff is:' . $quantityDiff);
        if (Utility::isNotEmpty($stockInventory)) {
            $totalQuantityDiff = 0;
            $invalid = false;
            foreach ($stockInventory as $key => $row) {
                $requiredParams = array('inventory_id', 'corporation_id', 'goods_id', 'store_id', 'unit', 'quantity', 'quantity_active', 'quantity_before', 'quantity_diff', 'quantity_frozen', 'stock_in_id', 'type');
                //必填参数校验
                if (!Utility::checkRequiredParamsNoFilterInject($row, $requiredParams)) {
                    $invalid = true;
                    break;
                }

                $totalQuantityDiff += $row['quantity_diff'];
            }

            if ($invalid) {
                return BusinessError::outputError(OilError::$STOCK_INVENTORY_DETAIL_PARAMS_PASS_ERROR);
            }

            if (bccomp($totalQuantityDiff, $quantityDiff, 4) != 0) {
                return BusinessError::outputError(OilError::$STOCK_INVENTORY_DETAIL_QUANTITY_DIFF_NOT_MATCH);
            }
        } else {
            return BusinessError::outputError(OilError::$STOCK_INVENTORY_DETAIL_EMPTY);
        }

        return true;
    }

    /**
     * @desc 保存损耗分摊明细
     * @param array $details
     * @param int $goodsDetailId
     * @return array|int
     */
    public static function saveStockInventoryDetail($details, $goodsDetailId) {
        Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' pass transaction params are:' . json_encode($details) . ' goods_detail_id is:' . $goodsDetailId);
        if (Utility::isEmpty($details) || empty($goodsDetailId)) {
            BusinessException::throw_exception(OilError::$PARAMS_PASS_ERROR);
        }

        $data = StockInventoryDetail::model()->findAll('goods_detail_id = :goodsDetailId', array('goodsDetailId' => $goodsDetailId));
        $p = array();
        if (Utility::isNotEmpty($data)) {
            foreach ($data as $v) {
                $p[$v["detail_id"]] = $v["detail_id"];
            }
        }

        foreach ($details as $row) {
            if (array_key_exists($row["detail_id"], $p)) {
                $obj = StockInventoryDetail::model()->findByPk($row["detail_id"]);
                unset($p[$row["detail_id"]]);
            } else {
                $obj = new StockInventoryDetail();
            }
            unset($row['detail_id']);
            $obj->goods_detail_id = $goodsDetailId;
            $obj->setAttributes($row, false);
            $obj->save();
        }
        if (count($p) > 0) {
            foreach ($p as $val) {
                StockInventoryDetail::model()->deleteByPk($val);
            }
        }
    }

    /**
     * @desc 根据损耗分摊更新库存数量
     * @param array $params 损耗分摊明细
     * @param int $op_type 操作类型   0：损耗分摊提交   1：库存盘点审核驳回
     */
    public static function updateGoodsStock($params, $op_type = 0) {
        if (Utility::isNotEmpty($params)) {
            foreach ($params as $key => $row) {
                $row['quantity_diff'] = abs($row['quantity_diff']);
                if (!empty($row['stock_in_id']) && !empty($row['goods_id']) && !empty($row['quantity_diff']) && !empty($row['type'])) {
                    $stockModel = Stock::model()->find('stock_in_id = :stockInId and goods_id = :goodsId', array('stockInId' => $row['stock_in_id'], 'goodsId' => $row['goods_id']));
                    if (!empty($stockModel->stock_id)) {
                        if ($op_type == 0) {
                            if ($row['type'] == ConstantMap::STOCK_INVENTORY_LOSS) {
                                if ($row['quantity_diff'] > $stockModel->quantity_balance || $row['quantity_diff'] > $stockModel->quantity) {
                                    BusinessException::throw_exception(OilError::$STOCK_BALANCE_NOT_ENOUGH, array('stock_in_code' => $stockModel->stockIn->code, 'stock_id' => $stockModel->stock_id, 'quantity' => $row['quantity_diff'], 'quantity_balance' => $stockModel->quantity_balance));
                                }

                                $res = StockService::reduce($stockModel->stock_id, $row['quantity_diff'], StockLog::METHOD_STOCK_CHECK);
                                if (!$res) {
                                    BusinessException::throw_exception(OilError::$STOCK_REDUCE_ERROR, array('stock_id' => $stockModel->stock_id, 'quantity' => $row['quantity_diff']));
                                }
                            } elseif ($row['type'] == ConstantMap::STOCK_INVENTORY_PROFIT) {
                                $res = StockService::add($stockModel->stock_id, $row['quantity_diff'], StockLog::METHOD_STOCK_CHECK);
                                if (!$res) {
                                    BusinessException::throw_exception(OilError::$STOCK_ADD_ERROR, array('stock_id' => $stockModel->stock_id, 'quantity' => $row['quantity_diff']));
                                }
                            }
                        } elseif ($op_type == 1) {
                            if ($row['type'] == ConstantMap::STOCK_INVENTORY_LOSS) {
                                $res = StockService::add($stockModel->stock_id, $row['quantity_diff'], StockLog::METHOD_STOCK_CHECK);
                                if (!$res) {
                                    BusinessException::throw_exception(OilError::$STOCK_ADD_ERROR, array('stock_id' => $stockModel->stock_id, 'quantity' => $row['quantity_diff']));
                                }
                            } elseif ($row['type'] == ConstantMap::STOCK_INVENTORY_PROFIT) {
                                if ($row['quantity_diff'] > $stockModel->quantity_balance || $row['quantity_diff'] > $stockModel->quantity) {
                                    BusinessException::throw_exception(OilError::$STOCK_BALANCE_NOT_ENOUGH, array('stock_in_code' => $stockModel->stockIn->code, 'stock_id' => $stockModel->stock_id, 'quantity' => $row['quantity_diff'], 'quantity_balance' => $stockModel->quantity_balance));
                                }

                                $res = StockService::reduce($stockModel->stock_id, $row['quantity_diff'], StockLog::METHOD_STOCK_CHECK);
                                if (!$res) {
                                    BusinessException::throw_exception(OilError::$STOCK_REDUCE_ERROR, array('stock_id' => $stockModel->stock_id, 'quantity' => $row['quantity_diff']));
                                }
                            }
                        }
                    }
                    unset($stockModel);
                }
            }
        }
    }
}