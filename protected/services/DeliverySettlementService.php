<?php

/**
 * Desc: 出库单
 */
class DeliverySettlementService {
	
    public static function detailsFormat($outOrder) {
		$storeGoods = array();
		$map = Map::$v;
		foreach ($outOrder as $order) {
            //作废的出库单不参与结算
		    if(StockOutService::isInvalid($order['status'])){
                continue;
            }

			foreach($order['details'] as $detail) {
				if(isset($storeGoods[$detail['contract']['contract_id'] . '-' . $detail['goods_id']])) {
					$goods = $storeGoods[$detail['contract']['contract_id'] . '-' . $detail['goods_id']];
					$goods['quantity'] += $detail['quantity'];
					$goods['quantity_settle'] = $goods['quantity'];
					$goods['quantity_sub'] += $detail['quantity']*$detail['stock']['unit_rate'];
					$goods['quantity_sub_settle'] += $detail['quantity']*$detail['stock']['unit_rate'];
					$goods['quantity_str'] = $goods['quantity'] . $map['goods_unit'][$goods['unit']]['name'];
					$goods['quantity_settle_sub'] = $goods['quantity_sub'];
					$storeGoods[$detail['contract']['contract_id'] . '-' . $detail['goods_id']] = $goods;
				} else {
					$goods = array();
					$goods['quantity'] = $detail['quantity'];

                    $goods['order_id'] = $detail['order_id'];
                    $goods['project_id'] = $detail['project_id'];

					$goods['detail_id'] = $detail['detail_id'];
					$goods['store_id'] = $detail['store']['store_id'];
					$goods['store_name'] = $detail['store']['name'];

					// 来源于合同的数据
					$goods['contract_id'] = $detail['contract']['contract_id'];
					$goods['contract_code'] = $detail['contract']['contract_code'];
					$goods['amount'] = 0;
					$goods['currency'] = $detail['contract']['currency'];

					$goods['goods_name'] = $detail['goods']['name'];
					$goods['goods_id'] = $detail['goods']['goods_id'];
					$goods['stock_id'] = $detail['stock']['stock_id'];
					$goods['stock_in_code'] = $detail['stock']['stockIn']['code'];

					// 换算比例和数量都在这里填写
					$goods['unit'] = $detail['stock']['unit'];
					$goods['quantity_str'] = $detail['quantity'] . $map['goods_unit'][$goods['unit']]['name'];
					$goods['units_in_use'][] = $detail['stock']['unit'];
					if(!empty($detail['stock']['sub']->attributes)) {
						$goods['unit_rate'] = $detail['stock']['unit_rate'];
						$goods['unit_sub'] = $detail['stock']['sub']['unit'];
						$goods['quantity_sub'] = $goods['quantity'] * $goods['unit_rate'];
						$goods['units_in_use'][] = $detail['stock']['sub']['unit'];
					} else {
						$goods['unit_rate'] = 1;
						$goods['unit_sub'] = $detail['stock']['unit'];
						$goods['quantity_sub'] = $goods['quantity'];
					}

					// 初始化默认值
					$goods['quantity_settle'] = $detail['quantity'];
					$goods['quantity_settle_sub'] = $detail['quantity']*$goods['unit_rate'];
					$goods['unit_rate_settle'] = $goods['unit_rate'];
					$goods['unit_settle'] = $goods['unit'];
					$goods['quantity_loss'] = 0;
					$goods['quantity_loss_sub'] = 0;

					$storeGoods[$goods['contract_id'] . '-' . $goods['goods_id']] = $goods;
				}
			}
		}
		return $storeGoods;
	}

    public static function checkParamsValid($goodsItems) {
        if (Utility::isNotEmpty($goodsItems)) {
            $goodsStoreArr = array();
            $invalid = false;
            foreach ($goodsItems as $key => $row) {
                $requiredParams = array('contract_id', 'quantity', 'quantity_sub', 'quantity_settle', 'quantity_settle_sub', 'price', 'price_sub', 'unit', 'unit_sub', 'amount');
                //必填参数校验
                if (!Utility::checkRequiredParamsNoFilterInject($row, $requiredParams)) {
                    $invalid = true;
                    break;
                }
            }

            if ($invalid) {
                return BusinessError::outputError(OilError::$TRANSACTION_REQUIRED_PARAMS_CHECK_ERROR);
            }
        } else {
            return BusinessError::outputError(OilError::$PROJECT_LAUNCH_NOT_TRANSACTION);
        }

        return true;
    }


