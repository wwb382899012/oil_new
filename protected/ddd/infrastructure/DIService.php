<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/10 11:07
 * Describe：
 */

namespace ddd\infrastructure;



class DIService
{

    /**
     * 获取对象
     * @param $class
     * @param array $params 构造函数参数
     * @param array $config 实例化后赋值的参数
     * @return object
     * @throws \Exception
     */
    public static function get($class,$params=[],$config=[])
    {
        try
        {
            return \Mod::$container->get($class,$params,$config);
        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }

    /**
     * 获取仓储对象
     * @param $class
     * @param array $params
     * @param array $config
     * @return object
     * @throws \Exception
     */
    public static function getRepository($class,$params=[],$config=[])
    {
        try
        {
            return \Mod::$container->get($class,$params,$config);
        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }

    /**
     * 根据接口获取相应的仓储实例
     * @param $interface
     * @param array $params
     * @param array $config
     * @return object
     * @throws \Exception
     */
    public static function getRepositoryByI($interface,$params=[],$config=[])
    {
        try
        {
            $class=static::getRepositoryClassNameByInterface($interface);
            return \Mod::$container->get($class,$params,$config);
        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }

    /**
     * 获取接口对应的实际类名
     * 'ddd\domain\iRepository\stock\IStockRepository'=>'definition'=>'ddd\repository\stock\StockRepository'
     * @param $interface
     * @return string
     */
    public static function getRepositoryClassNameByInterface($interface)
    {
        $interface=str_replace('ddd\\domain\\iRepository\\','ddd\\repository\\',$interface);
        $n=strripos($interface,'\\');
        $className=substr($interface,0,$n+1);
        $className.=substr($interface,$n+2);
        return $className;
    }
}