<?php

namespace ddd\Split\Application;

use ddd\Common\Application\TransactionService;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\Split\Domain\Model\Contract\Contract;
use ddd\Split\Domain\Model\Contract\IContractRepository;
use ddd\Split\Domain\Model\Stock\IStockInRepository;
use ddd\Split\Domain\Model\Stock\IStockOutRepository;
use ddd\Split\Domain\Model\StockSplit\IStockSplitApplyRepository;
use ddd\Split\Domain\Model\StockSplit\StockSplitApply;
use ddd\Split\Domain\Model\StockSplit\StockSplitEnum;
use ddd\Split\Dto\StockSplit\ContractDTO;
use ddd\Split\Dto\StockSplit\ContractSplitDTO;
use ddd\Split\Dto\StockSplit\StockBillDTO;
use ddd\Split\Dto\StockSplit\StockSplitBillDTO;
use ddd\Split\Dto\StockSplit\StockSplitInfoDTO;
use ddd\Split\Dto\TradeGoodsDTO;

/**
 * 出入库拆分应用层对象，具备事务能力
 * Class StockSplitService
 * @package ddd\Split\Application
 */
class StockSplitService extends TransactionService{

    /**
     * 添加
     */
    const SCENE_SAVE = 0;

    /**
     * 提交
     */
    const SCENE_SUBMIT = 1;

    /**
     * 驳回
     */
    const SCENE_CHECK_BACK = 3;

    /**
     * 审批通过
     */
    const SCENE_CHECK_PASS = 5;

    public function getStockSplitInfoDtoForViewScene($contractId):StockSplitInfoDTO{
        //获取原始合同
        $originContractEntity = DIService::getRepository(IContractRepository::class)->findByPk($contractId);
        if(empty($originContractEntity)){
            throw new ZEntityNotExistsException($contractId, Contract::class);
        }

        $dto = new StockSplitInfoDTO();

        $dto->origin_contract = $this->getOriginContractDtoForViewScene($originContractEntity);

        $stock_bill_ids = [];
        foreach($dto->origin_contract->stock_bill_items as $item){
            $stock_bill_ids[(string) $item->bill_id] = $item->bill_id;
        }

        $dto->contract_split_items = $this->getContractSplitItemsForViewScene($originContractEntity, $stock_bill_ids);

        return $dto;
    }

    public function getStockSplitInfoDtoForEditScene($contractId):StockSplitInfoDTO{
        //获取原始合同
        $originContractEntity = DIService::getRepository(IContractRepository::class)->findByPk($contractId);
        if(empty($originContractEntity)){
            throw new ZEntityNotExistsException($contractId, Contract::class);
        }

        $dto = new StockSplitInfoDTO();

        $dto->origin_contract = $this->getOriginContractDtoForEditScene($originContractEntity);

        $stock_bill_ids = [];
        foreach($dto->origin_contract->stock_bill_items as $item){
            $stock_bill_ids[(string) $item->bill_id] = $item->bill_id;
        }

        $dto->contract_split_items = $this->getContractSplitItemsForEditScene($originContractEntity,$dto->origin_contract,$stock_bill_ids);

        return $dto;
    }

    private function  getOriginContractDtoForViewScene(Contract & $originContractEntity):ContractDTO{
        $origin_contract_dto = new ContractDTO();
        $origin_contract_dto->fromEntityForViewScene($originContractEntity);
        $origin_contract_dto->stock_bill_items = [];

        //获取该合同下已经申请了的出入库拆分
        $stockSplitApplyEntities = $this->getStockSplitApplyEntities($originContractEntity->contract_id,true);

        $stockBillEntities = $originContractEntity->getAllStockBills();
        foreach($stockBillEntities as & $stockBillEntity){
            //不在出入库申请id里的直接跳过
            if(!isset($stockSplitApplyEntities[(string) $stockBillEntity->bill_id])){
                continue;
            }

            //该原合同下的出入库单已经有了出入库拆分数据，
            //则用存储的出入库拆分数据覆盖对应的属性
            $stock_split_apply = $stockSplitApplyEntities[(string) $stockBillEntity->bill_id];
            //
            $stock_bill_dto = new StockBillDTO();
            $stock_bill_dto->fromEntity($stockBillEntity);
            $stock_bill_dto->apply_id = $stock_split_apply->apply_id;
            $stock_bill_dto->status = $stock_split_apply->status;
            $stock_bill_dto->status_name = \Map::getStatusName("stock_bill_split_status",$stock_split_apply->status);
            $stock_bill_dto->is_can_split = false;
            $stock_bill_dto->is_can_view = true;
            $stock_bill_dto->is_can_check = $stock_split_apply->isOnChecking();
            $stock_bill_dto->is_split = true;

            $origin_contract_dto->stock_bill_items[] = $stock_bill_dto;
        }

        return $origin_contract_dto;
    }

