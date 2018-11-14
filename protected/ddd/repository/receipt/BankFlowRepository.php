<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/3/16 0016
 * Time: 9:56
 */

namespace ddd\repository\receipt;


use ddd\domain\entity\receipt\BankFlow;
use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;

class BankFlowRepository extends EntityRepository
{
    public function init()
    {
        $this->with = array();
    }

    public function getActiveRecordClassName()
    {
        return "BankFlow";
    }

    public function getNewEntity()
    {
        return new BankFlow();
    }



    /**
     * 数据模型转换成业务对象
     * @param $model
     * @return Project|Entity
     * @throws \Exception
     */
    public function dataToEntity($model)
    {
        $entity = BankFlow::create();
        $entity->setAttributes($model->getAttributes(), false);

        return $entity;
    }

    /**
     * 把对象持久化到数据
     * @param IAggregateRoot $entity
     * @return bool
     * @throws \Exception
     */
    public function store(IAggregateRoot $entity)
    {

    }
}