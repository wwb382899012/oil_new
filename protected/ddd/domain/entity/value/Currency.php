<?php
/**
 * Created by vector.
 * DateTime: 2018/3/20 18:56
 * Describe：币种值对象
 */

namespace ddd\domain\entity\value;


use ddd\Common\Domain\BaseValue;

class Currency extends BaseValue
{

    /**
    * @var      int
    */
    public $id;
    
    /**
    * @var      string
    */
    public $name;
    
    /**
    * @var      string
    */
    public $ico;



    const RMB=1;
    const DOLLAR=2;

    public function __construct($id=0,$name="",$ico="")
    {
        $this->id=$id;
        $this->name=$name;
        $this->ico=$ico;
    }

    /**
     * 返回配置信息
     * @return array
     */
    public static function getConfigs()
    {
        return [
            self::RMB=>new Currency(1, '人民币','￥'),
            self::DOLLAR=>new Currency(2, '美元','$'),
        ];
    }

    /**
     * 返回当前币种信息对象
     * @return Currency|Object
     */
    public static function getCurrency($currencyId='')
    {
        if(empty($currencyId))
            return "";

        $configs = static::getConfigs();
        return $configs[$currencyId];
    }


}