<?php

use ddd\domain\entity\Attachment;
use ddd\Split\Application\StockSplitService;
use ddd\repository\stock\StockInRepository;
use ddd\Split\Domain\Model\StockSplit\StockInSplitApply;
use ddd\Split\Domain\Model\StockSplit\StockInSplitDetail;
use ddd\Split\Domain\Model\TradeGoods;
use ddd\domain\entity\value\Quantity;
use ddd\Split\Repository\StockSplit\StockSplitApplyRepository;
use PHPUnit\Framework\TestCase;


class StockInSplitServiceTest extends TestCase{

    public static $multi_stock_in_id = 201801040005;
    public static $stock_in_id = 201711300001;
    public $unit = 2;

    /**
     * @after
     */
    public function setUp(){
        $this->unit = 2;
        self::$stock_in_id = 201711300001;
    }

    public function testAdd(){
        $stockInEntity = StockInRepository::repository()->findByPk(self::$stock_in_id);
        $this->assertNotNull($stockInEntity);

        $contract_id = 4;
        $split_contract_id = 5; //拆分合同标识/id
        $goods_id = 3;
        $quantity = 206.48;

        $tradeGoodsEntity = TradeGoods::create($goods_id);
        $tradeGoodsEntity->goods_id = $goods_id;
        $tradeGoodsEntity->quantity = new Quantity($quantity,$this->unit);

        $stockInSplitDetail = StockInSplitDetail::create($stockInEntity,$split_contract_id);
        $stockInSplitDetail->addGoodsItem($tradeGoodsEntity);

        $file1 = Attachment::create();
        $file1->name = '测试文件1';
        $file1->file_url = 'static/tmp/test_file_1.pdf';

        $file2 = Attachment::create();
        $file2->name = '测试文件2';
        $file2->file_url = 'static/tmp/test_file_2.pdf';


        $stockInSplitApplyEntity = StockInSplitApply::create($stockInEntity,$contract_id);
        $stockInSplitApplyEntity->addSplitDetail($stockInSplitDetail);
        $stockInSplitApplyEntity->addFile($file1);
        $stockInSplitApplyEntity->addFile($file2);
        $stockInSplitApplyEntity->remark = "auto test generate data";

        $service = new StockSplitService();
        $result = $service->save($stockInSplitApplyEntity);

        $this->assertTrue($result);
    }

    public function testMultiAdd(){
        $stockInEntity = StockInRepository::repository()->findByPk(self::$multi_stock_in_id);
        $this->assertNotNull($stockInEntity);

        $contract_id = 122;  //原合同ID
        $split_contract_id = 5; //拆分合同标识/id
        $goods_id = 8;
        $quantity = 500;

        $tradeGoodsEntity = TradeGoods::create($goods_id);
        $tradeGoodsEntity->goods_id = $goods_id;
        $tradeGoodsEntity->quantity = new Quantity($quantity,$this->unit);

        $stockInSplitDetail = StockInSplitDetail::create($stockInEntity,$split_contract_id);
        $stockInSplitDetail->addGoodsItem($tradeGoodsEntity);


        $goods_id = 10;
        $quantity = 1000;
        $tradeGoodsEntity = TradeGoods::create($goods_id);
        $tradeGoodsEntity->goods_id = $goods_id;
        $tradeGoodsEntity->quantity = new Quantity($quantity,$this->unit);
        $stockInSplitDetail->addGoodsItem($tradeGoodsEntity);


        $stockInSplitEntity = StockInSplitApply::create($stockInEntity,$contract_id);
        $stockInSplitEntity->addSplitDetail($stockInSplitDetail);
        $stockInSplitEntity->remark = "auto test generate data";



        $split_contract_id = 6; //拆分合同标识/id
        $goods_id = 8;
        $quantity = 400;

        $tradeGoodsEntity = TradeGoods::create($goods_id);
        $tradeGoodsEntity->goods_id = $goods_id;
        $tradeGoodsEntity->quantity = new Quantity($quantity,$this->unit);

        $stockInSplitDetail = StockInSplitDetail::create($stockInEntity,$split_contract_id);
        $stockInSplitDetail->addGoodsItem($tradeGoodsEntity);


        $goods_id = 10;
        $quantity = 800;
        $tradeGoodsEntity = TradeGoods::create($goods_id);
        $tradeGoodsEntity->goods_id = $goods_id;
        $tradeGoodsEntity->quantity = new Quantity($quantity,$this->unit);
        $stockInSplitDetail->addGoodsItem($tradeGoodsEntity);


        $stockInSplitEntity->addSplitDetail($stockInSplitDetail);



        $service = new StockSplitService();
        $result = $service->save($stockInSplitEntity);

        $this->assertTrue($result);
    }

