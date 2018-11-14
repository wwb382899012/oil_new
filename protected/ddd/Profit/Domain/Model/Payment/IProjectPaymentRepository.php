<?php
/**
 * User: liyu
 * Date: 2018/8/23
 * Time: 18:35
 * Desc: IProjectPaymentRepository.phpsitory.php
 */

namespace ddd\Profit\Domain\Model\Payment;


use ddd\Common\Domain\IRepository;

interface IProjectPaymentRepository extends IRepository
{
    function findByProjectId($projectId);
}