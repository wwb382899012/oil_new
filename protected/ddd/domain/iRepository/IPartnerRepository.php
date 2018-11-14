<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/4/16 0016
 * Time: 16:41
 */

namespace ddd\domain\iRepository;


use ddd\Common\Domain\IRepository;
use ddd\domain\entity\Partner;

interface IPartnerRepository extends IRepository
{
    function submit(Partner $stockOut);

    function checkBack(Partner $stockOut);

    function checkPass(Partner $stockOut);
}