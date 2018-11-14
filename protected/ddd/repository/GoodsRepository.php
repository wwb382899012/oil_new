<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 11:33
 * Describe：
 */

namespace ddd\repository;


use ddd\Common\Repository\EntityRepository;
use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\Goods;

class GoodsRepository extends EntityRepository
{
    public function init()
    {
        /*$this->entityClassName='domain\entity\Goods';
        $this->activeRecordClassName="Goods";*/
    }

    public function getActiveRecordClassName()
    {
        // TODO: Implement getActiveRecordClassName() method.
        return "Goods";
    }

    public function getNewEntity()
    {
        // TODO: Implement getNewEntity() method.
        return new Goods();
    }




    public function dataToEntity($model)
    {
        $entity=$this->getNewEntity();
        if(!empty($entity))
        {
            $entity->setAttributes($model->getAttributes(),false);
        }
        return $entity;
    }

}