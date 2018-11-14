<?php
/**
 * Desc:
 * User:  vector
 * Date: 2018/4/24
 * Time: 10:02
 */

namespace ddd\domain\iRepository\stock;


use ddd\domain\entity\stock\LadingBill;
use ddd\Common\Domain\IRepository;

interface ILadingBillRepository extends IRepository
{
    function submit(LadingBill $ladingBill);

    function setSettledBack(LadingBill $ladingBill);

    function setOnSettling(LadingBill $ladingBill);

    function setSettled(LadingBill $ladingBill);

}
