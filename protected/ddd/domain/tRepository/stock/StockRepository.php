<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/19 16:48
 * Describe：
 */

namespace ddd\domain\tRepository\stock;


use ddd\domain\iRepository\stock\IStockRepository;
use ddd\infrastructure\DIService;

trait StockRepository
{
    /**
     * @var IStockRepository
     */
    protected $stockRepository;

    /**
     * 获取合同仓储
     * @return IStockRepository
     * @throws \Exception
     */
    protected function getStockRepository()
    {
        if (empty($this->stockRepository))
        {
            $this->stockRepository=DIService::getRepository(IStockRepository::class);
        }
        return $this->stockRepository;
    }
}