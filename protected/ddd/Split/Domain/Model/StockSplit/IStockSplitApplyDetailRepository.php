<?php

namespace ddd\Split\Domain\Model\StockSplit;

use ddd\Common\Domain\IRepository;

interface IStockSplitApplyDetailRepository extends IRepository{
    public function findByContractId($contractId): StockSplitDetail;
}