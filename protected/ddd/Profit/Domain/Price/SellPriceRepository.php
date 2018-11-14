<?php
/**
 * Desc: 合同销售单价仓储trait
 * User: vector
 * Date: 2018/8/30
 * Time: 17:02
 */

namespace ddd\Profit\Domain\Price;

use ddd\infrastructure\DIService;


trait SellPriceRepository
{
    /**
     * @var ISellPriceRepository
     */
    protected $sellPriceRepository;

    /**
     * @desc 获取合同销售单价仓储
     * @return ISellPriceRepository
     * @throws \Exception
     */
    protected function getSellPriceRepository()
    {
        if(empty($this->sellPriceRepository)) {
            $this->sellPriceRepository = DIService::getRepository(ISellPriceRepository::class);
        }

        return $this->sellPriceRepository;
    }
}