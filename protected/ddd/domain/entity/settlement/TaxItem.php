<?php


namespace ddd\domain\entity\settlement;

use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\value\Tax;

class TaxItem extends BaseEntity
{

    #region property

    /**
     * 税收名目
     * @var   Tax
     */
    public $tax;

    /**
     * 税率
     * @var   float
     */
    public $tax_rate;

    /**
     * 税收总金额
     * @var   int
     */
    public $tax_amount;

    /**
     * 税收单价
     * @var   int
     */
    public $tax_price;

    /**
     * 备注
     * @var   text
     */
    public $remark;

    #endregion


    /**
     * 创建对象
     * @return TaxItem
     */
    public static function create()
    {
        $entity = new TaxItem();
        return $entity;
    }
};