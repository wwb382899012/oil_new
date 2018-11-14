<?php
/**
 * Desc: 合同拆分仓储trait
 * User: susiehuang
 * Date: 2018/5/28 0028
 * Time: 17:14
 */

namespace ddd\Split\Domain\Model\ContractSplit;

use ddd\infrastructure\DIService;

trait ContractSplitRepository
{
    /**
     * @var IContractSplitRepository
     */
    protected $contractSplitRepository;

    /**
     * @desc 获取合同拆分仓储
     * @return IContractSplitRepository
     * @throws \Exception
     */
    public function getContractSplitRepository()
    {
        if(empty($this->contractSplitRepository)) {
            $this->contractSplitRepository = DIService::getRepository(IContractSplitRepository::class);
        }

        return $this->contractSplitRepository;
    }
}