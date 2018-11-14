<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/19 16:45
 * Describe：
 */

namespace ddd\domain\tRepository\contract;


use ddd\domain\iRepository\contract\ITradeGoodsRepository;
use ddd\infrastructure\DIService;

trait TradeGoodsRepository
{
    /**
     * @var ITradeGoodsRepository
     */
    protected $tradeGoodsRepository;

    /**
     * 获取合同仓储
     * @return ITradeGoodsRepository
     * @throws \Exception
     */
    protected function getTradeGoodsRepository()
    {
        if (empty($this->tradeGoodsRepository))
        {
            $this->tradeGoodsRepository=DIService::getRepository(ITradeGoodsRepository::class);
        }
        return $this->tradeGoodsRepository;
    }
}