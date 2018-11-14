<?php
/**
 * Desc: 合同采购单价仓储trait
 * User: vector
 * Date: 2018/8/30
 * Time: 17:02
 */

namespace ddd\Profit\Domain\Price;

use ddd\infrastructure\DIService;


trait BuyPriceRepository
{
    /**
     * @var IBuyPriceRepository
     */
    protected $buyPriceRepository;

    /**
     * @desc 获取合同采购单价仓储
     * @return IBuyPriceRepository
     * @throws \Exception
     */
    protected function getBuyPriceRepository()
    {
        if(empty($this->buyPriceRepository)) {
            $this->buyPriceRepository = DIService::getRepository(IBuyPriceRepository::class);
        }

        return $this->buyPriceRepository;
    }
}