<?php
/**
 * Created by vector.
 * DateTime: 2017/10/10 15:38
 * Describe：
 */

class CrossOrderService
{
    //判断是否可以添加或修改
    public static function isCanAddOrEdit($contractId, $goodsId, $type=ConstantMap::ORDER_CROSS_TYPE)
    {
        $status = 0;
        if(empty($contractId) || empty($goodsId))
            return $status;

        $order = CrossOrder::model()->find(array(
            "condition"=>"contract_id=".$contractId." and goods_id=".$goodsId." and type=".$type,
            "order"=>"cross_id desc"));
        
        if(!empty($order->cross_id)){
            $status = $order->status;
        }
        return $status;
    }

    //判断是否可以添加或修改
    public static function isOrderCanAddOrEdit($crossId)
    {
        $status = 0;
        if(empty($crossId))
            return $status;

        $order = CrossOrder::model()->findAllToArray(array("condition"=>"relation_cross_id=".$crossId, "order"=>"cross_id desc"));
        
        if(Utility::isNotEmpty($order)){
            $status = $order[0]['status'];
        }
        return $status;
    }

    //根据contractId和goodsId获取总配额数量和总出库数量
    public static function getAllocateTotal($contractId, $goodsId)
    {
        $total = 0;
        if(empty($contractId) || empty($goodsId))
            return $total;
        $order = DeliveryOrderDetail::model()->findAllToArray("contract_id=".$contractId." and goods_id=".$goodsId);
        if(Utility::isNotEmpty($order)){
            foreach ($order as $key => $value) {
                $total += $value['quantity'];
            }
        }
        return $total;
    }


    //根据contractId和goodsId获取总出库数量
    public static function getOutTotal($contractId, $goodsId)
    {
        $total = 0;
        if(empty($contractId) || empty($goodsId))
            return $total;
        $order = StockOutDetail::model()->findAllToArray("contract_id=".$contractId." and goods_id=".$goodsId);
        if(Utility::isNotEmpty($order)){
            foreach ($order as $key => $value) {
                $total += $value['quantity'];
            }
        }
        return $total;
    }

    //保存调货或还货明细
    public static function saveCrossDetail($goodsItems, $crossId, $isSave, $type=ConstantMap::ORDER_CROSS_TYPE) {
        if(empty($crossId) || Utility::isEmpty($goodsItems) || empty($type))
            return;

        $sql    = "select detail_id from t_cross_detail where cross_id=" . $crossId;
        $data   = Utility::query($sql);
        $p      = array();
        if (Utility::isNotEmpty($data)) {
            foreach ($data as $v) {
                $p[$v["detail_id"]] = $v['detail_id'];
            }
        }
        // print_r($goodsItems);die;
        if(Utility::isNotEmpty($goodsItems)){
            foreach ($goodsItems as $row) {
                if (array_key_exists($row["detail_id"], $p)) {
                    $crossDetail = CrossDetail::model()->findByPk($row["detail_id"]);
                    if (empty($crossDetail->detail_id)) {
                        unset($p[$row["detail_id"]]);
                        return;
                    }
                } else {
                    $crossDetail = new CrossDetail();
                }
                $crossDetail->cross_id      = $crossId;
                $crossDetail->contract_id   = $row["contract_id"];
                $crossDetail->project_id    = $row["project_id"];
                $crossDetail->type          = $type;
                $crossDetail->goods_id      = $row['goods_id'];
                $crossDetail->stock_id      = $row['stock_id'];
                $crossDetail->store_id      = $row['store_id'];
                $crossDetail->quantity      = $row['quantity'];
                $crossDetail->quantity_balance  = $row['quantity'];
                $crossDetail->save();
                unset($p[$row["detail_id"]]);
                if(empty($isSave)){
                    $r = StockService::freeze($row['stock_id'], $row['quantity']);
                    if(!$r)
                        BusinessException::throw_exception(OilError::$FROZEN_STOCK_QUANTITY_ERROR, array('stock_id' => $row['stock_id']));
                }
            }
        }
        if (count($p) > 0) {
            CrossDetail::model()->deleteAll('detail_id in(' . implode(',', $p) . ')');
        }
    }


