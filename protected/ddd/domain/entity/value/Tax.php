<?php
/**
 * Created by vector.
 * DateTime: 2018/3/20 18:56
 * Describe：税收名目值对象
 */

namespace ddd\domain\entity\value;


use ddd\Common\Domain\BaseValue;


class Tax extends BaseValue
{

    /**
    * @var      int
    */
    public $id;
    
    /**
    * @var      string
    */
    public $name;


    const EXCISE    = 1;
    const ADDED_TAX = 2;
    const TARIFF    = 3;

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
            self::EXCISE=>new Tax(1, '消费税'),
            self::ADDED_TAX=>new Tax(2, '增值税'),
            self::TARIFF=>new Tax(3, '关税'),
        ];
    }

    /**
     * 返回当前税收名目信息
     * @return Tax|Object
     */
    public static function getTax($subjectId="")
    {
        if(empty($subjectId))
            return "";

        $configs = static::getConfigs();
        return $configs[$subjectId];
    }


}