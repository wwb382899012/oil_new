<?php

use ddd\repository\stock\LadingBillRepository;
use ddd\repository\stock\StockInRepository;
use ddd\application\stock\StockInService;
use ddd\domain\entity\stock\StockIn;
use ddd\domain\entity\value\Quantity;
use PHPUnit\Framework\TestCase;

class StockInServiceTest extends TestCase{

    protected $service;
    protected  static $entity;

    /**
     *  设置基境(fixture)
     */
    protected function setUp(){
        $this->service = new StockInService();
    }

    /**
     *
     */
    public function testAdd():StockIn{
        $stockNoticeOrderEntity = LadingBillRepository::repository()->findByPk(201804160009);
        $this->assertNotEmpty($stockNoticeOrderEntity);

        $stockInOrderEntity = StockIn::create($stockNoticeOrderEntity);
        $result = $this->service->add($stockInOrderEntity);
        $this->assertTrue($result);

        self::$entity = $stockInOrderEntity;

        return $stockInOrderEntity;
    }

    /**
     * @depends testAdd
     * @param $stockInEntity
     * @return StockIn
     * @throws Exception
     */
    public function testUpdate($stockInEntity):StockIn{
        $quantity = 2002;

        $stockInItemEntity = $stockInEntity->items[1];
        $stockInItemEntity->quantity = new Quantity($quantity, 2);

        $result = $this->service->update($stockInEntity);
        $this->assertTrue($result);

        $newStockInEntity = StockInRepository::repository()->findByPk($stockInEntity->getId());
        $this->assertNotEmpty($newStockInEntity);
        $this->assertEquals($newStockInEntity->items[1]->quantity->quantity, $quantity);

        return $newStockInEntity;
    }

    /**
     * @depends testUpdate
     * @param $stockInEntity
     * @return StockIn
     * @throws Exception
     */
    public function testSubmit($stockInEntity):StockIn{
        $this->assertNotEmpty($stockInEntity);

        //断言这个类继承至
        $this->assertInstanceOf(StockIn::class, $stockInEntity);

        $result = $this->service->submit($stockInEntity);

        //断言这类有该属性
        $this->assertClassHasAttribute('stock_in_id', StockIn::class);

        $this->assertTrue($result);

        return $stockInEntity;
    }

    /**
     * @depends testSubmit
     * @param $stockInEntity
     * @return ddd\domain\entity\stock\StockIn
     */
    public function testRevocation($stockInEntity):StockIn{
        $result = $this->service->revocation($stockInEntity);
        $this->assertTrue($result);

        return $stockInEntity;
    }

    /**
     * @depends testRevocation
     * @param $stockInEntity
     * @return StockIn
     * @throws Exception
     */
    public function testCheckBack($stockInEntity):StockIn{
        $stockInEntity = $this->testSubmit($stockInEntity);

        $this->assertEquals(\StockIn::STATUS_SUBMIT, $stockInEntity->status);

        $result = $this->service->checkBack($stockInEntity);
        $this->assertTrue($result);

        return $stockInEntity;
    }

    /**
     * @depends testCheckBack
     * @param $stockInEntity
     * @return StockIn
     * @throws Exception
     */
    public function testCheckPass($stockInEntity):StockIn{
        $stockInEntity = $this->testSubmit($stockInEntity);

        $this->assertEquals(\StockIn::STATUS_SUBMIT, $stockInEntity->status);

        $result = $this->service->checkPass($stockInEntity);
        $this->assertTrue($result);

        return $stockInEntity;
    }

    /**
     * @afterClass
     */
    public static function tearDownSomeOtherSharedFixtures(){
        if(empty(self::$entity)){
            return;
        }

        \StockIn::model()->deleteByPk(self::$entity->getId());
        \StockInDetail::model()->deleteAll('stock_in_id = :stock_in_id', ['stock_in_id'=>self::$entity->getId()]);
    }

}
