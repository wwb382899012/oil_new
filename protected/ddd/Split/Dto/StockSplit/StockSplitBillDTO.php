<?php

/**
 *
 */
namespace ddd\Split\Dto\StockSplit;

use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\infrastructure\DIService;
use ddd\Split\Domain\Model\Stock\IStockInRepository;
use ddd\Split\Domain\Model\Stock\IStockOutRepository;
use ddd\Split\Domain\Model\Stock\StockOut;
use ddd\Split\Domain\Model\StockSplit\StockSplitDetail;
use ddd\Split\Domain\Model\StockSplit\StockSplitEnum;
use ddd\Split\Dto\TradeGoodsDTO;

/**
 * 出入库实体DTO
 * Class StockBillSplitDTO
 * @package ddd\Split\Dto
 */
class StockSplitBillDTO extends BaseDTO{

    /**
     * 申请标识
     * @var
     */
    public $apply_id = 0;

    /**
     * 出入库id
     * @var big integer
     */
    public $bill_id;

    /**
     * 出入库单号
     * @var string
     */
    public $bill_code;

    /**
     * 商品明细
     * @var array TradeGoodsDTO
     */
    public $goods_items = [];

    public $new_stock_bill = [
        "bill_id"=> 0,
        "bill_code"=> ""
    ];

    public function rules(){
        return [];
    }

    public function fromEntityForEditScene(StockSplitDetail & $stockSplitDetailEntity):void{
        $this->setAttributes($stockSplitDetailEntity->getAttributes());
        $this->goods_items = [];

        if(StockSplitEnum::TYPE_STOCK_IN == $stockSplitDetailEntity->type){
            $originStockBillEntity = DIService::getRepository(IStockInRepository::class)->findByPk($stockSplitDetailEntity->bill_id);
        }else{
            $originStockBillEntity = DIService::getRepository(IStockOutRepository::class)->findByPk($stockSplitDetailEntity->bill_id);
        }
        //设置来源出入库单编号
        $this->bill_code = $originStockBillEntity->bill_code;

        if(!empty($stockSplitDetailEntity->new_bill_id)){
            if(StockSplitEnum::TYPE_STOCK_IN == $stockSplitDetailEntity->type){
                $splitStockBillEntity = DIService::getRepository(IStockInRepository::class)->findByPk($stockSplitDetailEntity->bill_id);
            }else{
                $splitStockBillEntity = DIService::getRepository(IStockOutRepository::class)->findByPk($stockSplitDetailEntity->bill_id);
            }

            $newStockBillDto = new StockBillDTO();
            $newStockBillDto->fromEntity($splitStockBillEntity);
            $this->new_stock_bill = $newStockBillDto;
        }

        if(\Utility::isNotEmpty($stockSplitDetailEntity->getGoodsItems())){
            foreach($stockSplitDetailEntity->getGoodsItems() as $tradeGoodsEntity){
                $tradeGoodsDto = new TradeGoodsDTO();
                $tradeGoodsDto->fromEntity($tradeGoodsEntity);
                $this->goods_items[] = $tradeGoodsDto;
            }
        }
    }

    /**
     * 从实体对象生成DTO对象
     * @param BaseEntity $entity
     * @throws \Exception
     */
    public function fromEntity(BaseEntity $entity){
        $this->setAttributes($entity->getAttributes());

        $is_sell_contract = ($entity instanceof StockOut);
        if($is_sell_contract){
            $stock_out_repository = DIService::getRepository(IStockOutRepository::class);
            $original_stock_bill_entity = $stock_out_repository->findByPk($entity->original_id);
        }else{
            $stock_in_repository = DIService::getRepository(IStockInRepository::class);
            $original_stock_bill_entity = $stock_in_repository->findByPk($entity->original_id);
        }
        //设置来源出入库单id、编号
        $this->bill_id = $original_stock_bill_entity->bill_id;
        $this->bill_code = $original_stock_bill_entity->bill_code;
        $this->new_stock_bill = [
            "bill_id"=> $entity->bill_id,
            "bill_code"=> $entity->bill_code,
        ];

        //设置商品
        $this->goods_items = [];
        foreach($entity->items as & $goods_item){
            $tradeGoodsDto = new TradeGoodsDTO();
            $tradeGoodsDto->fromEntity($goods_item);
            $this->goods_items[] = $tradeGoodsDto;
        }
    }

    /**
     * 转换成实体对象
     */
    public function toEntity(){
        //nobody
    }
}