<?php
/**
 * Created by: wwb
 * Date: 2018/6/1
 * Time: 17:45
 * Desc: StockInRepository
 */

namespace ddd\Profit\Repository\Settlement;


use ConstantMap;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Currency\CurrencyService;
use ddd\Common\Domain\Value\UnitEnum;
use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\domain\entity\value\Quantity;
use ddd\domain\entity\value\Price;
use ddd\infrastructure\error\ZModelNotExistsException;
use ddd\Profit\Domain\Model\Settlement\DeliveryItem;
use ddd\Profit\Domain\Model\Settlement\DeliveryOrder;
use ddd\Profit\Domain\Model\Settlement\SettlementItem;
use ddd\Profit\Domain\Model\Stock\DeliverySettlementDetail;
use ddd\Profit\Domain\Service\UnitService;
use ddd\repository\contract\ContractRepository;
use ddd\Profit\Domain\Model\Stock\DeliveryOrderDetail;
use ddd\Common\Domain\Value\Money;

class DeliveryOrderRepository extends EntityRepository
{


    public function init() {

        $this->with = array('details','details.contractGoods');

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
        $entity->settle_status = $model->contractSettlement->status;

        if (is_array($model->settlementDetails)) {
            foreach ($model->settlementDetails as $data) {
                $item = new SettlementItem();
                $item->goods_id = $data->goods_id;
                $item->exchange_rate = $data->exchange_rate;
                $item->price = new Money($data->price,$data->currency);
                $item->price_cny = new Money($data->price_cny,$data->currency);

                $entity->addSettleItem($item);
            }
        }


        if(is_array($model->stockOutDetails)){
            foreach ($model->stockOutDetails as & $data) {

                $item = new DeliveryItem();
                $item->goods_id = $data->goods_id;
                $item->contract_id = $data->stock->stockIn->notice->contract_id;
                $item->stock_in_id = $data->stock->stock_in_id;
                $item->exchange_rate = $data->contractGoods->unit_convert_rate;
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