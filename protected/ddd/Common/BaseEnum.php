<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/11 17:38
 * Describe：
 */

namespace ddd\Common;


use system\components\base\Object;

class BaseEnum extends Object
{
    private static $_enums=[];
    private $constantItems;

    /**
     * 返回静态实例对象
     * @return static
     */
    public static function instance()
    {
        $className=get_called_class();
        if(isset(self::$_enums[$className]))
            return self::$_enums[$className];
        else
        {
            $instance=self::$_enums[$className]=new $className();
            return $instance;
        }
    }

    /**
     * 获取当前类的常量，即获取所有枚举项
     * @return array
     * @throws \Exception
     */
    public function getEnums()
    {
        if(empty($this->constantItems))
        {
            $reflect = new \ReflectionClass(get_class($this));
            $this->constantItems=$reflect->getConstants();
        }
        return $this->constantItems;
    }
}