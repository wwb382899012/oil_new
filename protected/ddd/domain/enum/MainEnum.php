<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/13 15:10
 * Describe：
 */

namespace ddd\domain\enum;

use ddd\Common\BaseEnum;

class MainEnum extends BaseEnum
{
    /**
     * 是主信息
     */
    const IS_MAIN = 1;
    /**
     * 不是主信息
     */
    const IS_NOT_MAIN = 0;

}