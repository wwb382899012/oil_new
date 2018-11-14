<?php
/**
 * User: liyu
 * Date: 2018/6/15
 * Time: 17:18
 * Desc: ContractSplitServiceTest.php
 */

use ddd\repository\GoodsRepository;
use ddd\Split\Application\ContractSplitService;
use ddd\Split\Dto\AttachmentDTO;
use ddd\Split\Dto\ContractSplit\ContractSplit\ContractSplitDTO;
use ddd\Split\Dto\ContractSplit\ContractSplit\StockSplitDetailDTO;
use ddd\Split\Dto\ContractSplit\ContractSplitApplyDTO;
use ddd\Split\Dto\TradeGoodsDTO;
use ddd\Split\Repository\Stock\StockInRepository;
use ddd\Split\Repository\Stock\StockOutRepository;
use PHPUnit\Framework\TestCase;

class ContractSplitServiceTest extends TestCase
{
    use \ddd\Split\Domain\Model\ContractSplit\ContractSplitApplyRepository;
    use \ddd\Split\Domain\Model\Contract\ContractRepository;

    public static $entity;
    public $contract;
    public static $contractId;
    public $service;
    public static $applyId;
    public $partner;

    public function setUp() {
        self::$contractId = 1087;
        $this->contract = $this->getContractRepository()->findByPk(self::$contractId);
        $this->service = new ContractSplitService();
        $this->partner = \ddd\repository\PartnerRepository::repository()->findByPk(466);
    }

    public function testSave() {
        if (!self::$entity) {
            $csaDto = $this->initDTO();
            $res = $this->service->save($csaDto);
            self::$entity = $this->getContractSplitApplyRepository()->findByContractId(self::$contractId);
        }
        return self::$entity;
    }

    private function initDTO() {
        $csaDto = new ContractSplitApplyDTO();
        $csaDto->contract_id = self::$contractId;
        $csaDto->contract_code = $this->contract->contract_code;
        $csaDto->remark = 'test save contract_split_apply';

        $file1 = new AttachmentDTO();
        $file1->name = '测试文件1';
        $file1->file_url = 'static/tmp/test_file_1.pdf';

        $file2 = new AttachmentDTO();
        $file2->name = '测试文件2';
        $file2->file_url = 'static/tmp/test_file_2.pdf';

        $files = [$file1, $file2];
        $csaDto->files = $files;
        $contract_split_item1 = new ContractSplitDTO();
        $contract_split_item1->split_id = 1001;
        $contract_split_item1->partner_id = $this->partner->partner_id;
        $contract_split_item1->partner_name = $this->partner->name;
        $contract_split_item1->goods_items = $this->initGoodsItem();
        $contract_split_item1->stock_split_items = $this->initStockSplitItem($contract_split_item1->split_id);//TODO

        $contract_split_item2 = new ContractSplitDTO();
        $contract_split_item2->split_id = 1002;
        $contract_split_item2->partner_id = $this->partner->partner_id;
        $contract_split_item2->partner_name = $this->partner->name;
        $contract_split_item2->goods_items = $this->initGoodsItem(2);
        $contract_split_item2->stock_split_items = $this->initStockSplitItem($contract_split_item2->split_id);

        $contract_split_items = [$contract_split_item1, $contract_split_item2];
        $csaDto->contract_split_items = $contract_split_items;
        return $csaDto;
    }

    private function initGoodsItem($quantity = 1) {
        $item = [];
        foreach ($this->contract->goods_items as $key => $goods_item) {
            $tradeGoods = new TradeGoodsDTO();
            $tradeGoods->goods_id = $goods_item->goods_id;
            $tradeGoods->goods_name = GoodsRepository::repository()->findByPk($goods_item->goods_id)->name;
            $tradeGoods->quantity = $quantity;
            $tradeGoods->unit = $goods_item->quantity->unit;
            $item[$key] = $tradeGoods;
        }
        return $item;
    }

    private function initStockSplitItem($splitId) {
        $items = [];
        if ($this->contract->type == 1) {//采购合同
            $stockInfo = StockInRepository::repository()->findAllByContractId($this->contract->contract_id);
        } else {
            $stockInfo = StockOutRepository::repository()->findAllByContractId($this->contract->contract_id);
        }
        if (\Utility::isNotEmpty($stockInfo)) {
            foreach ($stockInfo as $item) {
                $dto = new StockSplitDetailDTO();
                $dto->split_id = $splitId;//TODO
                $dto->bill_id = $item->bill_id;//TODO
                $dto->bill_code = $item->bill_code;//TODO
                $dto->goods_items = $this->initGoodsItem(1);//TODO
                $items[] = $dto;
            }
        }
        return $items;
    }

    /**
     * @depends testSave
     */
    public function testGetContractSplitApply($entity) {
        $res = $this->service->getContractSplitApply($entity->contract_id);
        $this->assertNotEmpty($res);
    }

    /**
     * @depends testSave
     */
    public function testSubmit($entity) {
        $persistent = true;
        $res = $this->service->submit($entity, $persistent);
        $this->assertTrue($res);
        return $entity;
    }

    /**
     * @depends testSubmit
     */
    public function testCheckBack($entity) {
        $persistent = true;
        $res = $this->service->checkBack($entity, $persistent);
        $this->assertTrue($res);
    }

    /**
     * @depends testSubmit
     */
    public function testCheckPass($entity) {
        $persistent = true;
        $res = $this->service->checkPass($entity, $persistent);
        $this->assertTrue($res);
    }

    /**
     * @afterClass
     */
    public static function tearDownSomeOtherSharedFixtures() {
        //TODO
    }
}
