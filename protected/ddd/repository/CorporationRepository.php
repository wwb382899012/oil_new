<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 11:33
 * Describe：
 */

namespace ddd\repository;


use ddd\Common\Repository\EntityRepository;
use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\Corporation;

class CorporationRepository extends EntityRepository
{
    public function init()
    {
        /*$this->entityClassName='domain\entity\Goods';
        $this->activeRecordClassName="Goods";*/
    }

    public function getActiveRecordClassName()
    {
        // TODO: Implement getActiveRecordClassName() method.
        return "Corporation";
    }

    public function getNewEntity()
    {
        // TODO: Implement getNewEntity() method.
        return new Corporation();
    }




}