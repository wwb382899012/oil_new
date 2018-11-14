<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/19 16:48
 * Describe：
 */

namespace ddd\domain\tRepository\stock;


use ddd\domain\iRepository\stock\IStockInRepository;
use ddd\infrastructure\DIService;

trait StockInRepository
{
    /**
     * @var IStockInRepository
     */
    protected $stockInRepository;

    /**
     * 获取合同仓储
     * @return IStockInRepository
     * @throws \Exception
     */
    protected function getStockInRepository()
    {
        if (empty($this->stockInRepository))
        {
            $this->stockInRepository=DIService::getRepository(IStockInRepository::class);
        }
        return $this->stockInRepository;
    }
}