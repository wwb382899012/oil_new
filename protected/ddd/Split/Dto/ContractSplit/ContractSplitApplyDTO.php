<?php

namespace ddd\Split\Dto\ContractSplit;

use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\Split\Domain\Model\Contract\Contract;
use ddd\Split\Domain\Model\Contract\IContractRepository;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApply;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApplyEnum;
use ddd\Split\Domain\Model\ContractSplit\IContractSplitApplyRepository;
use ddd\Split\Domain\Model\ContractSplit\StockSplit;
use ddd\Split\Domain\Model\ICheckLog;
use ddd\Split\Domain\Model\Stock\IStockInRepository;
use ddd\Split\Domain\Model\Stock\IStockOutRepository;
use ddd\Split\Dto\AttachmentDTO;
use ddd\Split\Dto\CheckLogDTO;
use ddd\Split\Dto\TradeGoodsDTO;

/**
 * 合同拆分申请DTO
 * Class ContractSplitApplyDTO
 * @package ddd\Split\Dto\ContractSplit
 */
class ContractSplitApplyDTO extends BaseDTO{

    /**
     * 源合同id
     * @var   int
     */
    public $contract_id;

    /**
     * 源合同编号
     * @var   string
     */
    public $contract_code;

    /**
     * 拆分申请id
     * @var   int
     */
    public $apply_id;

    public $type;

    /**
     * 申请编号
     * @var   string
     */
    public $apply_code;

    /**
     * 备注
     * @var   string
     */
    public $remark;

    /**
     * 合同拆分信息
     * @var   array
     */
    public $contract_split_items = [];

    /**
     * 附件
     * @var   array
     */
    public $files = [];

    /**
     * 状态
     * @var   int
     */
    public $status;

    /**
     * 状态名称
     * @var
     */
    public $status_name;

    /**
     * 生效时间
     * @var   int
     */
    public $effect_time;

    /**
     *
     * @var
     */
    public $is_can_edit;

    public $audit_log = [];

    /**
     * 没有勾选平移操做的bill_ids, [bill_id] = bill_id
     * @var array
     */
    protected $un_split_bill_ids = [];

    public function rules(){
        return [
            ['contract_split_items', 'validateContractSplit'],
        ];
    }

    /**
     * @param array $billIds
     */
    public function setUnSplitBills(array $billIds):void{
        $this->un_split_bill_ids = $billIds;
    }

    /**
     * 是否未勾选拆分
     * @deprecated
     * @param $billId
     * @return bool
     */
    public function isUnSplitBill($billId){
        if(empty($billId)){
            return false;
        }

        if(\Utility::isEmpty($this->un_split_bill_ids)){
            return false;
        }

        return isset($this->un_split_bill_ids[(string) $billId]);
    }

    /**
     * 对DTO进行赋值
     * @param array $params
     * @throws \Exception
     */
    public function assignDTO(array $params){
        if(!isset($params['contract_id']) || !\Utility::checkQueryId($params['contract_id'])
            || !isset($params['contract_split_items']) || \Utility::isEmpty($params['contract_split_items'])){
            throw new ZInvalidArgumentException('contract_id,contract_split_items');
        }

        $this->setAttributes($params);
        $this->contract_split_items = [];
        $this->files = [];

        $this->contract_split_items = $this->getContractSplitDtoArray($params['contract_split_items']);

        if(\Utility::isNotEmpty($params['files'])){
            foreach($params['files'] as $file){
                $file_dto = new AttachmentDTO();
                $file_dto->setAttributes($file);
                $this->files[] = $file_dto;
            }
        }
    }

