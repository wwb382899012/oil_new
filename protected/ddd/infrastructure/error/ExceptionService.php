<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 17:56
 * Describe：
 */

namespace ddd\infrastructure\error;

class ExceptionService
{
    /**
     * 参数为空异常
     * @param $paramName
     * @param array $params
     * @throws \Exception
     */
    public static function throwArgumentNullException($paramName,$params=array())
    {
        $message="\"{class}.{function}\"的参数\"".$paramName."\"为Null";
        if(is_array($params))
        {
            $customParams=array();
            foreach ($params as $k=>$v)
            {
                $customParams["{".$k."}"]=$v;
            }
            $message=strtr($message,$customParams);
        }
        else
            $message="参数\"".$paramName."\"为Null";
        throw new \Exception($message);
    }

    /**
     * 模型数据不存在，主要是根据主键读取数据库时为空的异常
     * @param $id
     * @param $modelClassName
     * @throws \Exception
     */
    public static function throwModelDataNotExistsException($id,$modelClassName)
    {
        throw new \Exception("主键为".$id."的".$modelClassName."数据对象不存在");
    }

    /**
     * 数据模型保存失败
     * @param \CActiveRecord $model
     * @throws \Exception
     */
    public static function throwModelSaveFalseException(\CActiveRecord $model)
    {
        throw new \Exception("数据对象".get_class($model)."[".$model->getPrimaryKey()."]保存失败");
    }

    /**
     * 数据模型删除失败
     * @param \CActiveRecord $model
     * @throws \Exception
     */
    public static function throwModelDeleteFalseException(\CActiveRecord $model)
    {
        throw new \Exception("数据对象".get_class($model)."[".$model->getPrimaryKey()."]删除失败");
    }

    /**
     * 抛出异常
     * @param $businessError
     * @param array $params
     * @throws \Exception
     */
    public static function throwBusinessException($businessError,$params=array())
    {
        if(is_array($params))
        {
            $customParams=array();
            foreach ($params as $k=>$v)
            {
                $customParams["{".$k."}"]=$v;
            }
            $message=strtr($businessError[1],$customParams);
        }

        $code=$businessError[0];
        throw new \Exception($message,$code);
    }

    /**
     * 实体对象实例不存在
     * @param $id
     * @param $entityName
     * @throws \Exception
     */
    public static function throwEntityInstanceNotExistsException($id,$entityName)
    {
        throw new \Exception("主键为".$id."的".$entityName."实体对象不存在");
    }

    /**
     * 获取格式化后的错误信息
     * @param $message
     * @param null $params
     * @return string
     */
    public static function formatExceptionMessage($message,$params=null)
    {
        if(is_array($params))
        {
            $customParams=array();
            foreach ($params as $k=>$v)
            {
                $customParams["{".$k."}"]=$v;
            }
            $message=strtr($message,$customParams);
        }

        return $message;
    }

    /**
     * 获取业务异常信息
     * @param $businessError
     * @param array $params
     * @return string
     */
    public static function getBusinessExceptionMessage($businessError,$params=array())
    {
        if(is_array($params))
        {
            $customParams=array();
            foreach ($params as $k=>$v)
            {
                $customParams["{".$k."}"]=$v;
            }
            $message=strtr($businessError[1],$customParams);
        }
        return $message;
    }
}