<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/5/31 0031
 * Time: 17:39
 */

namespace ddd\Split\Repository\ContractSplit;


use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\domain\entity\value\Quantity;
use ddd\Split\Domain\Model\ContractSplit\ContractSplit;
use ddd\Split\Domain\Model\ContractSplit\IContractSplitRepository;
use ddd\Split\Domain\Model\TradeGoods;

class ContractSplitRepository extends EntityRepository implements IContractSplitRepository{

    public function getNewEntity(){
        return new ContractSplit();
    }

    public function getActiveRecordClassName(){
        return 'ContractSplit';
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
     * @return ContractSplit
     * @throws \Exception
     */
    public function dataToEntity($model){
        $entity = $this->getNewEntity();
        $values = $model->getAttributes();
        unset($values['split_id']);
        $entity->setId($model->split_id);
        $entity->setAttributes($values);
        if(\Utility::isNotEmpty($model->goodsItems)){
            foreach($model->goodsItems as $g){
                $splitGoodsEntity = TradeGoods::create($g->goods_id);
                $splitGoodsEntity->quantity = new Quantity($g->quantity, $g->unit);
                $entity->addGoodsItem($splitGoodsEntity);
            }
        }

        return $entity;
    }

    public function findByNewContractId($newContractId){
       return $this->find('t.new_contract_id = :contract_id ',[':contract_id' => $newContractId]);
    }
}