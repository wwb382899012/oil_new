<?php
/**
 * Desc: 合同拆分仓储trait
 * User: susiehuang
 * Date: 2018/5/28 0028
 * Time: 17:14
 */

namespace ddd\Split\Domain\Model\ContractSplit;

use ddd\infrastructure\DIService;

trait ContractSplitApplyRepository
{
    /**
     * @var IContractSplitApplyRepository
     */
    protected $contractSplitApplyRepository;

    /**
     * @desc 获取合同拆分申请仓储
     * @return IContractSplitApplyRepository
     * @throws \Exception
     */
    public function getContractSplitApplyRepository()
    {
        if(empty($this->contractSplitApplyRepository)) {
            $this->contractSplitApplyRepository = DIService::getRepository(IContractSplitApplyRepository::class);
        }

        return $this->contractSplitApplyRepository;
    }
}