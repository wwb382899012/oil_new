<?php

namespace ddd\Split\Dto\ContractSplit;


use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;

/**
 * 拆分申请列表DTO,提供合同平移列表查看用
 * Class ContractSplitApplyListDTO
 * @package ddd\Split\Dto\ContractSplit
 */
class ContractSplitApplyListDTO extends BaseDTO{
    /**
     * @var OriginContractDTO
     */
    public $origin_contract;

    /**
     * @var array
     */
    public $apply_list = [];

    public function fromEntity(BaseEntity $entity){
        $origin_contract_dto = new OriginContractDTO();
        $origin_contract_dto->fromEntity($entity);
        $this->origin_contract = $origin_contract_dto;

        $this->apply_list = [];

        $applyList = \ContractSplitApply::model()->findALL('contract_id = :contract_id',[':contract_id' => $entity->contract_id]);
        foreach($applyList as $key => & $row){
            $this->apply_list[$key] = $row->getAttributes(true, array_merge(\Utility::getCommonIgnoreAttributes()));
            $this->apply_list[$key]['status_name'] = \Map::getStatusName('contract_split_apply_check_status', $row->status);

            if(\Utility::isEmpty($row->contractSplits)){
                continue;
            }

            foreach($row->contractSplits as $k => $contractSplitModel){
                $this->apply_list[$key]['new_contract'][$k] = [
                    'contract_id' => $contractSplitModel->new_contract_id,
                    'partner_id' => $contractSplitModel->partner_id,
                    'partner_name' => $contractSplitModel->partner->name,
                    'contract_code' => $contractSplitModel->new_contract->contract_code
                ];
            }
        }

    }
}