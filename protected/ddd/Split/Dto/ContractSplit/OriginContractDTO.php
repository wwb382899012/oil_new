<?php

namespace ddd\Split\Dto\ContractSplit;

use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;

/**
 * 原合同DTO
 * Class OriginContractDTO
 * @package ddd\Split\Dto\ContractSplit
 */
class OriginContractDTO extends BaseDTO{

    public $contract_id = 0;

    public $contract_code = '';

    public $type = 0;

    public $type_name = '';

    public $project_id = 0 ;

    public $project_code = '';

    public $partner_id = 0 ;

    public $partner_name = '';

    public $corporation_id = 0 ;

    public $corp_name = '';

    /**
     * 由实体对象赋值
     * @param BaseEntity $entity
     * @throws \CDbException
     * @throws \CException
     */
    public function fromEntity(BaseEntity $entity){
        $contract = \Contract::model()->findByPk($entity->contract_id);

        $this->setAttributes($entity->getAttributes());
        $this->type_name = \Map::getStatusName('buy_sell_type',$entity->type);
        $this->project_code = $contract->project->project_code;
        $this->partner_name = $contract->partner->name;
        $this->corp_name = $contract->corporation->name;
    }
}