    /**
     * 生成合同拆分明细DTO数组
     * @param array $contractSplitItems 合同拆分明细数组
     * @return array
     * @throws \Exception
     */
    private function getContractSplitDtoArray(array & $contractSplitItems){
        $contract_split_items = [];
        foreach($contractSplitItems as $itemKey => & $contractSplitItem){
            $tmp_split_id = $this->generateTmpSplitId($itemKey);
            $contractSplitDto = new ContractSplitDTO();
            $contractSplitDto->setTmpSplitId($tmp_split_id);
            $contractSplitDto->setAttributes($contractSplitItem);
            $contractSplitDto->stock_bill_items = [];
            $contractSplitDto->goods_items = [];

            //设置拆分合同的出入库单明细
            if(\Utility::isNotEmpty($contractSplitItem['stock_bill_items'])){
                $contractSplitDto->stock_bill_items = $this->getStockSplitDetailDtoArray($contractSplitItem['stock_bill_items'], $tmp_split_id);
            }

            //设置拆分合同的商品明细
            if(\Utility::isNotEmpty($contractSplitItem['goods_items'])){
                foreach($contractSplitItem['goods_items'] as & $goodsItem){
                    $tradeGoodsDto = new TradeGoodsDTO();
                    $tradeGoodsDto->setAttributes($goodsItem);
                    $contractSplitDto->goods_items[] = $tradeGoodsDto;
                }
            }

            $contract_split_items[] = $contractSplitDto;
        }

        return $contract_split_items;
    }

    /**
     * 生成出入库单拆分明细DTO数组
     * @param array $stockBillItems 出入库单拆分明细数组
     * @return array
     * @throws \Exception
     */
    private function getStockSplitDetailDtoArray(array & $stockBillItems, $tmp_split_id){
        $stock_bill_items = [];
        foreach($stockBillItems as & $stock_bill_item){
            $stockSplitDetailDto = new StockSplitDetailDTO();
            $stockSplitDetailDto->setTmpSplitId($tmp_split_id);
            $stockSplitDetailDto->setAttributes($stock_bill_item);
            $stockSplitDetailDto->goods_items = [];

            if(\Utility::isNotEmpty($stock_bill_item['goods_items'])){
                foreach($stock_bill_item['goods_items'] as & $goodsItem){
                    $tradeGoodsDto = new TradeGoodsDTO();
                    $tradeGoodsDto->setAttributes($goodsItem);
                    $stockSplitDetailDto->goods_items[] = $tradeGoodsDto;
                }
            }
            $stock_bill_items[] = $stockSplitDetailDto;
        }

        return $stock_bill_items;
    }


    /**
     * 从实体对象生成DTO对象
     * @param   BaseEntity $entity
     * @throws  \Exception
     */
    public function fromEntityForViewScene(BaseEntity $entity){
        $this->fromEntityX($entity, true);
    }

    /**
     * 从实体对象生成DTO对象，提供给编辑页面
     * @param   BaseEntity $entity
     * @throws  \Exception
     */
    public function fromEntityForEditScene(BaseEntity $entity){
        $this->fromEntityX($entity, false);
    }

