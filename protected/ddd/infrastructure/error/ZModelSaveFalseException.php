<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/30 15:29
 * Describe：
 */

namespace ddd\infrastructure\error;


class ZModelSaveFalseException extends ZException
{
    public function __construct(\CActiveRecord $model)
    {
        $message="数据对象".get_class($model)."[".$model->getPrimaryKey()."]保存失败";
        $code=BusinessError::MODEL_SAVE_FALSE;
        parent::__construct($message, $code);
    }
}