<?php
/**
 * Created by: wwb
 * Date: 2018/6/1
 * Time: 17:45
 * Desc: StockInRepository
 */

namespace ddd\Profit\Repository;


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

        $this->with = array('details','details.contractGoods','stockOutDetails','stockOutDetails.stock','stockOutDetails.stock.stockIn');

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