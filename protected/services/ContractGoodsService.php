<?php

/**
 * Desc: 子合同交易明细服务
 * User: susiehuang
 * Date: 2017/8/30 0031
 * Time: 11:05
 */
class ContractGoodsService {
    /**
     * @desc 将商品交易明细拆分成商品交易明细和代理费用明细
     * @param array $goodsItem
     * @return array
     */
    public static function formatTransactionData($goodsItem) {
        $res = array('contractGoods' => array(), 'contractAgent' => array());
        if (Utility::isNotEmpty($goodsItem)) {
            foreach ($goodsItem as $key => $row) {
                $res['contractGoods'][$key]['detail_id'] = $row['detail_id'];
                $res['contractGoods'][$key]['type'] = $row['exchange_type']; //采销类型
                $res['contractGoods'][$key]['goods_id'] = $row['goods_id'];
                // $res['contractGoods'][$key]['goods_describe'] = $row['goods_describe'];
                $res['contractGoods'][$key]['refer_target'] = $row['refer_target'];
                $res['contractGoods'][$key]['price'] = $row['price'];
                $res['contractGoods'][$key]['quantity'] = $row['quantity'];
                $res['contractGoods'][$key]['quantity_actual'] = $row['quantity'];
                $res['contractGoods'][$key]['unit'] = $row['unit'];
                $res['contractGoods'][$key]['amount'] = $row['amount'];
                $res['contractGoods'][$key]['amount_cny'] = $row['amount_cny'];
                $res['contractGoods'][$key]['currency'] = $row['currency'];
                $res['contractGoods'][$key]['more_or_less_rate'] = $row['more_or_less_rate'];

                $res['contractAgent'][$key]['detail_id'] = $row['agent_detail_id'];
                $res['contractAgent'][$key]['goods_id'] = $row['goods_id'];
                $res['contractAgent'][$key]['type'] = $row['type']; //计费方式
                $res['contractAgent'][$key]['price'] = $row['type'] == ConstantMap::AGENT_FEE_CALCULATE_BY_AMOUNT ? $row['agent_price'] : 0;
                $res['contractAgent'][$key]['quantity'] = $row['quantity'];
                $res['contractAgent'][$key]['fee_rate'] = $row['type'] == ConstantMap::AGENT_FEE_CALCULATE_BY_PRICE ? $row['fee_rate'] : 0;
                $res['contractAgent'][$key]['unit'] = $row['agent_unit'];
                $res['contractAgent'][$key]['amount_cny'] = $row['agent_amount'];
                $res['contractAgent'][$key]['amount'] = $row['agent_amount'];
                $res['contractAgent'][$key]['currency'] = ConstantMap::CURRENCY_RMB;
            }
        }

        return $res;
    }

    /**
     * @desc 格式化前端展示交易明细信息
     * @param array $goodsItems
     * @param array $contractAgents
     * @param int $type
     * @param float $exchange_rate
     * @return array
     */
    public static function reverseContractGoodsItems($goodsItems, $contractAgents, $type, $exchange_rate) {
        $res = array();
        if(Utility::isNotEmpty($goodsItems) && Utility::checkQueryId($type)) {
            foreach ($goodsItems as $key => $row) {
                if($row['quantity']==0){//数量为0的不展示
                    continue;
                }
                $res[$key]['detail_id'] = $row['detail_id'];
                $res[$key]['goods_id'] = $row['goods_id'];
                $res[$key]['price'] = $row['price'];
                $res[$key]['quantity'] = $row['quantity'];
                $res[$key]['amount_cny'] = $row['amount_cny'];
                $res[$key]['amount'] = $row['amount'];
                $res[$key]['unit'] = $row['unit'];
                $res[$key]['more_or_less_rate'] = $row['more_or_less_rate'];
                // $res[$key]['goods_describe'] = $row['goods_describe'];
                $res[$key]['refer_target'] = $row['refer_target'];
                $res[$key]['currency'] = $row['currency'];
                $res[$key]['exchange_rate'] = $exchange_rate;
                $res[$key]['goods_name'] = !empty($row->goods)?$row->goods->name:'';
                $res[$key]['unit_convert_rate'] = $row['unit_convert_rate'];

                if(Utility::isNotEmpty($contractAgents)) {
                    foreach ($contractAgents as $k => $v) {
                        if($v['goods_detail_id'] == $row['detail_id']) {
                            $res[$key]['type'] = $v['type'];
                            $res[$key]['agent_price'] = $v['price'];
                            $res[$key]['agent_unit'] = $v['unit'];
                            $res[$key]['fee_rate'] = $v['fee_rate'];
                            $res[$key]['agent_amount'] = $v['amount'];
                            $res[$key]['agent_detail_id'] = $v['detail_id'];
                        }
                    }
                }
                
                $res[$key]['exchange_type'] = $type;
            }
        }
        return $res;
    }

