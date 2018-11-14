<?php

/**
 * Desc: 入库通知单结算
 */
class StockBatchSettlementService {

    /**
     * @desc 获取附件信息
     * @param $id
     * @param $type
     * @return array
     */
    public static function getAttachment($id, $type = '') {
        if (empty($id)) {
            return array();
        }
        if (!empty($type)) {
            $type = ' and type=' . $type;
        }

        $sql = "select * from t_stock_batch_settlement_attachment where base_id=" . $id . " and status=1" . $type . " order by type asc";
        $data = Utility::query($sql);
        $attachments = array();
        foreach ($data as $v) {
            $attachments[$v["type"]][] = $v;
        }
        return $attachments;
    }

    /**
     *   计算基本数据
     *
     */
    /*public static function setDefaultValue(&$stockNoticeGoods, $stockInList, $buyLocks, $contractGoods, $contract) {
        $displayValues = array();
        // 10 - 16 入库通知单又不能重复
        foreach ($stockNoticeGoods as &$goods) {
            $thisValue = empty($displayValues[$goods['goods_id']])?StockBatchSettlementService::getDefaultValue():$displayValues[$goods['goods_id']];
            $thisValue['goods_name'] = $goods['goods']['name'];
            $thisValue['goods_id'] = $goods['goods_id'];
            $thisValue['contract_id'] = $goods['contract_id'];
            $thisValue['batch_id'] = $goods['batch_id'];
            $thisValue['unit_rate'] = $goods['unit_rate'];
            $thisValue['quantity'] += $goods['quantity'];
            $thisValue['display_unit'] = $goods['unit'];
            $thisValue['display_currency'] = $goods['currency'];
            $thisValue['amount_currency'] = $goods['currency'];
            // 处理sub信息
            if(!empty($goods['sub'])) {
                $thisValue['quantity_sub'] += $goods['sub']['quantity'];
                $thisValue['display_unit_sub'] = $goods['sub']['unit'];
            } else {
                $thisValue['quantity_sub'] += $goods['quantity'];
                $thisValue['display_unit_sub'] = $goods['unit'];
            }
            // 计算数量
            foreach($stockInList as $stockIn) {
                foreach ($stockIn['details'] as $stockInGoods) {
                    if($thisValue['goods_id'] == $stockInGoods['goods_id']) {
                        // 这里处理一下, 数字和
                        if($thisValue['display_unit'] == $stockInGoods['unit']) {
                            $thisValue['display_done_unit'] = $stockInGoods['unit'];
                            $thisValue['quantity_done'] += $stockInGoods['quantity'];
                            $thisValue['display_done_unit_sub'] = isset($stockInGoods['sub']['unit'])?$stockInGoods['sub']['unit']:$stockInGoods['unit'];
                            $thisValue['quantity_done_sub'] += isset($stockInGoods['sub']['quantity'])?$stockInGoods['sub']['quantity']:$stockInGoods['quantity'];
                        } else {
                            $thisValue['display_done_unit'] = $stockInGoods['sub']['unit'];
                            $thisValue['quantity_done'] += $stockInGoods['quantity'] / $stockInGoods['unit_rate'];
                            $thisValue['display_done_unit_sub'] = isset($stockInGoods['sub']['unit'])?$stockInGoods['sub']['unit']:$stockInGoods['unit'];
                            $thisValue['quantity_done_sub'] += isset($stockInGoods['sub']['quantity'])?$stockInGoods['sub']['quantity']:$stockInGoods['quantity'] / $stockInGoods['unit_rate'];
                        }
                    }
                }
            }
            $thisValue['amount'] = number_format($thisValue['amount']/ 100, 2);
            if($contractGoods) {
                foreach ($contractGoods as $contractGood) {
                    if($contractGood['goods_id'] == $thisValue['goods_id']) {
                        $thisValue['amount_currency'] = $contractGood['currency'];
                        $thisValue['display_currency'] = $contractGood['currency'];
                    }
                }
            }

            // 计算价格
            foreach($buyLocks as $buyLock) {
                if(isset($buyLock['lockPriceDetail']))
                {
                    foreach ($buyLock['lockPriceDetail'] as $lockDetail) {
                        if($thisValue['goods_id'] == $lockDetail['goods_id']) {
                            // 这里处理一下, 数字和
                            $thisValue['amount'] += $lockDetail['amount']*$lockDetail['quantity'];

                            // 如果有锁价,用锁价的币种
                            $thisValue['display_currency'] = $lockDetail['currency'];
                            $thisValue['amount_currency'] = $lockDetail['currency'];
                        }
                    }
                }
            }
            $thisValue['price'] = ($thisValue['quantity']==0)?0:$thisValue['amount'] / $thisValue['quantity'];
            $thisValue['unit_in_use'] = array_unique(array($thisValue['display_unit'], $thisValue['display_unit_sub']));
            $goods['quantity_done_sub'] += round($goods['quantity_done'] * $goods['unit_rate'], 2);
            foreach ($thisValue as $key => $value) {
                if(is_numeric($value)) {
                    $thisValue[$key] = round($value, 2);
                }
            }
            $thisValue['price'] = $thisValue['price'];
            $thisValue['amount'] = $thisValue['amount'];
            $displayValues[$thisValue['goods_id']] = $thisValue;
        }
        return $displayValues;
    }*/

