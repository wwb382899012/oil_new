<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/8 17:07
 * Describe：
 */

namespace ddd\domain\iRepository\stock;


use ddd\domain\entity\stock\Stock;
use ddd\Common\Domain\IRepository;

interface IStockRepository extends IRepository
{
    function freeze(Stock $stock,$quantity);
    function unFreeze(Stock $stock,$quantity);
    function out(Stock $stock,$quantity);
    function refund(Stock $stock,$quantity);
}