    /**
     * @desc 检查交易明细参数是否合法
     * @param int $type
     * @param int $price_type
     * @param array $transactions
     * @param int $exchange_rate
     * @return bool|string
     */
    public static function checkParamsValid($type, $price_type, $transactions, $exchange_rate) {
        if (Utility::isNotEmpty($transactions)) {
            $invalid = false;
            $totalCurrencyAmount = 0;
            $totalAmount = 0;
            $requiredParams = array('goods_id', 'quantity', 'unit', 'price');
            if($price_type==2 && $type==1)
                    $requiredParams[] = "refer_target";

            foreach ($transactions as $key => $row) {
                //必填参数校验
                if (!Utility::checkRequiredParamsNoFilterInject($row, $requiredParams)) {
                    $invalid = true;
                    break;
                }

                // if ($row['price'] * $row['quantity'] != $row['amount']) {

                if(bccomp(round($row['price']*$row['quantity']), $row['amount'],  2) != 0){
                    return BusinessError::outputError(OilError::$TRANSACTION_CURRENCY_AMOUNT_ERROR);
                }

                // if ($row['amount'] * $exchange_rate != $row['amount_cny']) {
                if(bccomp(round($row['amount'] * $exchange_rate), $row['amount_cny'],  2) != 0){
                    return BusinessError::outputError(OilError::$TRANSACTION_AMOUNT_ERROR);
                }
                $totalCurrencyAmount += $row['amount'];
                $totalAmount += $row['amount_cny'];
            }

            if ($invalid) {
                return BusinessError::outputError(OilError::$TRANSACTION_REQUIRED_PARAMS_CHECK_ERROR);
            }
        } else {
            return BusinessError::outputError(OilError::$PROJECT_LAUNCH_NOT_TRANSACTION);
        }

        return true;
    }

    /**
     * @desc 保存商品交易明细，同时保存交易代理费
     * @param array $goodsItems
     * @param int $contract_id
     * @param int $operateType #操作类型（0：保存  1：暂存）
     * @return array|int
     */
    public static function saveContractGoodsAndAgentFee($goodsItems, $contract_id, $operateType = 0) {
        Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' pass transaction params are:' . json_encode($goodsItems) . ', contract_id is:' . $contract_id);
        if(!$operateType && Utility::isEmpty($goodsItems)) {
            return;
        }
        if (empty($contract_id)) {
            BusinessException::throw_exception(OilError::$PARAMS_PASS_ERROR);
        }

        $contract = Contract::model()->findByPk($contract_id);

        $sql = "select * from t_contract_goods where project_id=" . $contract->project_id . " and contract_id = " . $contract_id;
        $data = Utility::query($sql);
        $p = array();
        if (Utility::isNotEmpty($data)) {
            foreach ($data as $v) {
                $p[$v["detail_id"]] = $v["detail_id"];
            }
        }

