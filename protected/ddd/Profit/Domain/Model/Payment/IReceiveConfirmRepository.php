<?php
/**
 * User: liyu
 * Date: 2018/8/13
 * Time: 19:23
 * Desc: IReceiveConfirmRepository.php
 */

namespace ddd\Profit\Domain\Model\Payment;


use ddd\Common\Domain\IRepository;

interface IReceiveConfirmRepository extends IRepository
{
    function findByContract($contractId);
}