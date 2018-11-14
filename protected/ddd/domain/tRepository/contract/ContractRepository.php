<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/19 16:41
 * Describe：
 */

namespace ddd\domain\tRepository\contract;


use ddd\domain\iRepository\contract\IContractRepository;
use ddd\infrastructure\DIService;

trait ContractRepository
{
    /**
     * @var IContractRepository
     */
    protected $contractRepository;

    /**
     * 获取合同仓储
     * @return IContractRepository
     * @throws \Exception
     */
    protected function getContractRepository()
    {
        if (empty($this->contractRepository))
        {
            $this->contractRepository=DIService::getRepository(IContractRepository::class);
        }
        return $this->contractRepository;
    }
}