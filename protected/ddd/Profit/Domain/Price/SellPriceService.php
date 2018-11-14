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
use ddd\Profit\Domain\Price\SellPriceRepository;


class SellPriceService extends BaseService
{

    use SellPriceRepository;

    /**
     * createSellPrice  创建销售单价对象
     * @param * @param Contract $contract
     * @param bool $persistent
       @throw * @throws \ddd\infrastructure\error\ZException
     * @return static
     */
    public function createSellPrice(Contract $contract,$persistent=false)
    {

        $sellPriceEntity=SellPrice::create($contract);
        
        if($persistent)
           $return = $this->getSellPriceRepository()->store($sellPriceEntity);

        return $return;
    }




}