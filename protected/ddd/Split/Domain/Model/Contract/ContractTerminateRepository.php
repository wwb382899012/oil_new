<?php

/*
 * Created By: yu.li
 * DateTime:2018-5-29 12:03:19.
 * Desc:TerminateRepository
 */

namespace ddd\Split\Domain\Model\Contract;

use ddd\infrastructure\DIService;

trait ContractTerminateRepository
{

    private $contractTerminateRepository;

    public function getContractTerminateRepository()
    {
        if (empty($this->contractTerminateRepository)) {
            $this->contractTerminateRepository = DIService::getRepository(IContractTerminateRepository::class);
        }
        return $this->contractTerminateRepository;
    }

}
