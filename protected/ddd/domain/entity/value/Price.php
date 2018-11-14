<?php
/**
 * Created by vector.
 * DateTime: 2018/3/20 18:56
 * Describe：计价值对象
 */

namespace ddd\domain\entity\value;


use ddd\Common\Domain\BaseValue;
use ddd\Common\Domain\IValue;

class Price extends BaseValue
{

    /**
    * @var      int
    */
    public $price;
    
    /**
    * @var      int
    */
    public $currency;

    public function __construct($price=0,$currency=0)
    {
        $this->price=$price;
        $this->currency=$currency;
    }


    public function equals(IValue $value)
    {
        return $this->price===$value->price && $this->currency===$value->currency;
    }


}