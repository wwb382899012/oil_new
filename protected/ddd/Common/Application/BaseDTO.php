<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 10:42
 * Describe：
 */

namespace ddd\Common\Application;


use ddd\Common\BaseModel;
use ddd\Common\Domain\BaseEntity;

abstract class BaseDTO extends BaseModel
{
    public function __construct(BaseModel $entity=null)
    {
        parent::__construct();
        if(!empty($entity))
            $this->fromEntity($entity);
    }

    /**
     * 由实体对象赋值
     * @param BaseModel $entity
     * @throws \Exception
     */
    public function fromEntity(BaseEntity $entity)
    {
        $this->setAttributes($entity->getAttributes());
    }

}