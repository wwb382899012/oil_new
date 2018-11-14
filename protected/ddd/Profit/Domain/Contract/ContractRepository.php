<?php
/**
 * Desc: 合同仓储trait
 * User: vector
 * Date: 2018/8/28
 * Time: 11:13
 */

namespace ddd\Profit\Domain\Contract;

use ddd\infrastructure\DIService;


trait ContractRepository
{
    /**
     * @var IContractRepository
     */
    protected $contractRepository;

    /**
     * @desc 获取合同仓储
     * @return IContractRepository
     * @throws \Exception
     */
    protected function getContractRepository()
    {
        if(empty($this->contractRepository)) {
            $this->contractRepository = DIService::getRepository(IContractRepository::class);
        }

        return $this->contractRepository;
    }
}