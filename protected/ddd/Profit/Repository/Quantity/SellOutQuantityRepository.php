<?php
/**
 * Created by vector.
 * DateTime: 2018/8/29 15:49
 * Describe：
 */

namespace ddd\Profit\Repository\Quantity;


use ddd\Common\Domain\Value\Quantity;

use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\BaseRepository;
use ddd\Profit\Domain\Quantity\GoodsOutQuantityItem;
use ddd\Profit\Domain\Quantity\ISellOutQuantityRepository;
use ddd\Profit\Domain\Quantity\SellOutQuantity;
use ddd\Profit\Domain\Service\EventService;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\error\ZModelSaveFalseException;

class SellOutQuantityRepository extends BaseRepository implements ISellOutQuantityRepository
{

    /**
     * 根据单据id查找销售出库数量信息
     * @param $billId
     * @return SellOutQuantity|null
     */
    public function findByBillId($billId)
    {
        $model=\DeliveryOrder::model()->with('outDetails')->findByPk($billId);
        if(empty($model))
            return null;
        
        return $this->dataToEntity($model);
    }

    /**
     *
     * @param \DeliveryOrder $model
     * @return SellOutQuantity
     */
    protected function dataToEntity($model)
    {
        $entity = new SellOutQuantity();
        $entity->bill_id = $model->order_id;
        $entity->contract_id = $model->contract_id;
        if(is_array($model->outDetails))
        {
            foreach ($model->outDetails as $out)
            {
                $item = new GoodsOutQuantityItem();
                $item->stock_in_id  = $out->stock_in_id;
                $item->contract_id  = $out->contract_id;
                $item->goods_id     = $out->goods_id;
                $item->out_quantity = new Quantity($out->out_quantity, $out->unit);
                
                $entity->addOutItem($item);
            }
        }
        return $entity;
    }


    /**
     * 把对象持久化到数据
     * @param IAggregateRoot $entity
     * @return SellOutQuantity
     * @throws \Exception
     */
    public function store(IAggregateRoot $entity)
    {
        if(empty($entity))
            throw new ZException("SellOutQuantity对象不存在");

        if(empty($entity->out_items))
            throw new ZException("GoodsOutQuantityItem对象不存在");

        $model = array();
        if(!empty($entity->bill_id))
            $model = \DeliveryOrder::model()->with("outDetails")->findByPk($entity->bill_id);

        if(!empty($model->outDetails))
            throw new ZException("发货单编码".$model->code."的销售出库数量信息已经存在");

        $items = $entity->out_items;

        if (is_array($items) && count($items) > 0)
        {
            foreach ($items as $item)
            {
                $outItem = new \GoodsOutQuantityDetail();
                $outItem->bill_id      = $entity->bill_id;
                $outItem->contract_id  = $item->contract_id;
                $outItem->stock_in_id  = $item->stock_in_id;
                $outItem->goods_id     = $item->goods_id;
                $outItem->out_quantity = $item->out_quantity->quantity;
                $outItem->unit         = $item->out_quantity->unit->id;

                $res = $outItem->save();
                if (!$res)
                    throw new ZModelSaveFalseException($outItem);
            }
        }

        return $entity;
    }

    function findByPk($id, $condition = '', $params = array())
    {
        // TODO: Implement findByPk() method.
    }

    function find($condition = '', $params = array())
    {
        // TODO: Implement find() method.
    }

    function findAll($condition = '', $params = array())
    {
        // TODO: Implement findAll() method.
    }
}