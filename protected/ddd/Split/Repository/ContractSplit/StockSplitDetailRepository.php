<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/5/31 0031
 * Time: 20:01
 */

namespace ddd\Split\Repository\ContractSplit;


use ddd\Common\Domain\Value\Quantity;
use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApplyEnum;
use ddd\Split\Domain\Model\ContractSplit\IStockSplitDetailRepository;
use ddd\Split\Domain\Model\ContractSplit\StockSplitDetail;
use ddd\Split\Domain\Model\TradeGoods;

class StockSplitDetailRepository extends EntityRepository implements IStockSplitDetailRepository{

    public function getNewEntity(){
        return new StockSplitDetail();
    }

    public function getActiveRecordClassName(){
        return 'ContractStockSplitDetail';
    }

    public function init(){
        $this->with = array('stockSplit', 'contractSplit');
    }

    /**
     * 把对象持久化到数据
     * @param IAggregateRoot $entity
     * @throws \Exception
     */
    public function store(IAggregateRoot $entity){
        //保存合同平移
        $id = $entity->getId();
        if(!empty($id)){
            $model = $this->model()->with($this->with)->findByPk($id);
            if(empty($model)){
                ExceptionService::throwModelDataNotExistsException($id, $this->getActiveRecordClassName());
            }
        }else{
            $this->activeRecordClassName = $this->getActiveRecordClassName();
            $model = new $this->activeRecordClassName;
        }
        $values = $entity->getAttributes();
        $values = \Utility::unsetCommonIgnoreAttributes($values);
        $model->setAttributes($values);

        if(!$model->save()){
            throw new ZModelSaveFalseException($model);
        }
    }

    /**
     * 数据模型转换成业务对象
     * @param $model
     * @return StockSplitDetail
     * @throws \Exception
     */
    public function dataToEntity($model){
        $entity = $this->getNewEntity();
        $values = $model->getAttributes();
        unset($values['split_detail_id']);
        $entity->setId($model->split_detail_id);
        $entity->setAttributes($values);
        $entity->bill_id = $values['bill_id'];
        if($model->contractSplit->contractSplitApply->contract->type == ContractSplitApplyEnum::CONTRACT_TYPE_BUY){
            $entity->type = ContractSplitApplyEnum::STOCK_TYPE_IN;
        }else{
            $entity->type = ContractSplitApplyEnum::STOCK_TYPE_OUT;
        }
        if(\Utility::isNotEmpty($model->items)){
            foreach($model->items as $g){
                $splitGoodsEntity = TradeGoods::create($g->goods_id);
                $splitGoodsEntity->quantity = new Quantity($g->quantity, $g->unit);
                $entity->addSplitGoodsItem($splitGoodsEntity);
            }
        }

        return $entity;
    }

    /**
     * @desc 获取合同拆分下的出入库拆分明细
     * @param  int $splitId
     * @return array
     */
    public function findBySplitId($splitId){
        return $this->findAll('split_id=:splitId order by create_time desc', array('splitId' => $splitId));
    }

    public function updateNewBillId($id,$billId){
        return \ContractStockSplitDetail::model()->updateByPk($id,['new_bill_id'=>$billId]);
    }
}