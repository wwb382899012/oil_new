<?php
/**
 * Desc: 合同拆分仓储trait
 * User: susiehuang
 * Date: 2018/5/28 0028
 * Time: 17:14
 */

namespace ddd\Split\Domain\Model\ContractSplit;

use ddd\infrastructure\DIService;
use ddd\Split\Domain\Model\ContractSplit\IStockSplitDetailRepository;

trait StockSplitDetailRepository
{
    /**
     * @var IStockSplitDetailRepository
     */
    protected $stockSplitDetailRepository;

    /**
     * @desc 获取出入库拆分明细仓储
     * @return IStockSplitDetailRepository
     * @throws \Exception
     */
    public function getStockSplitDetailRepository()
    {
        if(empty($this->stockSplitDetailRepository)) {
            $this->stockSplitDetailRepository = DIService::getRepository(IStockSplitDetailRepository::class);
        }

        return $this->stockSplitDetailRepository;
    }
}