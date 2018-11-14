<?php
/**
 * Created by youyi000.
 * DateTime: 2018/7/20 18:19
 * Describe：
 */

namespace ddd\Common\Domain\Value;


class Quantity extends BaseCommonValue
{
    #region property

    /**
     * 数量
     * @var   float
     */
    public $quantity;

    /**
     * 计量单位
     * @var   Unit
     */
    public $unit;

    #endregion

    public function __construct($quantity=0,$unit=null)
    {
        parent::__construct();
        $this->quantity=$quantity;
        if(empty($unit))
            $unit=Unit::createT();
        $this->unit=$unit;
    }

    /**
     * 创建数量
     * @param int $quantity
     * @param null $unit
     * @return Quantity
     * @throws \Exception
     */
    public static function create($quantity=0,$unit=null)
    {
        return new static($quantity,$unit);
    }

}