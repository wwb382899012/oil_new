<?php
/**
 * Desc:
 * User:  vector
 * Date: 2018/4/23
 * Time: 18:28
 */

namespace ddd\domain\iRepository\contractSettlement;


use ddd\domain\entity\contractSettlement\SaleContractSettlement;
use ddd\Common\Domain\IRepository;

interface ISaleContractSettlementRepository extends IRepository
{
    function submit(SaleContractSettlement $saleContractSettlement);

    function back(SaleContractSettlement $saleContractSettlement);

    function trash(SaleContractSettlement $saleContractSettlement);

    function setSettled(SaleContractSettlement $saleContractSettlement);

    function addAndSaveGoodsAmount(SaleContractSettlement $saleContractSettlement);
}