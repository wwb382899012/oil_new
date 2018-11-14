<?php

namespace ddd\Split\Domain\Model\ContractSplit;

use ddd\infrastructure\DIService;

trait ContractStockSplitRepository
{
    /**
     * @var IContractSplitApplyRepository
     */
    protected $contractStockSplitRepository;

    /**
     * @desc
     * @return IContractStockSplitRepository
     * @throws \Exception
     */
    public function getContractSplitApplyRepository()
    {
        if(empty($this->contractStockSplitRepository)) {
            $this->contractStockSplitRepository = DIService::getRepository(IContractStockSplitRepository::class);
        }

        return $this->contractStockSplitRepository;
    }
}