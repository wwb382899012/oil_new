<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 11:21
 * Describe：
 */

namespace ddd\application;


class UnitService
{
    public static function getName($unit)
    {
        return \Map::$v["goods_unit"][$unit]["name"];
    }
}