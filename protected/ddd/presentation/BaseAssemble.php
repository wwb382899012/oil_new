<?php
/**
 * Created by youyi000.
 * DateTime: 2018/5/3 15:03
 * Describe：
 */

namespace ddd\presentation;


use system\components\base\Object;

class BaseAssemble extends Object
{
    private static $_assembles=array();

    /**
     * 返回静态Assemble对象
     * @return BaseAssemble
     */
    public static function service()
    {
        $className=get_called_class();
        if(isset(self::$_assembles[$className]))
            return self::$_assembles[$className];
        else
        {
            $service=self::$_assembles[$className]=new $className();
            return $service;
        }
    }
}