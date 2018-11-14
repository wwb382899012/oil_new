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
use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\domain\entity\contractSettlement\SettlementMode;
use ddd\domain\entity\value\Quantity;
use ddd\domain\entity\value\Price;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ZModelNotExistsException;
use ddd\Profit\Domain\Model\Corporation;
use ddd\Profit\Domain\Model\Project;

class CorporationRepository extends EntityRepository
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
        return new Corporation();
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName() {
        return "Corporation";
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
        $entity->corporation_name = $model->name;
        return $entity;
    }

    public function store(IAggregateRoot $entity){

    }


}