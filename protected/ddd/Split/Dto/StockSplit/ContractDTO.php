<?php

namespace ddd\Split\Dto\StockSplit;

use ddd\Common\Domain\BaseEntity;
use ddd\infrastructure\DIService;
use ddd\repository\PartnerRepository;
use ddd\Split\Application\StockSplitService;
use ddd\Split\Domain\Model\Contract\Contract;
use ddd\Split\Domain\Model\Contract\SellContract;
use ddd\Split\Domain\Model\Stock\IStockInRepository;
use ddd\Split\Domain\Model\Stock\IStockOutRepository;
use ddd\Split\Domain\Model\StockSplit\IStockSplitApplyRepository;
use ddd\Split\Dto\BaseContractDTO;
use ddd\Split\Dto\TradeGoodsDTO;

/**
 * 拆分合同实体DTO
 * Class SplitContractDTO
 * @package ddd\Split\Dto
 */
class ContractDTO extends BaseContractDTO{

    public function rules(){
        return [];
    }

    /**
     * 从实体对象生成DTO对象
     * @param Contract $entity
     * @throws \Exception
     */
    public function fromEntityForEditScene(Contract $entity){
        $this->setAttributes($entity->getAttributes());
        if(!empty($entity->partner_id)){
            $partner = PartnerRepository::repository()->findByPk($entity->partner_id);
            $this->partner_name = $partner->name;
        }

        //设置商品
        $this->goods_items = [];
        foreach($entity->goods_items as & $goods_item){
            $tradeGoodsDto = new TradeGoodsDTO();
            $tradeGoodsDto->fromEntity($goods_item);
            $this->goods_items[] = $tradeGoodsDto;
        }

        $stockBillEntities = $entity->getAllStockBills();
        foreach($stockBillEntities as & $stockBillEntity){
            $stock_bill_dto = new StockBillDTO();
            $stock_bill_dto->fromEntity($stockBillEntity);

            $stock_bill_dto->apply_id = 0;
            $stock_bill_dto->status = 0;
            $stock_bill_dto->status_name = '';
            $stock_bill_dto->is_can_view = false;
            $stock_bill_dto->is_can_split = false;
            $stock_bill_dto->is_split = false;

            $this->stock_bill_items[] = $stock_bill_dto;
        }
    }

    public function fromEntityForViewScene(Contract $entity){
        $this->setAttributes($entity->getAttributes());
        if(!empty($entity->partner_id)){
            $partner = PartnerRepository::repository()->findByPk($entity->partner_id);
            $this->partner_name = $partner->name;
        }

        //设置商品
        $this->goods_items = [];
        foreach($entity->goods_items as & $goods_item){
            $tradeGoodsDto = new TradeGoodsDTO();
            $tradeGoodsDto->fromEntity($goods_item);
            $this->goods_items[] = $tradeGoodsDto;
        }

        $stockBillEntities = $entity->getAllStockBills();
        foreach($stockBillEntities as & $stockBillEntity){
            $stock_bill_dto = new StockBillDTO();
            $stock_bill_dto->fromEntity($stockBillEntity);

            $stock_bill_dto->apply_id = 0;
            $stock_bill_dto->status = 0;
            $stock_bill_dto->status_name = '';
            $stock_bill_dto->is_can_view = false;
            $stock_bill_dto->is_can_split = false;
            $stock_bill_dto->is_split = false;

            $this->stock_bill_items[] = $stock_bill_dto;
        }
    }

    /**
     * 从实体对象生成DTO对象
     * @param BaseEntity $entity
     * @throws \Exception
     */
    public function fromEntity(BaseEntity $entity){
        $this->setAttributes($entity->getAttributes());
        if(!empty($entity->partner_id)){
            $partner = PartnerRepository::repository()->findByPk($entity->partner_id);
            $this->partner_name = $partner->name;
        }

        //设置商品
        $this->goods_items = [];
        foreach($entity->goods_items as & $goods_item){
            $tradeGoodsDto = new TradeGoodsDTO();
            $tradeGoodsDto->fromEntity($goods_item);
            $this->goods_items[] = $tradeGoodsDto;
        }

        //获取出入库已经进行拆分申请的数据
        $stock_split_apply_repository = DIService::getRepository(IStockSplitApplyRepository::class);
        $stock_split_applies = $stock_split_apply_repository->findAll('contract_id=' . $entity->contract_id);

        $tmp = $stock_split_applies;
        $stock_split_applies = [];
        foreach($tmp as & $stockBillEntity){
            $stock_split_applies[$stockBillEntity->bill_id] = $stockBillEntity;
        }

        $stock_bill_ids = array_keys($stock_split_applies);
        $stockBillEntities = StockSplitService::service()->getCanSplitStockBillEntitiesForEditScene($entity->isBuyContract(),$entity->contract_id, $stock_bill_ids);

        foreach($stockBillEntities as & $stockBillEntity){
            $stock_bill_dto = new StockBillDTO();
            $stock_bill_dto->fromEntity($stockBillEntity);

            //覆盖属性，改出入库单已经有数据记录
            if(isset($stock_split_applies[$stockBillEntity->bill_id])){
                $stock_split_apply = $stock_split_applies[$stockBillEntity->bill_id];
                $stock_bill_dto->apply_id = $stock_split_apply->apply_id;
                $stock_bill_dto->status = $stock_split_apply->status;
                $stock_bill_dto->status_name = \Map::getStatusName("stock_bill_split_status",$stock_split_apply->status);
                $stock_bill_dto->is_can_split = $stock_split_apply->isCanEdit();
                $stock_bill_dto->is_can_view = ($stock_split_apply->isCanView());
                $stock_bill_dto->is_split = true;
            }else{
                $stock_bill_dto->apply_id = 0;
                $stock_bill_dto->status = 0;
                $stock_bill_dto->status_name = '';
                $stock_bill_dto->is_can_view = false;
                $stock_bill_dto->is_can_split = true;
                $stock_bill_dto->is_split = false;
            }

            $this->stock_bill_items[] = $stock_bill_dto;
        }
    }

    /**
     * 转换成实体对象
     * @return Contract
     */
    public function toEntity(){
        return null;
    }
}