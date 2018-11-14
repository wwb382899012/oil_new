<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/30 15:21
 * Describe：
 */

namespace ddd\infrastructure\error;


class ZModelNotExistsException extends ZException
{
    public function __construct($id,$modelClassName)
    {
        $message="主键为".$id."的".$modelClassName."数据对象不存在";
        $code=BusinessError::MODEL_NOT_EXISTS;
        parent::__construct($message, $code);
    }


}