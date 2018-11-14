<?php

/**
 * Desc: 发货单明细服务
 * User: susiehuang
 * Date: 2017/10/10 0031
 * Time: 11:05
 */
class DeliveryOrderDetailService {
    /**
     * @desc 发货单明细参数校验
     * @param array $details
     * @param array $deliveryOrderParams
     * @return bool
     */
    public static function checkParamsValid($details, $deliveryOrderParams) {
        if (Utility::isNotEmpty($details)) {
            $goodsQuantity = array();
            $invalid = false;

            //不同的销售号不能一起选择
            $project_ids = array();
            for($i=0;$i<count($details);$i++){
                if($i > 0 && !isset($project_ids[ $details[$i]['contract_id']])){
                    return BusinessError::outputError(OilError::$DESTRIBUTE_GOODS_NOT_DIFF_CONTRACT_CODE);
                }
                $project_ids[ $details[$i]['contract_id']] =  $details[$i]['contract_id'];
            }

            foreach ($details as $row) {
                $requiredParams = array('contract_id', 'project_id', 'goods_id', 'quantity');
                //必填参数校验
                if (!Utility::checkRequiredParamsNoFilterInject($row, $requiredParams)) {
                    $invalid = true;
                    break;
                }

                $contractModel = Contract::model()->findByPk($row['contract_id']);
                if (empty($contractModel->contract_id)) {
                    return BusinessError::outputError(OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $row['contract_id']));
                }

                $projectModel = Project::model()->findByPk($row['project_id']);
                if (empty($projectModel->project_id)) {
                    return BusinessError::outputError(OilError::$PROJECT_NOT_EXIST, array('project_id' => $row['project_id']));
                }

                //配货明细校验
                $invalid2 = false;
                $distributedQuantity = 0;
                if (array_key_exists('stock_delivery_detail', $row) && Utility::isNotEmpty($row['stock_delivery_detail'])) {
                    foreach ($row['stock_delivery_detail'] as $r) {
                        $stockRequiredParams = array('project_id', 'contract_id', 'goods_id', 'stock_id', 'type', 'quantity');
                        if ($r['type'] > ConstantMap::DISTRIBUTED_NORMAL) {
                            array_push($stockRequiredParams, 'cross_detail_id');
                        }

                        /*if ($deliveryOrderParams['type'] == ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE) { //经仓
                            array_push($stockRequiredParams, 'store_id');
                        }*/

                        //必填参数校验
                        if (!Utility::checkRequiredParamsNoFilterInject($r, $stockRequiredParams)) {
                            $invalid2 = true;
                            break 2;
                        }

                        if ($r['type'] == ConstantMap::DISTRIBUTED_NORMAL) { //本项目配货

                            $stockModel = Stock::model()->findByPk($r['stock_id']);
                            $contractGoodsModel = ContractGoods::model()->find("contract_id=".$r['contract_id']." and goods_id=".$r['goods_id']);
                            $stockQuantity=0;
                            if($contractGoodsModel->unit == $stockModel->unit){
                                $stockQuantity = $stockModel->quantity_balance;
                            }else{//配货单位与库存单位不一致时，需要转换
                                $stockQuantity = $stockModel->sub->quantity;
                            }

                            if ($r['quantity'] > $stockQuantity) {
                                return BusinessError::outputError(
                                    OilError::$DISTRIBUTED_QUANTITY_GT_STOCK_QUANTITY_BALANCE,
                                    array(
                                        'code' => $stockModel->stockIn->code,
                                        'cross_code' => '本项目',
                                        'distributed_quantity' => $r['quantity'],
                                        'quantity_balance' => $stockModel->quantity_balance
                                    )
                                );
                            }
                        } else { //其他项目配货（借还货的）
                            $crossDetail = CrossDetail::model()->findByPk($r['cross_detail_id']);
                            if ($r['quantity'] > $crossDetail->quantity_balance) {
                                return BusinessError::outputError(
                                    OilError::$DISTRIBUTED_QUANTITY_GT_STOCK_QUANTITY_BALANCE,
                                    array(
                                        'code' => $crossDetail->stock->stockIn->code,
                                        'cross_code' => $crossDetail->crossOrder->cross_code,
                                        'distributed_quantity' => $r['quantity'],
                                        'quantity_balance' => $crossDetail->quantity_balance
                                    )
                                );
                            }
                        }

                        $distributedQuantity += $r['quantity'];
                    }

                    //配货总数量与发货数量不一致
                    if (bccomp($row['quantity'], $distributedQuantity, 4) != 0) {
                        return BusinessError::outputError(
                            OilError::$DELIVERY_QUANTITY_NOT_EQUAL_DISTRIBUTED_QUANTITY,
                            array(
                                'delivery_quantity' => $row['quantity'],
                                'distributed_quantity' => $distributedQuantity
                            )
                        );
                    }
                } else {
                    if ($deliveryOrderParams['type'] == ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE) {
                        return BusinessError::outputError(OilError::$DESTRIBUTE_GOODS_NOT_EXIST);
                    }
                }

                //直调同一个品名的发货数量不能超过入库单同品名的入库单数量
                if ($deliveryOrderParams['type'] == ConstantMap::STOCK_NOTICE_TYPE_DIRECT_TRANSFER) { //直调
                    if (array_key_exists($row['goods_id'], $goodsQuantity)) {
                        $goodsQuantity[$row['goods_id']] += $row['quantity'];
                    } else {
                        $goodsQuantity[$row['goods_id']] = $row['quantity'];
                    }
                }
            }

            if ($invalid) {
                return BusinessError::outputError(OilError::$DELIVERY_ORDER_DETAIL_PARAMS_ERROR);
            }

            if ($invalid2) {
                return BusinessError::outputError(OilError::$DISTRIBUTED_DETAIL_PARAMS_ERROR);
            }

            $stockInGoodsQuantity = array();
            if ($deliveryOrderParams['type'] == ConstantMap::STOCK_NOTICE_TYPE_DIRECT_TRANSFER) { //直调
                $stockInDetails = StockInDetail::model()->findAll('stock_in_id = :stockInId', array('stockInId' => $deliveryOrderParams['stock_in_id']));
                if (Utility::isNotEmpty($stockInDetails)) {
                    foreach ($stockInDetails as $v) {
                        $stockInGoodsQuantity[$v->goods_id] = $v->quantity;
                    }
                }
            }

            if (Utility::isNotEmpty($stockInGoodsQuantity)) {
                foreach ($stockInGoodsQuantity as $key => $val) {
                    if (Utility::isNotEmpty($goodsQuantity)) {
                        if (array_key_exists($key, $goodsQuantity) && $goodsQuantity[$key] > $val) {
                            $goods_name = GoodsService::getSpecialGoodsNames($key);

                            return BusinessError::outputError(
                                OilError::$DELIVERY_QUANTITY_GT_STOCK__IN_QUANTITY,
                                array(
                                    'goods_name' => $goods_name,
                                    'delivery_quantity' => $goodsQuantity[$key],
                                    'stock_in_quantity' => $val
                                )
                            );
                        }
                    }
                }
            }
        } else {
            return BusinessError::outputError(OilError::$DELIVERY_ORDER_DETAIL_NOT_ALLOW_NULL);
        }

