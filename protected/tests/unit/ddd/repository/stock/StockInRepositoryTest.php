<?php

use \ddd\domain\entity\stock\StockIn;
use \ddd\repository\stock\StockInRepository;
use \PHPUnit\Framework\TestCase;


class StockInRepositoryTest extends TestCase{
    protected $repository;
    protected $stock_in_id = 201805250001;

    public function setUp(){
        $this->repository = new StockInRepository();
    }

    public function testDataToEntity() : StockIn{
        $model = \StockIn::model()->with('details')->findByPk($this->stock_in_id);
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

        $entity->status = \StockIn::STATUS_NEW;
        $result = $this->repository->submit($entity);
        $this->assertTrue($result);



        $newEntity = $this->repository->findByPk($entity->getId());
        $this->assertEquals($newEntity->status,\StockIn::STATUS_NEW);

        $newEntity->status = $oldStatus;
        $result = $this->repository->submit($newEntity);
        $this->assertTrue($result);

        $newEntity = $this->repository->findByPk($entity->getId());
        $this->assertEquals($newEntity->status,\StockIn::STATUS_NEW);
    }
}