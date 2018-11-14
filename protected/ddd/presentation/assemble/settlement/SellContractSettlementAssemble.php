<?php
/**
 * Created by youyi000.
 * DateTime: 2018/5/3 15:02
 * Describe：
 */

namespace ddd\presentation\assemble\settlement;

use ddd\presentation\settlement\SettlementGoods;

class SellContractSettlementAssemble extends SettlementAssemble
{
    /**
     * 获取发货单结算列表
     * @return null|string
     */
    public function getBillSettlements($settlementDTO)
    {
        $bill_settlements=array();
        if(!empty($settlementDTO->delivery_orders)){
            foreach($settlementDTO->delivery_orders as $k=>$v){
                $row = $this->assemble($v);
                $bill_settlements[]=$row;
            }
        }
        return $bill_settlements;
    }
    /**
     * 获取商品结算 ： 入库单数量或出库单数量
     * @return null|string
     */
    public function getGoodsBillQuantity($goods_id){
        if(!empty($this->_settlementDTO->settlementGoods)){
            foreach($this->_settlementDTO->settlementGoods as $k=>$v){
                if($goods_id==$v->goods_id)
                    return $v->out_quantity;

            }
        }
    }
    /**
     * 获取商品结算 ： 入库单数量或出库单数量 子单位
     * @return null|string
     */
    public function getGoodsBillQuantitySub($goods_id){
        if(!empty($this->_settlementDTO->settlementGoods)){
            foreach($this->_settlementDTO->settlementGoods as $k=>$v){
                if($goods_id==$v->goods_id)
                    return $v->out_quantity_sub;

            }
        }
    }
    /**
     * 获取商品结算 ： 入库单信息或出库单信息
     * @return null|string
     */
    public function getGoodsBillItems($goods_id){
        if(!empty($this->_settlementDTO->settlementGoods)){
            foreach($this->_settlementDTO->settlementGoods as $k=>$v){
                if($goods_id==$v->goods_id){
                    $return = array();
                    if(!empty($v->order_items)){
                        foreach ($v->order_items as $key=>$value) {
                            $settlementGoods = new SettlementGoods();
                            $attr=$value->getAttributes();
                            $settlementGoods->setAttributes($attr);
                            $settlementGoods->bill_id=$value->order_id;
                            $settlementGoods->bill_code=$value->delivery_code;
                            $settlementGoods->bill_quantity=$value->in_quantity;
                            $settlementGoods->bill_quantity_sub=$value->in_quantity_sub;

                            $settlementGoods->quantity=$value->quantity;
                            $settlementGoods->quantity_sub=$value->quantity_sub;
                            $settlementGoods->quantity_loss=$value->quantity_loss;
                            $settlementGoods->quantity_loss_sub=$value->quantity_loss_sub;
                            $settlementGoods->settlementGoodsDetail=$value->settlementGoodsDetail;

                            $return[]=$settlementGoods;
                        }
                    }
                    return $return;

                }

            }
        }
    }
}