<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/13 17:34
 * Describe：
 */

namespace ddd\Profit\Domain\Price\Contract;


use ddd\Common\Domain\BaseEvent;

class ContractGoodsConfirmedEvent extends BaseEvent
{
    public $contract_id=0;
    public $goods_id=0;
    public $quantity=0;
    public $price=0;

}