    //生成采销合同  --- todo autoCreateContract
    public static function autoCreateContract($crossId, $goodsId)
    {
        if(empty($crossId) || empty($goodsId))
            return;

        $sql = "select b.project_id as buy_project_id,b.partner_id as buy_partner_id,
                b.corporation_id as buy_corporation_id,b.price_type as buy_price_type,
                b.currency as buy_currency,c1.formula as buy_formula,c1.price as buy_price,
                b.manager_user_id as buy_manager_user_id,b.exchange_rate as buy_exchange_rate,
                d.quantity,
                s.project_id as sell_project_id,s.partner_id as sell_partner_id,
                s.corporation_id as sell_corporation_id,s.price_type as sell_price_type,
                s.currency as sell_currency,c2.formula as sell_formula,c2.price as sell_price,
                s.manager_user_id as sell_manager_user_id,a.exchange_rate as sell_exchange_rate,
                from t_cross_order o 
                left join t_cross_detail d on o.cross_id=d.cross_id
                left join t_contract b on o.contract_id=b.contract_id
                left join t_partner pa1 on b.partner_id=pa1.partner_id
                left join t_contract s on d.contract_id=s.contract_id
                left join t_partner pa2 on s.partner_id=pa2.partner_id
                left join t_corporation co1 on b.corporation_id=co1.corporation_id
                left join t_corporation co2 on s.corporation_id=co2.corporation_id
                left join t_contract_goods c1 on c1.contract_id=b.contract_id
                left join t_contract_goods c2 on c2.contract_id=s.contract_id
                where o.cross_id=".$crossId." and c1.goods_id=".$goodsId." and c2.goods_id=".$goodsId;

        $data = Utility::query($sql);
        if(Utility::isEmpty($data))
            return;

        $total_quantity = 0.0;
        foreach ($data as $key => $value) {
            $total_quantity += $value['quantity'];
        }

        $buyContract    = new Contract();
        $sellContract   = new Contract();
        $nowUserId      = Utility::getNowUserId();
        $nowTime        = new CDbExpression("now()");

        $buyContract->project_id = $data[0]['buy_project_id'];
        $buyContract->partner_id = $data[0]['buy_partner_id'];
        $buyContract->type = ConstantMap::BUY_TYPE;
        $buyContract->category = ConstantMap::BUY_SALE_CONTRACT_TYPE_AUTO;
        $buyContract->corporation_id = $data[0]['buy_corporation_id'];
        $buyContract->currency = $data[0]['buy_currency'];
        $buyContract->exchange_rate = $data[0]['buy_exchange_rate'];
        $buyContract->price_type = $data[0]['buy_price_type'];
        $buyContract->manager_user_id = $data[0]['buy_manager_user_id'];
        $buyContract->status = Contract::STATUS_BUSINESS_CHECKED;
        $buyContract->status_time = $nowTime;
        $buyContract->create_user_id = $nowUserId;
        $buyContract->create_time = $nowTime;
        $buyContract->update_user_id = $nowUserId;
        $buyContract->update_time = $nowTime;
        $buyContract->save();


        $sellContract->project_id = $data[0]['sell_project_id'];
        $sellContract->partner_id = $data[0]['sell_partner_id'];
        $sellContract->type = ConstantMap::SALE_TYPE;
        $sellContract->category = ConstantMap::SELL_SALE_CONTRACT_TYPE_AUTO;
        $sellContract->corporation_id = $data[0]['sell_corporation_id'];
        $sellContract->currency = $data[0]['sell_currency'];
        $sellContract->exchange_rate = $data[0]['sell_exchange_rate'];
        $sellContract->price_type = $data[0]['sell_price_type'];
        $sellContract->manager_user_id = $data[0]['sell_manager_user_id'];
        $sellContract->status = Contract::STATUS_BUSINESS_CHECKED;
        $sellContract->status_time = $nowTime;
        $sellContract->create_user_id = $nowUserId;
        $sellContract->create_time = $nowTime;
        $sellContract->update_user_id = $nowUserId;
        $sellContract->update_time = $nowTime;
        $sellContract->save();


        // ContractService::generateContractCode($buyContract->contract_id);
    }

    //更新库存数量
    public static function frozenStockQuantity($stockId,$quantity)
    {
        $rows=Stock::model()->updateByPk($stockId
            ,array("quantity_balance"=>new CDbExpression("quantity_balance-".$quantity),"update_time"=>new CDbExpression("now()"))
            ,"quantity_balance>=".$quantity
        );
        
        if($rows==1)
        {
            return true;
        }
        else
            return false;

    }

