<?php
/**
 * Created by youyi000.
 * DateTime: 2018/7/20 18:07
 * Describe：
 */

namespace ddd\Common\Domain\Value;


use ddd\Common\Domain\BaseValue;
use ddd\Common\Domain\IValue;
use system\components\base\Object;

class BaseCommonValue extends BaseValue implements IValue
{

    /**
     * 是否相等
     * @param IValue $value
     * @return bool
     */
    public function equals(IValue $value)
    {
        return $value===$this;
    }


}