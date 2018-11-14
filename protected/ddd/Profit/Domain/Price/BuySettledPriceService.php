<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/10 15:43
 * Describe：
 */

namespace ddd\Profit\Domain\Price;


use ddd\Common\Domain\BaseService;
use ddd\Profit\Domain\Contract\Contract;
use ddd\Profit\Domain\Model\Settlement\LadingBillSettlement;
use ddd\Profit\Domain\Price\BuySettledPriceRepository;

class BuySettledPriceService extends BaseService
{

    use BuySettledPriceRepository;

    /**
     * createBuyPrice 创建采购单价对象
     * @param * @param Contract $contract
     * @param bool $persistent
       @throw * @throws \ddd\infrastructure\error\ZException
     * @return static
     */
    public function createBuySettledPrice(LadingBillSettlement $ladingSettlement,$persistent=false)
    {

        $BuySettledPriceEntity=BuySettledPrice::create($ladingSettlement);

        if($persistent)
            $return = $this->getBuySettledPriceRepository()->store($BuySettledPriceEntity);

        return $return;
    }




}