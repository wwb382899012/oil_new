<?php

use ddd\repository\stock\StockOutRepository;
use ddd\Split\Application\StockSplitService;
use ddd\Split\Domain\Model\StockSplit\StockOutSplitApply;
use ddd\Split\Domain\Model\StockSplit\StockOutSplitDetail;
use ddd\Split\Domain\Model\TradeGoods;
use ddd\domain\entity\value\Quantity;
use ddd\Split\Repository\StockSplit\StockSplitApplyRepository;
use PHPUnit\Framework\TestCase;


class StockOutSplitServiceTest extends TestCase{

    /**
     *
     */
    public function setUp(){

    }

    public function testAdd(){
        $stockInEntity = StockOutRepository::repository()->findByPk(201801240001);
        $this->assertNotNull($stockInEntity);

        $contract_id = 4;
        $split_contract_id = 5; //拆分合同标识/id
        $goods_id = 12;
        $quantity = 10.48;

        $tradeGoodsEntity = TradeGoods::create($goods_id);
        $tradeGoodsEntity->goods_id = $goods_id;
        $tradeGoodsEntity->quantity = new Quantity($quantity,2);

        $stockInSplitDetail = StockOutSplitDetail::create($stockInEntity,$split_contract_id);
        $stockInSplitDetail->addGoodsItem($tradeGoodsEntity);

        $stockInSplitEntity = StockOutSplitApply::create($stockInEntity,$contract_id);
        $stockInSplitEntity->addSplitDetail($stockInSplitDetail);
        $stockInSplitEntity->remark = "auto test generate data";

        $service = new StockSplitService();
        $result = $service->save($stockInSplitEntity);

        $this->assertTrue($result);
    }

    public function testUpdate(){
        $stockSplitEntity = StockSplitApplyRepository::repository()->findByPk(58);
        $this->assertNotNull($stockSplitEntity);

        $split_contract_id = 5; //拆分合同标识/id

        $stockInSplitDetail = $stockSplitEntity->details[$split_contract_id];

        //商品明细2
        $goods_id = 12;
        $quantity = 9.00;
        $tradeGoodsEntity = TradeGoods::create($goods_id);
        $tradeGoodsEntity->goods_id = $goods_id;
        $tradeGoodsEntity->quantity = new Quantity($quantity,2);

        $stockInSplitDetail->removeGoodsItem($goods_id);
        $stockInSplitDetail->addGoodsItem($tradeGoodsEntity);

        $service = new StockSplitService();
        $result = $service->save($stockSplitEntity);

        $this->assertTrue($result);
    }

    public function testMultiAdd(){
        
    }

}