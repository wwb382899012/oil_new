<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/8 15:45
 * Describe：
 *      合同商品仓储类接口
 */

namespace ddd\domain\iRepository\contract;


use ddd\domain\entity\contract\TradeGoods;
use ddd\Common\Domain\IRepository;

interface ITradeGoodsRepository extends IRepository
{

    function findByContractIdAndGoodsId($contractId, $goodsId);

    function saveUnitStore(TradeGoods $contractGoods);

    function saveUnitPrice(TradeGoods $contractGoods);

    function saveStockQuantity(TradeGoods $contractGoods, $quantity);

    function saveStockInQuantity(TradeGoods $contractGoods, $quantity);

    function saveStockOutQuantity(TradeGoods $contractGoods, $quantity);

    function saveLockType(TradeGoods $contractGoods);

}