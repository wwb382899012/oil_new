<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/20 15:44
 * Describe：
 */

namespace ddd\infrastructure;


class Utility
{

    /**
     * 返回依赖注入容器
     * @return \system\components\di\Container
     */
    public static function getDIContainer()
    {
        return \Mod::$container;
    }

    /**
     * 获取当天日期字符串
     * @return string
     */
    public static function getToday()
    {
        return self::getDate();
    }

    /**
     * 获取当前时间字符串
     * @return string
     */
    public static function getNow()
    {
        return self::getDateTime();
    }

    /**
     * 获取日期
     * @param string $dateStr ，默认为当前日期
     * @return string
     */
    public static function getDate($dateStr = "")
    {
        if (empty($dateStr))
            return date("Y-m-d");
        else
            return date("Y-m-d", strtotime($dateStr));
    }

    /**
     * 获取当前时间
     * @param string $timeStr ，默认为当前时间
     * @return string
     */
    public static function getDateTime($timeStr = "")
    {
        if (empty($timeStr))
            return date("Y-m-d H:i:s");
        else
            return date("Y-m-d H:i:s", strtotime($timeStr));
    }

}