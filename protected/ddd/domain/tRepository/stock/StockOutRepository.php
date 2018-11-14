<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/19 16:48
 * Describe：
 */

namespace ddd\domain\tRepository\stock;


use ddd\domain\iRepository\stock\IStockOutRepository;
use ddd\infrastructure\DIService;

trait StockOutRepository
{
    /**
     * @var IStockOutRepository
     */
    protected $stockOutRepository;

    /**
     * 获取合同仓储
     * @return IStockOutRepository
     * @throws \Exception
     */
    protected function getStockOutRepository()
    {
        if (empty($this->stockOutRepository))
        {
            $this->stockOutRepository=DIService::getRepository(IStockOutRepository::class);
        }
        return $this->stockOutRepository;
    }
}