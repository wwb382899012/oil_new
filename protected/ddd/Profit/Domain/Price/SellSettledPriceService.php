<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/10 15:43
 * Describe：
 */

namespace ddd\Profit\Domain\Price;


use ddd\Common\Domain\BaseService;
use ddd\Common\Domain\Value\Quantity;
use ddd\domain\entity\value\Price;
use ddd\Profit\Domain\Contract\Contract;
use ddd\Profit\Domain\Model\Settlement\DeliveryOrderSettlement;
use ddd\Profit\Domain\Price\SellSettledPriceRepository;


class SellSettledPriceService extends BaseService
{

    use SellSettledPriceRepository;

    /**
     * createSellSettledPrice 创建销售结算单价
     * @param * @param DeliveryOrderSettlement $deliveryOrderSettlement
     * @param bool $persistent
       @throw * @throws \ddd\infrastructure\error\ZException
     * @return static
     */
    public function createSellSettledPrice(DeliveryOrderSettlement $deliveryOrderSettlement,$persistent=false)
    {

        $SellSettledPriceEntity=SellSettledPrice::create($deliveryOrderSettlement);

        if($persistent)
           $return = $this->getSellSettledPriceRepository()->store($SellSettledPriceEntity);

        return $return;
    }




}