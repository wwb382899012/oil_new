<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/13 15:10
 * Describe：
 */

namespace ddd\domain\enum;

use ddd\Common\BaseEnum;

class AddSubtractEnum extends BaseEnum
{
    /**
     * 增加
     */
    const ADD=1;
    /**
     * 减少
     */
    const SUBTRACT=-1;

}