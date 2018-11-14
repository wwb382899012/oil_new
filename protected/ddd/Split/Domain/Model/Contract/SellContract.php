<?php

/*
 * Created By: yu.li
 * DateTime:2018-5-29 11:51:21.
 * Desc:销售合同
 */

namespace ddd\Split\Domain\Model\Contract;

use ddd\infrastructure\DIService;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApplyStatus;
use ddd\Split\Domain\Model\ContractSplit\IContractSplitApplyRepository;
use ddd\Split\Domain\Model\ContractSplit\SellContractSplitApply;
use ddd\Split\Domain\Model\Stock\IStockOutRepository;
use ddd\Split\Repository\ContractSplit\SellContractSplitApplyRepository;


class SellContract extends Contract{
    #region property

    /**
     * 已出库平移出库单
     * @var   array
     */
    private $split_stock_outs = [];

    private $stockOutRepository;

    private $contractSplitApplyRepository;

    #endregion

    public function init(){
        $this->type = ContractEnum::SELL_CONTRACT;

        $this->stockOutRepository = DIService::getRepository(IStockOutRepository::class);
        $this->contractSplitApplyRepository = DIService::getRepository(IContractSplitApplyRepository::class);

        parent::init();
    }

    /**
     * 创建对象
     */
    public static function create($params = []){
        $entity = new static($params);
        return $entity;
    }

    /**
     * 是否可出库平移
     * @return bool
     * @throws \CException
     */
    public function isCanStockOutSplit(){
        //是否存在审批通过的销售合同平移
        $flag = false;
        if(!$this->hasContractSplitApplyCheckPass()){
            return $flag;
        }
        //TODO原合同存在出库审批通过 且 （未平移 或 已平移但可编辑）的出库单
        if(\Utility::isEmpty($this->getCanSplitStockOuts())){
            return $flag;
        }
        return true;
    }

    /**
     * 获取可平移的出库单
     * @return   array
     */
    public function getCanSplitStockBills():array{
        if(\Utility::isNotEmpty($this->can_split_stock_bills)){
            return $this->can_split_stock_bills;
        }

        //出库单状态>=审核通过 & 剩余可平移商品数>0
        $stockOuts = $this->stockOutRepository->findAllByContractId($this->contract_id);
        if(\Utility::isNotEmpty($stockOuts)){
            foreach($stockOuts as $stockOut){
                if($stockOut->isCanSplit()){
                    $this->can_split_stock_bills[$stockOut->bill_id] = $stockOut;
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
                ':status' => \StockOutOrder::STATUS_SUBMITED,
            ],
        ];
        $stockBillEntities = $this->stockOutRepository->findAll($condition);
        if(\Utility::isEmpty($stockBillEntities)){
            return [];
        }

        $this->all_stock_bill_entities = [];
        foreach($stockBillEntities as $stockOutEntity){
            $this->all_stock_bill_entities[(string) $stockOutEntity->bill_id] = $stockOutEntity;
        }

        return $this->all_stock_bill_entities;
    }

    /**
     * 获取已出库平移出库单
     * @return   array
     */
    public function getSplitStockBills():array{
        if(\Utility::isNotEmpty($this->split_stock_outs)){
            return $this->split_stock_outs;
        }

        $stockOuts = $this->stockOutRepository->findAllByContractId($this->contract_id);
        if(\Utility::isNotEmpty($stockOuts)){
            foreach($stockOuts as $stockOut){
                if($stockOut->isSplit()){
                    $this->split_stock_outs[$stockOut->bill_id] = $stockOut;
                }
            }
        }
        return $this->split_stock_outs;
    }

    /**
     * 是否存在待审核的合同平移
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
     * 是否存在审核通过的销售合同平移
     * @return bool
     * @throws \Exception
     */
    function hasContractSplitApplyCheckPass():bool{
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
     * 是否存在待审核的出库平移
     * @return bool|mixed
     */
    public function hasStockSplitApplyOnChecking():bool{
        $stockOuts = $this->stockOutRepository->findAllByContractId($this->contract_id);
        if(\Utility::isEmpty($stockOuts)){
            return false;
        }

        foreach($stockOuts as $stockOut){
            if($stockOut->hasSplitApplyInChecking()){
                return true;
            }
        }
        return false;
    }

}
