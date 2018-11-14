<?php

namespace ddd\Split\Repository;

use ddd\Common\Domain\BaseEntity;
use ddd\Common\Repository\EntityRepository;
use ddd\Split\Domain\Model\ICheckLog;

class CheckLog extends EntityRepository implements ICheckLog{

    /**
     * @return BaseEntity|\ddd\Split\Domain\Model\CheckLog
     * @throws \Exception
     */
    public function getNewEntity(){
        return new \ddd\Split\Domain\Model\CheckLog();
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName(){
        return 'CheckLog';
    }

    /**
     * 数据模型转换成业务对象
     * @param $model
     * @return BaseEntity|\ddd\Split\Domain\Model\CheckLog
     * @throws \Exception
     */
    public function dataToEntity($model){
        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes());
        $entity->node_name = $model->checkNode->node_name;
        $entity->checker = $model->user->name;
        $entity->result = \Map::getStatusName('check_status',$model->check_status);

        return $entity;
    }

    public function findAllByObjIdAndBusinessId($objId, $businessId){
        return $this->findAll('obj_id=:obj_id AND business_id=:business_id',[':obj_id'=>$objId,':business_id'=>$businessId]);
    }
}