<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/12 9:47
 * Describe：
 */

namespace ddd\domain\entity\risk;


use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\value\EnumValue;
use ddd\domain\enum\AddSubtractEnum;

abstract class RiskAmountLog extends BaseEntity
{
    /**
     * @var      bigint
     */
    public $id;

    /**
     * @var      bigint
     */
    public $amountId;

    /**
     * @var      int
     */
    public $category;

    /**
     * @var      int
     */
    public $method;

    /**
     * @var      int
     */
    public $relation_id;

    /**
     * @var      int
     */
    public $amount;

    /**
     * @var      string
     */
    public $remark;

    /**
     * @var      datetime
     */
    public $create_time;

    public function initMethod($amount = null)
    {
        $amount = $amount !== null ? $amount : $this->amount;
        if ($amount >= 0)
            $this->method = AddSubtractEnum::ADD;
        else
            $this->method = AddSubtractEnum::SUBTRACT;
    }

}