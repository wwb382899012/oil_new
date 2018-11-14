<?php
/**
 * User: liyu
 * Date: 2018/8/14
 * Time: 17:01
 * Desc: InvoiceEventServiceTest.php
 */

use ddd\Profit\Application\InvoiceEventService;
use PHPUnit\Framework\TestCase;

class ProfitEventServiceTest extends TestCase
{

    public $service;
    public $order_id;
    public $batch_id;
    public $out_order_id;
    public $sell_contract_id;
    public $buy_contract_id;

    public function setUp() {
        $this->service = new \ddd\Profit\Application\ProfitEventService();
        $this->order_id = 201807070040;
        $this->batch_id = 201807090001;
        $this->sell_contract_id=1347;
        $this->buy_contract_id=1350;
        $this->out_order_id=201807070007;
    }

    public function testonDeliverySettlePass() {
        $this->service->onDeliverySettlePass($this->order_id);
    }

    public function testonContractSettlePass() {
        $this->service->onContractSettlePass($this->sell_contract_id);
    }

    public function testonStockOutPass() {
        $this->service->onStockOutPass($this->out_order_id);
    }

    public function testonBatchSettlePass() {
        $this->service->onBatchSettlePass($this->batch_id);
    }

    public function testonBuyContractSettlePass() {
        $this->service->onBuyContractSettlePass($this->buy_contract_id);
    }




}
