<?php


namespace ddd\Split\Dto\StockSplit;

use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\Value\Quantity;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\infrastructure\MathUtility;
use ddd\Split\Domain\Model\Contract\IContractRepository;
use ddd\Split\Domain\Model\Stock\IStockInRepository;
use ddd\Split\Domain\Model\Stock\IStockOutRepository;
use ddd\Split\Domain\Model\StockSplit\IStockSplitApplyRepository;
use ddd\Split\Domain\Model\StockSplit\StockSplitApply;
use ddd\Split\Domain\Model\StockSplit\StockSplitDetail;
use ddd\Split\Domain\Model\StockSplit\StockSplitEnum;
use ddd\Split\Domain\Model\TradeGoods;
use ddd\Split\Dto\AttachmentDTO;
use ddd\Split\Repository\Stock\StockInRepository;
use ddd\Split\Repository\Stock\StockOutRepository;

/**
 * 提交、保存使用
 * Class StockSplitApplyDTO
 * @package ddd\Split\Dto\StockSplit
 */
class StockSplitApplyDTO extends BaseDTO{

    public $apply_id = 0;

    public $contract_id = 0;

    public $bill_id = 0;

    /**
     * 是否勾选拆分
     * @var bool
     */
    public $is_split = true;

    public $type = 0;

    public $remark = '';

    public $split_items = [];

    public $files = [];

    public function rules(){
        return [
            ['bill_id', 'validateOriginStockBill'],
            ['contract_id', 'required', 'message' => 'contract_id不能为空！'],
            ['type', 'required', 'message' => 'type不能为空！'],
            ['split_items', 'validateSplitItems'],
        ];
    }

    public function setSplitItems(array $split_items = []){
        $this->split_items = $split_items;
    }

    public function addSplitItems(StockSplitApplyDetailDTO $dto){
        $this->split_items[] = $dto;
    }

    /**
     * 从提交的数据生成DTO对象
     * @param array $postData
     * @return $this
     * @throws \Exception
     */
    public function assignDTO(array $postData){
        $this->setAttributes($postData);
        $this->setSplitItems([]);

        $contract_id_code_map =  $this->getContractIdCodeMap($postData['split_items']);

        foreach($postData['split_items'] as $datum){
            $detail_dto = new StockSplitApplyDetailDTO();
            $detail_dto->setAttributes($datum);
            $detail_dto->contract_code = $contract_id_code_map[$detail_dto->contract_id] ?? '';
            $detail_dto->setGoodsItems([]);
            foreach($datum['goods_items'] as $item){
                $goods_item_dto = new GoodsItemsDTO();
                $goods_item_dto->setAttributes($item);
                $detail_dto->addGoodsItems($goods_item_dto);
            }
            $this->addSplitItems($detail_dto);
        }

        if(isset($postData['files']) && is_array($postData['files'])){
            $this->files = [];
            foreach($postData['files'] as & $datum){
                $file_dto = new AttachmentDTO();
                $file_dto->setAttributes($datum);
                $this->files[] = $file_dto;
            }
        }

        return $this;
    }

    private function getContractIdCodeMap($splitItems):array{
        $contract_id_code_map = [];
        foreach($splitItems as $datum){
            $contract_id_code_map[$datum['contract_id']] = $datum['contract_id'];
        }
        $contract_models =  \Contract::model()->findAll('t.contract_id IN('. implode(',',$contract_id_code_map) . ')');
        foreach($contract_models as & $contract_model){
            $contract_id_code_map[$contract_model->contract_id] = $contract_model->contract_code;
        }

        return $contract_id_code_map;
    }

