<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/4/16 0016
 * Time: 16:41
 */

namespace ddd\domain\iRepository\stock;


use ddd\domain\entity\stock\StockOut;
use ddd\Common\Domain\IRepository;

interface IStockOutRepository extends IRepository
{
    function submit(StockOut $stockOut);

    function checkBack(StockOut $stockOut);

    function checkPass(StockOut $stockOut);
}