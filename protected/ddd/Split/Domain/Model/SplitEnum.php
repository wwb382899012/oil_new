<?php

namespace ddd\Split\Domain\Model;


use ddd\Common\BaseEnum;

class SplitEnum extends BaseEnum{
    /**
     * 是否虚拟单
     */
    const IS_VIRTUAL = 1;
    const IS_REALITY = 0;
}