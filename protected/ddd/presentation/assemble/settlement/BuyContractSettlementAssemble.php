<?php
/**
 * Created by youyi000.
 * DateTime: 2018/5/3 15:02
 * Describe：
 */

namespace ddd\presentation\assemble\settlement;

use ddd\presentation\settlement\SettlementGoods;

class BuyContractSettlementAssemble extends SettlementAssemble
{

    /**
     * 获取入库通知单结算列表
     * @return null|string
     */
    public function getBillSettlements($settlementDTO)
    {
        $bill_settlements=array();
        if(!empty($settlementDTO->lading_bills)){
            foreach($settlementDTO->lading_bills as $k=>$v){
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
                    return $v->in_quantity;

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
                    return $v->in_quantity_sub;

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

                    if(!empty($v->lading_items)){
                        foreach ($v->lading_items as $key=>$value) {
                            $settlementGoods = new SettlementGoods();
                            $attr=$value->getAttributes();
                            $settlementGoods->setAttributes($attr);
                            $settlementGoods->bill_id=$value->batch_id;
                            $settlementGoods->bill_code=$value->batch_code;
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