<?php

use ddd\repository\stock\LadingBillRepository;
use \PHPUnit\Framework\TestCase;


class LadingBillRepositoryTest extends TestCase{

    protected $repository;
    protected $batch_id = 201804160020;

    public function setUp(){
        $this->repository = new LadingBillRepository();
    }

    public function testDataToEntity(){
        $model = StockNotice::model()->findByPk($this->batch_id);
        $this->assertNotEmpty($model);

        $entity =  $this->repository->dataToEntity($model);

        $this->assertNotEmpty($entity);

        return $entity;
    }

    /**
     * @depends testDataToEntity
     * @param $entity
     * @throws Exception
     */
    public function testStore($entity){
        $this->assertNotEmpty($entity);

        $result = $this->repository->store($entity);

        $this->assertTrue($result);
    }

    /**
     * @depends testDataToEntity
     * @param $entity
     * @throws Exception
     */
    public function testSubmit($entity){
        $this->assertNotEmpty($entity);

        $entity = $this->repository->findByPk($entity->getId());
        $this->assertNotEmpty($entity);

        $oldStatus = $entity->status;

        $entity->status = StockNotice::STATUS_NEW;
        $result = $this->repository->submit($entity);
        $this->assertTrue($result);



        $newEntity = $this->repository->findByPk($entity->getId());
        $this->assertEquals($newEntity->status,StockNotice::STATUS_NEW);

        $newEntity->status = $oldStatus;
        $result = $this->repository->submit($newEntity);
        $this->assertTrue($result);

        $newEntity = $this->repository->findByPk($entity->getId());
        $this->assertEquals($newEntity->status,StockNotice::STATUS_NEW);
    }
}