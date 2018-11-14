<?php

use ddd\Split\Domain\Model\Contract\BuyContract;
use ddd\Split\Domain\Model\Contract\SellContract;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApply;
use ddd\Split\Domain\Model\Stock\StockIn;
use ddd\Split\Domain\Model\Stock\StockOut;
use ddd\Split\Domain\Service\SplitService;
use PHPUnit\Framework\TestCase;

class SplitServiceTest extends TestCase
{

    public function testGenerateStockIn() {
        $contract = new BuyContract();
        $contract->original_id = 10;
        $contract->contract_id = 2001;
        $contract->create_time = date('Y-m-d H:i:s');
        $contract->update_time = date('Y-m-d H:i:s');

        $stockIn = new StockIn();
        $stockIn->bill_id = 9999;

        $contractSplitApply = new ContractSplitApply();
        $contractSplitApply->contract_id = 10;

        $service = new SplitService();
        $service->generateStockIn($contract, $stockIn, $contractSplitApply);
        $this->assertTrue(true);
    }

    public function testGenerateStockOut() {
        $contract = new SellContract();
        $contract->original_id = 13;
        $contract->contract_id = 2001;
        $contract->create_time = date('Y-m-d H:i:s');
        $contract->update_time = date('Y-m-d H:i:s');

        $stockIn = new StockOut();
        $stockIn->bill_id = 201806120005;

        $contractSplitApply = new ContractSplitApply();
        $contractSplitApply->contract_id = 10;

        $service = new SplitService();
        $service->generateStockOut($contract, $stockIn, $contractSplitApply);
        $this->assertTrue(true);
    }

    public function testGenerateContracts() {
        try {
            $service = new SplitService();
            $contractId = 1640;
            $applyId = 12;
            $contract = \ddd\repository\contract\ContractRepository::repository()->findByPk($contractId);
            $splitApply = \ddd\Split\Repository\ContractSplit\ContractSplitApplyRepository::repository()->findByPk($applyId);
            $res = $service->generateContracts($contract, $splitApply);
            $this->assertTrue($res);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

}