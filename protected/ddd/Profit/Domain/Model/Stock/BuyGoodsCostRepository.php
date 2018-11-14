<?php
/**
 * Desc: 商品成本仓储trait
 * User: susiehuang
 * Date: 2018/5/28 0028
 * Time: 17:14
 */

namespace ddd\Profit\Domain\Model\Stock;

use ddd\infrastructure\DIService;
use ddd\Profit\Domain\Model\Stock\IBuyGoodsCostRepository;
trait BuyGoodsCostRepository
{
    /**
     * @var $buyGoodsCostRepository
     */
    protected $buyGoodsCostRepository;

    /**
     * @desc 获取商品成本仓储
     * @return IBuyGoodsCostRepository
     * @throws \Exception
     */
    protected function getBuyGoodsCostRepository()
    {
        if(empty($this->buyGoodsCostRepository)) {
            $this->buyGoodsCostRepository = DIService::getRepository(IBuyGoodsCostRepository::class);
        }

        return $this->buyGoodsCostRepository;
    }
}