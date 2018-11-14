<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/13 16:58
 * Describe：
 */

namespace ddd\Profit\Domain\Price\Stock;


use ddd\Common\Domain\BaseEvent;

class LadingSettledEvent extends BaseEvent
{
    public $contract_id=0;
    public $bill_id=0;
    public $goods_id=0;
    public $quantity=0;
    public $price=0;
}