    private function  getOriginContractDtoForEditScene(Contract & $originContractEntity):ContractDTO{
        $origin_contract_dto = new ContractDTO();
        $origin_contract_dto->fromEntityForEditScene($originContractEntity);

        $origin_contract_dto->stock_bill_items = [];
        $stockSplitApplyEntities = $this->getStockSplitApplyEntities($originContractEntity->contract_id);

        $stockBillEntities = $this->getCanSplitStockBillEntitiesForEditScene($originContractEntity->isBuyContract(),$originContractEntity->contract_id, $stockSplitApplyEntities);
        //
        foreach($stockBillEntities as & $stockBillEntity){
            $stock_bill_dto = new StockBillDTO();
            $stock_bill_dto->fromEntity($stockBillEntity);

            //该原合同下的出入库单已经有了出入库拆分数据，
            //则用 暂时存的出入库拆分数据覆盖对应的属性
            if(isset($stockSplitApplyEntities[$stockBillEntity->bill_id])){
                $stock_split_apply = $stockSplitApplyEntities[$stockBillEntity->bill_id];
                $stock_bill_dto->apply_id = $stock_split_apply->apply_id;
                $stock_bill_dto->status = $stock_split_apply->status;
                $stock_bill_dto->status_name = \Map::getStatusName("stock_bill_split_status",$stock_split_apply->status);
                $stock_bill_dto->is_can_split = $stock_bill_dto->is_can_split && $stock_split_apply->isCanEdit();
                $stock_bill_dto->is_can_view = $stock_split_apply->isCanView();
                $stock_bill_dto->is_split = $stock_split_apply->isSplit();
                $stock_bill_dto->is_can_save = $stock_split_apply->isCanSubmit();
                $stock_bill_dto->is_can_submit = !$stock_bill_dto->is_can_save && $stock_split_apply->isCanSubmit();
            }else{
                $stock_bill_dto->apply_id = 0;
                $stock_bill_dto->status = 0;
                $stock_bill_dto->status_name = '';
                $stock_bill_dto->is_can_view = false;
                $stock_bill_dto->is_can_split = true;
                $stock_bill_dto->is_split = \Utility::isEmpty($stockSplitApplyEntities) ? true : false;
                $stock_bill_dto->is_can_save = true;
                $stock_bill_dto->is_can_submit = false;
            }

            $origin_contract_dto->stock_bill_items[] = $stock_bill_dto;
        }


        return $origin_contract_dto;
    }

    /**
     * @param $originContractId
     * @param bool $isEffective 是否有效
     * @return array
     * @throws \Exception
     */
    private function getStockSplitApplyEntities($originContractId,$isEffective = false):array{
        $where = 'contract_id = :contract_id';
        $params = [':contract_id'=> $originContractId];
        if($isEffective){
            $where .= ' AND status >= :status';
            $params [':status'] = StockSplitEnum::STATUS_NEW;
        }

        //获取出入库已经进行拆分申请的数据
        $stock_split_applies = DIService::getRepository(IStockSplitApplyRepository::class)->findAll(
            $where , $params);

        $tmp = $stock_split_applies;
        $stock_split_applies = [];
        foreach($tmp as & $stockBillEntity){
            $stock_split_applies[(string) $stockBillEntity->bill_id] = $stockBillEntity;
        }

        return $stock_split_applies;
    }

    /**
     *
     * @param bool  $isBuyContract 是否销售合同
     * @param       $contractId 合同id
     * @param array $stockBillIds
     * @return mixed
     * @throws \Exception
     */
    public function getCanSplitStockBillEntitiesForEditScene(bool $isBuyContract,$contractId,array $stockBillIds):array{
        $status = ($isBuyContract ? \StockIn::STATUS_PASS : \StockOutOrder::STATUS_SUBMITED);
        $sub_where = "t.split_status=0 AND t.status >= ". $status;
        if(empty($stockBillIds)){
            //获取还能进行拆分的出入库单
            $where = "t.contract_id=:contract_id AND ".$sub_where;
        }else{
            $where = $isBuyContract ? 't.stock_in_id IN' : 't.out_order_id IN';
            $where = $where.'('.implode(',',array_keys($stockBillIds)).')';
            $where = "(($sub_where) OR ($where))";
        }
        $where = ' t.contract_id=:contract_id AND '.$where;

        //销售合同
        if($isBuyContract){
            $stockBillEntities = DIService::getRepository(IStockInRepository::class)->findAll($where, [':contract_id' => $contractId]);
        }else{
            $stockBillEntities = DIService::getRepository(IStockOutRepository::class)->findAll($where, [':contract_id' => $contractId]);
        }

        return $stockBillEntities;
    }

