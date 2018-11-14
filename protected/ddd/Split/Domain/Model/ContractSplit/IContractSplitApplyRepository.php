<?php
/**
 * Desc: 合同平移申请仓储接口
 * User: susiehuang
 * Date: 2018/5/28 0028
 * Time: 17:16
 */

namespace ddd\Split\Domain\Model\ContractSplit;


use ddd\Common\Domain\IRepository;

interface IContractSplitApplyRepository extends IRepository
{
    function submit(ContractSplitApply $contractSplitApply);

    function reject(ContractSplitApply $contractSplitApply);

    function checkPass(ContractSplitApply $contractSplitApply);

    function findByContractId($contractId);

    function findAllByContractId($contractId);
}