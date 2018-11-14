<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/19 16:46
 * Describe：
 */

namespace ddd\domain\tRepository\contract;


use ddd\domain\iRepository\contract\IContractStatRepository;
use ddd\infrastructure\DIService;

trait ContractStatRepository
{
    /**
     * @var IContractStatRepository
     */
    protected $contractStatRepository;

    /**
     * 获取合同仓储
     * @return IContractStatRepository
     * @throws \Exception
     */
    protected function getContractStatRepository()
    {
        if (empty($this->contractStatRepository))
        {
            $this->contractStatRepository=DIService::getRepository(IContractStatRepository::class);
        }
        return $this->contractStatRepository;
    }
}