    /**
     * 从实体对象生成DTO对象
     * @param BaseEntity $entity
     * @param bool $isViewScene
     * @throws ZEntityNotExistsException
     * @throws \CException
     */
    private function fromEntityX(BaseEntity $entity,$isViewScene = false){
        $values = $entity->getAttributes();
        unset($values['contract_split_items']);
        unset($values['stock_bill_items']);
        unset($values['files']);
        $this->setAttributes($values);
        $this->un_split_bill_ids = [];
        $this->contract_split_items = [];
        $this->files = [];

        $contractEntity = DIService::getRepository(IContractRepository::class)->findByPk($entity->contract_id);
        if(empty($contractEntity)){
            throw new ZEntityNotExistsException($entity->contract_id, 'Contract');
        }

        $contractSplits = $entity->getContractSplits();
        $stockSplits = $isViewScene ? $entity->getEffectiveStockSplits() : $entity->getStockSplits();

        if(\Utility::isNotEmpty($contractSplits)){
            foreach($contractSplits as $itemKey => & $contractSplitEntity){
                $contractSplitDto = new ContractSplitDTO();
                $contractSplitDto->setTmpSplitId($itemKey);
                $contractSplitDto->fromEntity($contractSplitEntity);
                $contractSplitDto->stock_bill_items = [];

                if(\Utility::isNotEmpty($stockSplits)){
                    foreach($stockSplits as $billId => & $stockSplitEntity){
                        $stockSplitDetailEntity = $stockSplitEntity->getStockSplitDetail($contractSplitDto->getTmpSplitId());
                        if(!empty($stockSplitDetailEntity)){
                            if($contractEntity->type == ContractSplitApplyEnum::CONTRACT_TYPE_BUY){
                                $billEntity = DIService::getRepository(IStockInRepository::class)->findByPk($billId);
                                if(empty($billEntity)){
                                    throw new ZEntityNotExistsException($billId, 'StockIn');
                                }
                                $stockSplitDetailEntity->type = ContractSplitApplyEnum::STOCK_TYPE_IN;

                            }else{
                                $billEntity = DIService::getRepository(IStockOutRepository::class)->findByPk($billId);
                                if(empty($billEntity)){
                                    throw new ZEntityNotExistsException($billId, 'StockOut');
                                }
                                $stockSplitDetailEntity->type = ContractSplitApplyEnum::STOCK_TYPE_OUT;
                            }

                            $stockSplitDetailDto = new StockSplitDetailDTO();
                            $stockSplitDetailDto->setTmpSplitId($contractSplitDto->getTmpSplitId());
                            $stockSplitDetailDto->fromEntity($stockSplitDetailEntity);

                            $contractSplitDto->stock_bill_items[] = $stockSplitDetailDto;
                        }
                    }
                }
                $this->contract_split_items[] = $contractSplitDto;
            }
        }

        $files = $entity->getFiles();
        if(\Utility::isNotEmpty($files)){
            foreach($files as & $v){
                $attachmentDto = new AttachmentDTO();
                $attachmentDto->fromEntity($v);
                $this->files[] = $attachmentDto;
            }
        }

        $checkLogEntities = DIService::getRepository(ICheckLog::class)->findAllByObjIdAndBusinessId($entity->apply_id, \FlowService::BUSINESS_CONTRACT_SPLIT_CHECK);

        $this->audit_log = [];
        if(\Utility::isNotEmpty($checkLogEntities)){
            foreach($checkLogEntities as & $logEntity){
                $log_dto =new CheckLogDTO();
                $log_dto->fromEntity($logEntity);
                $this->audit_log[] = $log_dto;
            }
        }

        $this->status_name = \Map::getStatusName('contract_split_apply_status', $entity['status']);
        $this->is_can_edit = $entity->isCanEdit();
    }

    private function generateTmpSplitId($id){
        return 1000 + (int)$id;
    }

    /**
     * 转换成实体对象
     * @return ContractSplitApply
     * @throws \Exception
     */
    public function toEntity(){
        $entity = new ContractSplitApply();

        $entity->setAttributes($this->getAttributes());
        if(empty($this->apply_id) || empty($this->apply_code)){
            $entity->generateCode();
        }

        if(!empty($this->apply_id)){
            $originEntity = DIService::getRepository(IContractSplitApplyRepository::class)->findByPk($this->apply_id);
            if(empty($originEntity)){
                throw new ZEntityNotExistsException($this->apply_id, 'ContractSplitApply');
            }
            $entity->type = $originEntity->type;
            $entity->apply_code = $originEntity->apply_code;
            $entity->status = $originEntity->status;
        }
        $entity->status_time = new \CDbExpression("now()");

        $entity->clearContractSplitItems();
        $entity->clearStockSplitItems();
        $entity->clearFiles();

        if(\Utility::isNotEmpty($this->files)){
            foreach($this->files as $file){
                $entity->addFile($file->toEntity());
            }
        }

        if(\Utility::isNotEmpty($this->contract_split_items)){
            $originContractEntity = DIService::getRepository(IContractRepository::class)->findByPk($this->contract_id);
            if(empty($originContractEntity)){
                throw new ZEntityNotExistsException($this->contract_id, 'Contract');
            }
            $entity->type = $originContractEntity->type;

            if(\Utility::isNotEmpty($originContractEntity->goods_items)){
                $entity->initBalanceGoods($originContractEntity);
            }

            foreach($this->contract_split_items as $key => $contractSplitDto){
                $entity->addContractSplit($contractSplitDto->toEntity(), $contractSplitDto->getTmpSplitId());
            }

            $bill_ids = $this->getSplitStockBills();
            foreach($bill_ids as $bill_id => $bill_code){
                if($originContractEntity->type == ContractSplitApplyEnum::CONTRACT_TYPE_BUY){
                    $stockBillEntity = DIService::getRepository(IStockInRepository::class)->findByPk($bill_id);
                }else{
                    $stockBillEntity = DIService::getRepository(IStockOutRepository::class)->findByPk($bill_id);
                }
                if(empty($stockBillEntity)){
                    throw new ZEntityNotExistsException($bill_id, 'StockBill');
                }

                //如果该出/入库单未勾选拆分，则该明细下的所有拆分数据都是0
                $is_un_split_bill =  $this->isUnSplitBill($bill_id);

                $stockSplitEntity = new StockSplit();
                $stockSplitEntity->bill_id = $bill_id;
                $stockSplitEntity->bill_code = $bill_code;
                $stockSplitEntity->initBalanceGoods($stockBillEntity);
                $stockSplitEntity->status = $is_un_split_bill ?  ContractSplitApplyEnum::STATUS_UN_SPLIT : ContractSplitApplyEnum::STATUS_SPLIT;

                $stockSplitDetailDtos = $this->getTransverseStockSplitDetailDtos($bill_id);
                foreach($stockSplitDetailDtos as $stockSplitDetailDto){
                    $stockSplitEntity->addSplitDetail($stockSplitDetailDto->toEntity(), $stockSplitDetailDto->getTmpSplitId());
                }

                $entity->addStockSplit($stockSplitEntity);
            }
        }

        return $entity;
    }