    public static function saveGoodsInfos($outOrders, $goodInfos) {
        $settlements = array();
        foreach ($outOrders as $outOrder) {
            foreach($outOrder->details as $detail) {
                $settlement = new DeliverySettlementDetail();
                $settlement->order_id = $detail->order_id;
                $settlement->contract_id = $detail->contract_id;
                $settlement->project_id = $detail->project_id;
                $settlement->goods_id = $detail->goods_id;
                $settlement->detail_id = $detail->detail_id;
                $settlement->status = DeliverySettlementDetail::STATUS_NEW;
                $settlements[$settlement->contract_id . '-' . $settlement->goods_id] = $settlement;
            }
        }

        foreach ($goodInfos as $infos) {
            foreach ($settlements as $settlement) {

                if($settlement->goods_id == $infos['goods_id'] && $settlement->contract_id == $infos['contract_id']) {

                    $contract = Contract::model()->findByPk($infos['contract_id']);
                    $exchange_rate = $contract->exchange_rate;
                    $exchange_rate = empty($exchange_rate) ? 1 : $exchange_rate;

                    $settlement->unit_rate = $infos['unit_rate_settle'];
                    $settlement->price = $infos['price'];
                    $settlement->currency = $infos['currency'];
                    $settlement->unit = $infos['unit_settle'];
                    // $settlement->unit_settle = $infos['unit_settle'];
                    // $settlement->quantity = $infos['quantity'];
                    // $settlement->quantity_actual = $infos['quantity_actual'];             
                    $settlement->quantity_settle = $infos['quantity_settle'];
                    $settlement->quantity = $infos['quantity'];

                    $settlement->quantity_loss = $infos['quantity_loss'];
                    $settlement->amount = $infos['amount'];
                    $settlement->amount_cny = $infos['amount'] * $exchange_rate;
                    $settlement->save();

                    $sub = DeliverySettlementDetailSub::model()->findByPk($settlement->settle_id);
                    $sub = empty($sub->attributes)?new DeliverySettlementDetailSub():$sub;
                    $sub->settle_id = empty($sub->settle_id)?$settlement->settle_id:$sub->settle_id;
                    $sub->price = $infos['price_sub'];
                    $sub->unit = $infos['unit_sub'];
                    $sub->unit_rate = $infos['unit_rate_settle'];
                    $sub->amount = $infos['amount'];
                    
                    $sub->quantity_settle = $infos['quantity_settle_sub'];
                    $sub->quantity = $infos['quantity_sub'];
                    $sub->quantity_loss = $infos['quantity_loss_sub'];
                    $sub->save();
                    if($infos['unit_rate'] != 1) {
                        $sub = DeliverySettlementDetailSub::model()->findByPk($settlement->settle_id);
                        $sub = empty($sub->attributes)?new DeliverySettlementDetailSub():$sub;
                        $sub->settle_id = empty($sub->settle_id)?$settlement->settle_id:$sub->settle_id;
                        $sub->price = $infos['price_sub'];
                        $sub->unit = $infos['unit_sub'];
                        $sub->unit_rate = $infos['unit_rate_settle'];
                        $sub->amount = $infos['amount'];
                        
                        $sub->quantity_settle = $infos['quantity_settle_sub'];
                        $sub->quantity = $infos['quantity_sub'];
                        $sub->quantity_loss = $infos['quantity_loss_sub'];
                        $sub->save();
                    }
                }
            }
        }
    }


    public static function settlementDetailFormat($settlementDetail)
    {
        $data = array();
        if(Utility::isEmpty($settlementDetail))
            return $data;
        $goodsItem = array();
        foreach ($settlementDetail as $detail) {
            $goodsItems['detail_id'] = $detail->detail_id;
            $goodsItems['project_id'] = $detail->project_id;
            $goodsItems['order_id'] = $detail->order_id;
            $goodsItems['contract_id'] = $detail->contract_id;
            $goodsItems['contract_code'] = $detail->contract->contract_code;
            $goodsItems['goods_id'] = $detail->goods_id;
            $goodsItems['goods_name'] = $detail->goods->name;
            $goodsItems['quantity'] = $detail->quantity;
            $goodsItems['unit'] = $detail->unit;
            $goodsItems['quantity_sub'] = $detail->sub->quantity;
            $goodsItems['unit_sub'] = $detail->sub->unit;
            $goodsItems['unit_rate'] = $detail->unit_rate;
            $goodsItems['unit_rate_settle'] = $detail->unit_rate;
            $goodsItems['price'] = $detail->price;
            $goodsItems['price_sub'] = $detail->price_sub;
            $goodsItems['unit_settle'] = $detail->unit;
            $goodsItems['quantity_settle'] = $detail->quantity_settle;
            $goodsItems['quantity_settle_sub'] = $detail->sub->quantity_settle;
            $goodsItems['quantity_loss'] = $detail->quantity_loss;
            $goodsItems['quantity_loss_sub'] = $detail->sub->quantity_loss;
            $goodsItems['currency'] = $detail->currency;
            $goodsItems['amount'] = $detail->amount;
            // $goodsItems['quantity_str'] = $detail->quantity
            $goodsItems['units_in_use'][] = $detail->unit;
            if($detail->sub->unit != $detail->unit)
                $goodsItems['units_in_use'][] = $detail->sub->unit;

            $goodsItems['settle_id'] = $detail->settle_id;

            $data[$goodsItems['contract_id'] . '-' . $goodsItems['goods_id']] = $goodsItems;
        }

        return $data;
    }