    /**
     * 转换成实体对象
     * @return StockSplitApply
     * @throws \Exception
     */
    public function toEntity(){
        $is_stock_in_split = (StockSplitEnum::TYPE_STOCK_IN == $this->type);

        $stockBillEntity = null;
        if($is_stock_in_split){
            $stockBillEntity = StockInRepository::repository()->findByPk($this->bill_id);
        }else{
            $stockBillEntity = StockOutRepository::repository()->findByPk($this->bill_id);
        }

        if(empty($this->apply_id)){
            $stockSplitApplyEntity = StockSplitApply::create($stockBillEntity,$this->contract_id);
        }else{
            //获取出入库已经进行拆分申请,但没有审核的数据
            $stockSplitApplyEntity = DIService::getRepository(IStockSplitApplyRepository::class)->findByApplyId($this->apply_id);
            if(empty($stockSplitApplyEntity)){
                throw new ZEntityNotExistsException($this->apply_id, 'StockSplitApply');
            }
        }

        $stockSplitApplyEntity->setAttributes($this->getAttributes());
        $stockSplitApplyEntity->status = $this->is_split ? StockSplitEnum::STATUS_NEW : StockSplitEnum::STATUS_INVALID;
        $stockSplitApplyEntity->clearDetails();
        $stockSplitApplyEntity->initBalanceGoods($stockBillEntity);

        foreach($this->split_items as & $split_item){
            $stockSplitDetail = StockSplitDetail::create($stockBillEntity,$split_item->contract_id);
            foreach($split_item->goods_items as & $goods_item){
                $tradeGoodsEntity = new TradeGoods();
                $tradeGoodsEntity->goods_id = $goods_item->goods_id;
                $tradeGoodsEntity->quantity = new Quantity($goods_item->quantity,$goods_item->unit);
                $stockSplitDetail->addGoodsItem($tradeGoodsEntity);
            }
            $stockSplitApplyEntity->addSplitDetail($stockSplitDetail);
        }

        //设置附件
        $stockSplitApplyEntity->clearFiles();
        foreach($this->files as & $fileDto){
            $stockSplitApplyEntity->addFile($fileDto->toEntity());
        }

        return $stockSplitApplyEntity;
    }

    public function validateOriginStockBill($attribute){
        $origin_bill_id = $this->$attribute;//当前属性值

        if(StockSplitEnum::TYPE_STOCK_IN == $this->type){
            $originStockBillEntity = DIService::getRepository(IStockInRepository::class)->findByPk($origin_bill_id);
        }else{
            $originStockBillEntity = DIService::getRepository(IStockOutRepository::class)->findByPk($origin_bill_id);
        }
        if(empty($originStockBillEntity)){
            $this->addError('bill_id','原出入库单信息不存在，不能进行平移！');
            return;
        }

        if(!$originStockBillEntity->isCanSplit()){
            $this->addError('bill_id','原出入库单不能进行平移,请刷新页面重新提交！');
            return;
        }

    }

