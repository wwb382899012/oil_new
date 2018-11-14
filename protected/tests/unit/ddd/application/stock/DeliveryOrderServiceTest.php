<?php

use ddd\repository\stock\DeliveryOrderRepository;
use ddd\application\stock\DeliveryOrderService;
use ddd\domain\entity\stock\DeliveryOrder;
use ddd\domain\entity\value\Quantity;
use PHPUnit\Framework\TestCase;

class DeliveryOrderServiceTest extends TestCase{

    protected $service;
    protected  static $entity = [];
    protected $stock_in_id = 201804090002;

    /**
     *  设置基境(fixture)
     */
    protected function setUp(){
        $this->service = new DeliveryOrderService();
    }

    /**
     * @return DeliveryOrder
     * @throws Exception
     */
    public function testGetDeliveryOrderEntity():DeliveryOrder{
        $contractId = 1046;

        $contract = \ddd\repository\contract\ContractRepository::repository()->findByPk($contractId);

        $stock1 = new ddd\domain\entity\stock\Stock();
        $stock1->stock_id = 1;
        $stock1->goods_id = 10;

        $stock2 = new ddd\domain\entity\stock\Stock();
        $stock2->stock_id = 3;
        $stock2->goods_id = 3;

        $stock3 = new ddd\domain\entity\stock\Stock();
        $stock3->stock_id = 2;
        $stock3->goods_id = 10;

        $stock4 = new ddd\domain\entity\stock\Stock();
        $stock4->stock_id = 4;
        $stock4->goods_id = 9;

        $stockDeliveryDetail1 = \ddd\domain\entity\stock\DistributionOrderItem::create($stock1);
        $stockDeliveryDetail1->quantity = new Quantity(40, 0);
        $stockDeliveryDetail1->remark = "nnnnnnnnnnnnnnnnnn";
        $stockDeliveryDetail2 = \ddd\domain\entity\stock\DistributionOrderItem::create($stock2);
        $stockDeliveryDetail2->quantity = new Quantity(60, 0);
        $stockDeliveryDetail2->remark = "fffffffffffffffffff";

        $stockDeliveryDetail3 = \ddd\domain\entity\stock\DistributionOrderItem::create($stock3);
        $stockDeliveryDetail3->quantity = new Quantity(4, 0);
        $stockDeliveryDetail3->remark = "kkkkkkkkkkkkkkkk";
        $stockDeliveryDetail4 = \ddd\domain\entity\stock\DistributionOrderItem::create($stock4);
        $stockDeliveryDetail4->quantity = new Quantity(6, 0);
        $stockDeliveryDetail4->remark = "lllllllllllllllllll";

        $deliveryOrderDetail1 = \ddd\domain\entity\stock\DeliveryOrderDetail::create(1);
        $deliveryOrderDetail1->quantity = new Quantity(200, 0);
        $deliveryOrderDetail1->quantity_actual = new Quantity(100, 0);
        $deliveryOrderDetail1->remark = "abckdef";
        $deliveryOrderDetail1->addStockDeliveryItem($stockDeliveryDetail1);
        $deliveryOrderDetail1->addStockDeliveryItem($stockDeliveryDetail2);

        $deliveryOrderDetail2 = \ddd\domain\entity\stock\DeliveryOrderDetail::create(3);
        $deliveryOrderDetail2->quantity = new Quantity(200, 0);
        $deliveryOrderDetail2->quantity_actual = new Quantity(100, 0);
        $deliveryOrderDetail2->remark = "jkiuekii";
        $deliveryOrderDetail2->addStockDeliveryItem($stockDeliveryDetail3);
        $deliveryOrderDetail2->addStockDeliveryItem($stockDeliveryDetail4);


        $deliveryOrderEntity = \ddd\domain\entity\stock\StoreDeliveryOrder::create($contract);
        $deliveryOrderEntity->addDetailsItem($deliveryOrderDetail1);
        $deliveryOrderEntity->addDetailsItem($deliveryOrderDetail2);
        $deliveryOrderEntity->remark = "ttttttttttttttttt";

        $this->assertNotEmpty($deliveryOrderEntity->getId());

        self::$entity[] = $deliveryOrderEntity;

        return $deliveryOrderEntity;
    }

