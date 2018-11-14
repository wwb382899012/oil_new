<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 14:30
 * Describe：
 */

namespace ddd\Common\Application;


use system\components\base\Object;

class BaseService extends Object
{
    private static $_services=array();

    /**
     * 返回静态Service对象
     * @return static
     */
    public static function service()
    {
        $className=get_called_class();
        if(isset(self::$_services[$className]))
            return self::$_services[$className];
        else
        {
            $service=self::$_services[$className]=new $className();
            return $service;
        }
    }
}