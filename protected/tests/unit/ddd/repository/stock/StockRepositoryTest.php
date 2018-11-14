<?php

use \ddd\repository\stock\StockRepository;
use \PHPUnit\Framework\TestCase;


class StockRepositoryTest extends TestCase{

    protected $repository;
    protected static $entity;

    protected $quantity = 500;

    public function setUp(){
        $this->repository = new StockRepository();

        $stockModel = new Stock();
        $stockModel->stock_id = 0;
        $stockModel->quantity = 5000;
        $stockModel->quantity_balance = 5000;
        $stockModel->quantity_frozen = 0;
        $stockModel->quantity_out = 0;
        $stockModel->save();

        self::$entity = $this->repository->findByPk($stockModel->stock_id);
    }

    public function testFreeze(){
        $this->repository->freeze(self::$entity, $this->quantity);

        $entity = $this->repository->findByPk(self::$entity->stock_id);
        $this->assertEquals($entity->quantity, self::$entity->quantity);
        $this->assertEquals($entity->quantity, $entity->quantity_balance + $this->quantity);
        $this->assertEquals($entity->quantity_frozen, $this->quantity);

        return $entity;
    }

    /**
     * @depends testFreeze
     * @param $entity
     * @return \ddd\domain\entity\BaseEntity|null
     * @throws Exception
     */
    public function testUnFreeze($entity){
        $this->repository->unFreeze($entity, $this->quantity);

        $entity = $this->repository->findByPk(self::$entity->stock_id);
        $this->assertEquals($entity->quantity, self::$entity->quantity);
        $this->assertEquals($entity->quantity_balance, self::$entity->quantity);
        $this->assertEquals($entity->quantity_frozen, 0);

        return $entity;
    }
    
    public function testOut(){
        $this->repository->out(self::$entity, $this->quantity);

        $entity = $this->repository->findByPk(self::$entity->stock_id);
        $this->assertEquals($entity->quantity, self::$entity->quantity);
        $this->assertEquals($entity->quantity, $entity->quantity_balance + $this->quantity);
        $this->assertEquals($entity->quantity_frozen, 0);
        $this->assertEquals($entity->quantity_out, $this->quantity);

        return $entity;
    }

    /**
     * @depends testOut
     * @param $entity
     * @return \ddd\domain\entity\BaseEntity|null
     * @throws Exception
     */
    public function testRefund($entity){
        $this->repository->refund($entity, $this->quantity);

        $entity = $this->repository->findByPk(self::$entity->stock_id);
        $this->assertEquals($entity->quantity, self::$entity->quantity);
        $this->assertEquals($entity->quantity_balance, self::$entity->quantity);
        $this->assertEquals($entity->quantity_frozen, 0);
        $this->assertEquals($entity->quantity_out, 0);

        return $entity;
    }

    /**
     * @afterClass
     */
    public static function tearDownSomeOtherSharedFixtures(){
        if(empty(self::$entity)){
            return;
        }

        \Stock::model()->deleteByPk(self::$entity->getId());
    }
}