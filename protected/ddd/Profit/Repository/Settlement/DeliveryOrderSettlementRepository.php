<?php
/**
 * Desc:发货单结算仓储
 * User: wwb
 * Date: 2018/5/31 0031
 * Time: 17:39
 */

namespace ddd\Profit\Repository\Settlement;


use ddd\Common\Domain\Currency\Currency;
use ddd\Common\Domain\Currency\CurrencyService;
use ddd\Common\Domain\Value\Money;
use ddd\Common\Domain\Value\Quantity;
use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\domain\entity\value\Price;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\infrastructure\error\ZModelDeleteFalseException;
use ddd\infrastructure\error\ZModelNotExistsException;
use ddd\infrastructure\error\ZModelSaveFalseException;
use ddd\infrastructure\Utility;
use ddd\Profit\Domain\Model\Settlement\DeliveryItem;
use ddd\Profit\Domain\Model\Settlement\DeliveryOrderSettlement;
use ddd\Profit\Domain\Model\Settlement\IDeliveryOrderSettlementRepository;
use ddd\Profit\Domain\Model\Settlement\ILadingBillSettlementRepository;
use ddd\Profit\Domain\Model\Settlement\LadingBillSettlement;
use ddd\Profit\Domain\Model\Settlement\SettlementItem;



class DeliveryOrderSettlementRepository extends EntityRepository implements IDeliveryOrderSettlementRepository
{

    public function getNewEntity()
    {
        return new DeliveryOrderSettlement();
    }


    public function init()
    {

    }

    public function getActiveRecordClassName()
    {
        return 'DeliverySettlement';
    }

    public function dataToEntity($model)
    {
        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes(), false);
        $entity->bill_id = $model->order_id;
        $entity->settle_currency = $model->currency== CurrencyService::CNY ?  Currency::createCNY() : Currency::createUSD();
        $entity->project_id = $model->project_id;
       /* $entity->amount_goods = new Money($model->amount_goods,$model->currency);
        $entity->amount_other = new Money($model->amount_other,$model->currency);
        $entity->amount = new Money($model->amount,$model->currency);*/

        if(!empty($model->contractSettlementGoods)){
            foreach($model->contractSettlementGoods as &$value){
                $item = new SettlementItem();
                $item->goods_id  = $value->goods_id;
                $item->exchange_rate = $value->exchange_rate;
                $item->price = new Money($value->price,$model->currency);
                $item->price_cny = new Money($value->price_cny,$model->currency);
                /*$item->amount = new Money($value->amount,$model->currency);
                $item->amount_cny = new Money($value->amount_cny,$model->currency);*/

                $entity->addSettleItem($item);
            }
        }

        if(!empty($model->deliveryOrder->stockOutDetails)){
            foreach($model->deliveryOrder->stockOutDetails as &$value){
                $item = new DeliveryItem();

                $item->goods_id  = $value->goods_id;
                $item->stock_in_id = $value->stock->stock_in_id;
                $item->contract_id = $value->stock->stockIn->notice->contract_id;
                $item->exchange_rate = $value->contractGoods->unit_convert_rate;
                $item->out_quantity = new Quantity($value->quantity,$value->stock->unit);

                $entity->addDeliveryItem($item);
            }
        }

        return $entity;
    }



    /**
     * 把对象持久化到数据
     * @param IAggregateRoot $entity
     * @return int
     * @throws \Exception
     */
    public function store(IAggregateRoot $entity)
    {

    }

    /**
     * 根据发货单
     * @param $orderId
     * @return LadingSettlement
     */
    function findByOrderId($orderId)
    {
        return $this->find('t.order_id=' . $orderId);
    }


}