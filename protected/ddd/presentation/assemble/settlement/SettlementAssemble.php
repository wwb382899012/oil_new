<?php
/**
 * Created by youyi000.
 * DateTime: 2018/5/3 15:04
 * Describe：
 */

namespace ddd\presentation\assemble\settlement;


use ddd\presentation\BaseAssemble;
use ddd\presentation\settlement\Settlement;
use ddd\presentation\settlement\SettlementGoods;


abstract class SettlementAssemble extends BaseAssemble
{
    protected $_settlementDTO;
    /**
     * 获取原始提单或发货单id
     * @return int
     */
    public function getBillId()
    {
        return 0;
    }

    /**
     * 获取原始提单或发货单的编码
     * @return null|string
     */
    public function getBillCode()
    {
        // TODO: Implement getBillCode() method.
        return null;
    }
    /**
     * 获取发货单结算列表或入库通知单结算列表
     * @return null|string
     */
    public function getBillSettlements($settlementDTO){
        return array();
    }

    /**
     * 获取商品结算 ： 提单或发货单id
     * @return null|string
     */
    public function getGoodsBillId($goods_id){
        return 0;
    }
    /**
     * 获取商品结算 ： 提单或发货单编号
     * @return null|string
     */
    public function getGoodsBillCode($goods_id){
        return null;
    }
    /**
     * 获取商品结算 ： 入库单数量或出库单数量
     * @return null|string
     */
    public function getGoodsBillQuantity($goods_id){
        return null;
    }
    /**
     * 获取商品结算 ： 入库单数量或出库单数量 子单位
     * @return null|string
     */
    public function getGoodsBillQuantitySub($goods_id){
        return null;
    }
    /**
     * 获取商品结算 ： 入库单信息或出库单信息
     * @return null|string
     */
    public function getGoodsBillItems($goods_id){
        return array();
    }

    /**
     * 获取结算单的明细项
     * @return array
     */
    public function getBillItems()
    {
        return [];
    }


    /**
     * 返回结算对象
     * @param SettlementDTO $settlementDTO
     * @return Settlement
     * @throws \Exception
     */
    public function assemble($settlementDTO)
    {
        $settlement=new Settlement();
        $this->_settlementDTO = $settlementDTO;
        $value = $this->_settlementDTO->getAttributes();
        unset($value['settlementGoods']);
        //结算单 赋值
        $settlement->setAttributes($value);
        $settlement->bill_id=$this->getBillId();
        $settlement->bill_code=$this->getBillCode();
        $settlement->settle_currency = $this->_settlementDTO->settle_currency;
        //商品结算信息
        if(!empty($this->_settlementDTO->settlementGoods)){
            foreach($this->_settlementDTO->settlementGoods as $k=>$v){
                $settlementGoods = new SettlementGoods();
                $attr=$v->getAttributes();
                $settlementGoods->setAttributes($attr);
                $settlementGoods->bill_id=$this->getGoodsBillId($v->goods_id);
                $settlementGoods->bill_code=$this->getGoodsBillCode($v->goods_id);
                $settlementGoods->bill_quantity=$this->getGoodsBillQuantity($v->goods_id);
                $settlementGoods->bill_quantity_sub=$this->getGoodsBillQuantitySub($v->goods_id);
                $settlementGoods->bill_items=$this->getGoodsBillItems($v->goods_id);
                $settlementGoods->quantity=$v->quantity;
                $settlementGoods->quantity_sub=$v->quantity_sub;
                $settlementGoods->quantity_loss=$v->quantity_loss;
                $settlementGoods->quantity_loss_sub=$v->quantity_loss_sub;
                $settlementGoods->settlementGoodsDetail=$v->settlementGoodsDetail;
                $settlement->settlementGoods[]=$settlementGoods;
            }
        }
        //入库通知单结算列表或者发货单结算列表
        $settlement->bill_settlements=$this->getBillSettlements($settlementDTO);

        return $settlement;
    }
}