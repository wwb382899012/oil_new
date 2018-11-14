<?php

use ddd\domain\entity\stock\StockOut;
use ddd\repository\stock\DistributionOrderRepository;
use ddd\repository\stock\StockOutRepository;
use ddd\application\stock\StockOutService;

use ddd\domain\entity\value\Quantity;
use PHPUnit\Framework\TestCase;

class StockOutServiceTest extends TestCase{

    protected $service;
    protected  static $entity;

    /**
     *  设置基境(fixture)
     */
    protected function setUp(){
        $this->service = new StockOutService();
    }

    /**
     * @return StockOut
     * @throws Exception
     */
    public function testAdd() :StockOut{
        $deliveryOrderId = 201805060174;

        //配货单的基础是仓库
        $store_id = 0;
        $stockDeliveryOrderEntity = DistributionOrderRepository::repository()->findByPk($deliveryOrderId, 'store_id = :store_id', ['store_id' => $store_id]);

        $stockOutOrderEntity = StockOut::create($stockDeliveryOrderEntity);
        $stockOutOrderEntity->out_date = '2018-05-02';
        $stockOutOrderEntity->remark = "dddddddddddddddddddddddd";

        //设置配货数量
        foreach($stockOutOrderEntity->items as & $item){
            $item->quantity = $item->delivery_quantity;
        }

        $result = $this->service->save($stockOutOrderEntity);
        $this->assertTrue($result);

        $newStockOutOrderEntity = StockOutRepository::repository()->findByPk($stockOutOrderEntity->getId());
        $this->assertNotEmpty($newStockOutOrderEntity);

        self::$entity = $newStockOutOrderEntity;

        return $stockOutOrderEntity;
    }

    /**
     * @depends testAdd
     * @param $stockOutOrderEntity
     * @return StockOut
     * @throws Exception
     */
    public function testUpdate($stockOutOrderEntity) : StockOut{
        $stockOutOrderEntity->items[10]->quantity = new Quantity(55, 0);

        $stockOutOrderEntity->items[3] = $stockOutOrderEntity->items[10];
        $stockOutOrderEntity->items[3]->out_id = null;
        $stockOutOrderEntity->items[3]->quantity = new Quantity(10, 0);
        unset($stockOutOrderEntity->items[10]);

        $result = $this->service->update($stockOutOrderEntity);
        $this->assertTrue($result);

        return $stockOutOrderEntity;
    }

    /**
     * @depends testUpdate
     * @param $stockOutOrderEntity
     * @return StockOut
     * @throws Exception
     */
    public function testSubmit($stockOutOrderEntity) : StockOut{
        $result = $this->service->submit($stockOutOrderEntity);
        $this->assertTrue($result);

        return $stockOutOrderEntity;
    }

    /**
     * @depends testSubmit
     * @param $stockOutOrderEntity
     * @return StockOut
     */
    public function testRevocation($stockOutOrderEntity) : StockOut{
        $result = $this->service->revocation($stockOutOrderEntity);
        $this->assertTrue($result);

        return $stockOutOrderEntity;
    }

    /**
     * @depends testRevocation
     * @param $stockOutOrderEntity
     * @return StockOut
     * @throws Exception
     */
    public function testCheckBack($stockOutOrderEntity) : StockOut{

        $stockOutOrderEntity = $this->testSubmit($stockOutOrderEntity);

        $result = $this->service->checkBack($stockOutOrderEntity);
        $this->assertTrue($result);

        return $stockOutOrderEntity;
    }

    /**
     * @depends testCheckBack
     * @param $stockOutOrderEntity
     * @return StockOut
     * @throws Exception
     */
    public function testCheckPass($stockOutOrderEntity) : StockOut{
        $stockOutOrderEntity = $this->testSubmit($stockOutOrderEntity);

        $result = $this->service->checkPass($stockOutOrderEntity);
        $this->assertTrue($result);

        return $stockOutOrderEntity;
    }

    /**
     * @afterClass
     */
    public static function tearDownSomeOtherSharedFixtures(){
        if(empty(self::$entity)){
            return;
        }

        \StockOutOrder::model()->deleteByPk(self::$entity->getId());
        \StockOutDetail::model()->deleteAll('out_order_id = :out_order_id', ['out_order_id'=>self::$entity->getId()]);
    }
}