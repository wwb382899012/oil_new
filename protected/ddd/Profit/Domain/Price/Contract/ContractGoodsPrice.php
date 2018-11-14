<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/13 16:05
 * Describe：
 */

namespace ddd\Profit\Domain\Price\Contract;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\Money;

class ContractGoodsPrice extends BaseEntity
{
    #region property

    /**
     * 标识id
     * @var   int
     */
    public $id;

    /**
     * 合同id
     * @var   int
     */
    public $contract_id = 0;

    /**
     * 商品id
     * @var   int
     */
    public $goods_id = 0;

    /**
     * 价格
     * @var   Money
     */
    public $price;

    /**
     * 更新时间
     * @var   datetime
     */
    public $update_time;

    /**
     * 创建时间
     * @var   datetime
     */
    public $create_time;

    #endregion

    public function __construct(?array $params = null)
    {
        parent::__construct($params);
        $this->price=new Money(0);
    }

    public static function create()
    {
        return new static();
    }
}