<?php

/**
 * Desc: 库存盘点数据服务
 * User: susiehuang
 * Date: 2017/11/14 0009
 * Time: 17:03
 */
class StockInventoryService {
    /**
     * @desc 检查是否可进行盘点操作
     * @param int $corporationId
     * @param int $storeId
     * @param int $goodsId
     * @param int $unit
     * @return bool
     */
    public static function checkIsCanAdd($corporationId, $storeId, $goodsId, $unit) {
        if (Utility::checkQueryId($corporationId) && Utility::checkQueryId($storeId) && Utility::checkQueryId($goodsId) && Utility::checkQueryId($unit)) {
            $stockInventoryGoods = StockInventoryGoodsDetail::model()->with('stockInventory')->findAll('t.goods_id = :goodsId and t.unit = :unit and stockInventory.corporation_id = :corporationId and stockInventory.store_id = :storeId and stockInventory.status = :status', array('corporationId' => $corporationId, 'storeId' => $storeId, 'goodsId' => $goodsId, 'unit' => $unit, 'status' => StockInventory::STATUS_SUBMIT));
            if (Utility::isNotEmpty($stockInventoryGoods)) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * @desc 获取最新的可编辑的库存盘点信息
     * @param int $corporationId
     * @param int $storeId
     * @param int $goodsId
     * @param int $unit
     * @return bool
     */
    public static function getNewestCanEditStockInventory($corporationId, $storeId, $goodsId, $unit) {
        if (Utility::checkQueryId($corporationId) && Utility::checkQueryId($storeId) && Utility::checkQueryId($goodsId) && Utility::checkQueryId($unit)) {
            $sql = 'select b.inventory_id from t_stock_inventory_goods_detail a left join t_stock_inventory b on b.inventory_id = a.inventory_id where b.corporation_id = ' . $corporationId . ' and b.store_id = ' . $storeId . ' and a.goods_id = ' . $goodsId . ' and a.unit = ' . $unit . ' and b.status < ' . StockInventory::STATUS_SUBMIT . ' order by b.inventory_id desc limit 1';
            $data = Utility::query($sql);
            if (Utility::isNotEmpty($data)) {
                return $data[0]['inventory_id'];
            }
        }

        return 0;
    }

    /**
     * @判断是否可以修改
     * @param $status
     * @return bool
     */
    public static function checkIsCanEdit($status) {
        if ($status < StockInventory::STATUS_SUBMIT) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @desc 获取库存明细
     * @param array [
     *      'corporationId' => 1    #交易主体id，必填
     *      'storeId' => 1          #仓库id，必填
     *      'goodsId' => 1          #品名id，必填
     *      'unit' => 1             #单位id，必填
     * ]
     * @param int $onlyActive #是否只查询可用库存
     * @return array
     */
    public static function getStockDetail($params, $onlyActive = 0) {
        $data = array();
        if (Utility::checkQueryId($params['corporationId']) && Utility::checkQueryId($params['goodsId']) && Utility::checkQueryId($params['unit'])) {
            $query = '';
            /*if (!empty($onlyActive)) {
                $query .= ' having sum(a.quantity_balance) > 0';
            } else {
                $query .= ' having sum(a.quantity_balance) >= 0 or sum(a.quantity_frozen) >= 0';
            }*/
            $sql = 'select a.stock_in_id,c.code as stock_in_code,sum(a.quantity_balance) AS quantity_active,sum(a.quantity_frozen) AS quantity_frozen,sum(a.quantity_balance)+sum(a.quantity_frozen) as quantity_before from t_stock a 
                    left join t_contract b on a.contract_id = b.contract_id 
                    left join t_stock_in c on a.stock_in_id = c.stock_in_id  
                    where b.corporation_id = ' . $params['corporationId'] . ' and a.store_id = ' . $params['storeId'] . ' and a.goods_id = ' . $params['goodsId'] . ' and a.unit = ' . $params['unit'] . ' 
                    group by a.stock_in_id' . $query;

            $data = Utility::query($sql);
        }

        return $data;
    }

    /**
     * @desc 获取库存
     * @param array [
     *      'corporationId' => 1    #交易主体id，必填
     *      'storeId' => 1          #仓库id，必填
     *      'goodsId' => 1          #品名id，必填
     *      'unit' => 1             #单位id，必填
     * ]
     * @return array
     */
    public static function getStockQuantity($params) {
        $data = array();
        if (Utility::checkQueryId($params['corporationId']) && Utility::checkQueryId($params['storeId']) && Utility::checkQueryId($params['goodsId']) && Utility::checkQueryId($params['unit'])) {
            $sql = 'select sum(a.quantity_balance) AS quantity_active,sum(a.quantity_frozen) AS quantity_frozen,sum(a.quantity_balance)+sum(a.quantity_frozen) as quantity_before from t_stock a 
                    left join t_contract p on p.contract_id = a.contract_id 
                    where p.corporation_id = ' . $params['corporationId'] . ' and a.store_id = ' . $params['storeId'] . ' and a.goods_id = ' . $params['goodsId'] . ' and a.unit = ' . $params['unit'];

            $res = Utility::query($sql);
            $data = $res[0];
        }

        return $data;
    }

    /**
     * @desc 初始化库存盘点明细
     * @param array [
     *      'corporationId' => 1    #交易主体id，必填
     *      'storeId' => 1          #仓库id，必填
     *      'goodsId' => 1          #品名id，必填
     *      'unit' => 1             #单位id，必填
     * ]
     * @param int $inventoryId
     * @param array $detail
     * @return array
     */
    public static function formatStockInventoryDetail($params, $inventoryId, $detail = array()) {
        $stockInventoryDetail = array();
        if (Utility::checkQueryId($params['corporationId']) && Utility::checkQueryId($params['storeId']) && Utility::checkQueryId($params['goodsId']) && Utility::checkQueryId($params['unit']) && Utility::checkQueryId($inventoryId)) {
            $stockInventoryDetail = StockInventoryService::getStockDetail($params);
            if (Utility::isNotEmpty($stockInventoryDetail)) {
                foreach ($stockInventoryDetail as $key => $row) {
                    $stockInventoryDetail[$key]['inventory_id'] = $inventoryId;
                    $stockInventoryDetail[$key]['corporation_id'] = $params['corporationId'];
                    $stockInventoryDetail[$key]['store_id'] = $params['storeId'];
                    $stockInventoryDetail[$key]['goods_id'] = $params['goodsId'];
                    $stockInventoryDetail[$key]['unit'] = $params['unit'];
                    $stockInventoryDetail[$key]['quantity_diff'] = 0;
                    if (Utility::isNotEmpty($detail)) {
                        foreach ($detail as $val) {
                            if ($row['stock_in_id'] == $val['stock_in_id']) {
                                $stockInventoryDetail[$key]['detail_id'] = $val['detail_id'];
                                $stockInventoryDetail[$key]['quantity_diff'] = $val['quantity_diff'];
                            }
                        }
                    }
                }
            }
        }

        return $stockInventoryDetail;
    }

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
     * @desc 保存库存盘点明细
     * @param array $params
     * @param int $inventoryId
     * @return array|int
     */
    public static function saveStockInventoryGoodsDetail($params, $inventoryId) {
        Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' pass transaction params are:' . json_encode($params));
        if (Utility::isEmpty($params)) {
            BusinessException::throw_exception(OilError::$PARAMS_PASS_ERROR);
        }

        $data = StockInventoryGoodsDetail::model()->findAll('inventory_id = :inventoryId', array('inventoryId' => $inventoryId));
        $p = array();
        if (Utility::isNotEmpty($data)) {
            foreach ($data as $v) {
                $p[$v["goods_detail_id"]] = $v["goods_detail_id"];
            }
        }

        if (array_key_exists($params["goods_detail_id"], $p)) {
            $obj = StockInventoryGoodsDetail::model()->findByPk($params["goods_detail_id"]);
            unset($p[$params["goods_detail_id"]]);
        } else {
            $obj = new StockInventoryGoodsDetail();
        }
        unset($params['status']);
        unset($params['goods_detail_id']);
        $obj->setAttributes($params, false);
        $obj->inventory_id = $inventoryId;
        $obj->save();

        if (count($p) > 0) {
            foreach ($p as $val) {
                StockInventoryGoodsDetail::model()->deleteByPk($val);
            }
        }

        return $obj->goods_detail_id;
    }


    /**
     * @desc 获取附件信息
     * @param $id
     * @param $type
     * @return array
     */
    public static function getAttachments($id, $type = '') {
        if (empty($id)) {
            return array();
        }
        if (!empty($type)) {
            $type = ' and type=' . $type;
        }

        $sql = "select * from t_stock_inventory_attachment where base_id = " . $id . " and status = 1" . $type . " order by type asc";
        $data = Utility::query($sql);
        $attachments = array();
        foreach ($data as $v) {
            $attachments[$v["type"]][] = $v;
        }

        return $attachments;
    }

    /**
     * @desc 获取最新的审批明细id
     * @param int $inventoryId
     * @return int
     */
    public static function getNewestCheckDetailId($inventoryId) {
        if (Utility::checkQueryId($inventoryId)) {
            $sql = 'select detail_id from t_check_detail where obj_id = ' . $inventoryId . ' and business_id = ' . FlowService::BUSINESS_STOCK_INVENTORY . ' and status = 1 order by detail_id desc limit 1';
            $data = Utility::query($sql);
            if (Utility::isNotEmpty($data)) {
                return $data[0]['detail_id'];
            } else {
                return 0;
            }
        }

        return 0;
    }

    /**
     * @desc 库存盘点审批通过添加库存操作日志信息
     * @param int $inventory_id
     * @throws
     */
    public static function addStockLogAfterCheckPass($inventory_id) {
        if (Utility::checkQueryId($inventory_id)) {
            $detail = StockInventoryDetail::model()->findAll('inventory_id = :inventoryId', array('inventoryId' => $inventory_id));
            if (Utility::isNotEmpty($detail)) {
                foreach ($detail as $key => $row) {
                    $stock = Stock::model()->find('stock_in_id = :stockInId and goods_id = :goodsId', array('stockInId' => $row['stock_in_id'], 'goodsId' => $row['goods_id']));
                    if (!empty($stock->stock_id)) {
                        $stockArr = $stock->getAttributes('stock_id', 'goods_id', 'unit');
                        $stockArr['type'] = $row['type'] == ConstantMap::STOCK_INVENTORY_PROFIT ? StockLog::TYPE_IN : StockLog::TYPE_OUT;
                        $stockArr['method'] = StockLog::METHOD_STOCK_CHECK;
                        $stockArr['to_contract_id'] = $stock->contract_id;
                        $stockArr['quantity_balance'] = $stock->quantity_balance + $stock->quantity_frozen;
                        $stockArr['quantity'] = abs($row['quantity_diff']);
                        $stockArr['relation_id'] = $row['detail_id'];
                        StockLog::addStockLog($stockArr);
                    }
                }
            }
        }
    }
}