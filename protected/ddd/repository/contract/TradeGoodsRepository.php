<?php
/**
 * Created by youyi000.
 * DateTime: 2018/2/28 18:40
 * Describe：
 */

namespace ddd\repository\contract;


use ddd\domain\entity\contract\TradeGoods;
use ddd\Common\Domain\BaseEntity;
use ddd\domain\iRepository\contract\ITradeGoodsRepository;
use ddd\infrastructure\error\ZException;
use ddd\Common\Repository\EntityRepository;

class TradeGoodsRepository extends EntityRepository implements ITradeGoodsRepository
{

    public function getActiveRecordClassName()
    {
        // TODO: Implement getActiveRecordClassName() method.
        return "ContractGoods";
    }

    /**
     * @return \ddd\Common\Domain\BaseEntity|TradeGoods
     * @throws \Exception
     */
    public function getNewEntity()
    {
        // TODO: Implement getNewEntity() method.
        return new TradeGoods();
    }

    /**
     * 数据模型转换成业务对象
     *      一般子类需要重写该方法
     * @param $model
     * @return BaseEntity
     * @throws \Exception
     */
    public function dataToEntity($model)
    {
        $entity=$this->getNewEntity();
        if(!empty($entity))
        {
            $entity->setAttributes($model->getAttributes(),false);
        }
        return $entity;
    }

    public function findByContractIdAndGoodsId($contractId,$goodsId)
    {
        $condition="contract_id=".$contractId." and goods_id=".$goodsId."";
        return $this->find($condition);
    }


    /**
     * 保存库存第二单位
     * @param TradeGoods $contractGoods
     * @throws ZException
     */
    public function saveUnitStore(TradeGoods $contractGoods)
    {
        $rows=\ContractGoods::model()->updateByPk($contractGoods->getId()
            ,array(
                      "unit_store"=>$contractGoods->unit_store,
                      "update_time"=>new \CDbExpression("now()")
                  )
            ,"unit_store=0"
        );
        if($rows!==1)
            throw new ZException("保存库存第二单位失败");
    }

    /**
     * 保存锁价单位
     * @param TradeGoods $contractGoods
     * @throws ZException
     */
    public function saveUnitPrice(TradeGoods $contractGoods)
    {
        $rows=\ContractGoods::model()->updateByPk($contractGoods->getId()
            ,array(
                                                      "unit_price"=>$contractGoods->unit_store,
                                                      "update_time"=>new \CDbExpression("now()")
                                                  )
            ,"unit_price=0"
        );
        if($rows!==1)
            throw new ZException("保存锁价单位失败");
    }

    /**
     * 保存合同库存数量
     * @param TradeGoods $contractGoods
     * @param $quantity
     * @throws ZException
     */
    public function saveStockQuantity(TradeGoods $contractGoods, $quantity)
    {
        $rows=\ContractGoods::model()->updateByPk($contractGoods->getId()
            ,array(
                      "quantity_stock"=>new \CDbExpression("quantity_stock+".$quantity),
                      "update_time"=>new \CDbExpression("now()")
                  )
        );
        if($rows!==1)
            throw new ZException("更新库存数量失败");
    }

    /**
     * 保存合同入库数量
     * @param TradeGoods $contractGoods
     * @param $quantity
     * @throws ZException
     */
    public function saveStockInQuantity(TradeGoods $contractGoods, $quantity)
    {
        $rows=\ContractGoods::model()->updateByPk($contractGoods->getId()
            ,array(
                                                      "quantity_stock_in"=>new \CDbExpression("quantity_stock_in+".$quantity),
                                                      "update_time"=>new \CDbExpression("now()")
                                                  )
        );
        if($rows!==1)
            throw new ZException("更新合同商品入库数量失败");
    }

    /**
     * 保存合同出库数量
     * @param TradeGoods $contractGoods
     * @param $quantity
     * @throws ZException
     */
    public function saveStockOutQuantity(TradeGoods $contractGoods, $quantity)
    {
        $rows=\ContractGoods::model()->updateByPk($contractGoods->getId()
            ,array(
                                                      "quantity_stock_out"=>new \CDbExpression("quantity_stock_out+".$quantity),
                                                      "update_time"=>new \CDbExpression("now()")
                                                  )
        );
        if($rows!==1)
            throw new ZException("更新合同商品出库数量失败");
    }

    /**
     * 保存锁价方式
     * @param TradeGoods $contractGoods
     * @throws ZException
     */
    public function saveLockType(TradeGoods $contractGoods)
    {
        $rows=\ContractGoods::model()->updateByPk($contractGoods->getId()
            ,array(
                      "lock_type"=>$contractGoods->lock_type,
                      "update_time"=>new \CDbExpression("now()")
                  )
            ,"lock_type=0 or lock_type is null"
        );
        if($rows!==1)
            throw new ZException("保存锁价方式失败");
    }

}