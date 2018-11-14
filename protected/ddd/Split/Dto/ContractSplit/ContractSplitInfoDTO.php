<?php

namespace ddd\Split\Dto\ContractSplit;

use ddd\Common\Application\BaseDTO;
use ddd\infrastructure\error\ZInvalidArgumentException;
use ddd\Split\Dto\Contract\ContractDTO;

/**
 * 合同拆分信息，编辑、审核、查看详情用
 * Class ContractSplitInfoDTO
 * @package ddd\Split\Dto\ContractSplit
 */
class ContractSplitInfoDTO extends BaseDTO{

    public $origin_contract;

    public $contract_split_apply;

    public function rules(){
        return [
            ["origin_contract", "validateContractDto"],
            ["contract_split_apply", 'validateContractSplitApplyDto'],
        ];
    }

    /**
     * 对DTO进行赋值
     * @param array $params
     * @throws \Exception
     */
    public function assignDTO(array $params){
        if (!isset($params['origin_contract']) || \Utility::isEmpty($params['contract_split_apply'])){
            throw new ZInvalidArgumentException('origin_contract,contract_split_apply');
        }

        $contractDto = new ContractDTO();
        $contractDto->assignDTO($params['origin_contract']);
        $this->origin_contract = $contractDto;

        $applyDto = new ContractSplitApplyDTO();
        $applyDto->assignDTO($params['contract_split_apply']);
        $applyDto->contract_code = $contractDto->contract_code;

        //没有勾选平移操做的bill_ids
        $applyDto->setUnSplitBills($contractDto->getUnSplitBills());
        $this->contract_split_apply = $applyDto;
    }

    public function validateContractDto(){
        $dto = $this->origin_contract;
        if (!$dto->validate()){
            $this->addErrors($dto->getErrors());
        }
    }

    public function validateContractSplitApplyDto(){
        $dto = $this->contract_split_apply;
        if (!$dto->validate()){
            $this->addErrors($dto->getErrors());
        }
    }

}