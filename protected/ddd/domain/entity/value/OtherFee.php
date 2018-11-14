<?php
/**
 * Created by vector.
 * DateTime: 2018/3/20 18:56
 * Describe：非货款类科目值对象
 */

namespace ddd\domain\entity\value;


use ddd\Common\Domain\BaseValue;


class OtherFee extends BaseValue
{

    /**
    * @var      int
    */
    public $id;
    
    /**
    * @var      string
    */
    public $name;
    //银行手续费，代理费，杂费

    const BANK_FEE    = 1;
    const AGENCY_FEE  = 2;
    const OTHER_FEE   = 3;

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
            self::BANK_FEE=>new OtherFee(1, '银行手续费'),
            self::AGENCY_FEE=>new OtherFee(2, '代理费'),
            self::OTHER_FEE=>new OtherFee(3, '杂费'),
        ];
    }

    /**
     * 返回当前非货款科目信息
     * @return OtherFee|Object
     */
    public static function getOtherFee($subjectId="")
    {
        if(empty($subjectId))
            return "";
        $configs = static::getConfigs();
        return $configs[$subjectId];
    }


}