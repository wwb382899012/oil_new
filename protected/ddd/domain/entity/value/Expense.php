<?php
/**
 * Created by vector.
 * DateTime: 2018/3/20 18:56
 * Describe：其他费用科目值对象
 */

namespace ddd\domain\entity\value;


use ddd\Common\Domain\BaseValue;

class Expense extends BaseValue
{

    /**
    * @var      int
    */
    public $id;
    
    /**
    * @var      string
    */
    public $name;

    const STORAGE_FEE=1;
    const PORT_CONSTRUCTION_FEE=2;
    const HARBOUR_DUES=3;
    const FACILITY_FEE=4;
    const STAGNATION_FEE=5;
    const DEMURRAGE_FEE=6;
    const DECLARATION_FEE=7;
    const FREIGHT=8;
    const INSURANCE_FEE=9;
    const LICENSE_FEE=10;
    const ACCEPTANCE_FEE=11;
    const AGENCY_FEE=12;
    const EXCISE=13;

    public function __construct($id=0,$name="")
    {
        $this->id=$id;
        $this->name=$name;
    }


    /**
     *1、仓储费 2、港建费 3、港务费 4、港口设施保安费 5、滞港费 6、滞期费 
     *7、报关费 8、运费 9、保险费  10、开证费  11、承兑费  12、代理费  13、消费税（从量征）
     * 返回配置信息
     * @return array
     */
    public static function getConfigs()
    {
        return [
            self::STORAGE_FEE=>new Expense(1, '仓储费'),
            self::PORT_CONSTRUCTION_FEE=>new Expense(2, '港建费'),
            self::HARBOUR_DUES=>new Expense(3, '港务费'),
            self::FACILITY_FEE=>new Expense(4, '港口设施保安费'),
            self::STAGNATION_FEE=>new Expense(5, '滞港费'),
            self::DEMURRAGE_FEE=>new Expense(6, '滞期费'),
            self::DECLARATION_FEE=>new Expense(7, '报关费'),
            self::FREIGHT=>new Expense(8, '运费'),
            self::INSURANCE_FEE=>new Expense(9, '保险费'),
            self::LICENSE_FEE=>new Expense(10, '开证费'),
            self::ACCEPTANCE_FEE=>new Expense(11, '承兑费'),
            self::AGENCY_FEE=>new Expense(12, '代理费'),
            self::EXCISE=>new Expense(13, '消费税（从量征）'),
        ];
    }

    /**
     * 返回当前其他费用科目信息
     * @return Expense|Object
     */
    public static function getExpense($subjectId="")
    {
        if(empty($subjectId))
            return "";
        $configs = static::getConfigs();
        return $configs[$subjectId];
    }


}