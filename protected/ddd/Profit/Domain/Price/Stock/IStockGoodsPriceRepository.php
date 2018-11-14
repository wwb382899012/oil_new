<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/13 16:10
 * Describe：
 */

namespace ddd\Profit\Domain\Price\Stock;


use ddd\Common\Domain\IRepository;

interface IStockGoodsPriceRepository extends IRepository
{
    /**
     * 根据入库通知单id和商品id获取价格
     * @param $ladingId
     * @param $goodsId
     * @return StockGoodsPrice|null
     */
    public function findByLadingIdAndGoodsId($ladingId,$goodsId);
}