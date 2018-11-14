<?php
/**
 * Created by youyi000.
 * DateTime: 2018/5/29 17:49
 * Describe：
 *      声明式判断
 */

namespace ddd\infrastructure;


use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\infrastructure\error\ZInvalidArgumentException;
use ddd\infrastructure\error\ZModelNotExistsException;
use ddd\infrastructure\error\ZModelSaveFalseException;

trait AssertionConcern
{

    /**
     * 声明不能为空
     * @param $aString
     * @param null $argumentName    参数名
     * @throws \Exception
     */
    protected function assertArgumentNotEmpty($aString,$argumentName=null)
    {
        if (null === $aString || empty($aString))
            throw new ZInvalidArgumentException($argumentName);
    }

    /**
     * 声明model保存成功
     * @param $result
     * @param \CActiveRecord $model
     * @throws \Exception
     */
    protected function assertModelSaveSuccess($result,\CActiveRecord $model)
    {
        if(!$result)
            throw new ZModelSaveFalseException($model);
    }

    /**
     * 声明读取的model不为空
     * @param $model
     * @param $modelClassName
     * @param int $id
     * @throws \Exception
     */
    protected function assetModelNotEmpty($model,$modelClassName,$id=0)
    {
        if(empty($model))
            throw new ZModelNotExistsException($id, $modelClassName);
    }

    /**
     * 声明实体不为空
     * @param $entity
     * @param $entityClassName
     * @param int $id
     * @throws \Exception
     */
    protected function assetEntityNotEmpty($entity,$entityClassName,$id=0)
    {
        if(empty($entity))
            throw new ZEntityNotExistsException($id, $entityClassName);
    }
}