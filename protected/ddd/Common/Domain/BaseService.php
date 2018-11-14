<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/4 11:12
 * Describe：
 */

namespace ddd\Common\Domain;

use system\components\base\Object;

abstract class BaseService extends Object
{

    private static $_services=array();

    /**
     * 返回静态service对象
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