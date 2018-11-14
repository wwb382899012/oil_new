<?php
/**
 * Desc: 合同采购结算单价仓储trait
 * User: vector
 * Date: 2018/8/30
 * Time: 17:02
 */

namespace ddd\Profit\Domain\Price;

use ddd\infrastructure\DIService;


trait BuySettledPriceRepository
{
    /**
     * @var IBuySettledPriceRepository
     */
    protected $buySettledPriceRepository;

    /**
     * @desc 获取合同采购结算单价仓储
     * @return IBuySettledPriceRepository
     * @throws \Exception
     */
    protected function getBuySettledPriceRepository()
    {
        if(empty($this->buySettledPriceRepository)) {
            $this->buySettledPriceRepository = DIService::getRepository(IBuySettledPriceRepository::class);
        }

        return $this->buySettledPriceRepository;
    }
}