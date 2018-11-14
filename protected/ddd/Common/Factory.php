<?php
/**
 * Created by youyi000.
 * DateTime: 2018/5/28 15:28
 * Describe：
 */

namespace ddd\Common;


use ddd\infrastructure\DIService;

class Factory
{
    /**
     * 获取对象的工厂方法
     * @param $class
     * @param null $params
     * @param null $configs
     * @return object
     * @throws \Exception
     */
    public static function createInstance($class,$params=null,$configs=null)
    {
        return DIService::get($class,$params,$configs);
    }
}