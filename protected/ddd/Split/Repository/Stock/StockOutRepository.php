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
use ddd\infrastructure\error\ZModelNotExistsException;
use ddd\Split\Domain\Model\SplitEnum;
use ddd\Split\Domain\Model\Stock\StockOut;
use ddd\Split\Domain\Model\TradeGoods;

class StockOutRepository extends EntityRepository{


    public function init(){
        $this->with = array('details', "deliveryOrder");
    }

    /**
     * 获取新的实体对象
     * @return BaseEntity|StockOut
     * @throws \Exception
     */
    public function getNewEntity(){
        return new StockOut();
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName(){
        return "StockOutOrder";
    }

    /**
     * 数据模型转换成业务对象
     * @param $model
     * @return Project|Entity
     * @throws \Exception
     */
    public function dataToEntity($model){
        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes(), false);
        $entity->bill_id = $model->out_order_id;
        $entity->bill_code = $model->code;
        $entity->is_virtual = (SplitEnum::IS_VIRTUAL == $model->is_virtual);
        $entity->clearGoodsItems();

        if(is_array($model->details)){
            foreach($model->details as & $data){
                $item = new TradeGoods();
                $item->goods_id = $data->goods_id;
                $item->quantity = new Quantity($data->quantity, $data->contractGoods->unit);
                $entity->addGoodsItem($item);
            }
        }

        return $entity;
    }

    public function store(IAggregateRoot $entity){
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
        $condition = "deliveryOrder.contract_id=".$contractId;

        return $this->findAll($condition);
    }
}