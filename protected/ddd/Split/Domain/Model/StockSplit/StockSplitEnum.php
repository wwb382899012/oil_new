<?php

namespace ddd\Split\Domain\Model\StockSplit;


use ddd\Common\BaseEnum;

/**
 * 出入库拆分常量
 * Class StockSplitEnum
 * @package ddd\Split\Domain\Model\StockSplitApply
 */
class StockSplitEnum extends BaseEnum{

    /**
     * 无效，不勾选平移
     */
    const STATUS_INVALID = -3;

    /**
     * 驳回
     */
    const STATUS_BACK = -1;

    /**
     * 新增加
     */
    const STATUS_NEW = 0;

    /**
     * 提交
     */
    const STATUS_SUBMIT = 1;

    /**
     * 审核通过
     */
    const STATUS_PASS = 10;

    /**
     * 入库
     */
    const TYPE_STOCK_IN = 1;

    /**
     * 出库
     */
    const TYPE_STOCK_OUT = 2;

    /**
     * 拆分状态，默认
     */
    const SPLIT_STATUS_DEFAULT = 0;

    /**
     * 拆分状态，拆分中
     */
    const SPLIT_STATUS_ONGOING = 1;

    /**
     * 拆分状态，已经拆分
     */
    const SPLIT_STATUS_END = 2;

    /**
     * 入库单/出库单 是拆分生成
     */
    const SPLIT_TYPE_SPLIT=1;

    /**
     * 入库单/出库单  不是拆分生成
     */
    const SPLIT_TYPE_NOT_SPLIT=0;
}