    /**
     *
     * @depends  testGetDeliveryOrderEntity
     */
    public function testAddStoreDeliveryOrder($deliveryOrderEntity):DeliveryOrder{
        $result = $this->service->add($deliveryOrderEntity);

        $this->assertTrue($result);

        $newDeliveryOrderEntity = DeliveryOrderRepository::repository()->findByPk($deliveryOrderEntity->getId());
        $this->assertNotEmpty($newDeliveryOrderEntity);

        return $newDeliveryOrderEntity;
    }

    /**
     * @depends testGetDeliveryOrderEntity
     * @param $deliveryOrderEntity
     * @return DeliveryOrder
     * @throws Exception
     */
    public function testAddDirectDeliveryOrder($deliveryOrderEntity):DeliveryOrder{
        $deliveryOrderEntity->stock_in_id = $this->stock_in_id;
        $result = $this->service->add($deliveryOrderEntity);

        $this->assertTrue($result);

        $newDeliveryOrderEntity = DeliveryOrderRepository::repository()->findByPk($deliveryOrderEntity->getId());
        $this->assertNotEmpty($newDeliveryOrderEntity);
        $this->assertEquals($newDeliveryOrderEntity->stock_in_id,$this->stock_in_id);

        return $newDeliveryOrderEntity;
    }

    /**
     * @depends testAddStoreDeliveryOrder
     * @param $deliveryOrderEntity
     * @return DeliveryOrder
     * @throws Exception
     */
    public function testUpdate($deliveryOrderEntity):DeliveryOrder{
        $this->assertNotEmpty($deliveryOrderEntity);

        $deliveryOrderEntity = \ddd\repository\stock\DeliveryOrderRepository::repository()->findByPk($deliveryOrderEntity->getId());


        //更新发货单明细
        $deliveryOrderDetail1 = $deliveryOrderEntity->items[1];
        $deliveryOrderDetail1->quantity = new Quantity(7000, 0);
        $deliveryOrderDetail1->quantity_actual = new Quantity(6000, 0);

        //更新对应的配货的明细
        $stockDeliveryDetail1 = $deliveryOrderDetail1->items[1];
        $stockDeliveryDetail1->quantity = new Quantity(800, 0);
        //
        $stockDeliveryDetail2 = $deliveryOrderDetail1->items[3];
        $stockDeliveryDetail2->quantity = new Quantity(500, 0);


        //
        $stock3 = new ddd\domain\entity\stock\Stock();
        $stock3->stock_id = 2;
        $stock3->goods_id = 10;

        $stock4 = new ddd\domain\entity\stock\Stock();
        $stock4->stock_id = 4;
        $stock4->goods_id = 9;

        $stockDeliveryDetail3 = \ddd\domain\entity\stock\DistributionOrderItem::create($stock3);
        $stockDeliveryDetail3->quantity = new Quantity(4, 0);
        $stockDeliveryDetail3->remark = "kkkkkkkkkkkkkkkk";
        $stockDeliveryDetail4 = \ddd\domain\entity\stock\DistributionOrderItem::create($stock4);
        $stockDeliveryDetail4->quantity = new Quantity(6, 0);
        $stockDeliveryDetail4->remark = "lllllllllllllllllll";

        //新增配货明细
        $deliveryOrderDetail2 = \ddd\domain\entity\stock\DeliveryOrderDetail::create(3);
        $deliveryOrderDetail2->quantity = new Quantity(1000, 0);
        $deliveryOrderDetail2->quantity_actual = new Quantity(100, 0);
        $deliveryOrderDetail2->remark = "remarkremarkremark";
        $deliveryOrderDetail2->addStockDeliveryItem($stockDeliveryDetail3);
        $deliveryOrderDetail2->addStockDeliveryItem($stockDeliveryDetail4);

        $deliveryOrderEntity->addDetailsItem($deliveryOrderDetail2);

        $result = $this->service->update($deliveryOrderEntity);
        $this->assertTrue($result);

        $deliveryOrderEntity = \ddd\repository\stock\DeliveryOrderRepository::repository()->findByPk($deliveryOrderEntity->getId());
        $this->assertNotEmpty($deliveryOrderEntity);

        return $deliveryOrderEntity;
    }