        $sql2 = "select * from t_contract_agent_detail where project_id=" . $contract->project_id . " and contract_id = " . $contract_id;
        $data2 = Utility::query($sql2);
        $p2 = array();
        if (Utility::isNotEmpty($data2)) {
            foreach ($data2 as $v) {
                $p2[$v["detail_id"]] = $v["detail_id"];
            }
        }

        if(Utility::isNotEmpty($goodsItems)){
            foreach ($goodsItems as $row) {
                if (array_key_exists($row["detail_id"], $p)) {
                    $contractGoods = ContractGoods::model()->findByPk($row["detail_id"]);
                    if (empty($contractGoods->detail_id)) {
                        unset($p[$row["detail_id"]]);
                        return;
                    }
                } else {
                    $contractGoods = new ContractGoods();
                }
                $contractGoods->contract_id = $contract_id;
                $contractGoods->project_id = $contract->project_id;
                $contractGoods->type = $row['exchange_type'];
                $contractGoods->goods_id = $row['goods_id'];
                $contractGoods->refer_target = $row['refer_target'];
                // $contractGoods->goods_describe = $row['goods_describe'];
                $contractGoods->price = $row['price'];
                $contractGoods->quantity = $row['quantity'];
                $contractGoods->quantity_actual = $row['quantity'];
                $contractGoods->amount_cny = $row['amount_cny'];
                $contractGoods->amount = $row['amount'];
                $contractGoods->currency = $row['currency'];
                $contractGoods->unit = $row['unit'];
                $contractGoods->more_or_less_rate = $row['more_or_less_rate'];
                $contractGoods->unit_convert_rate = $row['unit_convert_rate'];
                $contractGoods->save();
                unset($p[$row["detail_id"]]);
    
                if ($contract->category == ConstantMap::BUY_SALE_CONTRACT_TYPE_AGENT_IMPORT && ($operateType || $contract->agent_id)) { //代理进口合同
                    if (array_key_exists($row["agent_detail_id"], $p2)) {
                        $contractAgentDetail = ContractAgentDetail::model()->findByPk($row["agent_detail_id"]);
                        if (empty($contractAgentDetail->detail_id)) {
                            unset($p2[$v["agent_detail_id"]]);
                            return;
                        }
                    } else {
                        $contractAgentDetail = new ContractAgentDetail();
                    }
                    $contractAgentDetail->goods_detail_id = $contractGoods->detail_id;
                    $contractAgentDetail->contract_id = $contract_id;
                    $contractAgentDetail->project_id = $contract->project_id;
                    $contractAgentDetail->goods_id = $row['goods_id'];
                    $contractAgentDetail->type = $row['type'];
                    $contractAgentDetail->price = $row['type'] == ConstantMap::AGENT_FEE_CALCULATE_BY_AMOUNT ? $row['agent_price'] : 0;
                    $contractAgentDetail->quantity = $row['quantity'];
                    $contractAgentDetail->fee_rate = $row['type'] == ConstantMap::AGENT_FEE_CALCULATE_BY_PRICE ? $row['fee_rate'] : 0;
                    $contractAgentDetail->unit = $row['agent_unit'];
                    $contractAgentDetail->amount = $row['agent_amount'];
                    $contractAgentDetail->amount_cny = $row['agent_amount'];
                    $contractAgentDetail->currency = ConstantMap::CURRENCY_RMB;
                    $contractAgentDetail->save();
                    unset($p2[$row["agent_detail_id"]]);
                }
            }
        }

        if (count($p) > 0) {
            ContractGoods::model()->deleteAll('detail_id in(' . implode(',', $p) . ')');
        }

