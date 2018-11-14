<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/8 15:03
 * Describe：
 */

namespace ddd\repository\stock;


use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\domain\entity\stock\Stock;
use ddd\domain\iRepository\stock\IStockRepository;

class StockRepository extends EntityRepository implements IStockRepository
{
    public function getActiveRecordClassName()
    {
        // TODO: Implement getActiveRecordClassName() method.
        return "Stock";
    }

    public function getNewEntity()
    {
        // TODO: Implement getNewEntity() method.
        return new Stock();
    }

    /**
     * 更新冻结库存
     * @param Stock $stock
     * @param $quantity
     * @throws \Exception
     */
    public function freeze(Stock $stock,$quantity)
    {
        $rows=\Stock::model()->updateByPk($stock->getId()
            ,array(
                     "quantity_balance"=>new \CDbExpression("quantity_balance-".$quantity),
                     "quantity_frozen"=>new \CDbExpression("quantity_frozen+".$quantity),
                     "update_time"=>new \CDbExpression("now()")
                  )
            ,"quantity-quantity_out-quantity_frozen>=".$quantity
        );
        if($rows!==1)
            throw new \Exception("更新失败");

    }

    /**
     * 更新解除冻结
     * @param Stock $stock
     * @param $quantity
     * @throws \Exception
     */
    public function unFreeze(Stock $stock,$quantity)
    {
        $rows=\Stock::model()->updateByPk($stock->getId()
            ,array(
                      "quantity_balance"=>new \CDbExpression("quantity_balance+".$quantity),
                      "quantity_frozen"=>new \CDbExpression("quantity_frozen-".$quantity),
                      "update_time"=>new \CDbExpression("now()")
                  )
            ,"quantity_frozen>=".$quantity
        );
        if($rows!==1)
            throw new \Exception("更新失败");
    }

    /**
     * 更新出库
     * @param Stock $stock
     * @param $quantity
     * @throws \Exception
     */
    public function out(Stock $stock,$quantity)
    {
        $rows=\Stock::model()->updateByPk($stock->getId()
            ,array(
                     "quantity_balance"=>new \CDbExpression("quantity_balance-".$quantity),
                     "quantity_out"=>new \CDbExpression("quantity_out+".$quantity),
                     "update_time"=>new \CDbExpression("now()")
                 )
            ,"quantity-quantity_out-quantity_frozen>=".$quantity
        );
        if($rows!==1)
            throw new \Exception("更新失败");
    }

    /**
     * 更新退货
     * @param Stock $stock
     * @param $quantity
     * @throws \Exception
     */
    public function refund(Stock $stock,$quantity)
    {
        $rows=\Stock::model()->updateByPk($stock->getId(),
                                         array(
                                             "quantity_balance"=>new \CDbExpression("quantity_balance+".$quantity),
                                             "quantity_out"=>new \CDbExpression("quantity_out-".$quantity),
                                             "update_time"=>new \CDbExpression("now()")
                                         )
            ,"quantity_out>=".$quantity
        );
        if($rows!==1)
            throw new \Exception("更新失败");
    }


    function findByPk($id, $condition = '', $params = array()){
        // TODO: Implement findByPk() method.
    }

    function find($condition = '', $params = array()){
        // TODO: Implement find() method.
    }

    function findAll($condition = '', $params = array()){
        // TODO: Implement findAll() method.
    }

    function store(IAggregateRoot $entity){
        // TODO: Implement store() method.
    }
}