    /**
     * 测试可添加库存边界
     * @expectedException ddd\infrastructure\error\ZException
     * @throws Exception
     */
    public function testBalanceGoodsBorder(){
        $stockInEntity = StockInRepository::repository()->findByPk(self::$stock_in_id);
        $this->assertNotNull($stockInEntity);

        $contract_id = 4;
        $split_contract_id = 5; //拆分合同标识/id
        $goods_id = 3;
        $quantity = 10000000.48;

        $tradeGoodsEntity = TradeGoods::create($goods_id);
        $tradeGoodsEntity->goods_id = $goods_id;
        $tradeGoodsEntity->quantity = new Quantity($quantity,$this->unit);

        $stockInSplitDetail = StockInSplitDetail::create($stockInEntity,$split_contract_id);
        $stockInSplitDetail->addGoodsItem($tradeGoodsEntity);

        $stockInSplitEntity = StockInSplitApply::create($stockInEntity,$contract_id);
        $stockInSplitEntity->addSplitDetail($stockInSplitDetail);
        $stockInSplitEntity->remark = "auto test generate data";
    }

    /**
     * 测试重复添加
     * @expectedException Exception
     * @throws Exception
     */
    public function testRepeatAdd(){
        $stockInEntity = StockInRepository::repository()->findByPk(self::$stock_in_id);
        $this->assertNotNull($stockInEntity);

        $contract_id = 4;
        $split_contract_id = 5; //拆分合同标识/id
        $goods_id = 3;
        $quantity = 200.48;
        $unit = 2;

        $tradeGoodsEntity = TradeGoods::create($goods_id);
        $tradeGoodsEntity->goods_id = $goods_id;
        $tradeGoodsEntity->quantity = new Quantity($quantity,$unit);

        $stockInSplitDetail = StockInSplitDetail::create($stockInEntity,$split_contract_id);
        $stockInSplitDetail->addGoodsItem($tradeGoodsEntity);

        $stockInSplitEntity = StockInSplitApply::create($stockInEntity,$contract_id);
        $stockInSplitEntity->addSplitDetail($stockInSplitDetail);
        $stockInSplitEntity->remark = "auto test generate data";

        //重复添加
        $stockInSplitDetail->addGoodsItem($tradeGoodsEntity);
        $stockInSplitEntity->addSplitDetail($stockInSplitDetail);
    }

    /**
     *
     * @throws Exception
     */
    public function testRemoveItemMethod(){
        $stockInEntity = StockInRepository::repository()->findByPk(self::$stock_in_id);
        $this->assertNotNull($stockInEntity);

        $contract_id = 4;
        $split_contract_id = 5; //拆分合同标识/id
        $goods_id = 3;
        $quantity = 200.48;
        $unit = 2;

        $tradeGoodsEntity = TradeGoods::create($goods_id);
        $tradeGoodsEntity->goods_id = $goods_id;
        $tradeGoodsEntity->quantity = new Quantity($quantity,$unit);

        $stockInSplitDetail = StockInSplitDetail::create($stockInEntity,$split_contract_id);
        $stockInSplitDetail->addGoodsItem($tradeGoodsEntity);

        $stockInSplitEntity = StockInSplitApply::create($stockInEntity,$contract_id);
        $stockInSplitEntity->addSplitDetail($stockInSplitDetail);
        $stockInSplitEntity->remark = "auto test generate data";

        $stockInSplitEntity->removeSplitDetail($split_contract_id);

        $quantity = 300.52;
        $tradeGoodsEntity = TradeGoods::create($goods_id);
        $tradeGoodsEntity->goods_id = $goods_id;
        $tradeGoodsEntity->quantity = new Quantity($quantity,$unit);

        $stockInSplitDetail->clearGoodsItems();
        $stockInSplitDetail->addGoodsItem($tradeGoodsEntity);

        $stockInSplitEntity->addSplitDetail($stockInSplitDetail);
    }

    public function testUpdate(){
        $stockSplitEntity = StockSplitApplyRepository::repository()->findByPk(52);
        $this->assertNotNull($stockSplitEntity);

        $split_contract_id = 5; //拆分合同标识/id

        $stockInSplitDetail = $stockSplitEntity->details[$split_contract_id];

        //商品明细2
        $goods_id = 3;
        $quantity = 206.48;
        $tradeGoodsEntity = TradeGoods::create($goods_id);
        $tradeGoodsEntity->goods_id = $goods_id;
        $tradeGoodsEntity->quantity = new Quantity($quantity,$this->unit);

        $stockInSplitDetail->removeGoodsItem($goods_id);
        $stockInSplitDetail->addGoodsItem($tradeGoodsEntity);

        $service = new StockSplitService();
        $result = $service->save($stockSplitEntity);

        $this->assertTrue($result);
    }

    public function testCheckPass(){
        $stockSplitEntity = StockSplitApplyRepository::repository()->findByPk(52);
        $this->assertNotNull($stockSplitEntity);
        
        $stockSplitEntity->status = \ddd\Split\Domain\Model\StockSplit\StockSplitEnum::STATUS_SUBMIT;
        $service = new StockSplitService();
        $result = $service->checkPass($stockSplitEntity);
        $this->assertTrue($result);
    }

    /**
     * @afterClass
     */
    public static function tearDownSomeOtherSharedFixtures(){
        StockSplitApply::model()->deleteAll("bill_id = :bill_id",[":bill_id"=>self::$multi_stock_in_id]);
        StockSplitContract::model()->deleteAll("bill_id = :bill_id",[":bill_id"=>self::$multi_stock_in_id]);
        StockSplitDetail::model()->deleteAll("bill_id = :bill_id",[":bill_id"=>self::$multi_stock_in_id]);
    }
}