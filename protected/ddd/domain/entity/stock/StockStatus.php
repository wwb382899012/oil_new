<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/23 11:37
 * Describe：
 *  库存状态
 */

namespace ddd\domain\entity\stock;


class StockStatus
{
    /**
     * 可用的
     */
    const  AVAILABLE=1;

    /**
     * 已用完，不可用
     */
    const  EXHAUSTED=9;
}