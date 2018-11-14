<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/12 16:03
 * Describe：
 */

namespace ddd\domain\entity\base;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\BaseValue;
use ddd\domain\entity\value\Quantity;
use ddd\Common\Domain\IValue;

/**
 * @Name            商品交易信息
 * @DateTime        2018年4月11日 14:58:49
 * @Author          youyi000
 */
abstract class BaseContractGoods extends BaseEntity
{

    /**
     * 商品
     * @var   int
     */
    public $goods_id;

    /**
     * 数量
     * @var   Quantity
     */
    public $quantity;

    /**
     * 溢短装比
     * @var   float
     */
    public $more_or_less_rate;
    /**
     * 合同单位换算比
     * @var   float
     */
    public $unit_convert_rate;

    /**
     * 币种
     * @var   int
     */
    public $currency;

    /**
     * 单价
     * @var   int
     */
    public $price;

    /**
     * 总金额
     * @var   int
     */
    public $amount;

    /**
     * 计价参考标的
     * @var   int
     */
    public $refer_target;

    /**
     * 备注
     * @var   string
     */
    public $remark;

    public function equals(IValue $value)
    {
        return $this->goods_id===$value->goods_id && $this->quantity==$value->quantity;
    }


}