    //获取历史的调货明细
    public static function getAllOrderDetail($contractId, $goodsId)
    {
        $sql = "select o.cross_id,o.cross_code,cd.goods_id,cd.quantity,o.cross_date,cd.project_id,
                pa.partner_id,pa.name as partner_name,st.stock_id,s.store_id,s.name as store_name,
                c.contract_id,c.contract_code,o.remark,o.status,cd.quantity_out,o.order_index,
                st.unit,st.quantity_balance,si.code as stock_code,si.stock_in_id,cd.remark as d_remark
                from t_cross_order o 
                left join t_cross_detail cd on o.cross_id=cd.cross_id
                left join t_contract c on cd.contract_id=c.contract_id
                left join t_partner pa on c.partner_id=pa.partner_id
                left join t_storehouse s on cd.store_id=s.store_id
                left join t_stock st on cd.stock_id=st.stock_id
                left join t_stock_in si on st.stock_in_id=si.stock_in_id
                where o.contract_id=".$contractId.
                " and o.goods_id=".$goodsId.
                " and o.type=".ConstantMap::ORDER_CROSS_TYPE." order by o.cross_id desc,cd.detail_id asc ";
        $data = Utility::query($sql);

        return $data;
    }


    //根据crossId和contractId获取调货明细
    public static function getOrderDetailById($crossId, $contractId="")
    {
        $query = "";
        if(!empty($contractId))
            $query = " and cd.contract_id=".$contractId;
        
        $sql = "select o.cross_id,o.cross_code,cd.goods_id,g.name as goods_name,cd.quantity,o.cross_date,cd.project_id,
                pa.partner_id,pa.name as partner_name,st.stock_id,s.store_id,s.name as store_name,
                c.contract_id,c.contract_code,o.remark,o.status,cd.quantity_out,cd.quantity_frozen,o.order_index,
                c.corporation_id,c.partner_id,st.unit,st.quantity_balance,si.code as stock_code,
                si.stock_in_id,cd.detail_id as cross_detail_id
                from t_cross_order o 
                left join t_cross_detail cd on o.cross_id=cd.cross_id
                left join t_contract c on cd.contract_id=c.contract_id
                left join t_partner pa on c.partner_id=pa.partner_id
                left join t_storehouse s on cd.store_id=s.store_id
                left join t_stock st on cd.stock_id=st.stock_id
                left join t_stock_in si on st.stock_in_id=si.stock_in_id
                left join t_goods g on cd.goods_id=g.goods_id
                where o.cross_id=".$crossId.$query." order by o.cross_id,cd.detail_id asc ";
        $data = Utility::query($sql);

        return $data;
    }

    //根据调货单id获取对应的出库单
    public static function getAllOutOrder($crossDetailId)
    {
        $sql = "select o.out_order_id,o.code as out_code,s.out_id,c.stock_id,co.cross_id as relation_cross_id,
                si.code as stock_code,s.quantity_actual,c.unit,co.detail_id as cross_detail_id,
                co.contract_id,co.project_id,co.goods_id,co.store_id,ct.partner_id,ct.corporation_id
                from t_stock_out_order o 
                left join t_stock_out_detail s on o.out_order_id=s.out_order_id
                left join t_cross_detail co on co.detail_id=s.cross_detail_id
                left join t_stock c on co.stock_id=c.stock_id
                left join t_stock_in_detail st on c.stock_id=st.stock_id
                left join t_stock_in si on si.stock_in_id=st.stock_in_id 
                left join t_contract ct on ct.contract_id=co.contract_id
                where s.cross_detail_id=".$crossDetailId." order by o.out_order_id asc ";
        $data = Utility::query($sql);

        return $data;
    }

    /**
     * 出库存
     * @param $crossDetailId
     * @param $quantity
     * @return bool
     */
    public static function outCross($crossDetailId,$quantity)
    {
        $rows=CrossDetail::model()->updateByPk($crossDetailId,
            array(
                "quantity_balance"=>new CDbExpression("quantity_balance-".$quantity),
                "quantity_out"=>new CDbExpression("quantity_out+".$quantity),
                "update_time"=>new CDbExpression("now()")
                ),"quantity-quantity_out-quantity_frozen>=:quantity",
                array('quantity'=>$quantity)
        );
        if($rows==1)
        {
            return true;
        }
        else
            return false;
    }


    /**
     * 解冻库存
     * @param $crossDetailId
     * @param $quantity
     * @return bool
     */
    public static function unFreezeCross($crossDetailId,$quantity)
    {
        $rows=CrossDetail::model()->updateByPk($crossDetailId,
            array(
                "quantity_balance"=>new CDbExpression("quantity_balance+".$quantity),
                "quantity_frozen"=>new CDbExpression("quantity_frozen-".$quantity),
                "update_time"=>new CDbExpression("now()")
                ),"quantity_frozen>=".$quantity
            );
        if($rows==1)
        {
            return true;
        }
        else
            return false;
    }


