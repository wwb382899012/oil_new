<?php
/**
 * User: liyu
 * Date: 2018/8/16
 * Time: 15:08
 * Desc: PayReceiveEventServiceTest.php
 */

use ddd\Profit\Application\PayReceiveEventService;
use PHPUnit\Framework\TestCase;

class PayReceiveEventServiceTest extends TestCase
{

    public $service;
    public $contract_id;
    public $amount=46500000;

    public function setUp() {
        $this->service = new PayReceiveEventService();
        $this->contract_id = 1158;
    }

    public function testOnReceiveConfirm() {
        $res = $this->service->onReceiveConfirm($this->contract_id, 1);
        $this->assertTrue($res);
    }

    public function testOnPayConfirm() {
        $res = $this->service->onPayConfirm($this->contract_id, 1);
        $this->assertTrue($res);
    }

    public function testOnPayClaim() {
        $res = $this->service->onPayClaim($this->contract_id, 1);
        $this->assertTrue($res);
    }
}
