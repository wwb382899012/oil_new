<?php

namespace ddd\Split\Dto\ContractSplit;

use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApplyEnum;
use ddd\Split\Domain\Model\ContractSplit\StockSplitDetail;
use ddd\Split\Domain\Model\Stock\IStockInRepository;
use ddd\Split\Domain\Model\Stock\IStockOutRepository;
use ddd\Split\Dto\TradeGoodsDTO;

/**
 * 出入库拆分明细dto
 * Class StockSplitDetailDTO
 * @package ddd\Split\Dto\ContractSplit
 */
class StockSplitDetailDTO extends BaseDTO{

    /**
     * 出入库id
     * @var   int
     */
    public $bill_id;

    /**
     * 出入库编号
     * @var   string
     */
    public $bill_code;

    /**
     * 商品明细
     * @var   array
     */
    public $goods_items;

    /**
     * 新出入库
     * @var   int
     */
    public $new_stock_bill;

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
        return [];
    }

    /**
     * 从实体对象生成DTO对象
     * @param   BaseEntity $entity
     * @throws  \Exception
     */
    public function fromEntity(BaseEntity $entity){
        if(!empty($entity->bill_id)){
            if($entity->type == ContractSplitApplyEnum::STOCK_TYPE_IN){
                $oldStockBill = DIService::getRepository(IStockInRepository::class)->findByPk($entity->bill_id);
                if(empty($oldStockBill)){
                    throw new ZEntityNotExistsException($entity->bill_id, 'StockIn');
                }
            }else{
                $oldStockBill = DIService::getRepository(IStockOutRepository::class)->findByPk($entity->bill_id);
                if(empty($oldStockBill)){
                    throw new ZEntityNotExistsException($entity->bill_id, 'StockOut');
                }
            }
            $this->bill_id = $entity->bill_id;
            $this->bill_code = $oldStockBill->bill_code;
        }

        if(\Utility::isNotEmpty($entity->goods_items)){
            foreach($entity->goods_items as $g){
                $tradeGoodsDto = new TradeGoodsDTO();
                $tradeGoodsDto->fromEntity($g);
                $this->goods_items[] = $tradeGoodsDto;
            }
        }

        //新拆分出来的合同
        if(!empty($entity->new_bill_id)){

            if($entity->type == ContractSplitApplyEnum::STOCK_TYPE_IN){
                $newStockBill = DIService::getRepository(IStockInRepository::class)->findByPk($entity->new_bill_id);
                if(empty($newStockBill)){
                    throw new ZEntityNotExistsException($entity->new_bill_id, 'StockIn');
                }
            }else{
                $newStockBill = DIService::getRepository(IStockOutRepository::class)->findByPk($entity->new_bill_id);
                if(empty($newStockBill)){
                    throw new ZEntityNotExistsException($entity->new_bill_id, 'StockOut');
                }
            }

            $newStockBillDto = new StockBillDTO();
            $newStockBillDto->fromEntity($newStockBill);
            $this->new_stock_bill = $newStockBillDto;
        }
    }

    /**
     * 转换成实体对象
     * @return StockSplitDetail
     * @throws \Exception
     */
    public function toEntity(){
        $entity = new StockSplitDetail();
        $entity->bill_id = $this->new_stock_bill->bill_id;
        if(\Utility::isNotEmpty($this->goods_items)){
            foreach($this->goods_items as $g){
                $entity->addSplitGoodsItem($g->toEntity());
            }
        }

        return $entity;
    }
}