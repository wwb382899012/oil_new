<?php
/**
 * Created by youyi000.
 * DateTime: 2018/2/27 9:56
 * Describe：
 */

namespace ddd\domain\entity\value;


use ddd\Common\Domain\BaseValue;
use ddd\Common\Domain\IValue;

class Quantity extends BaseValue
{

    /**
     * 数量
     * @var      float
     */
    public $quantity;

    /**
     * 单位
     * @var      int
     */
    public $unit;

    public function __construct($quantity=0,$unit=0)
    {
        $this->quantity=$quantity;
        $this->unit=$unit;
        parent::__construct();
    }

    public function equals(IValue $value)
    {
        return $this->quantity===$value->quantity && $this->unit===$value->unit;
    }

    /**
     * 增加数量
     * @param $quantity
     */
    public function add($quantity)
    {
        $this->quantity+=$quantity;
    }

    /**
     * 减小数量
     * @param $quantity
     */
    public function subtract($quantity)
    {
        $this->quantity-=$quantity;
    }


}