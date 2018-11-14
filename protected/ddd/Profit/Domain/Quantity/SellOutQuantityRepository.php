<?php
/**
 * Desc: 结算出库数量仓储trait
 * User: vector
 * Date: 2018/8/28
 * Time: 11:13
 */

namespace ddd\Profit\Domain\Quantity;

use ddd\infrastructure\DIService;
use ddd\Profit\Domain\Quantity\ISellOutQuantityRepository;


trait SellOutQuantityRepository
{
    /**
     * @var $sellOutQuantityRepository
     */
    protected $sellOutQuantityRepository;

    /**
     * @desc 获取结算出库数量仓储
     * @return $sellOutQuantityRepository
     * @throws \Exception
     */
    protected function getSellOutQuantityRepository()
    {
        if(empty($this->sellOutQuantityRepository)) {
            $this->sellOutQuantityRepository = DIService::getRepository(ISellOutQuantityRepository::class);
        }

        return $this->sellOutQuantityRepository;
    }
}