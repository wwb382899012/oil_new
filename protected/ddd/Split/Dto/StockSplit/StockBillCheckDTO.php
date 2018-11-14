<?php

/**
 *
 */
namespace ddd\Split\Dto\StockSplit;

use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\Split\Domain\Model\Stock\StockIn;
use ddd\Split\Dto\TradeGoodsDTO;
use Map;

/**
 * 出入库实体DTO
 * Class StockBillDTO
 * @package ddd\Split\Dto
 */
class StockBillCheckDTO extends StockBillDTO{

    public $check_id = 0;

    /**
     * 申请标识
     * @var
     */
    public $apply_id = 0;

    /**
     * 出入库id
     * @var big integer
     */
    public $bill_id;

    /**
     * 出入库单号
     * @var string
     */
    public $bill_code;

    /**
     * 状态
     * @var
     */
    public $status;

    /**
     * 状态别名
     * @var string
     */
    public $status_name;

    /**
     * 是否虚拟单
     * @var bool
     */
    public $is_virtual = true;

    /**
     * 是否可审核
     * @var bool
     */
    public $is_can_check = false;

    public $is_can_view = false;

    /**
     * 商品明细
     * @var array TradeGoodsDTO
     */
    public $goods_items = [];

}