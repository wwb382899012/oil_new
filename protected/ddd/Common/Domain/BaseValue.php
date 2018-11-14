<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/2 18:15
 * Describe：
 */

namespace ddd\Common\Domain;


use ddd\Common\BaseModel;

class BaseValue extends BaseModel implements IValue
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