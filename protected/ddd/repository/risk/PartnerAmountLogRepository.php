<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/12 10:52
 * Describe：
 */

namespace ddd\repository\risk;


use ddd\Common\IAggregateRoot;
use ddd\infrastructure\error\ExceptionService;
use ddd\Common\Repository\EntityRepository;

abstract class PartnerAmountLogRepository extends EntityRepository
{
    abstract function getType();

    public function getActiveRecordClassName()
    {
        // TODO: Implement getActiveRecordClassName() method.
        return "PartnerAmountLog";
    }

    public function store(IAggregateRoot $entity)
    {
        $id = $entity->getId();
        if (!empty($id))
        {
            $model = $this->model()->findByPk($id);
            if (empty($model))
            {
                ExceptionService::throwModelDataNotExistsException($id, $this->getActiveRecordClassName());
            }
        } else
        {
            $this->activeRecordClassName = $this->getActiveRecordClassName();
            $model = new $this->activeRecordClassName;
        }
        //这里需要处理一下新增时设置主键值的问题
        $model->setAttributes($entity->getAttributes(), false);
        $model->type = $this->getType();
        $model->create_user_id = \Utility::getNowUserId();
        $model->create_time = \Utility::getDateTime();
        $model->save();
    }
}