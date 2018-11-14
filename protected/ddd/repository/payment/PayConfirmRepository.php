<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/3/16 0016
 * Time: 9:56
 */

namespace ddd\repository\payment;


use ddd\domain\entity\payment\PayConfirm;
use ddd\Common\IAggregateRoot;
use ddd\infrastructure\error\ExceptionService;
use ddd\repository\contract\ContractRepository;
use ddd\Common\Repository\EntityRepository;

class PayConfirmRepository extends EntityRepository
{
    public function init()
    {
        $this->with = array("apply");
    }

    public function getActiveRecordClassName()
    {
        return "Payment";
    }

    public function getNewEntity()
    {
        return new PayConfirm();
    }



    /**
     * 数据模型转换成业务对象
     * @param $model
     * @return Project|Entity
     * @throws \Exception
     */
    public function dataToEntity($model)
    {
        $entity = PayConfirm::create();
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

    /**
     * @desc 获取付款实付对应的合同
     * @param int $payment_id
     * @return int
     */
    public static function getContract($payment_id)
    {
        if (\Utility::checkQueryId($payment_id) && $payment_id > 0)
        {
            $model = \Payment::model()->findByPk($payment_id);
            if (empty($model))
            {
                ExceptionService::throwModelDataNotExistsException($payment_id, 'Payment');
            }

            $contractRepo = new ContractRepository();
            return $contractRepo->dataToEntity($model->apply->contract);
        }

        return null;
    }
}