        if (count($p2) > 0) {
            ContractAgentDetail::model()->deleteAll('detail_id in(' . implode(',', $p2) . ')');
        }
    }

    /**
     * 获取合同所有商品
     * @param $contractGoods    合同商品明细
     * @return array
     */
    public static function getContractAllGoods($contractGoods)
    {
        $allGoods=array();
        foreach ($contractGoods as $m)
        {
            $allGoods[]=array(
                "goods_id"=>$m["goods_id"],
                "name"=>$m["goods"]["name"],
                "unit"=>$m["unit"],
                "unit_sub"=>$m["unit_store"],
                "quantity"=>$m["quantity"],
            );
        }
        return $allGoods;
    }

    /**
     * 更新合同商品的计价单位
     * @param $contractId
     * @param $goodsId
     * @param $unit
     * @return bool
     */
    public static function updateGoodsPriceUnit($contractId,$goodsId,$unit)
    {
        if($unit<1)
            return false;
        $rows=ContractGoods::model()->updateAll(
            array("unit_price"=>$unit),
            "contract_id=".$contractId." and goods_id=".$goodsId." and unit_price<1"
        );
        if($rows==1)
            return true;
        else
            return false;
    }

    /**
     * 更新合同商品的库存单位
     * @param $contractId
     * @param $goodsId
     * @param $unit
     * @return bool
     */
    public static function updateGoodsStoreUnit($contractId,$goodsId,$unit)
    {
        if($unit<1)
            return false;
        $rows=ContractGoods::model()->updateAll(
            array("unit_store"=>$unit),
            "contract_id=".$contractId." and goods_id=".$goodsId." and unit_store<1"
        );
        if($rows==1)
            return true;
        else
            return false;
    }

    /**
     * 判断合同商品的计价单位，如果没有设置则更新，如果已经设置，比较是否一致，成功返回true，否则返回false
     * @param $contractId
     * @param $goodsId
     * @param $unit
     * @return bool
     */
    public static function checkGoodsPriceUnit($contractId,$goodsId,$unit)
    {
        if($unit<1)
            return false;
        $model=ContractGoods::model()->find("contract_id=".$contractId." and goods_id=".$goodsId);
        if(empty($model))
            return false;
        if(empty($model->unit_price))
        {
            return self::updateGoodsPriceUnit($contractId,$goodsId,$unit);
        }

        return $model->unit_price==$unit;

    }

    /**
     * 判断合同商品的库存单位，如果没有设置则更新，如果已经设置，比较是否一致，成功返回true，否则返回false
     * @param $contractId
     * @param $goodsId
     * @param $unit
     * @return bool
     */
    public static function checkGoodsStoreUnit($contractId,$goodsId,$unit)
    {
        if($unit<1)
            return false;
        $model=ContractGoods::model()->find("contract_id=".$contractId." and goods_id=".$goodsId);
        if(empty($model))
            return false;
        if(empty($model->unit_store))
        {
            return self::updateGoodsStoreUnit($contractId,$goodsId,$unit);
        }

        return $model->unit_store==$unit;

    }


    /**
     * @desc 获取库存单位
     * @param int $contractId
     * @param int $goodsId
     * @return int
     */
    public static function getGoodsUnitStore($contractId, $goodsId) {
        if(Utility::checkQueryId($contractId) && Utility::checkQueryId($goodsId)) {
            $contractGoods = ContractGoods::model()->find('contract_id = :contractId and goods_id = :goodsId', array('contractId' => $contractId, 'goodsId' => $goodsId));
            if(Utility::isNotEmpty($contractGoods->detail_id)) {
                return $contractGoods->unit_store;
            }
        }
        return 0;
    }

    public static function getStockInBatchGoodsQuantity($contractId, $goodsId, $unit) {
        $data = array('quantity' => 0, 'quantity_sub' => 0);
        $subSql = "select ifnull(detail_id, 0) from t_stock_in_batch_detail where contract_id = $contractId and goods_id=$goodsId and unit = $unit";
        $sql = "select ifnull(sum(quantity), 0) as total_quantity from t_stock_in_batch_detail where detail_id in($subSql)";
        $res = Utility::query($sql);
        if (!empty($res)) {
            $data['quantity'] = $res[0]['total_quantity'];
        }

        $sql1 = "select ifnull(sum(quantity), 0)  as total_quantity from t_stock_in_batch_detail_sub where detail_id in ($subSql)";

        $res1 = Utility::query($sql1);
        if (!empty($res1)) {
            $data['quantity_sub'] = $res1[0]['quantity_total'];
        }

        return $data;
    }

    /**
     * 建议作废
     * @deprecated
     * @param $contractId
     * @param $goodsId
     * @param $unit
     * @return array
     */
    public static function getStockInGoodsQuantity($contractId, $goodsId, $unit) {
        $data = array('quantity' => 0, 'quantity_sub' => 0);
        $sqlSub = 'select ifnull(stock_id, 0) from t_stock_in_detail a 
                left join t_stock_in b on b.stock_in_id=a.stock_in_id 
                left join t_stock_in_batch c on c.batch_id=b.batch_id 
                where a.contract_id=' . $contractId . ' and c.status>=' . StockNotice::STATUS_SUBMIT . ' and b.status>=' . StockIn::STATUS_PASS . ' and a.goods_id=' . $goodsId . ' and a.unit=' . $unit;
        $sql = 'select ifnull(sum(quantity),0) as total_quantity from t_stock_in_detail where stock_id in(' . $sqlSub . ')';
        $res = Utility::query($sql);
        if (Utility::isNotEmpty($res)) {
            $data['quantity'] = $res[0]['total_quantity'];
        }

        $sql1 = 'select ifnull(sum(quantity),0) as total_quantity from t_stock_in_detail_sub where stock_id in(' . $sqlSub . ')';
        $res1 = Utility::query($sql1);
        if (Utility::isNotEmpty($res1)) {
            $data['quantity_sub'] = $res1[0]['total_quantity'];
        }

        return $data;
    }

    public static function getStockInGoodsQuantityNew($contractId, $goodsId, $unit){
        $data = array('quantity' => 0, 'quantity_sub' => 0);
        $sqlSub = 'select ifnull(stock_id, 0) from t_stock_in_detail a 
                left join t_stock_in b on b.stock_in_id=a.stock_in_id 
                left join t_stock_in_batch c on c.batch_id=b.batch_id 
                where a.contract_id=' . $contractId . ' and c.status>=' . StockNotice::STATUS_SUBMIT . ' and b.status>=' . StockIn::STATUS_PASS . ' and a.goods_id=' . $goodsId;

        $sql = <<<SQL
SELECT 
  IFNULL(SUM(IF({$unit} = sid.unit, sid.quantity, IF({$unit} = sids.unit,sids.quantity,0))),0) AS quantity,
  IFNULL(SUM(IF({$unit} = sid.unit, sids.quantity, IF({$unit} = sids.unit,sid.quantity,0))),0) AS quantity_sub 
FROM t_stock_in_detail AS sid 
LEFT JOIN t_stock_in_detail_sub AS sids ON sid.stock_id = sids.stock_id 
WHERE sid.stock_id in({$sqlSub})
SQL;

        $res = Utility::query($sql);
        if (Utility::isNotEmpty($res)) {
           return $res[0];
        }

        return $data;
    }

    /**
     * @desc 获取交易商品入库未结算数量
     * @param $contractId
     * @param $goodsId
     * @param $unit
     * @return array
     */
    public static function getTradeGoodsInUnsettledQuantity($contractId, $goodsId, $unit) {
        $data = array('quantity' => 0, 'quantity_sub' => 0);
        /*$sqlSub = 'select ifnull(stock_id, 0) from t_stock_in_detail a
                   left join t_stock_in b on b.stock_in_id=a.stock_in_id 
                   left join t_stock_in_batch c on c.batch_id=b.batch_id 
                   where a.contract_id=' . $contractId . ' and c.status>=' . StockNotice::STATUS_SUBMIT . ' and c.status<' . StockNotice::STATUS_SETTLED . ' and b.status=' . StockIn::STATUS_PASS . ' and a.goods_id=' . $goodsId . ' and a.unit=' . $unit;*/
        $sqlSub = 'select ifnull(stock_id, 0) from t_stock_in_detail a 
                   left join t_stock_in b on b.stock_in_id=a.stock_in_id 
                   where a.contract_id=' . $contractId . ' and b.status=' . StockIn::STATUS_PASS . ' and a.goods_id=' . $goodsId . ' and a.unit=' . $unit;

        $sql = 'select ifnull(sum(quantity),0) as total_quantity from t_stock_in_detail where stock_id in(' . $sqlSub . ')';
        $res = Utility::query($sql);
        if (Utility::isNotEmpty($res)) {
            $data['quantity'] = $res[0]['total_quantity'];
        }

        $sql1 = 'select ifnull(sum(quantity),0) as total_quantity from t_stock_in_detail_sub where stock_id in(' . $sqlSub . ')';
        $res1 = Utility::query($sql1);
        if (Utility::isNotEmpty($res1)) {
            $data['quantity_sub'] = $res1[0]['total_quantity'];
        }

        return $data;
    }

    /**
     * @desc 获取合同明细出库数量
     * @param int $contractId
     * @param int $goodsId
     * @return int
     */
    public static function getStockOutGoodsQuantity($contractId, $goodsId) {
        $sql = 'select ifnull(sum(quantity), 0) as total_quantity from t_stock_out_detail a 
                left join t_stock_out_order b on b.out_order_id = a.out_order_id 
                where a.contract_id = ' . $contractId . ' and a.goods_id = ' . $goodsId . ' and b.status >= ' . StockOutOrder::STATUS_SUBMITED . ' and b.status <> ' . StockOutOrder::STATUS_SUBMIT;

        $res = Utility::query($sql);
        if (Utility::isNotEmpty($res)) {
            return $res[0]['total_quantity'];
        }
        return 0;
    }

    /**
     * @desc 获取交易商品出库未结算数量
     * @param $contractId
     * @param $goodsId
     * @return array
     */
    public static function getTradeGoodsOutUnsettledQuantity($contractId, $goodsId) {
        $sql = 'select ifnull(sum(quantity), 0) as total_quantity from t_stock_out_detail a 
                left join t_stock_out_order b on b.out_order_id = a.out_order_id 
                where a.contract_id = ' . $contractId . ' and a.goods_id = ' . $goodsId . ' and b.status = ' . StockOutOrder::STATUS_SUBMITED;

        $res = Utility::query($sql);
        if (Utility::isNotEmpty($res)) {
            return $res[0]['total_quantity'];
        }
        return 0;
    }

    public static function getStockBatchSettlementGoodsQuantity($contractId, $goodsId, $unit) {
        $data = array('quantity' => 0, 'quantity_sub' => 0);
        /*$sqlSub = 'select ifnull(a.settle_id, 0) from t_stock_batch_settlement a 
                LEFT JOIN t_check_detail b ON b.detail_id = a.detail_id
                where a.contract_id=' . $contractId . ' and b.status=1 and b.check_status=1 and a.goods_id=' . $goodsId . ' and a.unit=' . $unit;
		*/
		$sqlSub = 'select ifnull(a.item_id, 0) from t_stock_batch_settlement a 
                LEFT JOIN t_stock_in_batch b ON b.batch_id = a.batch_id
                where a.contract_id=' . $contractId . ' and b.status='.StockNotice::STATUS_SETTLED.' and a.goods_id=' . $goodsId . ' and a.unit=' . $unit;
        $sql = 'select ifnull(sum(quantity),0) as total_quantity from t_stock_batch_settlement where item_id in(' . $sqlSub . ')';

        $res = Utility::query($sql);
        if (Utility::isNotEmpty($res)) {
            $data['quantity'] = $res[0]['total_quantity'];
        }

        $sql1 = 'select ifnull(sum(quantity),0) as total_quantity from t_stock_batch_settlement_sub where item_id in(' . $sqlSub . ')';
        $res1 = Utility::query($sql1);
        if (Utility::isNotEmpty($res1)) {
            $data['quantity_sub'] = $res1[0]['total_quantity'];
        }

        return $data;
    }

    /**
     * 获取销售合同明细信息
     * @param $contractId
     * @param int $stockInId
     * @return array
     */
    public static function getSaleContractsDetails($contractId, $stockInId = 0) {
        if(empty($contractId)){
            return array();
        }

        //已经配货
        $deliveryStockSql = <<<SQL
SELECT sum(quantity) FROM t_stock_delivery_detail WHERE contract_id = b.contract_id AND goods_id = a.goods_id AND status=
SQL;
        $deliveryStockSql .= StockDeliveryDetail::STATUS_SUBMIT;

        //已经出库总量
        $statusString =  implode(',',array(StockOutOrder::STATUS_SUBMIT, StockOutOrder::STATUS_SUBMITED, StockOutOrder::STATUS_SETTLED));
        $outStockSql = <<<SQL
SELECT 
sum(sod.quantity) FROM t_stock_out_detail AS sod,t_stock_out_order AS soo 
WHERE sod.out_order_id = soo.out_order_id AND soo.`status` IN ( $statusString ) 
AND	sod.contract_id = b.contract_id AND sod.goods_id = a.goods_id
SQL;

        $sql = <<<SQL
SELECT 
a.detail_id AS contract_detail_id, a.contract_id, a.goods_id, 0 AS quantity, a.quantity AS contract_quantity, a.more_or_less_rate, 
a.unit, a.unit_store, b.project_id, b.contract_code, c.project_code, d.name AS goods_name, 
ifnull(( $deliveryStockSql ), 0) AS distributed_quantity,ifnull(( $outStockSql ),0) AS stock_out_quantity 
FROM t_contract_goods a 
LEFT JOIN t_contract b ON b.contract_id = a.contract_id 
LEFT JOIN t_project c ON c.project_id = b.project_id 
LEFT JOIN t_goods d ON d.goods_id = a.goods_id 
WHERE b.contract_id = $contractId
SQL;
        $sql .= ' AND b.type = ' . ConstantMap::SALE_TYPE . ' AND b.status>='.Contract::STATUS_BUSINESS_CHECKED;

        if (!empty($stockInId)) {
            $details = StockInDetail::model()->findAll(array('select' => 'goods_id', 'condition' => 'stock_in_id = :stockInId', 'params' => array('stockInId' => $stockInId)));
            if (Utility::isNotEmpty($details)) {
                $goods_ids = array();
                foreach ($details as $k => $v) {
                    $goods_ids[] = $v->goods_id;
                }

                $sql .= ' AND a.goods_id IN ('.implode(',',$goods_ids).')';
            }
        }

        $sql .= ' order by b.contract_id desc';

        $data = Utility::query($sql);
        if (Utility::isNotEmpty($data)) {
            foreach ($data as $key => $row) {
                $data[$key]['contract_quantity'] .= Map::$v['goods_unit'][$row['unit']]['name'] . '±' . ($row['more_or_less_rate'] * 100) . '%';
                $data[$key]['distributed_quantity'] = $row['distributed_quantity'] . (!empty($row['unit_store']) ? Map::$v['goods_unit'][$row['unit_store']]['name'] : '');
                $data[$key]['stock_out_quantity'] = $row['stock_out_quantity'] . (!empty($row['unit_store']) ? Map::$v['goods_unit'][$row['unit_store']]['name'] : '');
                $data[$key]['unit_store_desc'] = Map::$v['goods_unit'][$row['unit_store']]['name'];
                if (!empty($stockInId)) { //直调生成配货明细
                    $data[$key]['stock_delivery_detail'][] = StockDeliveryDetailService::initStockDeliveryDetail($stockInId, $row);
                }
            }
        }

        return $data;
    }

}
