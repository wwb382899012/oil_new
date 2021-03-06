<?php
/**
 * Created by youyi000.
 * DateTime: 2017/9/5 16:44
 * Describe：
 */

class BaseCacheActiveRecord extends BaseActiveRecord
{

    public static $cacheKeyPrefix="new_oil_";

    /**
     * 获取缓存key，子类继承一定要重写该方法!!!!
     * @param string $key
     * @return string
     */
    public static function getCacheKey($key="")
    {
        return static::$cacheKeyPrefix.__CLASS__.$key;
    }

    /**
     * 清除缓存
     * @param null $fieldName
     */
    public static function clearCache($fieldName=null)
    {
        if(!empty($fieldName))
            Utility::hDelCache(static::getCacheKey(),$fieldName);
        else
            Utility::clearCache(static::getCacheKey());

    }

    /**
     * 设置Redis缓存
     * @param $fieldName
     * @param $value
     */
    public static function setCache($fieldName,$value)
    {
        Utility::hSetCache(static::getCacheKey(),$fieldName,$value);
    }

    /**
     * 获取缓存
     * @param $fieldName
     * @return string
     */
    public static function getCache($fieldName)
    {
        if(Utility::hExists(static::getCacheKey(),$fieldName))
            return Utility::hGetCache(static::getCacheKey(),$fieldName);
        else
            return "";
    }

    public function afterDelete()
    {
        parent::afterDelete(); // TODO: Change the autogenerated stub
        static::clearCache();
    }

    protected function afterSave()
    {
        parent::afterSave(); // TODO: Change the autogenerated stub
        static::clearCache();
    }

    /**
     * Deletes rows with the specified primary key.
     * See {@link find()} for detailed explanation about $condition and $params.
     * @param mixed $pk primary key value(s). Use array for multiple primary keys. For composite key, each key value must be an array (column name=>column value).
     * @param mixed $condition query condition or criteria.
     * @param array $params parameters to be bound to an SQL statement.
     * @return integer the number of rows deleted
     */
    public function deleteByPk($pk, $condition = '', $params = array())
    {
        $rows= parent::deleteByPk($pk, $condition, $params); // TODO: Change the autogenerated stub
        static::clearCache();
        return $rows;
    }

    /**
     * Updates records with the specified primary key(s).
     * See {@link find()} for detailed explanation about $condition and $params.
     * Note, the attributes are not checked for safety and validation is NOT performed.
     * @param mixed $pk primary key value(s). Use array for multiple primary keys. For composite key, each key value must be an array (column name=>column value).
     * @param array $attributes list of attributes (name=>$value) to be updated
     * @param mixed $condition query condition or criteria.
     * @param array $params parameters to be bound to an SQL statement.
     * @return integer the number of rows being updated
     */
    public function updateByPk($pk, $attributes, $condition = '', $params = array())
    {
        $rows=parent::updateByPk($pk, $attributes, $condition, $params); // TODO: Change the autogenerated stub
        static::clearCache();
        return $rows;
    }


}