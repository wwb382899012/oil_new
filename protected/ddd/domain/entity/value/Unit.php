<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 11:23
 * Describe：
 */

namespace ddd\domain\entity\value;


use ddd\Common\Domain\BaseEntity;

class Unit extends BaseEntity
{
    public $id;

    public $name;

    public function __construct($id=0,$name="")
    {
        parent::__construct();
        $this->id=$id;
        $this->name=$name;
    }




}