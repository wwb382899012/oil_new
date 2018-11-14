<?php
/**
 * Created by wwb.
 * DateTime: 2018/3/21 11:35
 * Describe：结算明细
 */

namespace ddd\Profit\Domain\Model\Settlement;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Currency\Currency;
use ddd\Common\IAggregateRoot;
use ddd\Common\Domain\Value\Money;
use ddd\domain\entity\value\Quantity;

class SettlementItem extends BaseEntity
{

    /**
     * 商品id
     * @var   int
     */
    public $goods_id;

    /**
     * 结算汇率
     * @var   float
     */
    public $exchange_rate;

    /**
     * 结算单价
     * @var   Money
     */
    public $price;

    /**
     * 结算单价 人民币
     * @var   Money
     */
    public $price_cny;




    public function __construct()
    {
        parent::__construct();

        $this->price=new Money(0);
        $this->price_cny = new Money(0);

    }


}

