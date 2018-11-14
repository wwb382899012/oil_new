<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/12 10:39
 * Describe：
 */

namespace ddd\domain\entity\value;


use ddd\Common\Domain\BaseValue;
use ddd\Common\Domain\IValue;

class EnumValue extends BaseValue
{
    public $id;
    public $name;

    public function __construct($id=null,$name=null)
    {
        if(!empty($id))
            $this->id=$id;
        if(!empty($name))
            $this->name=$name;
    }

    public function equals(IValue $value)
    {
        return $this->id===$value->id;
    }



}