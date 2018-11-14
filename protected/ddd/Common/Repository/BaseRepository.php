<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/6 17:51
 * Describe：
 */

namespace ddd\Common\Repository;


use system\components\base\Object;

abstract class BaseRepository  extends Object
{

    private static $_repositories=array();

    /**
     * 返回静态Repository对象
     * @return static
     */
    public static function repository()
    {
        $className=get_called_class();
        if(isset(self::$_repositories[$className]))
            return self::$_repositories[$className];
        else
        {
            $repository=self::$_repositories[$className]=new $className();
            return $repository;
        }
    }
}