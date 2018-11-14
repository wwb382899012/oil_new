<?php

namespace ddd\Split\Dto\Contract;

use ddd\Common\Domain\BaseEntity;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ZInvalidArgumentException;
use ddd\repository\PartnerRepository;
use ddd\Split\Domain\Model\Contract\BuyContract;
use ddd\Split\Domain\Model\Contract\Contract;
use ddd\Split\Domain\Model\Contract\SellContract;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApplyEnum;
use ddd\Split\Domain\Model\Stock\IStockInRepository;
use ddd\Split\Domain\Model\Stock\IStockOutRepository;
use ddd\Split\Dto\BaseContractDTO;
use ddd\Split\Dto\ContractSplit\StockBillDTO;
use ddd\Split\Dto\TradeGoodsDTO;

/**
 * 原合同、拆分合同实体DTO
 * Class SplitContractDTO
 * @package ddd\Split\Dto
 */
class ContractDTO extends BaseContractDTO{

    public function rules(){
        return [];
    }

    public function assignDTO(array $params):void{
        if(!isset($params['contract_id']) || !\Utility::checkQueryId($params['contract_id'])){
            throw new ZInvalidArgumentException('contract_id');
        }

        $this->setAttributes($params);
    }

    public function fromEntityForEditScene(Contract & $entity):void{
        $this->fromEntity($entity);
    }

    public function fromEntity(BaseEntity $entity):void{
        $this->setAttributes($entity->getAttributes());
        $this->goods_items = [];
        $this->stock_bill_items = [];

        if(!empty($entity->partner_id)){
            $partner = PartnerRepository::repository()->findByPk($entity->partner_id);
            $this->partner_name = $partner->name;
        }

        //
        $goods_items = [];
        if(\Utility::isNotEmpty($entity->goods_items)){
            foreach($entity->goods_items as $k => $goods){
                $tradeGoodsDto = new TradeGoodsDTO();
                $tradeGoodsDto->fromEntity($goods);
                $goods_items[] = $tradeGoodsDto;
            }
        }
        $this->goods_items = $goods_items;

        //
        $stock_bill_entity_arr = $entity->getAllStockBills();
        if(\Utility::isEmpty($stock_bill_entity_arr)){
            return;
        }

        foreach($stock_bill_entity_arr as & $item){
            $stock_bill_dto = new StockBillDTO();
            $stock_bill_dto->fromEntity($item);

            $this->stock_bill_items[] = $stock_bill_dto;
        }
    }

    /**
     * 转换成实体对象
     */
    public function toEntity(){
        return null;
    }

    /**
     * 获取未勾选拆分的出入单ids, [bill_id] = bill_id
     * @return array
     */
    public function getUnSplitBills(){
        $bill_ids = [];
        foreach($this->stock_bill_items as $stock_bill_item){
            if(isset($stock_bill_item['is_split']) && ContractSplitApplyEnum::STATUS_UN_SPLIT == $stock_bill_item['is_split']){
                $bill_ids[$stock_bill_item['bill_id']] = $stock_bill_item['bill_id'];
            }
        }
        return $bill_ids;
    }
}