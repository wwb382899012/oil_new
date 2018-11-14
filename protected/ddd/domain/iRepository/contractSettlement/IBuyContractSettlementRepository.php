<?php
/**
 * Desc:
 * User:  vector
 * Date: 2018/4/23
 * Time: 17:31
 */

namespace ddd\domain\iRepository\contractSettlement;


use ddd\domain\entity\contractSettlement\BuyContractSettlement;
use ddd\Common\Domain\IRepository;

interface IBuyContractSettlementRepository extends IRepository
{
    function submit(BuyContractSettlement $buyContractSettlement);

    function back(BuyContractSettlement $buyContractSettlement);

    function trash(BuyContractSettlement $buyContractSettlement);

    function setSettled(BuyContractSettlement $buyContractSettlement);

    function addAndSaveGoodsAmount(BuyContractSettlement $buyContractSettlement);
}