<?php
/**
 * Created by vector.
 * DateTime: 2018/8/30 15:49
 * Describe：
 */

namespace ddd\Profit\Repository\Price;


use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\BaseRepository;
use ddd\Common\Repository\EntityRepository;
use ddd\Profit\Domain\Price\IBuyPriceRepository;
use ddd\Profit\Domain\Service\EventService;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\error\ZModelSaveFalseException;

class BuyPriceRepository extends BaseRepository implements IBuyPriceRepository
{

    /**
     * 把对象持久化到数据
     * @param IAggregateRoot $entity
     * @return Price
     * @throws \Exception
     */
    public function store(IAggregateRoot $entity)
    {
        if(empty($entity))
            throw new ZException("BuyPrice对象不存在");

        if(empty($entity->price_items))
            throw new ZException("GoodsPriceItem对象不存在");

        $model = array();
        if(!empty($entity->contract_id))
            $model = \Contract::model()->with("priceDetails")->findByPk($entity->contract_id);

        if(!empty($model->priceDetails))
            return ;
            //throw new ZException("合同编码".$model->contract_code."的采购价格信息已经存在");

        $items = $entity->price_items;

        if (is_array($items) && count($items) > 0)
        {
            foreach ($items as $item)
            {
                $priceItem = new \GoodsPriceDetail();
                $priceItem->project_id    = $entity->project_id;
                $priceItem->contract_id   = $entity->contract_id;
                $priceItem->is_settled    = $entity->is_settled;
                $priceItem->type          = $entity->type;
                $priceItem->goods_id      = $item->goods_id;
                $priceItem->price         = $item->price->amount;
                $priceItem->currency      = $item->price->currency;
                $priceItem->exchange_rate = $item->exchange_rate;
                $priceItem->price_cny     = $item->price_cny->amount;

                $res = $priceItem->save();
                if (!$res)
                    throw new ZModelSaveFalseException($priceItem);
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