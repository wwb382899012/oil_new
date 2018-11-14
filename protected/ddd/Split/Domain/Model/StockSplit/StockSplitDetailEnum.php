<?php

namespace ddd\Split\Domain\Model\StockSplit;


use ddd\Common\BaseEnum;

/**
 * 出入库拆分明细常量
 * Class StockSplitEnum
 * @package ddd\Split\Domain\Model\StockSplitApply
 */
class StockSplitDetailEnum extends BaseEnum{
    /**
     * 有效
     */
    const STATUS_EFFECTIVE = 1;

    /**
     * 无效
     */
    const STATUS_INVALID = 0;

}