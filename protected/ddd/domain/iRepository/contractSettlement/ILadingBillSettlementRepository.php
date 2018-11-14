<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/10 15:27
 * Describe：
 */

namespace ddd\domain\iRepository\contractSettlement;


use ddd\domain\entity\contractSettlement\LadingBillSettlement;
use ddd\Common\Domain\IRepository;

interface ILadingBillSettlementRepository extends IRepository
{

    function submit(LadingBillSettlement $ladingBillSettlement);

    function back(LadingBillSettlement $ladingBillSettlement);

    function trash(LadingBillSettlement $ladingBillSettlement);

    function setSettled(LadingBillSettlement $ladingBillSettlement);

}