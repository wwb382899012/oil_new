<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/13 16:51
 * Describe：
 */

namespace ddd\Profit\Domain\Price\Stock;


use ddd\Common\Domain\BaseService;
use ddd\Common\Domain\Value\Money;

class StockEventHandlerService extends BaseService
{

    /**
     * @var IStockGoodsPriceRepository
     */
    protected $stockGoodsPriceRepository;

    /**
     * 响应入库通知单确认事件
     * @param LadingConfirmedEvent $event
     * @throws \Exception
     */
    public function onLadingConfirmed(LadingConfirmedEvent $event)
    {
        $entity=StockGoodsPrice::create();
        $entity->bill_id=$event->bill_id;
        $entity->contract_id=$event->contract_id;
        $entity->goods_id=$event->goods_id;
        $entity->price=new Money($event->price);
        $this->stockGoodsPriceRepository->store($entity);
    }

    /**
     * 入库通知单结算事件
     * @param LadingSettledEvent $event
     * @throws \Exception
     */
    public function onLadingSettled(LadingSettledEvent $event)
    {
        $entity=$this->stockGoodsPriceRepository->findByLadingIdAndGoodsId($event->bill_id,$event->goods_id);
        if(empty($entity))
        {

        }
        $entity->price=new Money($event->price);
        $this->stockGoodsPriceRepository->store($entity);
    }

}