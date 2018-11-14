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
use ddd\Profit\Domain\Model\Stock\IStockNoticeCostRepository;
use ddd\Profit\Domain\Model\Stock\StockNoticeCost;
use ddd\Profit\Domain\Model\Stock\IDeliveryOrderDetailRepository;
use ddd\Profit\Domain\Model\Stock\IBuyGoodsCostRepository;
use ddd\Profit\Domain\Model\Stock\IStockNoticeRepository;
use ddd\Profit\Domain\Service\UnitService;
use ddd\Profit\Domain\Model\Stock\StockNoticeCostItem;

class StockNoticeCostRepository extends EntityRepository implements IStockNoticeCostRepository
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
        return new StockNoticeCost();
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName() {
        return "StockInBatchCost";
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
        $stockNotice = DIService::getRepository(IStockNoticeRepository::class)->findByPk($entity->batch_id);

        $items=array();
        $entity->setAttributes($stockNotice->getAttributes());
        if(!empty($stockNotice->items)){
            foreach($stockNotice->items as $key=>$value){
                $stockNoticeCostItem = new StockNoticeCostItem();
                $stockNoticeCostItem->setAttributes($value->getAttributes());
                $stockNoticeCostItem->settle_price=$value->settle_price;
                $stockNoticeCostItem->contract_price=$value->contract_price;

                if(!empty($model->details)){
                    foreach($model->details as $m=>$n){
                        if($value['goods_id']==$n['goods_id'])
                            $stockNoticeCostItem->id = $n['id'];
                    }
                }

                $items[$key]=$stockNoticeCostItem;
            }
        }
        $entity->items=$items;

        return $entity;
    }

    public function store(IAggregateRoot $entity){
        //入库通知单成本持久化
        $model = $this->model()->with($this->with)->find("t.batch_id=".$entity->batch_id);
        if (empty($model))
        { $this->activeRecordClassName = $this->getActiveRecordClassName();
            $model = new $this->activeRecordClassName;
        }
        $id=$model->id;
        $values = $entity->getAttributes();
        $values = \Utility::unsetCommonIgnoreAttributes($values);
        $model->setAttributes($values);
        $model->id=$id;
        $model->settle_status = $entity->settle_status;
        $model->price_type = $entity->price_type;

        if (!$model->save())
        {
            throw new ZModelSaveFalseException($model);
        }

        //入库通知单成本明细持久化
        if(!empty($entity->items)){
            foreach($entity->items as $key=>$valueEntity){
                //保存发货单利润
                $model = \StockInBatchCostDetail::model()->with($this->with)->find('t.batch_id='.$valueEntity->batch_id.' and t.goods_id='.$valueEntity->goods_id);
                if (empty($model))
                {
                    $model = new \StockInBatchCostDetail();
                }

                $id = $model->id;
                $values = $valueEntity->getAttributes();
                $values = \Utility::unsetCommonIgnoreAttributes($values);
                $model->setAttributes($values);
                $model->id=$id;
                $model->settle_status = $entity->settle_status;
                $model->contract_id = $entity->contract_id;
                $model->price_type = $entity->price_type;
                $model->settle_price=$values['settle_price']['price'];
                $model->settle_currency=$values['settle_price']['currency'];
                $model->contract_price=$values['contract_price']['price'];
                $model->contract_price_currency=$values['contract_price']['currency'];

                if (!$model->save())
                {
                    throw new ZModelSaveFalseException($model);
                }

            }
        }
        return $entity;

    }

    /**
     * 按入库通知单查询
     * @param batchId
     * @return StockIn
     */
    public function findByBatchId($batchId) {
        $condition = "t.batch_id=" . $batchId;
        $result = $this->findAll($condition);
        $result = array_values($result);
        return empty($result)?$result:$result[0];
    }
}