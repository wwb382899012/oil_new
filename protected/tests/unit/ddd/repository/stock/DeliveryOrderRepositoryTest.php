<?php

use ddd\repository\stock\DeliveryOrderRepository;
use \PHPUnit\Framework\TestCase;


class DeliveryOrderRepositoryTest extends TestCase{

    protected $repository;
    protected $order_id = 201805290193;

    public function setUp(){
        $this->repository = new DeliveryOrderRepository();
    }

    public function testDataToEntity(){
        $model = DeliveryOrder::model()->with('details')->findByPk($this->order_id);
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

        $entity->status = DeliveryOrder::STATUS_NEW;
        $result = $this->repository->submit($entity);
        $this->assertTrue($result);



        $newEntity = $this->repository->findByPk($entity->getId());
        $this->assertEquals($newEntity->status,DeliveryOrder::STATUS_NEW);

        $newEntity->status = $oldStatus;
        $result = $this->repository->submit($newEntity);
        $this->assertTrue($result);

        $newEntity = $this->repository->findByPk($entity->getId());
        $this->assertEquals($newEntity->status,DeliveryOrder::STATUS_NEW);
    }
}