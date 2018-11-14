<?php
/**
 * Desc: 入库通知单仓储trait
 * User: wwb
 * Date: 2018/5/28 0028
 * Time: 17:14
 */

namespace ddd\Profit\Domain\Model\Stock;

use ddd\infrastructure\DIService;
use ddd\Profit\Domain\Model\Stock\IStockNoticeRepository;
trait StockNoticeRepository
{
    /**
     * @var $buyGoodsCostRepository
     */
    protected $stockNoticeRepository;

    /**
     * @desc 获取入库通知单仓储
     * @return IBuyGoodsCostRepository
     * @throws \Exception
     */
    protected function getStockNoticeRepository()
    {
        if(empty($this->stockNoticeRepository)) {
            $this->stockNoticeRepository = DIService::getRepository(IStockNoticeRepository::class);
        }

        return $this->stockNoticeRepository;
    }
}