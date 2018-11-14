<?php

use \ddd\repository\stock\StockOutRepository;
use \PHPUnit\Framework\TestCase;


class StockOutRepositoryTest extends TestCase{

    protected $repository;
    protected $stock_out_id = 201804190010;

    public function setUp(){
        $this->repository = new StockOutRepository();
    }

    public function testDataToEntity(){
        $model = \StockOutOrder::model()->with('details')->findByPk($this->stock_out_id);
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

        $entity->status = \StockOutOrder::STATUS_SAVED;
        $result = $this->repository->submit($entity);
        $this->assertTrue($result);


        $newEntity = $this->repository->findByPk($entity->getId());
        $this->assertEquals($newEntity->status,\StockOutOrder::STATUS_SAVED);

        $newEntity->status = $oldStatus;
        $result = $this->repository->submit($newEntity);
        $this->assertTrue($result);

        $newEntity = $this->repository->findByPk($entity->getId());
        $this->assertEquals($newEntity->status,\StockOutOrder::STATUS_SAVED);
    }
}