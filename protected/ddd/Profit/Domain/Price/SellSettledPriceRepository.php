<?php
/**
 * Desc: 合同销售结算单价仓储trait
 * User: vector
 * Date: 2018/8/30
 * Time: 17:02
 */

namespace ddd\Profit\Domain\Price;

use ddd\infrastructure\DIService;


trait SellSettledPriceRepository
{
    /**
     * @var ISellSettledPriceRepository
     */
    protected $sellSettledPriceRepository;

    /**
     * @desc 获取合同销售结算单价仓储
     * @return ISellSettledPriceRepository
     * @throws \Exception
     */
    protected function getSellSettledPriceRepository()
    {
        if(empty($this->sellSettledPriceRepository)) {
            $this->sellSettledPriceRepository = DIService::getRepository(ISellSettledPriceRepository::class);
        }

        return $this->sellSettledPriceRepository;
    }
}