    private function getContractSplitItemsForViewScene(Contract & $originContractEntity,array $stock_bill_ids) :array{
        //获取已经拆分的合同
        $splitContractEntities = DIService::getRepository(IContractRepository::class)->findAll('original_id=:original_id',
            [':original_id' => $originContractEntity->contract_id]
        );
        if(empty($splitContractEntities)){
            throw new \Exception("未找到对应的出入库拆分合同记录！");
        }

        //出入库拆分申请
        $stockSplitApplyEntities = DIService::getRepository(IStockSplitApplyRepository::class)->findAllByContractId($originContractEntity->contract_id);
        if(empty($stockSplitApplyEntities)){
            throw new \Exception("未找到对应的出入库拆分申请记录！");
        }

        //出入库拆分申请
        $stockSplitApplyEntities = DIService::getRepository(IStockSplitApplyRepository::class)->findAllByContractId($originContractEntity->contract_id);
        $stock_bill_items = $this->getSubmittedStockBillDetailForViewScene($stockSplitApplyEntities);

        //设置拆分合同明细
        $contract_split_items = [];
        foreach($splitContractEntities as & $splitContractEntity){
            $contractSplitDTO = new ContractSplitDTO();
            $contractSplitDTO->fromEntityForEditScene($splitContractEntity);
            $contractSplitDTO->stock_bill_items = [];

            //设置原合同id、原合同编号
            $contractSplitDTO->contract_id = $originContractEntity->contract_id;
            $contractSplitDTO->contract_code = $originContractEntity->contract_code;

            foreach($stock_bill_ids as $bill_id){
                if(!isset($stock_bill_items[$splitContractEntity->contract_id][$bill_id])){
                    continue;
                }
                $contractSplitDTO->stock_bill_items[] = $stock_bill_items[$splitContractEntity->contract_id][$bill_id];
            }
            //
            $contract_split_items[] = $contractSplitDTO;
        }

        return $contract_split_items;
    }

    private function getContractSplitItemsForEditScene(Contract & $originContractEntity,ContractDTO & $originContractDto,array & $stock_bill_ids) :array{
        //获取已经拆分的合同
        $splitContractEntities = DIService::getRepository(IContractRepository::class)->findAll('original_id=:original_id',
            [':original_id' => $originContractEntity->contract_id]
        );

        if(empty($splitContractEntities)){
            throw new \Exception("未找到对应的出入库拆分合同记录！");
        }

        //出入库拆分申请
        $stockSplitApplyEntities_xxxxx = [];
        $stockSplitApplyEntities = DIService::getRepository(IStockSplitApplyRepository::class)->findAllByContractId($originContractEntity->contract_id);
        foreach($stockSplitApplyEntities as & $stockSplitApplyEntity){
            $stockSplitApplyEntities_xxxxx[(string)$stockSplitApplyEntity->bill_id] = $stockSplitApplyEntity;
        }

        //设置拆分合同明细
        $contract_split_items = [];
        foreach($splitContractEntities as & $splitContractEntity){
            $contractSplitDTO = new ContractSplitDTO();
            $contractSplitDTO->fromEntityForEditScene($splitContractEntity);
            $contractSplitDTO->stock_bill_items = [];

            //设置原合同id、原合同编号
            $contractSplitDTO->contract_id = $originContractEntity->contract_id;
            $contractSplitDTO->contract_code = $originContractEntity->contract_code;

            //根据原合同可拆分的出入库明细列表遍历，这也能确保数据一一对应排序。
            //如果原合同可拆分的出入库明细列表总数，在暂存出入库拆分信息之后变少了，则会自动过滤掉报错后的数据。
            //反之，原合同可拆分的出入库明细列表总数,在暂存出入库拆分信息之后变多了，则会自动根据原合同的出入库单(stockBillDto)信息构造一条
            foreach($originContractDto->stock_bill_items as & $stockBillDto){

                //如果数据库里没有，该可拆分  出入库单(原合同新增的可拆分出入库单stockBillDto)  的申请记录,则构造一条
                if(!isset($stockSplitApplyEntities_xxxxx[(string) $stockBillDto->bill_id])){
                    $stockSplitBillDto = $this->getStockSplitBillDtoFormStockBillDto($stockBillDto);
                    $contractSplitDTO->stock_bill_items[] = $stockSplitBillDto;

                    continue;
                }

                $stockSplitApplyEntity = $stockSplitApplyEntities_xxxxx[(string) $stockBillDto->bill_id];
                $stockSplitDetailEntity = $stockSplitApplyEntity->getSplitDetail($splitContractEntity->contract_id);
                //如果该申请记录里没有当前拆分合同对应的申请明细信息，则构造一条
                if(empty($stockSplitDetailEntity)){
                    $stockSplitBillDto = $this->getStockSplitBillDtoFormStockBillDto($stockBillDto);
                }else{
                    $stockSplitBillDto = new StockSplitBillDTO();
                    $stockSplitBillDto->fromEntityForEditScene($stockSplitDetailEntity);
                }
                $contractSplitDTO->stock_bill_items[] = $stockSplitBillDto;
            }

            $contract_split_items[] = $contractSplitDTO;
        }

        return $contract_split_items;
    }