    public function validateSplitItems($attribute){
        $splitItems = $this->$attribute;//当前属性值

        $goods_names = []; //所有商品名称数组
        $goods_total_quantities = [];
        //拆分合同的商品数量
        $contract_goods_quantity = $this->getSplitContractGoodsQuantity($splitItems);

        //
        foreach($splitItems as & $stockSplitApplyDetailDTO){
            if(\Utility::isEmpty($stockSplitApplyDetailDTO->goods_items)){
                continue;
            }

            foreach($stockSplitApplyDetailDTO->goods_items as & $tradeGoodsDto){
                //设置所有商品名称数组
                $goods_names[$tradeGoodsDto->goods_id] = $tradeGoodsDto->goods_name;

                $is_effective_goods = isset($contract_goods_quantity[$stockSplitApplyDetailDTO->contract_id][$tradeGoodsDto->goods_id]);

                if(!$is_effective_goods && $tradeGoodsDto->quantity > 0){
                    $goods_name = $goods_names[$tradeGoodsDto->goods_id];
                    $this->addError($attribute, '平移合同编号:'.$stockSplitApplyDetailDTO->contract_code.'的商品'.$goods_name.'不能进行平移！');
                    return;
                }

                //屏蔽判断，出入库平移可以为0
                if(false && $is_effective_goods && $tradeGoodsDto->quantity <= 0){
                    $goods_name = $goods_names[$tradeGoodsDto->goods_id];
                    $this->addError($attribute, '平移合同编号:'.$stockSplitApplyDetailDTO->contract_code.'的商品'.$goods_name.'数量必须大于0！');
                    return;
                }

                $quantity  = $goods_total_quantities[$tradeGoodsDto->goods_id] ?? 0;
                $goods_total_quantities[$tradeGoodsDto->goods_id] = $quantity + $tradeGoodsDto->quantity;
            }
        }

        if(StockSplitEnum::TYPE_STOCK_IN == $this->type){
            $stockBillEntity = DIService::getRepository(IStockInRepository::class)->findByPk($this->bill_id);
        }else{
            $stockBillEntity = DIService::getRepository(IStockOutRepository::class)->findByPk($this->bill_id);
        }
        if(empty($stockBillEntity)){
            $this->addError($attribute, '当前出入库单信息不存在！');
            return;
        }


        //判断横向总的商品总数
        $origin_goods_total_quantities = [];
        foreach($stockBillEntity->items as & $tradeGoodsEntity){
            $quantity = $origin_goods_total_quantities[$tradeGoodsEntity->goods_id] ?? 0;
            $origin_goods_total_quantities[$tradeGoodsEntity->goods_id] = $quantity + $tradeGoodsEntity->quantity->quantity;

            if(!isset($goods_total_quantities[$tradeGoodsEntity->goods_id])){
                //TODO:XXXXXX
            }

            $h_quantity = $goods_total_quantities[$tradeGoodsEntity->goods_id];
            $origin_quantity = $origin_goods_total_quantities[$tradeGoodsEntity->goods_id];
            if($h_quantity > $origin_quantity){
                $goods_name = $goods_names[$tradeGoodsEntity->goods_id];
                $this->addError($attribute, '被平移出入库单编号:'.$stockBillEntity->bill_code.' 的商品'.$goods_name.'，总的拆分数量:['.$h_quantity.'],不能超过该商品可平移数量:['.$origin_quantity.']');
                return;
            }
        }


        //判断原合同是否全部拆分完毕
        $contractEntity = DIService::getRepository(IContractRepository::class)->findByPk($this->contract_id);
        $goods_quantities = $contractEntity->getGoodsQuantities();
        foreach($goods_quantities as $goods_id => $quantity){
           if(!MathUtility::equal(0,$quantity,0)){
               continue;
           }

           $left_quantity = $origin_goods_total_quantities[$goods_id] ?? 0;
           $right_total_quantity = $goods_total_quantities[$goods_id] ?? 0;

           if(!MathUtility::equal($left_quantity,$right_total_quantity)){
               $goods_name = $goods_names[$goods_id];
               $this->addError($attribute, '当前被平移的商品'.$goods_name.'，必须全部平移！');
               return;
           }
        }

    }

    private function getSplitContractGoodsQuantity(& $splitItems):array{
        if(\Utility::isEmpty($splitItems)){
            return [];
        }

        $contract_goods_quantity = [];

        //获取拆分合同的商品数量
        $split_contract_ids = [];
        foreach($splitItems as & $stockSplitApplyDetailDTO){
            if(\Utility::isEmpty($stockSplitApplyDetailDTO->goods_items)){
                continue;
            }

            $split_contract_ids[$stockSplitApplyDetailDTO->contract_id] = $stockSplitApplyDetailDTO->contract_id;
        }

        if(empty($split_contract_ids)){
            return [];
        }

        $splitContractEntities = DIService::getRepository(IContractRepository::class)->findAll('t.contract_id IN( '.implode(',',$split_contract_ids) . ' )');
        if(\Utility::isEmpty($splitContractEntities)){
            return [];
        }

        foreach($splitContractEntities as $splitContractEntity){
            foreach($splitContractEntity->goods_items as $tradeGoodsEntity){
                if(0 >= $tradeGoodsEntity->quantity->quantity){
                    continue;
                }
                $contract_goods_quantity[$splitContractEntity->contract_id][$tradeGoodsEntity->goods_id] = $tradeGoodsEntity->quantity->quantity;
            }
        }

        return $contract_goods_quantity;
    }
}