<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/10 15:43
 * Describe：
 */

namespace ddd\Profit\Domain\Price;


use ddd\Common\Domain\BaseService;
use ddd\Profit\Domain\Contract\Contract;
use ddd\Profit\Domain\Price\BuyPriceRepository;


class BuyPriceService extends BaseService
{

    use BuyPriceRepository;

    /**
     * createBuyPrice 创建采购单价对象
     * @param * @param Contract $contract
     * @param bool $persistent
       @throw * @throws \ddd\infrastructure\error\ZException
     * @return static
     */
    public function createBuyPrice(Contract $contract,$persistent=false)
    {

        $buyPriceEntity=BuyPrice::create($contract);

        if($persistent)
            $return = $this->getBuyPriceRepository()->store($buyPriceEntity);

        return $return;
    }




}