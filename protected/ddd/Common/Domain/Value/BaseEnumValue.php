<?php
/**
 * Created by youyi000.
 * DateTime: 2018/7/26 15:51
 * Describe：
 */

namespace ddd\Common\Domain\Value;


use ddd\Common\Domain\IValue;

class BaseEnumValue extends BaseCommonValue
{
    /**
     * 标识或id
     * @var   int
     */
    public $id;

    /**
     * 名称
     * @var   string
     */
    public $name;

    /**
     * 是否相等
     * @param BaseEnumValue $value
     * @return bool
     */
    public function equals(IValue $value)
    {
        return $this->id===$value->id;
    }


}