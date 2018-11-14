<?php
/**
 * Created by youyi000.
 * DateTime: 2017/12/21 15:22
 * Describe：
 */

class ZFormatter extends CFormatter
{
    public function formatAmount($value)
    {

        return number_format($value/100,$this->numberFormat['decimals'],$this->numberFormat['decimalSeparator'],$this->numberFormat['thousandSeparator']);
    }
}