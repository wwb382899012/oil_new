<?php
/**
 * Created by: wwb
 * Date: 2018/6/1
 * Time: 17:45
 * Desc: StockInRepository
 */

namespace ddd\Profit\Repository\Stock;


use ConstantMap;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\domain\entity\value\Quantity;
use ddd\domain\entity\value\Price;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ZModelNotExistsException;
use ddd\Profit\Domain\Model\Stock\BuyGoodsCost;
use ddd\Profit\Domain\Model\Stock\IDeliveryOrderRepository;
use ddd\Profit\Domain\Model\Stock\IDeliveryOrderDetailRepository;
use ddd\Profit\Domain\Model\Stock\IBuyGoodsCostRepository;
use ddd\Profit\Domain\Service\GoodsPriceService;
use ddd\Profit\Domain\Service\UnitService;


class BuyGoodsCostRepository extends EntityRepository implements IBuyGoodsCostRepository
{


    public function init() {
        $this->with = array();
    }

    /**
     * 获取新的实体对象
     * @return BaseEntity|StockOut
     * @throws \Exception
     */
    public function getNewEntity() {
        return new BuyGoodsCost();
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName() {
        return "BuyGoodsCost";
    }

    /**
     * 数据模型转换成业务对象
     * @param $model
     * @return Project|Entity
     * @throws \Exception
     */
    public function dataToEntity($model) {
        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes(), false);
        return $entity;
    }

    public function store(IAggregateRoot $entity){
        //采购商品成本 持久化
        $model = $this->model()->with($this->with)->find('t.out_id='.$entity->out_id);
        if (empty($model))
        {
            $this->activeRecordClassName = $this->getActiveRecordClassName();
            $model = new $this->activeRecordClassName;
        }

        $id = $model->id;
        $values = $entity->getAttributes();
        $values = \Utility::unsetCommonIgnoreAttributes($values);
        $model->setAttributes($values);
        $model->id=$id;
        $model->out_quantity=$values['out_quantity']['quantity'];
        $model->unit=$values['out_quantity']['unit'];
        $model->goods_price=$values['goods_price']['price'];
        $model->currency=$values['goods_price']['currency'];

        if (!$model->save())
        {
            throw new ZModelSaveFalseException($model);
        }
        return $model;
    }

    /**
     * 按发货单查询
     * @param batchId
     * @return StockIn
     */
    public function findByOrderId($orderId) {
        $condition = "t.order_id=" . $orderId;
        return $this->findAll($condition);
    }
}