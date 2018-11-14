<?php
/**
 * Created by: yu.li
 * Date: 2018/5/30
 * Time: 20:22
 * Desc: ContractTerminateTest
 */

use ddd\Split\Domain\Model\Contract\BuyContract;
use ddd\Split\Domain\Model\Contract\ContractTerminate;
use ddd\Split\Repository\Contract\ContractRepository;
use PHPUnit\Framework\TestCase;

class ContractTerminateTest extends TestCase
{
    protected $entity;

    public function setUp() {
        $this->entity = new ContractTerminate();
        $this->entity->contract_id = 1091;
    }

    public function testCheckBack() {

    }

    public function testIsCanTerminate() {
        $contract = ContractRepository::repository()->findByPk($this->entity->contract_id);
        $res = $this->entity->isCanTerminate($contract);
        $this->assertTrue($res);
    }

    public function testIsCanSubmit() {

    }

    public function testAddFiles() {

    }

    public function testRemoveFiles() {

    }

    public function testIsCanEdit() {

    }

    public function testCreate() {

    }

    public function testCheckPass() {

    }

    public function testSubmit() {

    }
}