    private function getContractSplitItemsForEditSceneX(Contract & $originContractEntity,ContractDTO & $originContractDto,array & $stock_bill_ids) :array{
        //获取已经拆分的合同
        $splitContractEntities = DIService::getRepository(IContractRepository::class)->findAll('original_id=:original_id',
            [':original_id' => $originContractEntity->contract_id]
        );

        if(empty($splitContractEntities)){
            throw new \Exception("未找到对应的出入库拆分合同记录！");
        }

        //出入库拆分申请

        $stockSplitApplyEntities = DIService::getRepository(IStockSplitApplyRepository::class)->findAllByContractId($originContractEntity->contract_id);

        //二维数组 [split_contract_id][bill_id]
        $stock_bill_items = $this->getUncommittedStockBillDetail($originContractDto,$splitContractEntities,$stockSplitApplyEntities);

        //设置拆分合同明细
        $contract_split_items = [];
        foreach($splitContractEntities as & $splitContractEntity){
            $contractSplitDTO = new ContractSplitDTO();
            $contractSplitDTO->fromEntityForEditScene($splitContractEntity);
            $contractSplitDTO->stock_bill_items = [];

            //设置原合同id、原合同编号
            $contractSplitDTO->contract_id = $originContractEntity->contract_id;
            $contractSplitDTO->contract_code = $originContractEntity->contract_code;

            foreach($stock_bill_ids as $bill_id){
                if(!isset($stock_bill_items[$splitContractEntity->contract_id][$bill_id])){
                    continue;
                }
                $contractSplitDTO->stock_bill_items[] = $stock_bill_items[$splitContractEntity->contract_id][$bill_id];
            }

            $contract_split_items[] = $contractSplitDTO;
        }

        return $contract_split_items;
    }

    private function getSubmittedStockBillDetailForViewScene(array & $stockSplitApplyEntities):array{
        if(\Utility::isEmpty($stockSplitApplyEntities)){
            return [];
        }

        $stock_bill_items = [];
        foreach($stockSplitApplyEntities as & $stockSplitApplyEntity){
            if(\Utility::isEmpty($stockSplitApplyEntity->getDetails())){
                continue;
            }

            foreach($stockSplitApplyEntity->getDetails() as & $stockSplitDetailEntity){
                $stockSplitBillDto = new StockSplitBillDTO();
                $stockSplitBillDto->fromEntityForEditScene($stockSplitDetailEntity);

                $stock_bill_items[$stockSplitDetailEntity->contract_id][(string) $stockSplitDetailEntity->bill_id] = $stockSplitBillDto;
            }
        }

        return $stock_bill_items;
    }

