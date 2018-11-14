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
use ddd\Common\Domain\Currency\CurrencyService;
use ddd\Common\Domain\Value\UnitEnum;
use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\domain\entity\value\Quantity;
use ddd\domain\entity\value\Price;
use ddd\infrastructure\error\ZModelNotExistsException;
use ddd\Profit\Domain\Model\Stock\DeliveryOrder;
use ddd\Profit\Domain\Model\Stock\DeliverySettlementDetail;
use ddd\Profit\Domain\Service\UnitService;
use ddd\repository\contract\ContractRepository;
use ddd\Profit\Domain\Model\Stock\DeliveryOrderDetail;


class DeliveryOrderRepository extends EntityRepository
{


    public function init() {

        $this->with = array('details','details.contractGoods','stockOutDetails');

    }

    /**
     * 获取新的实体对象
     * @return BaseEntity|StockOut
     * @throws \Exception
     */
    public function getNewEntity() {
        return new DeliveryOrder();
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName() {
        return "DeliveryOrder";
    }

    /**
     * 数据模型转换成业务对象
     * @param $model
     * @return DeliveryOrder
     * @throws \Exception
     */
    public function dataToEntity($model) {
        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes(), false);
        //$contractEntity = ContractRepository::repository()->findByPk($entity->contract_id);
        $sumQuantity=0;
        $sumAmount=0;
        if (is_array($model->settlementDetails)) {
            foreach ($model->settlementDetails as $data) {
                if($model['status']==\DeliveryOrder::STATUS_SETTLE_PASS) {

                    //$settle_amount =new Money($data->amount_cny,CurrencyService::createCNY()); //new Price($data->amount_cny, ConstantMap::CURRENCY_RMB);
                    $settle_quantity = new Quantity($data->quantity_settle, $data->unit);
                    if($data->unit!=UnitEnum::UNIT_T && !empty($data->contractGoods) && $data->contractGoods->unit_convert_rate>0)
                    {
                        $q=round($data->quantity_settle/$data->contractGoods->unit_convert_rate,4);
                        $settle_quantity=new Quantity($q,UnitEnum::UNIT_T);
                    }
                    //$settle_quantity = UnitService::settleUnitTon($settle_quantity, $data->goods_id, $contractEntity);

                    $sumQuantity += $settle_quantity->quantity;
                    $sumAmount += $data->amount_cny;
                }
            }
        }
        $entity->settle_quantity = new Quantity($sumQuantity,UnitEnum::UNIT_T);
        $entity->settle_amount= new Price($sumAmount,ConstantMap::CURRENCY_RMB);

        if(is_array($model->stockOutDetails)){
            foreach ($model->stockOutDetails as & $data) {

                $item = new DeliveryOrderDetail();
                $item->out_id = $data->out_id;
                $item->goods_id = $data->goods_id;
                $item->order_id = $data->order_id;
                $item->out_order_id = $data->out_order_id;
                $item->batch_id = $data->stock->stockIn->batch_id;
                $item->out_quantity = new Quantity($data->quantity,$data->stock->unit);
                $entity->addDeliveryItem($item);
            }
        }

        return $entity;
    }

    public function store(IAggregateRoot $entity){

    }

    /**
     * 查询合同下所有的发货单
     * @param batchId
     * @return StockIn
     */
    public function findAllByContractId($contractId) {
        $condition = "t.contract_id=" . $contractId;

        return $this->findAll($condition);
    }
    /**
     * 查询合同下所有的发货单
     * @param batchId
     * @return StockIn
     */
    public function findByBatchId($batchId) {
        $condition = "stockIn.batch_id=" . $batchId;

        return $this->findAll($condition);
    }
}