    /**
     * @desc 格式化结算明细
     * @param array $settles
     * @param array $stockNoticeGoods
     * @param array $stockInList
     * @param array $buyLocks
     * @return array
     */
    public static function formatStockBatchSettlement($settles, $stockNoticeGoods, $stockInList, $buyLocks) {
        $res = array();
        if (Utility::isNotEmpty($stockNoticeGoods)) {
            foreach ($stockNoticeGoods as $key => $row) {
                if(Utility::isNotEmpty($settles)) {
                    foreach ($settles as $v) {
                        if($v['detail_id'] == $row['detail_id']) {
                            $res[$key] = $v->getAttributes(true, array_merge(Utility::getCommonIgnoreAttributes(), array('amount_cny', 'amount_sub', 'quantity_loss_sub', 'quantity_sub', 'unit_sub')));
                            $res[$key]['price_sub'] = !empty($v->sub->price) ? $v->sub->price : $v->price;
                            $res[$key]['quantity_sub'] = !empty($v->sub->quantity) ? $v->sub->quantity : $v->quantity;
                            $res[$key]['unit_sub'] = !empty($v->sub->unit) ? $v->sub->unit : $v->unit;
                            $res[$key]['quantity_loss_sub'] = $v->sub->quantity_loss;
                        }
                    }
                } else {
                    $res[$key]['batch_id'] = $row['batch_id'];
                    $res[$key]['project_id'] = $row['project_id'];
                    $res[$key]['contract_id'] = $row['contract_id'];
                    $res[$key]['detail_id'] = $row['detail_id'];
                    $res[$key]['goods_id'] = $row['goods_id'];
                    $res[$key]['unit_rate'] = $row['unit_rate'];
                    $res[$key]['unit'] = $row['unit'];
                    $res[$key]['unit_sub'] = !empty($row['sub']) ? $row['sub']['unit'] : $row['unit'];
                    if(Utility::isNotEmpty($buyLocks)) {
                        foreach($buyLocks as $buyLock) {
                            if(Utility::isNotEmpty($buyLock['lockPriceDetail'])){
                                foreach ($buyLock['lockPriceDetail'] as $lockDetail) {
                                    if($row['goods_id'] == $lockDetail['goods_id']) {
                                        $res[$key]['amount'] += $lockDetail['amount'] * $lockDetail['quantity'];
                                        // 如果有锁价,用锁价的币种
                                        $res[$key]['currency'] = $lockDetail['currency'];
                                    }
                                }
                            }
                        }
                        $res[$key]['price'] = ($res[$key]['batch_quantity'] == 0 || empty($res[$key]['amount'])) ? 0 : $res[$key]['amount'] / $res[$key]['batch_quantity'];
                    }
                }
                $contractGoods = ContractGoods::model()->find('contract_id = :contractId and goods_id = :goodsId', array('contractId' => $row['contract_id'], 'goodsId' => $row['goods_id']));
                if(empty($res[$key]['currency'])) {
                    $res[$key]['currency'] = $contractGoods->currency;
                }
                //入库通知单信息
                $res[$key]['exchange_rate'] = $contractGoods->contract->exchange_rate;
                $res[$key]['goods_name'] = $row['goods']['name'];
                $res[$key]['batch_quantity'] = $row['quantity'];
                $res[$key]['batch_quantity_sub'] = !empty($row['sub']) ? $row['sub']['quantity'] : $row['quantity'];
                $res[$key]['batch_unit_desc'] = Map::$v['goods_unit'][$row['unit']]['name'];
                $res[$key]['batch_unit_sub_desc'] = Map::$v['goods_unit'][!empty($row['sub']) ? $row['sub']['unit'] : $row['unit']]['name'];

                //入库单信息
                $res[$key]['stock_in_unit_desc'] = Map::$v['goods_unit'][$res[$key]['unit']]['name'];
                $res[$key]['stock_in_quantity'] = 0;
                $res[$key]['stock_in_unit_sub_desc'] = Map::$v['goods_unit'][$res[$key]['unit_sub']]['name'];
                $res[$key]['stock_in_quantity_sub'] = 0;
                if(Utility::isNotEmpty($stockInList)) {
                    foreach($stockInList as $stockIn) {
                        //作废的入库单不参与结算
                        if(StockInService::isInvalid($stockIn['status'])){
                            continue;
                        }

                        if(Utility::isNotEmpty($stockIn['details'])) {
                            foreach ($stockIn['details'] as $stockInGoods) {
                                if($row['goods_id'] == $stockInGoods['goods_id']) {
                                    if($res[$key]['unit'] == $stockInGoods['unit']) {
                                        $res[$key]['stock_in_unit_desc'] = Map::$v['goods_unit'][$stockInGoods['unit']]['name'];
                                        $res[$key]['stock_in_quantity'] += $stockInGoods['quantity'];
                                        if(!empty($res[$key]['unit_sub']) && $res[$key]['unit_sub'] == $stockInGoods['sub']['unit']) {
                                            $res[$key]['stock_in_unit_sub_desc'] = Map::$v['goods_unit'][isset($stockInGoods['sub']['unit'])?$stockInGoods['sub']['unit']:$stockInGoods['unit']]['name'];
                                            $res[$key]['stock_in_quantity_sub'] += isset($stockInGoods['sub']['quantity'])?$stockInGoods['sub']['quantity']:$stockInGoods['quantity'];
                                        }
                                    } elseif(!empty($stockInGoods['sub']['unit']) && $res[$key]['unit'] == $stockInGoods['sub']['unit']) {
                                        $res[$key]['stock_in_unit_desc'] = Map::$v['goods_unit'][$stockInGoods['sub']['unit']]['name'];
                                        $res[$key]['stock_in_quantity'] += $stockInGoods['sub']['quantity'];
                                        if(!empty($res[$key]['unit_sub']) && $res[$key]['unit_sub'] == $stockInGoods['unit']) {
                                            $res[$key]['stock_in_unit_sub_desc'] = Map::$v['goods_unit'][isset($stockInGoods['unit'])?$stockInGoods['unit']:$stockInGoods['sub']['unit']]['name'];
                                            $res[$key]['stock_in_quantity_sub'] += isset($stockInGoods['quantity'])?$stockInGoods['quantity']:$stockInGoods['sub']['quantity'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $res[$key]['units'] = array();
                if (!empty($row['unit'])) {
                    $res[$key]['units'][$row['unit']] = Map::$v['goods_unit'][$row['unit']];
                }

                if (!empty($row['sub']) && !empty($row['sub']['unit']) && $row['unit'] != $row['sub']['unit']) {
                    $res[$key]['units'][$row['sub']['unit']] = Map::$v['goods_unit'][$row['sub']['unit']];
                }
                $res[$key]['singleUnit'] = $row['sub'] == $row['sub']['unit'];
                $res[$key]['unit_desc'] = Map::$v['goods_unit'][$res[$key]['unit']]['name'];
                $res[$key]['unit_sub_desc'] = Map::$v['goods_unit'][$res[$key]['unit_sub']]['name'];
            }
        }

        return $res;
    }

    /**
     * @desc 保存结算明细
     * @param array $details
     * @param int $status
     */
    public static function saveStcokBatchSettlements($details, $status = 0) {
        if(Utility::isNotEmpty($details)) {
            foreach ($details as $key => $row) {
                $stockBatchSettlement = null;
                if(!empty($row['settle_id'])) {
                    $stockBatchSettlement = StockBatchSettlement::model()->findByPk($row['settle_id']);
                }
                if(empty($stockBatchSettlement)) {
                    $stockBatchSettlement = new StockBatchSettlement();
                }

                unset($row['settle_id']);
                $stockBatchSettlement->status = $status;
                $stockBatchSettlement->setAttributes($row, false);
                $stockBatchSettlement->unit_settle = $row['unit'];
                $stockBatchSettlement->save();
            }
        }
    }

    /*private static function getDefaultValue() {
        return array('quantity'=>0,
         'unit'=>0,
         'quantity_sub'=>0,
         'display_unit_sub'=>0,
         'quantity_done'=>0,
         'display_done_unit'=>0,
         'quantity_done_sub'=>0,
         'display_done_unit_sub'=>0,
         'display_done_unit_sub'=>0,
         'amount'=>0,
         'amount_currency'=>0,
         );
    }*/


    public static function checkParamsValid($goodsItems) {
        if (Utility::isNotEmpty($goodsItems)) {
            $invalid = false;
            foreach ($goodsItems as $key => $row) {
                $requiredParams = array('batch_id', 'project_id', 'contract_id', 'goods_id', 'detail_id', 'unit', 'unit_sub');
                $existParams = array('quantity', 'quantity_sub', 'amount', 'price', 'price_sub', 'quantity_loss', 'quantity_loss_sub', 'settle_id');
                //必填参数校验
                if (!Utility::checkRequiredParamsNoFilterInject($row, $requiredParams) || !Utility::checkMustExistParams($row, $existParams)) {
                    $invalid = true;
                    break;
                }

                $stockBatch = StockNotice::model()->findByPk($row['batch_id']);
                if (empty($stockBatch)) {
                    return BusinessError::outputError(OilError::$STOCK_BATCH_NOT_EXIST, array('batch_id' => $row['batch_id']));
                }

                //入库单
                $stockIns = StockIn::model()->findAllToArray('batch_id=:batch_id',array("batch_id"=>$row['batch_id']));
                if (Utility::isNotEmpty($stockIns)) {
                    foreach ($stockIns as $stockIn) {
                        if(!StockInService::isCanSettlement($stockIn['status'])) {
                            return BusinessError::outputError(OilError::$STOCK_BATCH_SETTLE_NOT_ALLOW);
                        }
                    }
                }
            }

            if ($invalid) {
                return BusinessError::outputError(OilError::$STOCK_BATCH_SETTLE_PARAMS_ERROR);
            }
        } else {
            return BusinessError::outputError(OilError::$STOCK_BATCH_SETTLE_NOT_ALLOW_EMPTY);
        }

        return true;
    }

    public static function saveGoodsInfos($stockNotice, $goodInfos, $status = 0) {
        foreach($stockNotice->details as $detail) {
            $settlement = StockBatchSettlement::model()->with('sub')->find(array('condition'=>'batch_id=:batch_id and goods_id=:goods_id', 'params'=>array('batch_id'=>$stockNotice->batch_id, 'goods_id'=>$detail->goods_id)));
            if(empty($settlement)) {
                $settlement = new StockBatchSettlement();
                $settlement->batch_id = $stockNotice->batch_id;
                $settlement->contract_id = $stockNotice->contract_id;
                $settlement->project_id = $stockNotice->project_id;
                $settlement->goods_id = $detail->goods_id;
                $settlement->detail_id = $detail->detail_id;
                $settlement->status = StockBatchSettlement::STATUS_NEW;
            }
            foreach ($goodInfos as $infos) {
                if($settlement->goods_id == $infos['goods_id']) {
                    $settlement->unit_rate = $infos['unit_rate'];
                    $settlement->price = $infos['price'];
                    $settlement->unit = $infos['unit'];
                    $settlement->unit_settle = $infos['unit_settle'];
                    // $settlement->quantity = $infos['quantity'];
                    // $settlement->quantity_actual = $infos['quantity_actual'];             
                    $settlement->quantity = $infos['quantity_actual'];

                    $settlement->quantity_loss = $infos['quantity_loss'];
                    $settlement->amount = $infos['amount'];
                    $settlement->remark = $infos['remark'];
                    $settlement->status = $status;
                    $settlement->save();
                    /*if($infos['unit_rate'] != 1) {
                        $sub = empty($settlement->sub->attributes)?new StockBatchSettlementSub():$settlement->sub;
                        $sub->settle_id = $settlement->settle_id;
                        $sub->price = $infos['price_sub'];
                        $sub->unit = $infos['unit_sub'];
                        $sub->quantity = $infos['quantity_actual_sub'];
                        $sub->unit_rate = $infos['unit_rate'];
                        $sub->amount = $infos['amount'];
                        $sub->quantity_loss = $infos['quantity_loss_sub'];
                        $sub->save();
                    }*/
                }
            }
        }
    }

    /**
     * @desc 检查入库通知单结算是否可修改
     * @param int $batchId
     * @return bool
     */
    public static function checkIsCanEdit($batchId) {
        if(Utility::checkQueryId($batchId)) {
            $settles = StockBatchSettlement::model()->findAll('batch_id = :batchId', array('batchId' => $batchId));
            $stockBatch = StockNotice::model()->findByPk($batchId);
            if($stockBatch->status == StockNotice::STATUS_SETTLE_BACK){
                return true;
            }
            if (Utility::isNotEmpty($settles)) {
                foreach ($settles as $key => $row) {
                    if($row->status >= StockBatchSettlement::STATUS_SUBMIT) {
                        return false;
                    }
                }
                return true;
            }
        }

        return false;
    }
}