    private function getSplitStockBills(){
        $bill_ids = [];
        if(\Utility::isNotEmpty($this->contract_split_items)){
            foreach($this->contract_split_items as $z_key => & $contractSplitItem){
                if(\Utility::isNotEmpty($contractSplitItem->stock_bill_items)){
                    foreach($contractSplitItem->stock_bill_items as & $stockSplitDetail){
                        $bill_ids[(string)$stockSplitDetail->bill_id] = $stockSplitDetail->bill_code;
                    }
                }
            }
        }

        return $bill_ids;
    }

    private function getTransverseStockSplitDetailDtos($bill_id){
        $stockSplitDetails = [];
        if(\Utility::isNotEmpty($this->contract_split_items)){
            foreach($this->contract_split_items as & $contractSplitItem){
                if(\Utility::isNotEmpty($contractSplitItem->stock_bill_items)){
                    foreach($contractSplitItem->stock_bill_items as & $stockSplitDetail){
                        $stockSplitDetails[(string)$stockSplitDetail->bill_id][] = $stockSplitDetail;
                    }
                }
            }
        }

        return $stockSplitDetails[(string)$bill_id];
    }


    /**
     * 合同拆分校验
     * @param $attribute
     * @throws \Exception
     */
    public function validateContractSplit($attribute){
        $contractSplits = $this->$attribute;//当前属性值
        //
        $goods_names = []; //所有商品名称数组
        $stock_bill_ids = []; //所有被拆分的出入库单id
        //
        $originContractGoods = []; //原始合同的商品数量
        $splitContractGoods = []; //拆分合同的总商品数量
        //
        $zSplitContractTotalQuantity = []; //纵向合同商品总数，二维数组，[纵向标识][商品id] = 数量
        //
        $zStockGoodsTotalQuantity = [];  //纵向出入库商品总数，二维数组，[纵向标识][商品id] = 数量
        $hStockGoodsTotalQuantity = []; //横向拆分出入库商品总数，二维数组，[出入库单id][商品id] = 数量

        //原合同
        $originContractEntity = DIService::getRepository(IContractRepository::class)->findByPk($this->contract_id);
        if(empty($originContractEntity)){
            $this->addError('contract_id','原合同信息不存在，不能进行平移！');
            return;
        }
        foreach($originContractEntity->goods_items as & $goods_item){
            $originContractGoods[$goods_item->goods_id] = (float)$goods_item->quantity->quantity;
        }

        //统计拆分合同的总商品数量,
        //$contractSplit  instanceof  ddd\Split\Dto\ContractSplit\ContractSplitDTO
        foreach($contractSplits as $key => & $contractSplit){

            //单个合同商品总数
            $single_contract_goods_total = 0;

            // $goods_item  instanceof  ddd\Split\Dto\TradeGoodsDTO;
            foreach($contractSplit->goods_items as & $goods_item){
                //设置纵向合同商品总数
                $z_total_quantity = $zSplitContractTotalQuantity[$key][$goods_item->goods_id] ?? 0;
                $zSplitContractTotalQuantity[$key][$goods_item->goods_id] = $z_total_quantity + (float)$goods_item->quantity;

                //设置拆分合同的总商品数量
                $quantity = $splitContractGoods[$goods_item->goods_id] ?? 0;
                $splitContractGoods[$goods_item->goods_id] = $quantity + (float)$goods_item->quantity;

                //设置单个合同商品总数
                $single_contract_goods_total += (float)$goods_item->quantity;

                //设置所有商品名称数组
                $goods_names[$goods_item->goods_id] = $goods_item->goods_name;
            }

            //如果单个商品的总数小于等于0，
            if($single_contract_goods_total <= 0){
                $this->addError($attribute, '平移合同的商品数量必须至少有一个大于0！');
                return false;
            }

            //$stock_bill_item  instanceof  ddd\Split\Dto\ContractSplit\StockSplitDetailDTO
            foreach($contractSplit->stock_bill_items as & $stock_bill_item){
                $stock_bill_ids[(string) $stock_bill_item->bill_id] = (string) $stock_bill_item->bill_id;

                foreach($stock_bill_item->goods_items as $z_goods_item){
                    //设置纵向出入库商品总数
                    $z_quantity = $zStockGoodsTotalQuantity[$key][$z_goods_item->goods_id] ?? 0;
                    $zStockGoodsTotalQuantity[$key][$z_goods_item->goods_id] = $z_quantity + (float)$z_goods_item->quantity;

                    //设置横向拆分出入库商品总数
                    $h_quantity = $hStockGoodsTotalQuantity[$stock_bill_item->bill_id][$z_goods_item->goods_id] ?? 0;
                    $hStockGoodsTotalQuantity[(string) $stock_bill_item->bill_id][$z_goods_item->goods_id] = $h_quantity + (float)$z_goods_item->quantity;
                }
            }
        }

        //检查提交的申请中是否存在当下不可平移的出入库单
        if(\Utility::isNotEmpty($stock_bill_ids)){
            $res = $this->checkStockBillIsCanSplit($originContractEntity,$stock_bill_ids);
            if(!$res){
                return;
            }
        }

        //拆分合同的商品，对应的出入库拆分每一项必须都大于0，或者都不平移
        $res = $this->checkTransverseGoodsQuantity($attribute,$originContractEntity,$contractSplits,$goods_names);
        if(!$res){
            return;
        }

        //横向约束，判断拆分的合同商品总数，是否大于原合同商品数量, 返回需要全部平移的商品id数组
        $need_goods_ids = $this->checkTransverseContractTotalQuantity($attribute, $goods_names, $originContractGoods, $splitContractGoods);
        if(false === $need_goods_ids){
            return;
        }

        //纵向约束，判断拆分的合同下的出入库单商品总数，是否大于拆分合同的商品数量
        //        $res = $this->checkLongitudinalStockTotalQuantity($attribute,$goods_names,$need_goods_ids,$zSplitContractTotalQuantity,$zStockGoodsTotalQuantity);
        //        if(!$res){
        //            return;
        //        }

        //横向约束，判断每一行的出入库平移
        if(\Utility::isNotEmpty($stock_bill_ids)){
            $res = $this->checkTransverseStockTotalQuantity($attribute, $goods_names, $originContractEntity->type, $need_goods_ids, $stock_bill_ids, $hStockGoodsTotalQuantity);
            if(!$res){
                return;
            }
        }

        //continue other check logic!
    }

