<?php
/**
 * User: liyu
 * Date: 2018/6/15
 * Time: 10:51
 * Desc: ContractRepository.php
 */

namespace ddd\Split\Domain\Model\Contract;


use ddd\infrastructure\DIService;

trait ContractRepository
{

    private $contractRepository;

    public function getContractRepository() {
        if (empty($this->contractRepository)) {
            $this->contractRepository = DIService::getRepository(IContractRepository::class);
        }
        return $this->contractRepository;
    }
}