        return true;
    }


    /**
     * @desc 保存发货单明细&配货明细
     * @param DeliveryOrder $deliveryOrder
     * @param array $deliveryOrderDetail
     * @return array|int
     */
    public static function saveDetails(DeliveryOrder $deliveryOrder,array $deliveryOrderDetail) {
        Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' pass transaction params are:' . json_encode($deliveryOrderDetail) . ' obj info is:' . json_encode($deliveryOrder));
        if (Utility::isEmpty($deliveryOrderDetail) || empty($deliveryOrder)) {
            BusinessException::throw_exception(OilError::$PARAMS_PASS_ERROR);
        }

        //新的发货单明细数组
        $newDeliveryOrderArray = self::saveDeliveryOrderDetail($deliveryOrder, $deliveryOrderDetail);
        if(Utility::isEmpty($newDeliveryOrderArray)){
            BusinessException::throw_exception(OilError::$DELIVERY_DETAIL_NOT_EXIST);
        }

        //保存配货明细
        self::saveStockDeliveryDetail($deliveryOrder, $newDeliveryOrderArray);
    }

    /**
     * 更新发货明细、配货明细为提交状态
     * @param DeliveryOrder $deliveryOrder
     */
    public static function updateDetailsIsSubmit(DeliveryOrder $deliveryOrder) {
        if (empty($deliveryOrder)) {
            BusinessException::throw_exception(OilError::$PARAMS_PASS_ERROR);
        }

        //TODO: 发货单明细的状态是否要变更？后期重构需要操作
        //DeliveryOrderDetail::model()->updateAll(array('status'=>DeliveryOrderDetail::STATUS_SUBMIT),'order_id = :orderId', array('orderId' => $deliveryOrder->order_id,'status'=>DeliveryOrderDetail::STATUS_SAVE));

        //所有配货明细状态更新为已经提交
        StockDeliveryDetail::model()->updateAll(
            array('status'=>StockDeliveryDetail::STATUS_SUBMIT),
            'order_id = :orderId AND status = :status',
            array('orderId' => $deliveryOrder->order_id,'status'=>StockDeliveryDetail::STATUS_SAVE)
        );
    }

    /**
     * 保存发货单明细
     * @param DeliveryOrder $deliveryOrder
     * @param array $deliveryOrderDetail
     * @return array
     */
    private static function saveDeliveryOrderDetail(DeliveryOrder $deliveryOrder,array $deliveryOrderDetail){
        //获取所有发货单明细
        $deliveryOrderDetailData = DeliveryOrderDetail::model()->findAll('order_id = :orderId', array('orderId' => $deliveryOrder->order_id));


        //旧的发货单明细数组
        $deliveryOrderDataArray = array();
        //旧的发货单明细id数组
        $oldDeliveryOrderDetailIds = array();

        if(Utility::isNotEmpty($deliveryOrderDetailData)){
            foreach ($deliveryOrderDetailData as & $row) {
                $oldDeliveryOrderDetailIds[$row["detail_id"]] = $row["detail_id"];
                //
                $deliveryOrderDataArray[$row["detail_id"]] = $row;
            }
        }

        //新的发货单明细数组
        $newDeliveryOrderDataArray = array();
        foreach ($deliveryOrderDetail as & $row) {
            //如果在旧的当中，更新就行
            if(isset($deliveryOrderDataArray[$row["detail_id"]])){
                $detail = $deliveryOrderDataArray[$row["detail_id"]];

                //同时标记为不需要删除
                unset($oldDeliveryOrderDetailIds[$row["detail_id"]]);
            }else{
                $detail = new DeliveryOrderDetail();
                $row['detail_id'] = null; //否则无法设置自增ID
            }

            $detail->order_id = $deliveryOrder->order_id;
            $detail->delivery_date = $deliveryOrder->delivery_date;
            // $detail->quantity_actual = !empty($row['quantity']) ? $row['quantity'] : 0;
            $detail->setAttributes($row, false);
            $detail->status = 0;
            $detail->save();

            $newDeliveryOrderDataArray[$detail->detail_id] = $detail->getAttributes();
            $newDeliveryOrderDataArray[$detail->detail_id]['stock_delivery_detail'] = isset($row['stock_delivery_detail']) ? $row['stock_delivery_detail'] : array();
        }

        //删除旧的明细
        if (count($oldDeliveryOrderDetailIds) > 0) {
            DeliveryOrderDetail::model()->deleteAll("detail_id IN (". implode(',',$oldDeliveryOrderDetailIds) .")");
        }

        unset($deliveryOrderDetailData);
        unset($deliveryOrderDataArray);

        return $newDeliveryOrderDataArray;
    }

    /**
     * 保存配货明细
     * @param DeliveryOrder $deliveryOrder 发货单对象
     * @param array $newDeliveryOrderDataArray 发货单明细
     */
    private static function saveStockDeliveryDetail(DeliveryOrder $deliveryOrder,array $newDeliveryOrderDataArray){
        //获取所有配货明细
        $stockDeliveryDetailData = StockDeliveryDetail::model()->findAll('order_id = :orderId', array('orderId' => $deliveryOrder->order_id));

        //需要删除的配货明细
        $stockDeliveryDetailIds = array();
        //存在的配货明细
        $stockDeliveryDetailArray = array();
        if(Utility::isNotEmpty($stockDeliveryDetailData)){
            foreach($stockDeliveryDetailData as & $deliveryDetailDatum){
                $stockDeliveryDetailArray[$deliveryDetailDatum['stock_detail_id']] = $deliveryDetailDatum;
                //
                $stockDeliveryDetailIds[$deliveryDetailDatum['stock_detail_id']] = $deliveryDetailDatum['stock_detail_id'];
            }
        }

        //是直调
        $isDirectTransfer = (ConstantMap::STOCK_NOTICE_TYPE_DIRECT_TRANSFER == $deliveryOrder->type);
        $isSubmit = ($deliveryOrder->status >= DeliveryOrder::STATUS_SUBMIT);

        $newStockDeliveryDetails = array();
        //根据新的发货单明细,生成新的配货明细
        foreach ($newDeliveryOrderDataArray as & $deliveryDetailDatum) {
            if (Utility::isEmpty($deliveryDetailDatum['stock_delivery_detail'])) {
                continue;
            }

            foreach ($deliveryDetailDatum['stock_delivery_detail'] as & $row) {
                //如果在旧的当中，更新就行
                if (isset($stockDeliveryDetailArray[$row["stock_detail_id"]])) {
                    $stockDeliveryDetail = $stockDeliveryDetailArray[$row["stock_detail_id"]];

                    //同时标记为不需要删除
                    unset($stockDeliveryDetailIds[$row["stock_detail_id"]]);
                } else {
                    $stockDeliveryDetail = new StockDeliveryDetail();

                    if ($isDirectTransfer) {
                        $stockDeliveryDetail->quantity = $deliveryDetailDatum['quantity'];
                        unset($row['quantity']);
                    }
                }

                $stockDeliveryDetail->order_id = $deliveryOrder->order_id;
                $stockDeliveryDetail->detail_id = $deliveryDetailDatum['detail_id'];
                $stockDeliveryDetail->quantity_actual = 0;
                $stockDeliveryDetail->setAttributes($row, false);
                $stockDeliveryDetail->status = $isSubmit ? StockDeliveryDetail::STATUS_SUBMIT : StockDeliveryDetail::STATUS_SAVE;
                $stockDeliveryDetail->save();

                //
                $newStockDeliveryDetails[] = $stockDeliveryDetail;
            }
        }

        if(Utility::isEmpty($newStockDeliveryDetails)){
            BusinessException::throw_exception(OilError::$DESTRIBUTE_GOODS_NOT_EXIST);
        }

        //删除旧的配货明细
        if (count($stockDeliveryDetailIds) > 0) {
            StockDeliveryDetail::model()->deleteAll("stock_detail_id IN (". implode(',',$stockDeliveryDetailIds) .")");
        }

        unset($stockDeliveryDetailData);
        unset($stockDeliveryDetailArray);
        unset($newStockDeliveryDetails);
    }
}
