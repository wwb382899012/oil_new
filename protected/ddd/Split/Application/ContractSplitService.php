<?php

namespace ddd\Split\Application;

use ddd\Common\Application\TransactionService;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ExceptionService;
use ddd\Split\Domain\Model\Contract\IContractRepository;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApply;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApplyEnum;
use ddd\Split\Domain\Model\ContractSplit\IContractSplitApplyRepository;
use ddd\Split\Domain\Model\ContractSplit\IContractSplitRepository;
use ddd\Split\Dto\Contract\ContractDTO;
use ddd\Split\Dto\ContractSplit\ContractSplitApplyDTO;
use ddd\Split\Dto\ContractSplit\ContractSplitApplyListDTO;
use ddd\Split\Dto\ContractSplit\ContractSplitInfoDTO;
use ddd\Split\Dto\ContractSplit\OriginContractDTO;
use ddd\Split\Repository\Contract\ContractRepository;

/**
 * 合同拆分应用层对象，具备事务能力
 * Class ContractSplitService
 * @package ddd\Split\Application
 */
class ContractSplitService extends TransactionService{
    protected $contractSplitApplyRepository;
    protected $contractSplitRepository;
    protected $contractRepository;

    public function __construct(){
        $this->contractSplitApplyRepository = DIService::getRepository(IContractSplitApplyRepository::class);
        $this->contractSplitRepository = DIService::getRepository(IContractSplitRepository::class);
        $this->contractRepository = DIService::getRepository(IContractRepository::class);
    }

    /**
     * 获取合同拆分详情,编辑用
     * @param int $contractId
     * @param int $applyId
     * @return ContractSplitInfoDTO|void
     * @throws \Exception
     */
    public function getContractSplitInfoDtoForEditScene(int $contractId,int $applyId = 0) :ContractSplitInfoDTO{
        //原来合同
        $originContractEntity = DIService::getRepository(IContractRepository::class)->findByPk($contractId);
        if(empty($originContractEntity)){
            ExceptionService::throwBusinessException(BusinessError::Contract_Not_Exists, array('contract_id' => $contractId));
        }

        //触发清理事件，清理之前保存了拆分数据，但未生效，当下不能进行拆分的
        if(!$originContractEntity->isCanContractSplit()){
            $contractSplitApplyEntity = DIService::getRepository(IContractSplitApplyRepository::class)->findByPk($applyId);
            if(empty($contractSplitApplyEntity)){
                ExceptionService::throwBusinessException(BusinessError::Contract_split_Apply_Not_Exists, array('apply_id' => $applyId));
            }
            $this->trash($contractSplitApplyEntity);

            ExceptionService::throwBusinessException(BusinessError::Contract_Cannot_Split_Apply, ['contract_code' => $originContractEntity->contract_code]);
        }

        //原合同
        $originContractDto = new ContractDTO();
        $originContractDto->fromEntity($originContractEntity);

        //编辑页面过滤掉不可以拆分的出入单
        $stock_bill_items = [];
        foreach($originContractDto->stock_bill_items as & $stock_bill_dto){
            if(!$stock_bill_dto->is_can_split){
                continue;
            }
            $stock_bill_items[] = $stock_bill_dto;
        }
        $originContractDto->stock_bill_items = $stock_bill_items;


        $contractSplitApplyDTO = null;
        if(!empty($applyId)){
            $contractSplitApplyEntity = DIService::getRepository(IContractSplitApplyRepository::class)->findByPk($applyId);
            if(empty($contractSplitApplyEntity)){
                ExceptionService::throwBusinessException(BusinessError::Contract_split_Apply_Not_Exists, array('apply_id' => $applyId));
            }

            if(!$contractSplitApplyEntity->isCanEdit()){
                ExceptionService::throwBusinessException(BusinessError::Contract_split_Apply_Not_Exists, array('apply_id' => $applyId));
            }

            if($contractSplitApplyEntity->contract_id != $originContractEntity->contract_id){
                ExceptionService::throwBusinessException(OilError::$PARAMS_PASS_ERROR);
            }

            //拆分申请
            $contractSplitApplyDTO = new ContractSplitApplyDTO();
            $contractSplitApplyDTO->fromEntityForEditScene($contractSplitApplyEntity);

            //清除暂时保存，但当下不能进行拆分的出入库单拆分数据
            foreach($contractSplitApplyDTO->contract_split_items as & $contract_split_item_dto){
                $stock_bill_items = [];
                foreach($contract_split_item_dto->stock_bill_items as & $stock_bill_item){
                    if($originContractEntity->isCanSplitStockBill($stock_bill_item->bill_id)){
                        $stock_bill_items[] = $stock_bill_item;
                    }
                }
                $contract_split_item_dto->stock_bill_items = $stock_bill_items;
            }

            //出入库拆分中未勾选拆分但保存了的，原合同的是否勾选拆分,标记为未勾选
            if(isset($originContractDto->stock_bill_items) && \Utility::isNotEmpty($originContractDto->stock_bill_items)){
                foreach($originContractDto->stock_bill_items as & $origin_contract_dto){
                    if(!$contractSplitApplyEntity->isEffectiveStockSplitBill($origin_contract_dto->bill_id)){
                        $origin_contract_dto->is_split = false;
                    }
                }
            }
        }

        $contractSplitInfoDTO = new ContractSplitInfoDTO();
        $contractSplitInfoDTO->origin_contract = $originContractDto;
        $contractSplitInfoDTO->contract_split_apply = $contractSplitApplyDTO;

        return $contractSplitInfoDTO;
    }

