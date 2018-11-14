<?php

/*
 * Created By: yu.li
 * DateTime:2018-5-29 12:03:19.
 * Desc:IContractTerminateRepository
 */

namespace ddd\Split\Domain\Model\Contract;

use ddd\Common\Domain\IRepository;

interface IContractTerminateRepository extends IRepository
{

    public function submit(ContractTerminate $contractTerminate);

    public function checkPass(ContractTerminate $contractTerminate);

    public function checkBack(ContractTerminate $contractTerminate);

    public function findByContractId($contractId);
}
