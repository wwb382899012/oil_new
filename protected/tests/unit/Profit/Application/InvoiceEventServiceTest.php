<?php
/**
 * User: liyu
 * Date: 2018/8/14
 * Time: 17:01
 * Desc: InvoiceEventServiceTest.php
 */

use ddd\Profit\Application\InvoiceEventService;
use PHPUnit\Framework\TestCase;

class InvoiceEventServiceTest extends TestCase
{

    public $service;
    public $invoice_application_id;
    public $invoice_id;
    public $contract_id;

    public function setUp() {
        $this->service = new InvoiceEventService();
        $this->invoice_application_id = 2018070400063;
        $this->invoice_id = 9;
        $this->contract_id=1303;
    }

    public function testOnInputInvoiceCheckPass() {
        $res=$this->service->onInputInvoiceCheckPass($this->contract_id, $this->invoice_application_id);
        $this->assertTrue($res);
    }

    public function testOnInvoiceCheckPass() {
        $res = $this->service->onInvoiceCheckPass($this->contract_id, $this->invoice_id);
        $this->assertTrue($res);
    }

    public function testContractProfitDataRepair() {

    }
}
