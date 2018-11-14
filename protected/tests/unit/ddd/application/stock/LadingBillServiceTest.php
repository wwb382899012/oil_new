<?php

use ddd\repository\stock\LadingBillRepository;
use ddd\application\stock\LadingBillService;
use ddd\domain\entity\stock\LadingBill;
use PHPUnit\Framework\TestCase;

class LadingBillServiceTest extends TestCase{

    protected $service;
    protected  static $entity;

    /**
     *  设置基境(fixture)
     */
    protected function setUp(){
        $this->service = new LadingBillService();
    }

    /**
     * TODO: 观察保存的数据是否有遗漏，并且再测试一下更新操作
     *
     * @return LadingBill
     * @throws Exception
     */
    public function testAdd():LadingBill{
        $contractId = 1049;

        $contract = \ddd\repository\contract\ContractRepository::repository()->findByPk($contractId);

        $stockNoticeOrderEntity = LadingBill::create($contract);
        $stockNoticeOrderEntity->currency = $contract->currency;
        $stockNoticeOrderEntity->remark = 'ddddddddddddddddddd';

        $service = new \ddd\application\stock\LadingBillService();
        $result = $service->add($stockNoticeOrderEntity);

        $this->assertTrue($result);

        $newStockNoticeOrderEntity = LadingBillRepository::repository()->findByPk($stockNoticeOrderEntity->getId());

        $this->assertNotEmpty($newStockNoticeOrderEntity);

        self::$entity = $newStockNoticeOrderEntity;

        return $newStockNoticeOrderEntity;
    }

    /**
     * @depends testAdd
     * @param $stockNoticeOrderEntity
     * @return LadingBill
     * @throws Exception
     */
    public function testUpdate($stockNoticeOrderEntity):LadingBill{
        $stockNoticeOrderEntity = LadingBillRepository::repository()->findByPk($stockNoticeOrderEntity->getId());

        $this->assertNotEmpty($stockNoticeOrderEntity);

        $ladingBillGoodsEntity = $stockNoticeOrderEntity->items[1];
        $ladingBillGoodsEntity->quantity = new Quantity(1000, 2);

        $service = new \ddd\application\stock\LadingBillService();
        $result = $service->update($stockNoticeOrderEntity);

        $this->assertTrue($result);

        return $stockNoticeOrderEntity;
    }

    /**
     * @depends testUpdate
     * @param $stockNoticeOrderEntity
     * @return LadingBill
     * @throws Exception
     */
    public function testSubmit($stockNoticeOrderEntity):LadingBill{
        $stockNoticeOrderEntity = $this->testSubmit($stockNoticeOrderEntity);

        $this->assertEquals(\StockIn::STATUS_NEW, $stockNoticeOrderEntity->status);

        $result = $this->service->submit($stockNoticeOrderEntity);
        $this->assertTrue($result);

        return $stockNoticeOrderEntity;
    }

    /**
     * @afterClass
     */
    public static function tearDownSomeOtherSharedFixtures(){
        if(empty(self::$entity)){
            return;
        }
        \StockNotice::model()->deleteByPk(self::$entity->getId());
        \StockNoticeDetail::model()->deleteAll('batch_id = :batch_id', ['batch_id'=>self::$stockInEntity->getId()]);
    }
}