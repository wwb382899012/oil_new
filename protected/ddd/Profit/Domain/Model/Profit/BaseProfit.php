<?php
/**
 * Desc: 利润报表基础类
 * User: wwb
 * Date: 2018/5/28 0028
 * Time: 15:39
 */

namespace ddd\Profit\Domain\Model\Profit;

use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\domain\entity\Attachment;
use ddd\domain\entity\value\Price;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\Utility;


abstract class BaseProfit extends BaseEntity implements IAggregateRoot
{
    /**
     * 实际毛利
     * @var      Price
     */
    public $actual_gross_profit;

    /**
     * 运费
     * @var      Price
     */
    public $freight;

    /**
     * 仓储费
     * @var      Price
     */
    public $warehouse_fee;

    /**
     * 杂费
     * @var      Price
     */
    public $miscellaneous_fee;

    /**
     * 增值税
     * @var      Price
     */
    public $vat;

    /**
     * 附加税
     * @var      Price
     */
    public $sur_tax;

    /**
     * 印花税
     * @var      Price
     */
    public $stamp_tax;

    /**
     * 税后毛利
     * @var      Price
     */
    public $after_tax_profit;

    /**
     * 资金成本
     * @var      Price
     */
    public $fund_cost;

    /**
     * 业务净利润
     * @var      Price
     */
    public $profit;

    /**
     * 已开票金额
     * @var      Price
     */
    public $sell_invoice_amount;

    /**
     * 已收票金额
     * @var      Price
     */
    public $buy_invoice_amount;

    /**
     * 已付上游款
     * @var      Price
     */
    public $pay_amount;

    /**
     * 已收下游款
     * @var      Price
     */
    public $receive_amount;

    public function __construct()
    {
        parent::__construct();
        $this->actual_gross_profit=new Price(0);
        $this->freight = new Price(0);
        $this->warehouse_fee = new Price(0);
        $this->miscellaneous_fee = new Price(0);
        $this->vat = new Price(0);
        $this->sur_tax = new Price(0);
        $this->stamp_tax = new Price(0);
        $this->actual_gross_profit = new Price(0);
        $this->fund_cost = new Price(0);
        $this->profit = new Price(0);
        $this->sell_invoice_amount = new Price(0);
        $this->buy_invoice_amount = new Price(0);
        $this->pay_amount  = new Price(0);
        $this->receive_amount = new Price(0);

    }

}
