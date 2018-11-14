<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/12 10:06
 * Describe：
 */

namespace ddd\repository;


use ddd\Common\Repository\EntityRepository;
use ddd\domain\entity\Partner;

class PartnerRepository extends EntityRepository
{
    public function getActiveRecordClassName()
    {
        // TODO: Implement getActiveRecordClassName() method.
        return "Partner";
    }

    public function getNewEntity()
    {
        // TODO: Implement getNewEntity() method.
        return new Partner();
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