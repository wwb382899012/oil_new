<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/4/16 0016
 * Time: 16:41
 */

namespace ddd\domain\iRepository\stock;


use ddd\domain\entity\stock\StockIn;
use ddd\Common\Domain\IRepository;

interface IStockInRepository extends IRepository
{
    function submit(StockIn $stockOut);

    function checkBack(StockIn $stockOut);

    function checkPass(StockIn $stockOut);
}