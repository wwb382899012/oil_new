<?php

namespace ddd\Split\Domain\Model\StockSplit;

use ddd\Common\Domain\IRepository;

interface IStockSplitApplyRepository extends IRepository{

    function submit(StockSplitApply $stockSplitApply);

    function checkBack(StockSplitApply $stockSplitApply);

    function checkPass(StockSplitApply $stockSplitApply);

    function findByBillId($billId);

    function findAllByContractId($contractId);

    function findByApplyId($applyId);
}