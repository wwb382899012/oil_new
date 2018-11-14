<?php

/*
 * Created By: yu.li
 * DateTime:2018-5-29 11:49:31.
 * Desc:采购合同
 */

namespace ddd\Split\Domain\Model\Contract;

use ddd\domain\entity\stock\StockIn;
use ddd\infrastructure\DIService;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApplyStatus;
use ddd\Split\Domain\Model\ContractSplit\IContractSplitApplyRepository;
use ddd\Split\Domain\Model\Stock\IStockInRepository;
use ddd\Split\Repository\ContractSplit\BuyContractSplitApplyRepository;

class BuyContract extends Contract{
    #region property

    /**
     * 已入库平移入库单
     * @var   array
     */
    private $split_stock_ins = [];

    private $stockInRepository;

    private $contractSplitApplyRepository;

    #endregion

    public function init(){
        $this->type = ContractEnum::BUY_CONTRACT;

        $this->stockInRepository = DIService::getRepository(IStockInRepository::class);
        $this->contractSplitApplyRepository = DIService::getRepository(IContractSplitApplyRepository::class);

        parent::init();
    }

    /**
     * 创建对象
     * @param array $params
     * @return BuyContract
     * @throws \Exception
     */
    public static function create($params = []){
        $entity = new static($params);
        return $entity;
    }

    /**
     * 是否可入库平移
     * @return bool
     * @throws \Exception
     */
    public function isCanStockInSplit(){
        //是否存在审批通过的合同平移
        $flag = false;
        if(!$this->hasContractSplitApplyCheckPass()){
            return $flag;
        }
        //TODO原合同存在出入库审批通过 且 （未平移 或 已平移但可编辑）的入库单
        if(\Utility::isEmpty($this->getCanSplitStockBills())){
            return $flag;
        }
        return true;
    }


    /**
     * 获取可平移的入库单
     * @return   array|StockIn
     */
    public function getCanSplitStockBills():array{
        if(\Utility::isNotEmpty($this->can_split_stock_bills)){
            return $this->can_split_stock_bills;
        }

        //入库单状态>=审核通过 & 剩余可平移商品数>0
        $stockIns = $this->stockInRepository->findAllByContractId($this->contract_id);
        if(\Utility::isNotEmpty($stockIns)){
            foreach($stockIns as $stockInEntity){
                if($stockInEntity->isCanSplit()){
                    $this->can_split_stock_bills[(string)$stockInEntity->bill_id] = $stockInEntity;
                }
            }
        }
        return $this->can_split_stock_bills;
    }

    /**
     * 获取是提交状态的出/入库单
     * @return   array
     */
    public function getAllStockBills():array{
        if(\Utility::isNotEmpty($this->all_stock_bill_entities)){
            return $this->all_stock_bill_entities;
        }

        $condition = [
            //'select' => 't.*',
            'order' => 't.status_time DESC',
            'condition' => 't.contract_id=:contract_id AND t.status >= :status',
            'params' => [
                ':contract_id' => $this->contract_id,
                ':status' => \StockIn::STATUS_PASS,
            ],
        ];
        $stockBillEntities = $this->stockInRepository->findAll($condition);
        if(\Utility::isEmpty($stockBillEntities)){
            return [];
        }

        $this->all_stock_bill_entities = [];
        foreach($stockBillEntities as $stockInEntity){
            $this->all_stock_bill_entities[(string) $stockInEntity->bill_id] = $stockInEntity;
        }

        return $this->all_stock_bill_entities;
    }

    /**
     * 获取已入库平移的入库单
     * @return array
     * @throws \Exception
     */
    public function getSplitStockBills():array{
        if(\Utility::isNotEmpty($this->split_stock_ins)){
            return $this->split_stock_ins;
        }

        $stockIns = $this->stockInRepository->findAllByContractId($this->contract_id);
        if(\Utility::isNotEmpty($stockIns)){
            foreach($stockIns as $stockIn){
                if($stockIn->isSplit()){
                    $this->split_stock_ins[$stockIn->bill_id] = $stockIn;
                }
            }
        }

        return $this->split_stock_ins;
    }

    /**
     * 获取是否存在审核中的合同平移
     * @return bool
     * @throws \Exception
     */
    public function hasContractSplitApplyOnChecking():bool{
        $contractSplitApply = $this->contractSplitApplyRepository->findByContractId($this->contract_id);
        if(\Utility::isEmpty($contractSplitApply)){
            return false;
        }

        foreach($contractSplitApply as $apply){
            if($apply->isOnChecking()){
                return true;
            }
        }

        return false;
    }

    /**
     * 获取是否存在审核通过的合同平移
     * @return bool
     * @throws \Exception
     */
    public function hasContractSplitApplyCheckPass():bool{
        $contractSplitApply = $this->contractSplitApplyRepository->findByContractId($this->contract_id);
        if(\Utility::isEmpty($contractSplitApply)){
            return false;
        }

        foreach($contractSplitApply as $apply){
            if($apply->isCheckPass()){
                return true;
            }
        }
        return false;
    }

    /**
     * 是否存在待审核的入库单平移申请
     * @return bool|mixed
     */
    public function hasStockSplitApplyOnChecking():bool{
        $stockIns = $this->stockInRepository->findAllByContractId($this->contract_id);
        if(\Utility::isEmpty($stockIns)){
            return false;
        }

        foreach($stockIns as $stockIn){
            if($stockIn->hasSplitApplyInChecking()){
                return true;
            }
        }

        return false;
    }

}
