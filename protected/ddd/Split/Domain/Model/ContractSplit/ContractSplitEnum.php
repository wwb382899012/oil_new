<?php

namespace ddd\Split\Domain\Model\ContractSplit;

use ddd\Common\BaseEnum;

class ContractSplitEnum extends BaseEnum{

    /**
     * 新合同等待审核通过
     */
    const STATUS_WAIT_CONFIRM = 0;

    /**
     * 新合同业务审核通过
     */
    const STATUS_BUSINESS_CHECKED = 1;
}