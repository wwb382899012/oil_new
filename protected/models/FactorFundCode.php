<?php

/**
 * Desc: 资金对接编号
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
class FactorFundCode extends BaseBusinessActiveRecord {
    const TYPE_INTERNAL=0; //内部
    const TYPE_EXTERNAL=1; //外部

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 't_factoring_fund_code';
    }

    /**
     * 根据资金对接编号查找对象信息
     * @param $code
     * @return CActiveRecord
     */
    public function findByCode($code)
    {
        return $this->find("code='".$code."'");
    }
}