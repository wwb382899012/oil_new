<?php
namespace ddd\Profit\Domain\EstimateProfit;

use ddd\Common\Domain\Value\BaseCommonValue;
use ddd\Common\Domain\Value\Money;

/**
 * @Name            预估成本信息
 * @DateTime        2018年8月23日 11:08:39
 * @Author          Administrator
 */
class EstimateCostInfo extends BaseCommonValue
{
    #region property
    
    /**
     * 预估单价 
     * @var   Money
     */
    public $price;
    
    /**
     * 预估数量 
     * @var   Quantity
     */
    public $quantity;
    
    /**
     * 预估金额 
     * @var   Money
     */ 
    public $amount;    

    #endregion
    
    public function __construct($quantity, $amount)
    {
        parent::__construct();
        
        $this->quantity = $quantity;
        $this->amount   = $amount;
        $this->price    = new Money();

        if(!empty($quantity->quantity)){
            if($quantity->quantity==0)
                $this->price    = new Money();
            else
                $this->price = new Money(round($amount->amount / $quantity->quantity), $amount->currency);
        }
    }
}

?>