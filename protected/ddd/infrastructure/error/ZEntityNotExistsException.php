<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/30 15:28
 * Describe：
 */

namespace ddd\infrastructure\error;


class ZEntityNotExistsException extends ZException
{
    public function __construct($id,$entityName)
    {
        $message="主键为".$id."的".$entityName."实体对象不存在";
        $code=BusinessError::ENTITY_NOT_EXISTS;
        parent::__construct($message, $code);
    }
}