    private function getUncommittedStockBillDetail(ContractDTO & $originContractDto,array $splitContractEntities,array & $stockSplitApplyEntities = []):array{
        $stock_bill_items = [];
        $uncommittedStockBillDtos = [];
        if(\Utility::isNotEmpty($stockSplitApplyEntities)){
            foreach($stockSplitApplyEntities as & $stockSplitApplyEntity){
                //存储了的出入库拆分ids
                $stock_bill_items = [];
                if(!empty($stockSplitApplyEntity) && \Utility::isNotEmpty($stockSplitApplyEntity->getDetails())){
                    foreach($stockSplitApplyEntity->getDetails() as & $stockSplitDetailEntity){
                        $stockSplitBillDto = new StockSplitBillDTO();
                        $stockSplitBillDto->fromEntityForEditScene($stockSplitDetailEntity);

                        $uncommittedStockBillDtos[$stockSplitDetailEntity->contract_id][(string) $stockSplitDetailEntity->bill_id] = $stockSplitBillDto;
                    }
                }
            }
        }

        //如果暂存的出入库拆分信息，少于原合同可拆分的出入库单,
        //则由原合同下面多出的可用出入库单信息生成，出入库拆分申请下的拆分信息。
        //目的是为了原合同的出入库信息，和暂存的出入拆分信息，数据能一一对应，以便前端好遍历
        $stockSplitBillDtos = [];
        if(\Utility::isNotEmpty($originContractDto->stock_bill_items)){
            foreach($originContractDto->stock_bill_items as & $stockBillDto){
                $stock_bill_items[$stockBillDto->bill_id] = $this->getStockSplitBillDtoFormStockBillDto($stockBillDto);
            }

            foreach($splitContractEntities as & $splitContractEntity){
                foreach($originContractDto->stock_bill_items as & $stockBillDto){
                    if(isset($uncommittedStockBillDtos[$splitContractEntity->contract_id][(string) $stockBillDto->bill_id])){
                        $stockSplitBillDtos[$splitContractEntity->contract_id][(string) $stockBillDto->bill_id] = $uncommittedStockBillDtos[$splitContractEntity->contract_id][(string) $stockBillDto->bill_id];
                        continue;
                    }

                    $stockSplitBillDtos[$splitContractEntity->contract_id][(string) $stockBillDto->bill_id] = $stock_bill_items[$stockBillDto->bill_id];
                }

            }
        }

        return $stockSplitBillDtos;
    }

    /**
     * 给未进行过拆分的出入库单生成对应的拆分信息，并设置数量为0
     * @param StockBillDTO $stockBillDto
     * @return StockSplitBillDTO
     * @throws \Exception
     */
    private function getStockSplitBillDtoFormStockBillDto(StockBillDTO & $stockBillDto):StockSplitBillDTO{
        $stockSplitBillDto = new StockSplitBillDTO();
        $stockSplitBillDto->apply_id = 0;
        $stockSplitBillDto->bill_id = $stockBillDto->bill_id;
        $stockSplitBillDto->bill_code = $stockBillDto->bill_code;
        $stockSplitBillDto->goods_items = [];

        if(\Utility::isNotEmpty($stockBillDto->goods_items)){
            foreach($stockBillDto->goods_items as & $originTradeGoodsDto){
                $tradeGoodsDto = new TradeGoodsDTO();
                $tradeGoodsDto->setAttributes($originTradeGoodsDto->getAttributes());
                $tradeGoodsDto->quantity = 0;
                $stockSplitBillDto->goods_items[] = $tradeGoodsDto;
            }
        }

        return $stockSplitBillDto;
    }

    /**
     * @param StockSplitApply $stockInSplitEntity
     * @return bool|string
     */
    public function save(StockSplitApply $stockInSplitEntity){
       return $this->handleEvent(self::SCENE_SAVE, $stockInSplitEntity);
    }

    /**
     * @param StockSplitApply $stockInSplitEntity
     * @return bool|string
     */
    public function submit(StockSplitApply $stockInSplitEntity){
        return $this->handleEvent(self::SCENE_SUBMIT, $stockInSplitEntity);
    }

    /**
     * @param StockSplitApply $stockInSplitEntity
     * @return bool|string
     */
    public function checkBack(StockSplitApply $stockInSplitEntity){
        return $this->handleEvent(self::SCENE_CHECK_BACK, $stockInSplitEntity);
    }

    /**
     * @param StockSplitApply $stockInSplitEntity
     * @return bool|string
     */
    public function checkPass(StockSplitApply $stockInSplitEntity){
        return $this->handleEvent(self::SCENE_CHECK_PASS, $stockInSplitEntity);
    }

    /**
     * @param $scene
     * @param StockSplitApply $stockInSplitEntity
     * @return bool|string
     */
    protected function handleEvent($scene, StockSplitApply $stockInSplitEntity){
        try{
            $this->beginTransaction();

            $service = new \ddd\Split\Domain\Service\StockSplit\StockSplitService();

            if(self::SCENE_SAVE == $scene){
                $service->save($stockInSplitEntity);
            }elseif(self::SCENE_SUBMIT == $scene){
                $service->submit($stockInSplitEntity);
            }elseif(self::SCENE_CHECK_BACK == $scene){
                $service->checkBack($stockInSplitEntity);
            }elseif(self::SCENE_CHECK_PASS == $scene){
                $service->checkPass($stockInSplitEntity);
            }else{

            }

            $this->commitTransaction();

            return true;
        }
        catch(\Exception $e)
        {
            $this->rollbackTransaction();
            return $e->getMessage();
        }
    }

}