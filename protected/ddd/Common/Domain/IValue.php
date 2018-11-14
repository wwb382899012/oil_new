<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/2 18:15
 * Describe：
 */

namespace ddd\Common\Domain;


interface IValue
{

    function equals(IValue $value);
}