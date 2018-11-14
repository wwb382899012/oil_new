<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/3/16 0016
 * Time: 11:49
 */

namespace ddd\domain\service\contract;


use ddd\domain\entity\contract\Contract;
use ddd\Contract\Domain\Model\Project\Project;
use ddd\domain\enum\MainEnum;
use ddd\domain\iRepository\contract\IContractRepository;
use ddd\domain\iRepository\project\IProjectRepository;
use ddd\Common\Domain\BaseService;
use ddd\domain\service\risk\ContractCheckEnum;
use ddd\domain\service\risk\event\ContractBusinessRejectEvent;
use ddd\domain\service\risk\event\ContractRiskRejectEvent;
use ddd\domain\service\risk\event\ContractSubmitEvent;
use ddd\domain\service\risk\PartnerAmountEventService;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\infrastructure\error\ZException;
use ddd\repository\contract\ContractRepository;

use ddd\repository\project\ProjectRepository;
use ddd\domain\entity\contractSettlement\SettlementMode;
use ddd\repository\stock\DeliveryOrderRepository;
use ddd\repository\stock\LadingBillRepository;
use ddd\repository\stock\StockInRepository;
use ddd\repository\stock\StockOutRepository;
use ddd\domain\service\stock\DeliveryOrderService;
use ddd\domain\service\stock\LadingBillService;

class ContractService extends BaseService
{
    protected $repository;

    public function init()
    {
        $this->getRepository();
        parent::init();
    }

    /**
     * 获取仓储
     * @return IContractRepository|object
     * @throws \Exception
     */
    protected function getRepository()
    {
        if (empty($this->repository))
        {
            $this->repository = DIService::getRepository(IContractRepository::class);
        }

        return $this->repository;
    }

    /**
     * @desc 合同提交
     * @param Contract $contractEntity
     * @throws \Exception
     */
    public function contractSubmit(Contract $contractEntity)
    {
        if ($contractEntity->isCanSubmit())
        {
            //更新合同信息
            $contractEntity->submit();

            //提交主合同时
            if ($contractEntity->is_main == MainEnum::IS_MAIN)
            {
                //更新项目的状态，标记不能再驳回
                $projectEntity = DIService::getRepository(IProjectRepository::class)->findByPk($contractEntity->project_id);
                if (empty($projectEntity->project_id))
                {
                    throw new ZEntityNotExistsException($contractEntity->project_id, Project::class);
                }

                $projectEntity->is_can_back = 0;
                DIService::getRepository(IProjectRepository::class)->saveCannotBack($projectEntity);

                //如果是双边合同，更新关联合同信息
                if (!empty($contractEntity->relation_contract_id))
                {
                    $relationContractEntity = $this->repository->findByPk($contractEntity->relation_contract_id);
                    if (empty($relationContractEntity->contract_id))
                    {
                        throw new ZEntityNotExistsException($contractEntity->relation_contract_id, Contract::class);
                    }
                    if ($relationContractEntity->isCanSubmit())
                    {
                        $relationContractEntity->submit();
                    }
                }
            }
        } else
        {
            ExceptionService::throwBusinessException(BusinessError::Contract_Cannot_Submit);
        }
    }

    /**
     * @desc 合同风控驳回
     * @param Contract $contractEntity
     * @throws \Exception
     */
    public function riskCheckBack(Contract $contractEntity)
    {
        if ($contractEntity->isCanRiskBack())
        {
            //更新合同信息
            $contractEntity->riskBack();

            //提交主合同时
            if ($contractEntity->is_main)
            {
                //如果是双边合同，更新关联合同信息
                if (!empty($contractEntity->relation_contract_id))
                {
                    $relationContractEntity = $this->repository->findByPk($contractEntity->relation_contract_id);
                    if (empty($relationContractEntity->contract_id))
                    {
                        throw new ZEntityNotExistsException($contractEntity->relation_contract_id, "Contract");
                    }

                    if ($relationContractEntity->isCanRiskBack())
                    {
                        $relationContractEntity->riskBack();
                    }
                }
            }
        } else
        {
            throw new ZException(BusinessError::Contract_Cannot_Risk_Back);
        }
    }

    /**
     * @desc 合同业务驳回
     * @param Contract $contractEntity
     * @throws \Exception
     */
    public function businessCheckBack(Contract $contractEntity)
    {
        if ($contractEntity->isCanBusinessBack())
        {
            //更新合同信息
            $contractEntity->businessBack();

            //提交主合同时
            if ($contractEntity->is_main == MainEnum::IS_MAIN)
            {
                //如果是双边合同，更新关联合同信息
                if (!empty($contractEntity->relation_contract_id))
                {
                    $relationContractEntity = $this->repository->findByPk($contractEntity->relation_contract_id);
                    if (empty($relationContractEntity->contract_id))
                    {
                        ExceptionService::throwModelDataNotExistsException($contractEntity->relation_contract_id, 'Contract');
                    }
                    if ($relationContractEntity->isCanBusinessBack())
                    {
                        $relationContractEntity->businessBack();
                    }
                }
            }
        } else
        {
            ExceptionService::throwBusinessException(BusinessError::Contract_Cannot_Submit);
        }
    }


