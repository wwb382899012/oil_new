<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/13 17:59
 * Describe：
 */

namespace ddd\Profit\Domain\Contract;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\Money;
use ddd\Common\Domain\Value\Quantity;

class ContractGoods extends BaseEntity
{

    public $goods_id=0;

    /**
     * @var Quantity
     */
    public $quantity;

    /**
     * @var Money   单价
     */
    public $price;

    /**
     * @var Money  单价（人民币）
     */
    public $price_cny;

    /**
     * 数量单位T的转换率
     * @var float
     */
    public $t_exchange_rate=1.0;

    public function __construct(?array $params = null)
    {
        parent::__construct($params);
        $this->quantity=new Quantity();
        $this->price=new Money();
    }


}