<?php
/**
 * Created by: yu.li
 * Date: 2018/6/1
 * Time: 17:45
 * Desc: StockInRepository
 */

namespace ddd\Split\Repository\Stock;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\domain\entity\value\Quantity;
use ddd\Split\Domain\Model\SplitEnum;
use ddd\Split\Domain\Model\Stock\StockIn;
use ddd\Split\Domain\Model\TradeGoods;

class StockInRepository extends EntityRepository{

    public function init(){
        $this->with = array("details");
    }

    /**
     * 获取新的实体对象
     * @return BaseEntity|StockIn
     * @throws \Exception
     */
    public function getNewEntity(){
        return new StockIn();
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName(){
        return "StockIn";
    }

    /**
     * 数据模型转换成业务对象
     * @param $model
     * @return \ddd\Common\Domain\BaseEntity|StockIn
     * @throws \Exception
     */
    public function dataToEntity($model){
        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes(), false);
        $entity->bill_id = $model->stock_in_id;
        $entity->bill_code = $model->code;
        $entity->is_virtual = (SplitEnum::IS_VIRTUAL == $model->is_virtual);
        $entity->clearGoodsItems();

        if(is_array($model->details)){
            foreach($model->details as & $row){
                $item = new TradeGoods();
                $item->goods_id = $row->goods_id;
                $item->quantity = new Quantity($row->quantity, $row->unit);
                $entity->addGoodsItem($item);
            }
        }

        return $entity;
    }

    public function store(IAggregateRoot $entity){
        ;
        $id = $entity->getId();
        if(!empty($id)){
            $model = $this->model()->findByPk($id);
            if(empty($model)){
                throw new ZModelNotExistsException($id, $this->getActiveRecordClassName());
            }
        }else{
            $this->activeRecordClassName = $this->getActiveRecordClassName();
            $model = new $this->activeRecordClassName;
        }

        $model->setAttributes($entity->getAttributes());

        if(!$model->save()){
            throw new ZModelSaveFalseException($model);
        }

        return $entity;
    }

    /**
     * 查询合同下所有的入库单
     * @param batchId
     * @return StockIn
     */
    public function findAllByContractId($contractId){
        $condition = "t.contract_id=".$contractId;

        return $this->findAll($condition);
    }
}