    /**
     * @desc 合同是否可以结算
     * @param Contract $contract
     * @return boolean
     */
    public function isCanSettle(Contract $contract)
    {
        $isBool = false;

        if(empty($contract))
            return $isBool;

        if($contract->type==\ConstantMap::BUY_TYPE){
            if($contract->settle_type==SettlementMode::LADING_BILL_MODE_SETTLEMENT){
                $isBool = $this->isLadingBillSettlementFinish($contract);
            }else{
                $isBool = $this->isStockInFinish($contract);
            }
        }else{
            if($contract->settle_type==SettlementMode::DELIVERY_ORDER_MODE_SETTLEMENT){
                $isBool = $this->isDeliveryOrderSettlementFinish($contract);
            }else{
                $isBool = $this->isStockOutFinish($contract);
            }
        }
        if($isBool){
            if($contract->status>=\Contract::STATUS_SETTLED_SUBMIT  || $contract->status==\Contract::STATUS_SETTLE_INVALIDITY)
                $isBool = "当前状态下的合同（".$contract->contract_code."）不能发起结算！";
        }

        return $isBool;
    }



    /**
     * @desc 合同下所有发货单是否结算完成
     * @param $contractId
     * @return boolean
     */
    public function isDeliveryOrderSettlementFinish(Contract $contract)
    {
        if(empty($contract))
            return "参数有误，不能发起合同结算！";
        
        $deliveryOrders = DeliveryOrderRepository::repository()->findAllByContractId($contract->contract_id);
        if (!empty($deliveryOrders)) {
            $num = 0;
            foreach ($deliveryOrders as $key => $row) {
                if($row->status!=\DeliveryOrder::STATUS_SETTLE_PASS) {
                    if($row->status==\DeliveryOrder::STATUS_SETTLE_INVALIDITY)
                        $num += 1;
                    else
                        return "销售合同（".$contract->contract_code."）下发货单（".$row->code."）未结算，不能发起合同结算！";
                }
            }

            if(count($deliveryOrders)==$num)
                return "销售合同（".$contract->contract_code."）下所有发货单都是作废状态，不能发起合同结算！";

            return true;
        }

        return "销售合同（".$contract->contract_code."）下没有发货单，不能发起合同结算！";
    }


    /**
     * @desc 合同下所有提单是否结算完成
     * @param $contractId
     * @return boolean
     */
    public function isLadingBillSettlementFinish(Contract $contract)
    {
        if(empty($contract))
            return "参数有误，不能发起合同结算";
        
        $ladingBills = LadingBillRepository::repository()->findAllByContractId($contract->contract_id);
        if (!empty($ladingBills)) {
            $num = 0;
            foreach ($ladingBills as $key => $row) {
                if($row->status!=\StockNotice::STATUS_SETTLED) {
                    if($row->status==\StockNotice::STATUS_SETTLE_INVALIDITY)
                        $num += 1;
                    else
                        return "采购合同（".$contract->contract_code."）下入库通知单（".$row->code."）未结算，不能发起合同结算！";
                }
            }

            if(count($ladingBills)==$num)
                return "采购合同（".$contract->contract_code."）下所有入库通知单都是作废状态，不能发起合同结算！";

            return true;
        }

        return "采购合同（".$contract->contract_code."）下没有入库通知单，不能发起合同结算！";
    }


    /**
     * @desc 合同下所有入库单是否完成
     * @param $contractId
     * @return boolean
     */
    public function isStockInFinish(Contract $contract)
    {
        if(empty($contract))
            return "参数有误，不能发起合同结算";
        
        $ladingBills = LadingBillRepository::repository()->findAllByContractId($contract->contract_id);
        if(!empty($ladingBills)){
            foreach ($ladingBills as $ladingBill) {
                $isBool = LadingBillService::service()->isStockInFinish($ladingBill);
                if($isBool !== true)
                    return "采购合同（".$contract->contract_code."）下".$isBool;
            }

            return true;
        }

        return "采购合同（".$contract->contract_code."）下没有入库通知单，不能发起合同结算！";
    }


    /**
     * @desc 合同下所有出库单是否完成
     * @param $contractId
     * @return boolean
     */
    public function isStockOutFinish(Contract $contract)
    {
        if(empty($contract))
            return "参数有误，不能发起合同结算";
        
        $deliveryOrders = DeliveryOrderRepository::repository()->findAllByContractId($contract->contract_id);
        if(!empty($deliveryOrders)){
            foreach ($deliveryOrders as $deliveryOrder) {
                $isBool = DeliveryOrderService::service()->isStockOutFinish($deliveryOrder);
                if($isBool !== true)
                    return "销售合同（".$contract->contract_code."）下".$isBool;
            }

            return true;
        }

        return "销售合同（".$contract->contract_code."）下没有发货单，不能发起合同结算！";
    }


}