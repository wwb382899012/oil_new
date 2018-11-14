<?php
/**
 * Desc: 出入库拆分仓储
 * User: susiehuang
 * Date: 2018/5/31 0031
 * Time: 19:41
 */

namespace ddd\Split\Repository\ContractSplit;


use ddd\Common\Repository\EntityRepository;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApplyEnum;
use ddd\Split\Domain\Model\ContractSplit\StockSplit;
use ddd\Split\Domain\Model\Stock\IStockInRepository;
use ddd\Split\Domain\Model\Stock\IStockOutRepository;

class StockSplitRepository extends EntityRepository{
    protected $stockSplitDetailRepository;

    public function init(){
        parent::init();
        $this->stockSplitDetailRepository = new StockSplitDetailRepository();
    }

    public function getNewEntity(){
        return new StockSplit();
    }

    public function getActiveRecordClassName(){
        return 'ContractStockSplit';
    }

    /**
     * 数据模型转换成业务对象
     * @param $model
     * @return StockSplit
     * @throws \Exception
     */
    public function dataToEntity($model){
        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes());
        $entity->setId($model->bill_id);

        if($model->type == ContractSplitApplyEnum::STOCK_TYPE_IN){
            $stockBillEntity = DIService::getRepository(IStockInRepository::class)->findByPk($model->bill_id);
            if(empty($stockBillEntity)){
                throw new ZEntityNotExistsException($model->bill_id, 'StockIn');
            }
        }else{
            $stockBillEntity = DIService::getRepository(IStockOutRepository::class)->findByPk($model->bill_id);
            if(empty($stockBillEntity)){
                throw new ZEntityNotExistsException($model->bill_id, 'StockOut');
            }
        }

        $entity->bill_code = $stockBillEntity->bill_code;

        $entity->initBalanceGoods($stockBillEntity);

        if(\Utility::isNotEmpty($model->splitDetails)){
            foreach($model->splitDetails as $detail){
                $detailEntity = $this->stockSplitDetailRepository->dataToEntity($detail);
                $entity->addSplitDetail($detailEntity, $detail->split_id, false);
            }
        }

        return $entity;
    }

}