    /**
     * @depends testUpdate
     * @param $deliveryOrderEntity
     * @return DeliveryOrder
     * @throws Exception
     */
    public function testSubmit($deliveryOrderEntity):DeliveryOrder{
        $this->assertNotEmpty($deliveryOrderEntity);

        $deliveryOrderEntity = \ddd\repository\stock\DeliveryOrderRepository::repository()->findByPk($deliveryOrderEntity->getId());

        $result = $this->service->submit($deliveryOrderEntity);
        $this->assertTrue($result);

        $deliveryOrderEntity = \ddd\repository\stock\DeliveryOrderRepository::repository()->findByPk($deliveryOrderEntity->getId());
        $this->assertNotEmpty($deliveryOrderEntity);

        return $deliveryOrderEntity;
    }

    /**
     * @depends testSubmit
     * @param $deliveryOrderEntity
     * @return DeliveryOrder
     * @throws Exception
     */
    public function testRevocation($deliveryOrderEntity):DeliveryOrder{
        $this->assertNotEmpty($deliveryOrderEntity);

        $deliveryOrderEntity = \ddd\repository\stock\DeliveryOrderRepository::repository()->findByPk($deliveryOrderEntity->getId());
        $result = $this->service->checkBack($deliveryOrderEntity);
        $this->assertTrue($result);

        $deliveryOrderEntity = \ddd\repository\stock\DeliveryOrderRepository::repository()->findByPk($deliveryOrderEntity->getId());
        $this->assertNotEmpty($deliveryOrderEntity);

        return $deliveryOrderEntity;
    }

    /**
     * @depends testRevocation
     * @param $deliveryOrderEntity
     * @return DeliveryOrder
     * @throws Exception
     */
    public function testCheckBack($deliveryOrderEntity):DeliveryOrder{
        $this->assertNotEmpty($deliveryOrderEntity);

        $deliveryOrderEntity = $this->testSubmit($deliveryOrderEntity);

        $deliveryOrderEntity = \ddd\repository\stock\DeliveryOrderRepository::repository()->findByPk($deliveryOrderEntity->getId());
        $result = $this->service->checkBack($deliveryOrderEntity);
        $this->assertTrue($result);

        $deliveryOrderEntity = \ddd\repository\stock\DeliveryOrderRepository::repository()->findByPk($deliveryOrderEntity->getId());
        $this->assertNotEmpty($deliveryOrderEntity);

        return $deliveryOrderEntity;
    }

    /**
     * @depends testCheckBack
     * @param $deliveryOrderEntity
     * @return DeliveryOrder
     * @throws Exception
     */
    public function testCheckPass($deliveryOrderEntity):DeliveryOrder{
        $this->assertNotEmpty($deliveryOrderEntity);

        $deliveryOrderEntity = $this->testSubmit($deliveryOrderEntity);

        $deliveryOrderEntity = \ddd\repository\stock\DeliveryOrderRepository::repository()->findByPk($deliveryOrderEntity->getId());
        $result = $this->service->checkPass($deliveryOrderEntity);
        $this->assertTrue($result);

        $deliveryOrderEntity = \ddd\repository\stock\DeliveryOrderRepository::repository()->findByPk($deliveryOrderEntity->getId());
        $this->assertNotEmpty($deliveryOrderEntity);

        return $deliveryOrderEntity;
    }

    /**
     * @afterClass
     */
    public static function tearDownSomeOtherSharedFixtures(){
        if(empty(self::$entity)){
            return;
        }

        foreach(self::$entity as $item){
            \DeliveryOrder::model()->deleteByPk($item->getId());
        }
    }
}