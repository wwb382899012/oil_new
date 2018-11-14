<?php

namespace ddd\Split\Repository;

use BaseActiveRecord;
use ddd\Common\Domain\BaseEntity;
use ddd\Common\Repository\EntityRepository;
use ddd\Split\Domain\Model\IGoods;

class Goods extends EntityRepository implements IGoods{

    /**
     * 获取新的实体对象
     * @return BaseEntity|\ddd\Split\Domain\Model\Goods
     * @throws \Exception
     */
    public function getNewEntity(){
        return new \ddd\Split\Domain\Model\Goods();;
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName(){
        return 'Goods';
    }

    /**
     * 数据模型转换成业务对象
     * @param BaseActiveRecord $model
     * @return BaseEntity|\ddd\Split\Domain\Model\Goods
     * @throws \Exception
     */
    public function dataToEntity($model){
        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes());
        return $entity;
    }

}