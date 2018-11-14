<?php

/**
 * Desc: 库存报表报表统计服务
 * User: wwb
 * Date: 2018/06/27 0009
 * Time: 15:03
 */
class CorporationStockService
{
    /**
     * @desc 合同商品库存报表
     */
    public static function corporationStock()
    {
        set_time_limit(0);
        $sql="select
                    a.*,b.id contract_goods_stock_detail_id,c.relation_contract_id,c.corporation_id,d.update_time goods_update_time
              from
                    t_contract_goods a
                    left join t_contract c on a.contract_id = c.contract_id
                    left join t_contract_goods_stock b on a.detail_id = b.detail_id
                    left join t_goods d on a.goods_id=d.goods_id
              where c.status >=".Contract::STATUS_BUSINESS_CHECKED." and c.type = ".ConstantMap::BUY_TYPE."
              order by a.detail_id asc";
        $contractGoodsList=Utility::query($sql);
        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try{
            if(Utility::isNotEmpty($contractGoodsList)){
                foreach ($contractGoodsList as $key=>$value) {
                    //if($value['contract_id']==886) {//1016  1157
                        $contractGoodsStock = ContractGoodsStock::model()->findByPk($value['contract_goods_stock_detail_id']);
                        if (empty($contractGoodsStock->id)) {
                            $contractGoodsStock = new ContractGoodsStock();
                        }
                        $contractGoodsStock->detail_id = $value['detail_id'];
                        $contractGoodsStock->contract_id = $value['contract_id'];
                        $contractGoodsStock->goods_id = $value['goods_id'];
                        $contractGoodsStock->goods_update_time = $value['goods_update_time'];
                        $contractGoodsStock->corporation_id = $value['corporation_id'];
                        $checkDetail = Utility::query("select * from t_check_detail where business_id=".ContractGoodsStock::BUSINESS_ID_CHECK3." and check_status=1 and obj_id=".$value['contract_id']);
                        if(!empty($checkDetail))
                            $contractGoodsStock->orderby_time = $checkDetail[0]['update_time'];
                        //在途库存
                        $contractGoodsStock->on_way_quantity = self::getStock($value['contract_id'],$value['relation_contract_id'],$value['goods_id'],1);
                        //在库库存
                        $contractGoodsStock->stock_quantity = self::getStock($value['contract_id'],$value['relation_contract_id'],$value['goods_id'],2);
                        //采购未执行
                        $notStockIn = self::getNotStockIn($value['contract_id'],$value['goods_id']);
                        $contractGoodsStock->unexecuted_quantity = $notStockIn['unexecuted_quantity'];
                        //已付未提
                        $contractGoodsStock->not_lading_quantity = $notStockIn['not_lading_quantity'];
                        $contractGoodsStock->save();
                    //}//if end
                }
            }
            //统计交易主体对应商品的库存情况
            $contractGoodsStockList = Utility::query("
                    select a.corporation_id,a.goods_id,sum(on_way_quantity) on_way_quantity,sum(unexecuted_quantity) unexecuted_quantity,
                           sum(not_lading_quantity) not_lading_quantity,sum(stock_quantity) stock_quantity,sum(reserve_quantity) reserve_quantity,
                           b.start_date,c.update_time goods_update_time
                    from
                          t_contract_goods_stock a
                          left join t_corporation b on a.corporation_id=b.corporation_id
                          left join t_goods c on a.goods_id=c.goods_id
                    group by a.corporation_id,a.goods_id order by a.corporation_id

            ");
            if(!empty($contractGoodsStockList)){
                foreach($contractGoodsStockList as $k=>$v){
                    $corporationGoodsStock = CorporationGoodsStock::model()->find("corporation_id=".$v['corporation_id']." and goods_id=".$v['goods_id']);
                    if(empty($corporationGoodsStock->id)){
                        $corporationGoodsStock = new CorporationGoodsStock();
                    }
                    $corporationGoodsStock->corporation_id=$v['corporation_id'];
                    $corporationGoodsStock->orderby_time=$v['start_date'];
                    $corporationGoodsStock->goods_id=$v['goods_id'];
                    $corporationGoodsStock->goods_update_time=$v['goods_update_time'];
                    $corporationGoodsStock->on_way_quantity=$v['on_way_quantity'];
                    $corporationGoodsStock->unexecuted_quantity=$v['unexecuted_quantity'];
                    $corporationGoodsStock->not_lading_quantity=$v['not_lading_quantity'];
                    $corporationGoodsStock->stock_quantity=$v['stock_quantity'];
                    $corporationGoodsStock->reserve_quantity=$v['reserve_quantity'];
                    $corporationGoodsStock->save();
                }
            }


            $trans->commit();
            echo 'success';
            return true;
        } catch(Exception $e) {
            $trans->rollback();
            echo $e->getMEssage();
            return false;
        }
    }

    /**
     * @name:onWay  在途货物数量，单位为吨
     * @desc:  入库单数量 - 出库单数量，
     * @param:* @param $contract_id
     * @param:* @param $relation_contract_id 销售合同id
     * @param $goods_id
     * @param:$type 1是在途库存（只统计虚拟仓库），2是在库库存（统计虚拟仓库之外）
       @throw:
     * @return:void
     */
    protected static function getStock($contract_id,$relation_contract_id=0,$goods_id,$type=2){
        $signal='in';
        if($type==2)
            $signal='not in';
        $stockIn =  Utility::query("
                select
                         a.*,b.unit contract_unit,b.unit_convert_rate,b.price contract_price,c.delivery_term,b.quantity contract_quantity,s.quantity quantity_sub,d.entry_date,
                         c.delivery_term,c.status contract_status,c.exchange_rate,c.currency contract_currency,c.amount_cny contract_amount_cny,s.unit sub_unit
                from
                         t_stock_in_detail a
                         left join t_contract_goods b on a.contract_id = b.contract_id and a.goods_id = b.goods_id
                         left join t_contract c on a.contract_id=c.contract_id
                         left join t_stock_in d on a.stock_in_id = d.stock_in_id
                         LEFT join t_stock_in_detail_sub s on a.stock_id = s.stock_id
                where a.contract_id = '{$contract_id}' and a.goods_id='{$goods_id}' and a.store_id ".$signal."(".ContractGoodsStock::STORE_ID_ONE.") and d.status>=".StockIn::STATUS_PASS."
         ");
        $stockInQuantity=0;//入库数量
        if(!empty($stockIn)){
            foreach($stockIn as $k=>$v){
                $stockInQuantity += self::getQuantityTon($v);
            }
        }
        $stockOut =  Utility::query("
                select
                         a.*,b.unit contract_unit,b.unit_convert_rate,b.price contract_price,c.delivery_term,b.quantity contract_quantity,
                         c.delivery_term,c.status contract_status,c.exchange_rate,c.currency contract_currency,c.amount_cny contract_amount_cny,
                         e.unit stock_in_unit,f.unit stock_in_unit_sub,e.unit_rate,g.unit_convert_rate buy_unit_convert_rate
                from
                         t_stock_out_detail a
                         left join t_contract_goods b on a.contract_id = b.contract_id and a.goods_id = b.goods_id
                         left join t_contract c on a.contract_id=c.contract_id
                         left join t_stock_out_order d on a.out_order_id = d.out_order_id
                         left join t_stock_in_detail e on a.stock_id=e.stock_id
                         left join t_stock_in_detail_sub f on a.stock_id=f.stock_id
                         left join t_contract_goods g on g.contract_id = e.contract_id and g.goods_id = e.goods_id

                where a.contract_id = '{$relation_contract_id}' and e.contract_id='{$contract_id}' and a.goods_id='{$goods_id}' and a.store_id ".$signal."(".ContractGoodsStock::STORE_ID_ONE.") and d.status=".StockOutOrder::STATUS_SUBMITED."
         ");

        $stockOutQuantity=0;//出库数量
        if(!empty($stockOut)){
            foreach($stockOut as $key=>$value){
                $stockOutQuantity+=self::getQuantityTon_sell($value);
            }
        }
        return $stockInQuantity - $stockOutQuantity;
    }

    /**
     * @name:getNotStockIn  采购未执行数量
     * @desc:
     * @param:* @param $contract_id
     * @param $goods_id
       @throw:
     * @return:void
     */
    protected static function getNotStockIn($contract_id,$goods_id){
        $unexecuted_quantity=0;//未执行数量
        $not_lading_quantity=0;//已付未提数量
        $contract_quantity=0;//合同约定数量
        $subject_list = ContractOverdue::SUBJECT_TYPE_ONE.",".ContractOverdue::SUBJECT_TYPE_SEVEN.",".ContractOverdue::SUBJECT_TYPE_EIGHT;
        if(!empty($contract_id)) {
            $contractInfo = Utility::query("select * from t_contract where contract_id = " . $contract_id);
            // 合同类型为“代理进口合同” 且 代理模式为“购销代理模式”，税款保证金 也算货款
            if($contractInfo[0]['category']==ConstantMap::BUY_SALE_CONTRACT_TYPE_AGENT_IMPORT && $contractInfo[0]['agent_type'] == ConstantMap::AGENT_TYPE_BUY_SALE)
                $subject_list = ContractOverdue::SUBJECT_TYPE_ONE.",".ContractOverdue::SUBJECT_TYPE_SIX.",".ContractOverdue::SUBJECT_TYPE_SEVEN.",".ContractOverdue::SUBJECT_TYPE_EIGHT;
        }

        $payAmount = ReportService::getContractPaymentNew($contract_id,$subject_list);//付款金额

        $stockIn = Utility::query("
                select
                         a.*,b.unit contract_unit,b.unit_convert_rate,b.price contract_price,c.delivery_term,b.quantity contract_quantity,s.quantity quantity_sub,d.entry_date,
                         c.delivery_term,c.status contract_status,c.exchange_rate,c.currency contract_currency,c.amount_cny contract_amount_cny,s.unit sub_unit
                from
                         t_stock_in_detail a
                         left join t_contract_goods b on a.contract_id = b.contract_id and a.goods_id = b.goods_id
                         left join t_contract c on a.contract_id=c.contract_id
                         left join t_stock_in d on a.stock_in_id = d.stock_in_id
                         left join t_stock_in_detail_sub s on a.stock_id = s.stock_id
                where a.contract_id = '{$contract_id}' and a.goods_id='{$goods_id}' and d.status>=".StockIn::STATUS_PASS."
         ");
        $contractGoods = Utility::query("select * from t_contract_goods where contract_id=".$contract_id." and goods_id=".$goods_id);
        if(!empty($contractGoods)){
            $contract_quantity = ($contractGoods[0]['unit_convert_rate']=='0.0000'?$contractGoods[0]['quantity']:($contractGoods[0]['quantity']/$contractGoods[0]['unit_convert_rate'])); //单位为吨
        }
        //采购未执行
        if($payAmount==0&&empty($stockIn)){//若没有实付，且没有入库，未执行数量为合同签约数量
            $unexecuted_quantity=$contract_quantity;
        }
        //已付未提
        if($payAmount>0){//已付
            $stockInQuantity=0;
            if(!empty($stockIn)){
                foreach ($stockIn as $stock_key=>$stock_value) {
                    $stockInQuantity += self::getQuantityTon($stock_value);
                }
            }
            $not_lading_quantity=$contract_quantity-round($stockInQuantity,4);
        }

        $return =array(
            'unexecuted_quantity'=>$unexecuted_quantity,
            'not_lading_quantity'=>$not_lading_quantity
        );
        return $return;

    }

    //返回计量单位为吨的数值
    protected static function getQuantityTon($value){
        $return =$value['quantity'];
        if($value['unit']==2)
            $return = $value['quantity'];
        elseif($value['contract_unit']==2){
            $return = $value['quantity_sub'];
        }else{
            if($value['unit']==$value['contract_unit']){//分2种情况
                if($value['sub_unit']==2)
                    $return = $value['quantity_sub'];
                else
                    $return = $value['quantity']/$value['unit_convert_rate'];
            }
            else
                $return = ($value['quantity']/$value['unit_rate'])/$value['unit_convert_rate'];
        }

        return $return;
    }
    /**
     * @name:getQuantityTon_sell
     * @desc: 返回计量单位为吨的数值
     * @param:* @param $value   contract_unit 合同单位 ，stock_in_unit 入库单位，stock_in_unit_sub 入库子单位，unit_rate 入库单位和子单位的转化比
     *                          unit_convert_rate 销售合同的单位换算比，buy_unit_convert_rate 采购合同的单位换算比， quantity出库数量
     * @throw:
     * @return:float
     */
    protected static function getQuantityTon_sell($value){
        /*if($value['contract_unit']==2){
            return $value['quantity'];
        }else{
            if(in_array($value['contract_unit'],array($value['stock_in_unit'],$value['stock_in_unit_sub']))){
                if(in_array('2',array($value['stock_in_unit'],$value['stock_in_unit_sub']))){//若入库单位里有吨，通过入库单换算比计算
                    if($value['stock_in_unit']==2)
                        return $value['quantity']*$value['unit_rate'];
                    elseif($value['stock_in_unit_sub']==2)
                        return $value['quantity']/$value['unit_rate'];
                }else{//若入库单位里没有吨，通过入库单换算比和合同换算比一起计算

                    if($value['stock_in_unit']==$value['contract_unit'])
                        return $value['quantity']/($value['unit_rate']*$value['buy_unit_convert_rate']);
                    elseif($value['stock_in_unit_sub']==$value['contract_unit'])
                        return $value['buy_unit_convert_rate']==0?$value['quantity']:(($value['quantity']*$value['unit_rate'])/$value['buy_unit_convert_rate']);
                }
            }else{//若出单位和入库单位不一致
                return $value['unit_convert_rate']==0?$value['quantity']:($value['quantity']/$value['unit_convert_rate']);
            }
        }*/
        return $value['unit_convert_rate']==0?$value['quantity']:($value['quantity']/$value['unit_convert_rate']);
    }
}