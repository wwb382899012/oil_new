<?php

namespace ddd\Split\Dto\StockSplit;

use ddd\Common\Domain\BaseEntity;
use ddd\infrastructure\DIService;
use ddd\repository\PartnerRepository;
use ddd\Split\Domain\Model\Contract\Contract;
use ddd\Split\Domain\Model\Contract\SellContract;
use ddd\Split\Domain\Model\Stock\IStockInRepository;
use ddd\Split\Domain\Model\Stock\IStockOutRepository;
use ddd\Split\Domain\Model\StockSplit\IStockSplitApplyDetailRepository;
use ddd\Split\Dto\BaseContractDTO;
use ddd\Split\Dto\TradeGoodsDTO;

/**
 * 拆分合同实体DTO
 * Class SplitContractDTO
 * @package ddd\Split\Dto
 */
class ContractSplitDTO extends BaseContractDTO{

    /**
     * @var Contract
     */
    public $new_contract;

    /**
     * 关联的出入库单数组
     * @return array StockBillDTO
     */
    public $stock_bill_items = [];

    public function rules(){
        return [];
    }

    /**
     * 从实体对象生成DTO对象
     * @param BaseEntity $entity
     * @throws \Exception
     */
    public function fromEntityForEditScene(BaseEntity & $entity){
        $this->setAttributes($entity->getAttributes());
        if(!empty($entity->partner_id)){
            $partner = PartnerRepository::repository()->findByPk($entity->partner_id);
            $this->partner_name = $partner->name;
        }

        //设置新合同id、新合同编号
        $this->contract_id = '';
        $this->contract_code = '';
        $this->new_contract = [
            "contract_id"=> $entity->contract_id,
            "contract_code"=> $entity->contract_code,
        ];

        //设置商品
        $this->goods_items = [];
        foreach($entity->goods_items as & $goods_item){
            $tradeGoodsDto = new TradeGoodsDTO();
            $tradeGoodsDto->fromEntity($goods_item);
            $this->goods_items[] = $tradeGoodsDto;
        }

        $stockBillEntities = $entity->getAllStockBills();
        foreach($stockBillEntities as & $stockBillEntity){
            $stock_bill_dto = new StockSplitBillDTO();
            $stock_bill_dto->fromEntity($stockBillEntity);

            $this->stock_bill_items[] = $stock_bill_dto;
        }
    }


    /**
     * 从实体对象生成DTO对象
     * @param BaseEntity $entity
     * @throws \Exception
     */
    public function fromEntity(BaseEntity $entity){
        $is_sell_contract = ($entity instanceof SellContract);

        $this->setAttributes($entity->getAttributes());
        if(!empty($entity->partner_id)){
            $partner = PartnerRepository::repository()->findByPk($entity->partner_id);
            $this->partner_name = $partner->name;
        }

        //设置新合同id、新合同编号
        $this->contract_id = '';
        $this->contract_code = '';
        $this->new_contract = [
            "contract_id"=> $entity->contract_id,
            "contract_code"=> $entity->contract_code,
        ];

        //设置商品
        $this->goods_items = [];
        foreach($entity->goods_items as & $goods_item){
            $tradeGoodsDto = new TradeGoodsDTO();
            $tradeGoodsDto->fromEntity($goods_item);
            $this->goods_items[] = $tradeGoodsDto;
        }

        $where = "t.contract_id=:contract_id";
        //销售合同
        if($is_sell_contract){
            $stock_out_repository = DIService::getRepository(IStockOutRepository::class);
            $stock_bill_entity_arr = $stock_out_repository->findAll($where, [':contract_id' => $entity->contract_id]);
        }else{
            $stock_in_repository = DIService::getRepository(IStockInRepository::class);
            $stock_bill_entity_arr = $stock_in_repository->findAll($where, [':contract_id' => $entity->contract_id]);
        }

        //获取出入库已经进行拆分申请的数据
        $stock_split_apply_detail_repository = DIService::getRepository(IStockSplitApplyDetailRepository::class);
        $stock_split_apply_details = $stock_split_apply_detail_repository->findAll('contract_id=' . $entity->contract_id);

        $stock_split_apply_details_x = [];
        foreach($stock_split_apply_details as & $item){
            $stock_split_apply_details_x[$item->bill_id] = $item;
        }

        $id_exits = [];
        foreach($stock_bill_entity_arr as & $item){
            $stock_bill_dto = new StockSplitBillDTO();
            $stock_bill_dto->fromEntity($item);

            //覆盖属性
            if(isset($stock_split_apply_details_x[$item->original_id])){
                $stock_bill_dto->apply_id = $stock_split_apply_details_x[$item->original_id]->apply_id;
            }

            $this->stock_bill_items[] = $stock_bill_dto;

            $id_exits[$stock_bill_dto->apply_id] = $stock_bill_dto->apply_id;
        }

        //设置未审核的明细
        foreach($stock_split_apply_details as & $item){
            if(isset($id_exits[$item->apply_id])){
                continue;
            }

            $stock_bill_dto = new StockSplitBillDTO();
            $stock_bill_dto->apply_id = $item->apply_id;
            $stock_bill_dto->bill_id = $item->bill_id;
            $stock_bill_dto->bill_code = '';

            $goods_items = [];
            foreach($item->getGoodsItems() as & $goods_item){
                $tradeGoodsDto = new TradeGoodsDTO();
                $tradeGoodsDto->fromEntity($goods_item);
                $goods_items[] = $tradeGoodsDto;
            }
            $stock_bill_dto->goods_items = $goods_items;

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