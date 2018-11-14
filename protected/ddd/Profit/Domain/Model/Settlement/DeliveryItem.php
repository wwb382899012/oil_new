<?php
/**
 * Created by wwb.
 * DateTime: 2018/3/21 11:35
 * Describe：出库明细
 */

namespace ddd\Profit\Domain\Model\Settlement;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Currency\Currency;
use ddd\Common\IAggregateRoot;
use ddd\Common\Domain\Value\Money;
use ddd\domain\entity\value\Quantity;

class DeliveryItem extends BaseEntity
{

    #region property


    /**
     * 商品id
     * @var   int
     */
    public $goods_id;

    /**
     * 入库单id
     * @var   int
     */
    public $stock_in_id;

    /**
     * 采购合同id
     * @var   int
     */
    public $contract_id;

    /**
     * 单位换算比
     * @var   int
     */
    public $exchange_rate;

    /**
     * 出库数量
     * @var   Quantity
     */
    public $out_quantity;




    #endregion

    public function __construct()
    {
        parent::__construct();
        $this->out_quantity=new Quantity(0);

    }


}

