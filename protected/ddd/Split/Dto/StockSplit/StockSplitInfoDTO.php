<?php

/**
 * 出入库拆分信息
 */
namespace ddd\Split\Dto\StockSplit;

use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\infrastructure\DIService;
use ddd\Split\Domain\Model\Contract\Contract;
use ddd\Split\Domain\Model\Contract\IContractRepository;

class StockSplitInfoDTO extends BaseDTO{

    /**
     * 原始合同
     * @var   ContractDTO
     */
    public $origin_contract;

    /**
     * 拆分合同明细
     * @var
     */
    public $contract_split_items = [];

}
