<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/30 15:30
 * Describe：
 */

namespace ddd\infrastructure\error;


class ZModelDeleteFalseException  extends ZException
{
    public function __construct(\CActiveRecord $model)
    {
        $message="数据对象".get_class($model)."[".$model->getPrimaryKey()."]删除失败";
        $code=BusinessError::MODEL_DELETE_FALSE;
        parent::__construct($message, $code);
    }
}