<?php
/**
 * Desc: 收款认领
 * User: susiehuang
 * Date: 2018/4/16 0016
 * Time: 16:41
 */

namespace ddd\domain\iRepository\payment;


use ddd\domain\entity\payment\PayClaim;
use ddd\Common\Domain\IRepository;

interface IPayClaimRepository extends IRepository
{
    function submit(PayClaim $payClaim);
}