    //更新t_cross_contract_detail
    public static function updateCrossContractDetail($crossId)
    {
        if(empty($crossId))
            return;

        $cross = CrossDetail::model()->findAllToArray("cross_id=".$crossId);
        if(Utility::isEmpty($cross))
            return;

        $res = array();
        foreach ($cross as $key => $value) {
            $res[$value['contract_id']]['cross_id']     = $value['cross_id']; 
            $res[$value['contract_id']]['project_id']   = $value['project_id']; 
            $res[$value['contract_id']]['goods_id']     = $value['goods_id']; 
            $res[$value['contract_id']]['type']         = $value['type']; 
            $res[$value['contract_id']]['quantity']    += $value['quantity']; 
        }

        $nowTime    = new CDbExpression("now()");
        $nowUserId  = Utility::getNowUserId();
        foreach ($res as $k => $v) {
            $obj = new CrossContractDetail();
            
            $obj->cross_id          = $v['cross_id'];
            $obj->type              = $v['type'];
            $obj->project_id        = $v['project_id'];
            $obj->contract_id       = $k;
            $obj->goods_id          = $v['goods_id'];
            $obj->quantity          = $v['quantity'];
            $obj->status            = 1;
            $obj->status_time       = $nowTime;
            $obj->create_time       = $nowTime;
            $obj->create_user_id    = $nowUserId;
            $obj->update_time       = $nowTime;
            $obj->update_user_id    = $nowUserId;
            $obj->save();

            /*$order = CrossOrder::model()->findByPk($v['cross_id']);
            $order->detail_id = $obj->detail_id;
            $order->save();*/
        }
        
    }


    //获取历史的还货明细
    public static function getReturnDetail($crossId, $contractId)
    {
        if(empty($crossId) || empty($contractId))
            return;
        $order = CrossOrder::model()->findAllToArray(array("condition"=>"relation_cross_id=".$crossId." and contract_id=".$contractId, "order"=>"cross_id asc"));
        // print_r($order);die;
        $data = array();
        foreach ($order as $key => $value) {
            $contractGoods = ContractGoods::model()->find('contract_id='.$value['contract_id']." and goods_id=".$value['goods_id']);
            $data[$value['cross_id']]['cross_code'] = $value['cross_code'];
            $data[$value['cross_id']]['contract_id']= $value['contract_id'];
            $data[$value['cross_id']]['quantity']   = $value['quantity'];
            $data[$value['cross_id']]['remark']     = $value['remark'];
            $data[$value['cross_id']]['type']       = $value['type'];
            $data[$value['cross_id']]['order_index']= $value['order_index'];
            $data[$value['cross_id']]['relation_cross_id']= $value['relation_cross_id'];
            $data[$value['cross_id']]['status']= $value['status'];
            $data[$value['cross_id']]['unit']  = $contractGoods->unit_store;
            if($value['type']==ConstantMap::ORDER_BACK_TYPE){
                // $detail = CrossDetail::model()->findAllToArray(array("condition"=>"cross_id=".$order->cross_id, "order"=>" detail_id asc"));
                $sql = "select c.*,i.code as stock_code,ct.contract_code,s.quantity_balance,s.unit
                        from t_cross_detail c 
                        left join t_contract ct on c.contract_id=ct.contract_id
                        left join t_stock s on c.stock_id=s.stock_id 
                        left join t_stock_in i on s.stock_in_id=i.stock_in_id
                        where c.cross_id=".$value['cross_id']." order by detail_id ";
                $details = Utility::query($sql);
                if(Utility::isNotEmpty($details)){
                    foreach ($details as $k => $v) {
                         $data[$v['cross_id']]['details'][] = $v;
                         $data[$v['cross_id']]['unit']      = $v['unit'];
                    }
                }
            }
        }

        return $data;
        
    }