    public static function saveSettlementDetail($goodInfos) {
        if(Utility::isEmpty($goodInfos))
            return array();

        foreach ($goodInfos as $infos) {
            if(empty($infos['settle_id'])){
                $settlement = new DeliverySettlementDetail();
                $settlement->order_id = $infos['order_id'];
                $settlement->contract_id = $infos['contract_id'];
                $settlement->project_id = $infos['project_id'];
                $settlement->goods_id = $infos['goods_id'];
                $settlement->detail_id = $infos['detail_id'];
                $settlement->status = DeliverySettlementDetail::STATUS_NEW;
                $settlement->create_time = new CDbExpression('now()');
                $settlement->create_user_id = Utility::getNowUserId();
            }else{
                $settlement = DeliverySettlementDetail::model()->findByPk($infos['settle_id']);
                if(empty($settlement->settle_id))
                    continue;
            }

            $contract = Contract::model()->findByPk($infos['contract_id']);
            $exchange_rate = $contract->exchange_rate;
            $exchange_rate = empty($exchange_rate) ? 1 : $exchange_rate;


            $settlement->unit_rate = $infos['unit_rate_settle'];
            $settlement->price = $infos['price'];
            $settlement->currency = $infos['currency'];
            $settlement->unit = $infos['unit_settle'];          
            $settlement->quantity_settle = $infos['quantity_settle'];
            $settlement->quantity = $infos['quantity'];

            $settlement->quantity_loss = $infos['quantity_loss'];
            $settlement->amount = $infos['amount'];
            $settlement->amount_cny = $infos['amount'] * $exchange_rate;
            $settlement->save();

            $sub = DeliverySettlementDetailSub::model()->findByPk($settlement->settle_id);
            if(empty($sub->settle_id)){
                $sub = new DeliverySettlementDetailSub();
                $sub->settle_id = $settlement->settle_id;
            }

            $sub->price = $infos['price_sub'];
            $sub->unit = $infos['unit_sub'];
            $sub->unit_rate = $infos['unit_rate_settle'];
            $sub->amount = $infos['amount'];
            
            $sub->quantity_settle = $infos['quantity_settle_sub'];
            $sub->quantity = $infos['quantity_sub'];
            $sub->quantity_loss = $infos['quantity_loss_sub'];
            $sub->update_time = new CDbExpression('now()');
            $sub->update_user_id = Utility::getNowUserId();
            $sub->save();
        }
    }

    /**
     * @desc 历史数据保存结算明细
     * @param $deliveryOrderId int
     * @param $history_params
     * @throws
     */
    public static function saveDeliverySettlementForHistoryData($deliveryOrderId, $history_params) {
        if (!Utility::checkQueryId($deliveryOrderId)) {
            throw new Exception('参数传入错误！');
        }
        $deliveryOrder = DeliveryOrder::model()->findByPk($deliveryOrderId);
        if (empty($deliveryOrder)) {
            Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' 发货单：' . $deliveryOrderId . '不存在！', CLogger::LEVEL_ERROR, 'oil.import.log');
            throw new Exception('发货单：' . $deliveryOrderId . '不存在！');
        }

        if (Utility::isNotEmpty($deliveryOrder->details)) {
            foreach ($deliveryOrder->details as $detail) {
                if ($deliveryOrder->status == DeliveryOrder::STATUS_SETTLE_PASS) {
                    $settle = DeliverySettlementDetail::model()->find('order_id=' . $deliveryOrderId . ' and detail_id=' . $detail->detail_id);
                    if (empty($settle)) {
                        $settle = new DeliverySettlementDetail();
                        $amount = $history_params['settle_quantity'] * $history_params['settle_price'] * 100;
                        $settle->order_id = $deliveryOrderId;
                        $settle->project_id = $detail->project_id;
                        $settle->contract_id = $detail->contract_id;
                        $settle->detail_id = $detail->detail_id;
                        $settle->settle_date = $deliveryOrder->delivery_date;
                        $settle->goods_id = $detail->goods_id;
                        $settle->price = $history_params['settle_price'] * 100;
                        $settle->unit = 2;
                        $settle->quantity = $detail->quantity;
                        $settle->quantity_settle = $history_params['settle_quantity'];
                        $settle->quantity_loss = $settle->quantity - $settle->quantity_settle;
                        $settle->amount = $amount;
                        $settle->amount_cny = $amount;
                        $settle->unit_rate = 1;
                        $settle->unit_rate_settle = 1;
                        $settle->currency = 1;
                        $settle->create_user_id = - 1;
                        $settle->create_time = Utility::getDateTime();
                        $settle->update_user_id = - 1;
                    }
                    $settle->update_time = Utility::getDateTime();
                    $resSettle = $settle->save();
                    if ($resSettle !== true) {
                        throw new Exception('发货单id:' . $deliveryOrderId . '，发货单明细id:' . $detail->detail_id . ' 保存发货单结算明细失败 error, result is:' . $resSettle);
                    }
                }
            }
        }
    }
}