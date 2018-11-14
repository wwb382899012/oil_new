<?php
/**
 * Desc: 收款认领
 * User: susiehuang
 * Date: 2018/4/16 0016
 * Time: 16:41
 */

namespace ddd\domain\iRepository\receipt;


use ddd\domain\entity\receipt\ReceiptClaim;
use ddd\Common\Domain\IRepository;

interface IReceiptClaimRepository extends IRepository
{
    function submit(ReceiptClaim $receiptClaim);
}