    //获取最新一条的还货明细
    public static function getCrossDetailById($relation_cross_id, $contract_id)
    {
        $data = array();
        if(empty($relation_cross_id))
            return $data;
        $cross  = CrossOrder::model()->findAllToArray(array("condition"=>"relation_cross_id=".$relation_cross_id." and contract_id=".$contract_id, "order"=>" cross_id desc"));
        $data   = $cross[0];

        if($data['type']==ConstantMap::ORDER_BACK_TYPE){
            $sql = "select c.*,i.code as stock_code,g.name as goods_name,
                    ct.contract_code,s.quantity_balance,s.unit
                    from t_cross_detail c 
                    left join t_contract ct on c.contract_id=ct.contract_id
                    left join t_stock s on c.stock_id=s.stock_id 
                    left join t_stock_in i on s.stock_in_id=i.stock_in_id
                    left join t_goods g on c.goods_id=g.goods_id
                    where c.cross_id=".$data['cross_id']." order by detail_id asc ";
            $details = Utility::query($sql);
            if(Utility::isNotEmpty($details)){
                $map = Map::$v;
                foreach ($details as $k => $v) {
                    $details[$k]['unit_format']  = $map['goods_unit'][$v['unit']]['name'];
                    $details[$k]['quantity_format'] = number_format($v['quantity_balance'], 2).$details[$k]['unit_format'];
                }
                $data['details'] = $details;
            }
        }

        return $data;
    }

    /**
     * 是否可以修改
     * @param $status
     * @return bool
     */
    public static function isCanEdit($status)
    {
        return ($status==CrossOrder::STATUS_BACK || $status==CrossOrder::STATUS_SAVED);
    }

    //获取借货明细
    public static function getCrossHead($crossId, $contractId)
    {
        $data = array();
        if(empty($crossId) || empty($contractId))
            return $data;

        $crossOrder     = CrossOrder::model()->with('contract', 'goods', 'project', 'contractGoods')->findByPk($crossId);
        $contract       = $crossOrder->contract;
        $goods          = $crossOrder->goods;
        $project        = $crossOrder->project;
        $contractGoods  = $crossOrder->contractGoods;
        $partner        = Partner::model()->findByPk($contract->partner_id);
        $buyContract    = Contract::model()->findByPk($contractId);//被借采购合同对象

        $data['detail_id']      = $contractGoods->detail_id;
        $data['contract_id']    = $contract->contract_id;
        $data['contract_code']  = $contract->contract_code;
        $data['goods_name']     = $goods->name;
        $data['project_id']     = $project->project_id;
        $data['project_code']   = $project->project_code;
        $data['partner_id']     = $partner->partner_id;
        $data['partner_name']   = $partner->name;
        $data['relation_cross_id']      = $crossOrder->cross_id;
        $data['relation_cross_code']    = $crossOrder->cross_code;
        $data['cross_date']     = $crossOrder->cross_date;
        $data['reason']         = $crossOrder->remark;
        $data['buy_id']         = $buyContract->contract_id;  //被借采购合同编号
        $data['buy_code']       = $buyContract->contract_code; //被借采购合同编码
        $data['buy_project_id'] = $buyContract->project_id; //被借采购合同项目编号

        return $data;
    }


    //获取调货明细
    public static function getCrossDetail($crossId, $contractId)
    {
        $crossDetail= array();
        if(empty($crossId) || empty($contractId))
            return $crossDetail;

        $orderDetail = CrossOrderService::getOrderDetailById($crossId, $contractId);
        if(Utility::isEmpty($orderDetail)){
            return $crossDetail;
        }

        
        $total_quantity = 0;
        $total_quantity_out = 0;
        foreach ($orderDetail as $key=>$value) {
            $total_quantity += $value['quantity']; 
            $total_quantity_out += $value['quantity_out']; 
            $crossDetail['detail'][$key] = $value;
            $unit = $value['unit'];
        }

        if(!empty($crossDetail)){
            $crossDetail['total_quantity']      = $total_quantity;
            $crossDetail['total_quantity_out']  = $total_quantity_out;
            $crossDetail['unit'] = $unit;
            // $crossDetail['unit']                = $contractGoods->unit_store;
        }

        return $crossDetail;
    }

    //获取还货明细
    public static function getReturnDetailById($crossId)
    {
        $data = array();
        if(empty($crossId))
            return $data;

        $sql = "select c.*,i.code as stock_code,g.name as goods_name,
                    ct.contract_code,s.quantity_balance,s.unit
                    from t_cross_detail c 
                    left join t_contract ct on c.contract_id=ct.contract_id
                    left join t_stock s on c.stock_id=s.stock_id 
                    left join t_stock_in i on s.stock_in_id=i.stock_in_id
                    left join t_goods g on c.goods_id=g.goods_id
                    where c.cross_id=".$crossId." order by detail_id asc ";
        $data = Utility::query($sql);

        return $data;
    }

}