    /**
     * 检查提交的申请中是否存在当下不可平移的出入库单
     * @param Contract $originContractEntity
     * @param array $stockBillIds
     * @return bool
     */
    private function checkStockBillIsCanSplit(Contract & $originContractEntity,array $stockBillIds):bool{
        $can_split_stock_bill_ids = $originContractEntity->getCanSplitStockBillIds();
        if(\Utility::isEmpty($can_split_stock_bill_ids)){
            $this->addError('contract_split_items','原合同信息不存在可平移的出入库单，不能进行平移！');
            return false;
        }

        foreach($stockBillIds as $bill_id){
            if(!$originContractEntity->isCanSplitStockBill($bill_id)){
                $this->addError('contract_split_items','平移信息中存在不可平移的出入库单，请刷新页面重新提交！');
                return false;
            }
        }

        return true;
    }

    /**
     * 拆分合同的商品，对应的出入库拆分每一项必须都大于0，或者都不平移
     * @param $attribute
     * @param Contract $originContractEntity
     * @param $contractSplits
     * @param $goodsNames
     * @return bool
     */
    private function checkTransverseGoodsQuantity($attribute,Contract $originContractEntity , $contractSplits,$goodsNames):bool {
        //获取每一个出入库单对应的商品数量
        $origin_stock_bill_goods_quantities = [];
        $canSplitStockBillEntities = $originContractEntity->getCanSplitStockBills();
        foreach($canSplitStockBillEntities as & $stockBillEntity){
            foreach($stockBillEntity->items as $tradeGoodsEntity){
                $quantity = $tradeGoodsEntity->quantity->quantity;
                $origin_stock_bill_goods_quantities[(string)$stockBillEntity->bill_id][$tradeGoodsEntity->goods_id] = $quantity;
            }
        }

        //虚拟单数组
        $bill_is_virtual_map = [];

        //获取每一个出入库单，对应的拆分出入库单商品数量
        $split_stock_bill_goods_quantities = [];
        foreach($contractSplits as $key => & $contractSplitDto){
            //$stock_bill_item  instanceof  ddd\Split\Dto\ContractSplit\StockSplitDetailDTO
            foreach($contractSplitDto->stock_bill_items as & $stock_bill_item){

                //过滤未平移出入库单的申请记录
                if(isset($this->un_split_bill_ids[$stock_bill_item->bill_id])){
                    continue;
                }

                foreach($stock_bill_item->goods_items as $tradeGoodsDTO){
                    $split_stock_bill_goods_quantities[$contractSplitDto->split_id][$tradeGoodsDTO->goods_id][$stock_bill_item->bill_id] = $tradeGoodsDTO->quantity;
                    $bill_is_virtual_map[$stock_bill_item->bill_id] = $originContractEntity->isVirtualStockBill($stock_bill_item->bill_id);
                }
            }
        }


        foreach($split_stock_bill_goods_quantities as $split_id => $item){
            foreach($item as $goods_id => $datum){
                $tag = null;
                foreach($datum as $bill_id => $quantity){
                    //虚拟单拆分数可以为0
                    $is_virtual = $bill_is_virtual_map[$bill_id] ?? false;
                    if($is_virtual){
                        continue;
                    }

                    if(null === $tag){
                        $tag = $quantity;
                        continue;
                    }

                    $goods_name = $goodsNames[$goods_id];

                    //对应的原出入库单的商品数量为0，拆分的数量也为0，跳过判断
                    $origin_quantity = $origin_stock_bill_goods_quantities[(string) $bill_id][$goods_id];
                    if(0 == $origin_quantity && 0 == $quantity){
                        continue;
                    }

                    //第一个不为0，则其他的必须不为0
                    if($tag > 0 && 0 >= $quantity){
                        $this->addError($attribute,'拆分合同的商品'.$goods_name.',对应的出入库拆分每一项必须都平移，或者都不平移！');
                        return false;
                    }

                    //第一个为零，则其他的必须为0
                    if(0 == $tag && 0 != $quantity){
                        $this->addError($attribute,'拆分合同的商品'.$goods_name.',对应的出入库拆分每一项必须都平移，或者都不平移！');
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * 横向约束，判断拆分的合同商品总数，是否大于原合同商品数量
     * @param $attribute
     * @param $goods_names
     * @param $originContractGoods
     * @param $splitContractGoods
     * @return array|bool
     */
    private function checkTransverseContractTotalQuantity($attribute, $goods_names, $originContractGoods, $splitContractGoods){
        //需要全部平移的商品id数组
        $need_goods_ids = [];

        //横向约束，判断拆分的合同商品总数，是否大于原合同商品数量
        foreach($originContractGoods as $goods_id => & $origin_quantity){
            $split_quantity = $splitContractGoods[$goods_id] ?? 0;

            //合同某个商品全部拆完，下面对应商品的出入库单也必须全部拆分完
            if(($split_quantity == $origin_quantity) && $split_quantity > 0){
                //设置需要全部平移的商品id数组
                $need_goods_ids[$goods_id] = $goods_id;
            }

            if($split_quantity > $origin_quantity){
                $goods_name = $goods_names[$goods_id];
                $this->addError($attribute, '商品：'.$goods_name.' 总的合同拆分数量:['.$split_quantity.'],不能超过该商品可平移数量:['.$origin_quantity.']');
                return false;
            }
        }

        return $need_goods_ids;
    }

    /**
     * 纵向约束，判断拆分的合同下的出入库单商品总数，是否大于拆分合同的商品数量
     * @param $attribute
     * @param $goods_names
     * @param $zSplitContractTotalQuantity 纵向合同商品总数，二维数组，[纵向标识][商品id] = 数量
     * @param $zStockGoodsTotalQuantity 纵向出入库商品总数，二维数组，[纵向标识][商品id] = 数量
     * @return bool
     */
    private function checkLongitudinalStockTotalQuantity($attribute, $goods_names, $need_goods_ids, $zSplitContractTotalQuantity, $zStockGoodsTotalQuantity){
        //纵向约束，判断拆分的合同下的出入库单商品总数，是否大于拆分合同的商品数量
        foreach($zSplitContractTotalQuantity as $key => & $goods_quantity){
            foreach($goods_quantity as $goods_id => & $split_quantity){
                $z_quantity = $zStockGoodsTotalQuantity[$key][$goods_id] ?? 0;

                if($z_quantity > $split_quantity){
                    $goods_name = $goods_names[$goods_id];
                    $this->addError($attribute, '商品：'.$goods_name.' 总的拆分数量:['.$z_quantity.'],不能超过该拆分合同商品数量:['.$split_quantity.']');
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * 横向约束，判断每一行的出入库平移
     * @param $attribute
     * @param $goods_names
     * @param $type
     * @param $need_goods_ids 需要全部平移的商品id数组
     * @param $stock_bill_ids
     * @param $hStockGoodsTotalQuantity
     * @return array|bool
     * @throws \Exception
     */
    private function checkTransverseStockTotalQuantity($attribute, $goods_names, $type, $need_goods_ids, $stock_bill_ids, & $hStockGoodsTotalQuantity){
        if(ContractSplitApplyEnum::CONTRACT_TYPE_BUY == $type){
            $originContractEntityArray = DIService::getRepository(IStockInRepository::class)->findAll('t.stock_in_id IN('.implode(',', $stock_bill_ids).')');
        }else{
            $originContractEntityArray = DIService::getRepository(IStockOutRepository::class)->findAll('t.out_order_id IN('.implode(',', $stock_bill_ids).')');
        }

        $bill_codes = [];
        $hOriginStockGoodsTotalQuantity = []; //横向原出入库商品总数，二维数组，[出入库单id][商品id] = 数量
        foreach($originContractEntityArray as & $originContractEntity){
            $bill_codes[$originContractEntity->bill_id] = $originContractEntity->bill_code;

            foreach($originContractEntity->items as $tradeGoods){
                $quantity = $hOriginStockGoodsTotalQuantity[$originContractEntity->bill_id][$tradeGoods->goods_id] ?? 0;
                $hOriginStockGoodsTotalQuantity[$originContractEntity->bill_id][$tradeGoods->goods_id] = $quantity + (float)$tradeGoods->quantity->quantity;
            }
        }

        foreach($hOriginStockGoodsTotalQuantity as $bill_id => $goods_quantity){
            foreach($goods_quantity as $goods_id => $origin_quantity){
                $h_quantity = $hStockGoodsTotalQuantity[$bill_id][$goods_id] ?? 0;
                if($h_quantity > $origin_quantity){
                    $bill_code = $bill_codes[$bill_id];
                    $goods_name = $goods_names[$goods_id];
                    $this->addError($attribute, '出入库单编号:'.$bill_code.'的商品'.$goods_name.'总的拆分数量:['.$h_quantity.'],不能超过该商品可平移数量:['.$origin_quantity.']');
                    return false;
                }

                //合同某个商品全部拆完，下面对应商品的出入库单也必须全部拆分完
                if(isset($need_goods_ids[$goods_id]) && ($h_quantity != $origin_quantity)){
                    $goods_name = $goods_names[$goods_id];
                    $this->addError($attribute, '拆分合同商品:'.$goods_name.'已全部平移，该商品所有的出入库单必须全部平移');
                    return false;
                }
            }
        }

        return true;
    }

}