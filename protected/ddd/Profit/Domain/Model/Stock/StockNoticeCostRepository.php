<?php
/**
 * Desc: 入库通知单成本仓储trait
 * User: susiehuang
 * Date: 2018/5/28 0028
 * Time: 17:14
 */

namespace ddd\Profit\Domain\Model\Stock;

use ddd\infrastructure\DIService;
use ddd\Profit\Domain\Model\Stock\IStockNoticeCostRepository;
trait StockNoticeCostRepository
{
    /**
     * @var $buyGoodsCostRepository
     */
    protected $stockNoticeCostRepository;

    /**
     * @desc 获取入库通知单成本仓储
     * @return IBuyGoodsCostRepository
     * @throws \Exception
     */
    protected function getStockNoticeCostRepository()
    {
        if(empty($this->stockNoticeCostRepository)) {
            $this->stockNoticeCostRepository = DIService::getRepository(IStockNoticeCostRepository::class);
        }

        return $this->stockNoticeCostRepository;
    }
}