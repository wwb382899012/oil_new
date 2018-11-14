<?php

/**
 * Desc: 配货明细服务
 * User: susiehuang
 * Date: 2017/10/10 0031
 * Time: 11:05
 */
class StockDeliveryDetailService {
    /**
     * @desc 根据入库单id、商品id初始化配货明细
     * @param int $stockInId
     * @param array $goodsInfo
     * @return array
     */
    public static function initStockDeliveryDetail($stockInId, $goodsInfo) {
        $res = array();
        if (Utility::isNotEmpty($goodsInfo) && Utility::checkQueryId($stockInId)) {
            $stockIn = StockIn::model()->findByPk($stockInId);
            if (empty($stockIn->stock_in_id)) {
                BusinessException::throw_exception(OilError::$STOCK_IN_NOT_EXIST, array('stock_in_id' => $stockInId));
            }
            $stock = Stock::model()->find('stock_in_id = :stockInId and goods_id = :goodsId', array('stockInId' => $stockInId, 'goodsId' => $goodsInfo['goods_id']));
            $res['project_id'] = $goodsInfo['project_id'];
            $res['contract_id'] = $goodsInfo['contract_id'];
            $res['goods_id'] = $goodsInfo['goods_id'];
            $res['store_id'] = $stock->store_id;
            $res['stock_id'] = !empty($stock->stock_id) ? $stock->stock_id : 0;
        }

        return $res;
    }

    /**
     * @desc 根据发货单ID冻结配货明细对应库存
     * @param int $orderId
     */
    public static function freezeStockByOrderId($orderId) {
        if (!Utility::checkQueryId($orderId)) {
            BusinessException::throw_exception(OilError::$PARAMS_PASS_ERROR);
        }

        $stockDeliveryOrder = StockDeliveryDetail::model()->findAll('order_id = :orderId', array('orderId' => $orderId));
        if (Utility::isEmpty($stockDeliveryOrder)) {
            BusinessException::throw_exception(OilError::$DESTRIBUTE_GOODS_NOT_EXIST);
        }

        foreach ($stockDeliveryOrder as & $row) {
            if ($row->type == ConstantMap::DISTRIBUTED_NORMAL) { //本项目，冻结库存
                $res = StockService::freeze($row->stock_id, $row->quantity);
                if (!$res) {
                    BusinessException::throw_exception(OilError::$STOCK_FREEZE_ERROR, array('stock_id' => $row->stock_id, 'quantity' => $row->quantity));
                }
            } else { //其他项目，冻结借还货库存
                $res = CrossStockService::freeze($row->cross_detail_id, $row->quantity);
                if (!$res) {
                    BusinessException::throw_exception(OilError::$CORSS_STOCK_FREEZE_ERROR, array('cross_detail_id' => $row->cross_detail_id, 'quantity' => $row->quantity));
                }
            }
        }
    }

    /**
     * @desc 根据发货单ID解冻配货明细对应库存
     * @param int $orderId
     */
    public static function unfreezeStockByOrderId($orderId) {
        if (!Utility::checkQueryId($orderId)) {
            BusinessException::throw_exception(OilError::$PARAMS_PASS_ERROR);
        }

        $stockDeliveryOrder = StockDeliveryDetail::model()->findAll('order_id = :orderId', array('orderId' => $orderId));
        if (Utility::isEmpty($stockDeliveryOrder)) {
            BusinessException::throw_exception(OilError::$DESTRIBUTE_GOODS_NOT_EXIST);
        }

        foreach ($stockDeliveryOrder as $key => $row) {
            if ($row->type == ConstantMap::DISTRIBUTED_NORMAL) { //本项目，解冻库存
                $res = StockService::unFreeze($row->stock_id, $row->quantity);
                if (!$res) {
                    BusinessException::throw_exception(OilError::$STOCK_UNFREEZE_ERROR, array('stock_id' => $row->stock_id, 'quantity' => $row->quantity));
                }
            } else { //其他项目，解冻借还货库存
                $res = CrossStockService::unfreeze($row->cross_detail_id, $row->quantity);
                if (!$res) {
                    BusinessException::throw_exception(OilError::$CORSS_STOCK_UNFREEZE_ERROR, array('cross_detail_id' => $row->cross_detail_id, 'quantity' => $row->quantity));
                }
            }
        }
    }

    /**
     * @desc 获取发货单明细对应的已配货数量
     * @param int $contract_id
     * @param int $goods_id
     * @return int
     */
    public static function getDistributedQuantity($contract_id, $goods_id) {
        if (Utility::checkQueryId($contract_id) && Utility::checkQueryId($goods_id)) {
            $sql = 'select sum(quantity) as distributed_quantity from t_stock_delivery_detail where contract_id = ' . $contract_id . ' and goods_id = ' . $goods_id.' and status='.StockDeliveryDetail::STATUS_SUBMIT;
            $data = Utility::query($sql);
            if (Utility::isNotEmpty($data)) {
                return $data[0]['distributed_quantity'];
            }
        }

        return 0;
    }

    /**
     * @desc 获取发货单明细对应的已出库数量
     * @param int $contract_id
     * @param int $goods_id
     * @return int
     */
    public static function getStockOutQuantity($contract_id, $goods_id) {
        if (Utility::checkQueryId($contract_id) && Utility::checkQueryId($goods_id)) {
            $sql = 'select sum(quantity) as stock_out_quantity from t_stock_out_detail where contract_id = ' . $contract_id . ' and goods_id = ' . $goods_id;
            $data = Utility::query($sql);
            if (Utility::isNotEmpty($data)) {
                return $data[0]['stock_out_quantity'];
            }
        }

        return 0;
    }
}