    /**
     * 获取合同拆分详情,查看用, 原出入库单和拆分出入库单数据左右对称
     * @param int $applyId
     * @return ContractSplitInfoDTO
     * @throws \Exception
     */
    public function getContractSplitInfoDtoForViewScene(int $applyId):ContractSplitInfoDTO{
        $contractSplitApplyEntity = DIService::getRepository(IContractSplitApplyRepository::class)->findByPk($applyId);

        if(empty($contractSplitApplyEntity)){
            ExceptionService::throwBusinessException(BusinessError::Contract_split_Apply_Not_Exists, array('apply_id' => $applyId));
        }

        //原合同
        $originContractEntity = DIService::getRepository(IContractRepository::class)->findByPk($contractSplitApplyEntity->contract_id);
        if(empty($originContractEntity)){
            ExceptionService::throwBusinessException(BusinessError::Contract_Not_Exists, array('contract_id' => $contractSplitApplyEntity->contract_id));
        }


        //拆分申请
        $contractSplitApplyDTO = new ContractSplitApplyDTO();
        $contractSplitApplyDTO->fromEntityForViewScene($contractSplitApplyEntity);

        //原合同
        $originContractDto = new ContractDTO();
        $originContractDto->fromEntity($originContractEntity);

        //清除没有勾选拆分、无效拆分的出入库单
        $stock_bill_items = [];
        $effective_stock_bill_ids = $contractSplitApplyEntity->getEffectiveStockSplitBillIds();
        foreach($originContractDto->stock_bill_items as & $stockBillDto){
            if(!isset($effective_stock_bill_ids[(string) $stockBillDto->bill_id])){
                continue;
            }

            //查看页面，所有的出入库单都不可以拆分，不可以勾选
            $stockBillDto->is_can_split = false;
            $stockBillDto->is_split = false;

            $stock_bill_items[] = $stockBillDto;
        }
        $originContractDto->stock_bill_items = $stock_bill_items;

        //
        $contractSplitInfoDTO = new ContractSplitInfoDTO();
        $contractSplitInfoDTO->origin_contract = $originContractDto;
        $contractSplitInfoDTO->contract_split_apply = $contractSplitApplyDTO;

        return $contractSplitInfoDTO;
    }

    /**
     * 获取申请列表
     * @param int $contractId
     * @return ContractSplitApplyListDTO
     * @throws \Exception
     */
    public function getApplyList(int $contractId): ContractSplitApplyListDTO{
        $originContractEntity = ContractRepository::repository()->findByPk($contractId);
        if(empty($originContractEntity)){
            ExceptionService::throwBusinessException(BusinessError::Contract_Not_Exists, array('contract_id' => $contractId));
        }

        $dto = new ContractSplitApplyListDTO();
        $dto->fromEntity($originContractEntity);

        return $dto;
    }

    /**
     * 保存
     * @param ContractSplitApply $entity
     * @return int|string
     * @throws \Exception
     */
    public function save(ContractSplitApply $entity){
        if(!$entity->isCanSubmit()){
            throw new \Exception(BusinessError::Contract_Split_Apply_Cannot_Edit);
        }

        try{
            $this->beginTransaction();
            $res = $entity->save();

            $this->commitTransaction();

            return intval($res);
        }catch(\Exception $e){
            $this->rollbackTransaction();

            return $e->getMessage();
        }
    }

    /**
     * 合同拆分提交
     * @param ContractSplitApply $entity
     * @param bool $persistent
     * @return bool|string
     * @throws \Exception
     */
    public function submit(ContractSplitApply $entity, $persistent = true){
        if(!$entity->isCanSubmit()){
            throw new \Exception(BusinessError::Contract_split_Apply_Not_Allow_Submit);
        }

        try{
            $this->beginTransaction();
            $entity->submit($persistent);
            $this->commitTransaction();

            return true;
        }catch(\Exception $e){
            $this->rollbackTransaction();

            return $e->getMessage();
        }
    }

    /**
     * 合同拆分审批驳回
     * @param ContractSplitApply $entity
     * @param bool $persistent
     * @return bool|string
     * @throws \Exception
     */
    public function checkBack(ContractSplitApply $entity, $persistent = true){
        if(!$entity->isOnChecking()){
            throw new \Exception(BusinessError::Contract_split_Apply_Not_Allow_Check_Back);
        }

        if(!empty($entity)){
            try{
                $this->beginTransaction();

                $entity->reject($persistent);

                $this->commitTransaction();

                return true;
            }catch(\Exception $e){
                $this->rollbackTransaction();

                return $e->getMessage();
            }
        }else{
            return false;
        }
    }

    /**
     * 合同拆分审批通过
     * @param ContractSplitApply $entity
     * @param bool $persistent
     * @return bool|string
     * @throws \Exception
     */
    public function checkPass(ContractSplitApply $entity, $persistent = true){
        if(!$entity->isOnChecking()){
            throw new \Exception(BusinessError::Contract_split_Apply_Not_Allow_Check);
        }

        if(!empty($entity)){
            try{
                $this->beginTransaction();
                $entity->checkPass($persistent);

                $this->commitTransaction();

                return true;
            }catch(\Exception $e){
                $this->rollbackTransaction();

                return $e->getMessage();
            }
        }else{
            return false;
        }
    }

    /**
     * 触发清理事件，清理之前保存了拆分数据，但未生效，当下不能进行拆分的
     * @param ContractSplitApply $entity
     * @param bool $persistent
     * @return bool|string
     * @throws \Exception
     */
    public function trash(ContractSplitApply $entity, $persistent = true){
        if(!$entity->isCanTrash()){
            throw new \Exception(BusinessError::Contract_split_Apply_Not_Allow_Trash);
        }

        if(!empty($entity)){
            try{
                $this->beginTransaction();

                $entity->trash($persistent);

                $this->commitTransaction();

                return true;
            }catch(\Exception $e){
                $this->rollbackTransaction();

                return $e->getMessage();
            }
        }else{
            return false;
        }
    }
}