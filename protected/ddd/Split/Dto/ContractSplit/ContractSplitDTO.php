<?php

namespace ddd\Split\Dto\ContractSplit;

use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\domain\iRepository\IPartnerRepository;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\Split\Domain\Model\Contract\IContractRepository;
use ddd\Split\Domain\Model\ContractSplit\ContractSplit;
use ddd\Split\Dto\Contract\ContractDTO;
use ddd\Split\Dto\TradeGoodsDTO;

/**
 * 合同拆分DTO，挂载到ContractSplitDTO下面
 * Class ContractSplitDTO
 * @package ddd\Split\Dto\ContractSplit
 */
class ContractSplitDTO extends BaseDTO{
    /**
     * id
     * @var int
     */
    public $split_id;

    /**
     * 合作方id
     * @var int
     */
    public $partner_id;

    /**
     * 合作方名称
     * @var string
     */
    public $partner_name;

    /**
     * 商品明细
     * @var array
     */
    public $goods_items = [];

    /**
     * 出入库拆分
     * @var   array
     */
    public $stock_bill_items;

    /**
     * 拆分生成的新合同信息
     * @var ContractDTO
     */
    public $new_contract;

    private $tmp_split_id;

    /**
     * @return mixed
     */
    public function getTmpSplitId(){
        return $this->tmp_split_id;
    }

    /**
     * @param mixed $tmp_split_id
     */
    public function setTmpSplitId($tmp_split_id):void{
        $this->tmp_split_id = $tmp_split_id;
    }

    public function rules(){
        return [['partner_id', 'required', 'message' => '请选择合作方'],];
    }

    /**
     * 从实体对象生成DTO对象
     * @param   BaseEntity $entity
     * @throws  \Exception
     */
    public function fromEntity(BaseEntity $entity){
        $this->split_id = $entity->split_id;
        $this->partner_id = $entity->partner_id;
        if(!empty($entity->new_contract_id)){
            $newContract = DIService::getRepository(IContractRepository::class)->findByPk($entity->new_contract_id);
            if(empty($newContract)){
                throw new ZEntityNotExistsException($entity->new_contract_id, 'Contract');
            }

            $newContract->split_type = 1;
            $contractDto = new ContractDTO();
            $contractDto->fromEntity($newContract);
            $this->new_contract = $contractDto;
        }

        $partnerEntity = DIService::getRepository(IPartnerRepository::class)->findByPk($entity->partner_id);
        if(empty($partnerEntity)){
            throw new ZEntityNotExistsException($entity->partner_id, 'Partner');
        }

        $this->partner_name = $partnerEntity->name;

        if(\Utility::isNotEmpty($entity->goods_items)){
            foreach($entity->goods_items as & $g){
                $tradeGoodsDto = new TradeGoodsDTO();
                $tradeGoodsDto->fromEntity($g);
                $this->goods_items[] = $tradeGoodsDto;
            }
        }
    }

    /**
     * 转换成实体对象
     * @return ContractSplit
     * @throws \Exception
     */
    public function toEntity(){
        $entity = new ContractSplit();
        $values = $this->getAttributes();
        unset($values['goods_items']);
        $entity->setAttributes($values);
        $entity->setId($this->split_id);
        $entity->new_contract_id = $this->new_contract->contract_id;
        if(\Utility::isNotEmpty($this->goods_items)){
            foreach($this->goods_items as $g){
                $entity->addGoodsItem($g->toEntity());
            }
        }

        return $entity;
    }
}