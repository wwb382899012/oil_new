<?php

/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2018/6/8
 * Time: 15:20
 */

namespace ddd\Split\Dto;

use ddd\Common\Application\BaseDTO;

/**
 * 原合同、拆分合同实体DTO
 * Class SplitContractDTO
 * @package ddd\Split\Dto
 */
abstract class BaseContractDTO extends BaseDTO{

    /**
     * 合同ID
     * @var big integer
     */
    public $contract_id;

    /**
     * 合同编号
     * @var string
     */
    public $contract_code;

    /**
     * 合作方名称
     * @var string
     */
    public $partner_name;

    /**
     * 商品明细
     * @var TradeGoodsDTO
     */
    public $goods_items = [];

    /**
     *  合同类型
     * @var int
     */
    public $type = 0;

    /**
     * 关联的出入库单数组
     * @return array StockBillDTO
     */
    public $stock_bill_items = [];

}