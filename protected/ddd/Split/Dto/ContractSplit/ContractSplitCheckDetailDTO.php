<?php

namespace ddd\Split\Dto\ContractSplit;

use ddd\Common\Application\BaseDTO;
use ddd\infrastructure\error\ZInvalidArgumentException;
use ddd\Split\Dto\Contract\ContractDTO;


class ContractSplitCheckDetailDTO extends BaseDTO{

    public $origin_contract;

    public $contract_split_apply;

    public $check_id;

    public $remark;

    public $status;

    public $status_name;

    public $detail=[];

    public function rules(){
        return [
//            ["origin_contract", "validateContractDto"],
//            ["contract_split_apply", 'validateContractSplitApplyDto'],
        ];
    }

    /**
     * 对DTO进行赋值
     * @param array $params
     * @throws \Exception
     */
    public function assignDTO(array $params){

    }

}