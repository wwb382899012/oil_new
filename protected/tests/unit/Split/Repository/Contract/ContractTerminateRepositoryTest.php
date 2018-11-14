<?php
/**
 * Created by: yu.li
 * Date: 2018/5/30
 * Time: 19:52
 * Desc: ContractTerminateRepositoryTest
 */

use ddd\Split\Domain\Model\Contract\ContractTerminate;
use ddd\Split\Repository\Contract\ContractTerminateRepository;
use \PHPUnit\Framework\TestCase;

class ContractTerminateRepositoryTest extends TestCase
{
    protected $repository;
    protected $contractId = 993;

    public function setUp() {
        $this->repository = new ContractTerminateRepository();
    }

    public function testFindContractTerminate() {
        $entity = $this->repository->findByContractId($this->contractId);
        $this->assertNotEmpty($entity);
        return $entity;
    }

    public function testStore() {
        $entity = ContractTerminate::create();
        $entity->reason = 'test contract terminate';
        $entity->contract_id = $this->contractId;
        $res = $this->repository->store($entity);
        $this->assertNotEmpty($res);
    }

}