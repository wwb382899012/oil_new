<?php

/**
 * Created by vector.
 * DateTime: 2018/9/23 9:23
 * Describe：调整方式值对象
 */

namespace ddd\domain\entity\value;


use ddd\Common\Domain\BaseValue;

class AdjustMode extends BaseValue
{
    
    /**
    * @var      int
    */
    public $id;
    
    /**
    * @var      string
    */
    public $name;


    const ADD=1;
    const REDUCE=2;

    public function __construct($id=0,$name="")
    {
        $this->id=$id;
        $this->name=$name;
    }

    /**
     * 返回配置信息
     * @return array
     */
    public static function getConfigs()
    {
        return [
            self::ADD=>new AdjustMode(1, '调增'),
            self::REDUCE=>new AdjustMode(2, '调减'),
        ];
    }

    /**
     * 返回当前调整方式信息对象
     * @return AdjustMode|Object
     */
    public static function getAdjustMode($modeId='')
    {
        if(empty($modeId))
            return "";

        $configs = static::getConfigs();
        return $configs[$modeId];
    }
}