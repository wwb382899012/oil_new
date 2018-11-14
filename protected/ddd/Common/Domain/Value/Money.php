<?php
/**
 * Created by youyi000.
 * DateTime: 2018/7/20 18:14
 * Describe：
 */

namespace ddd\Common\Domain\Value;


use ddd\Common\Domain\Currency\Currency;
use ddd\Common\Domain\Currency\CurrencyService;
use ddd\Common\Domain\IValue;
use ddd\infrastructure\error\ZException;

class Money extends BaseCommonValue
{
    #region property

    /**
     * 金额大小
     * @var   int
     */
    public $amount;

    /**
     * 币种
     * @var   Currency
     */
    public $currency;

    #endregion

    public function __construct($amount=0,$currency=null)
    {
        parent::__construct();
        $this->amount=$amount;
        if(empty($currency))
            $currency=CurrencyService::createCNY();
        $this->currency=$currency;
    }

    /**
     * 增加金额返回一个新Money对象
     * @param $amount
     * @return Money
     */
    public function addAmount($amount)
    {
        $amount=$this->amount+$amount;
        return new static($amount,$this->currency);
    }

    /**
     * 增加Money并返回一个新的Money对象
     * @param Money $money
     * @return Money
     * @throws \Exception
     */
    public function addMoney(Money $money)
    {
        if(!$this->currency->equals($money->currency))
            throw  new ZException("币种（Currency）不一致");
        return $this->addAmount($money->amount);
    }

    /**
     * 是否相等于
     * @param Money $value
     * @return bool
     */
    public function equals(IValue $value)
    {
        return ($this->amount==$value->amount && $this->currency->equals($value->currency));
    }


}