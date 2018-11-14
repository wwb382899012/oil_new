<?php

/*
 * Created By: yu.li
 * DateTime:2018-5-29 11:47:37.
 * Desc:Base Contract 
 */

namespace ddd\Split\Domain\Model\Contract;

use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ZException;
use ddd\Split\Domain\Model\TradeGoods;
use ddd\Split\Repository\ContractSplit\BuyContractSplitApplyRepository;

abstract class Contract extends BaseEntity implements IAggregateRoot{
    #region property

    /**
     * 合同id
     * @var   int
     */
    public $contract_id;

    /**
     * 合同编号
     * @var   string
     */
    public $contract_code;

    public $project_id;

    /**
     * 合作方
     * @var   int
     */
    public $partner_id = 0;

    /**
     * 交易主体ID
     * @var int
     */
    public $corporation_id = 0;

    /**
     * 1：采购合同
     * 2：销售合同
     * 合同类别
     * @var   int
     */
    public $type;

    /**
     * 合同状态
     * @var   int
     */
    public $status = 0;

    /**
     * 商品交易信息
     * @var   array
     */
    public $goods_items;

    /**
     * 0：原始
     * 1：拆分后的
     * 类型
     * @var   int
     */
    public $split_type = 0;

    /**
     * 原始合同ID
     * @var int
     */
    public $original_id = 0;

    public $is_main = 0;

    public $remark;

    /**
     * 创建日期
     * @var   datetime
     */
    public $create_time;

    /**
     * 更新日期
     * @var   datetime
     */
    public $update_time;

    /**
     * 可平移出/入库单
     * @var   array
     */
    protected $can_split_stock_bills = [];

    /**
     * 该合同下所有可用的出入库单实体
     * @var array
     */
    protected $all_stock_bill_entities = [];

    #endregion

    public function getId(){
        return $this->contract_id;
    }

    public function setId($contractId){
        $this->contract_id = $contractId;
    }

    /**
     * 是否可合同平移
     * @return   boolean
     */
    public function isCanContractSplit():bool{
        $flag = false;
        if($this->isChild()){//是否为子合同
            return $flag;
        }
        if($this->status < \Contract::STATUS_BUSINESS_CHECKED){
            return $flag;
        }

        if(!$this->hasGoods()){
            return $flag;
        }

        //是否有待审核的合同平移
        if($this->hasContractSplitApplyOnChecking()){
            return $flag;
        }
        //是否有待审核的出入库平移
        if($this->hasStockSplitApplyOnChecking()){
            return $flag;
        }
        return true;
    }


    /**
     * 是否有待审核的合同平移
     * @return bool
     */
    abstract function hasContractSplitApplyOnChecking():bool;


    /**
     * 是否有审核通过的合同平移
     */
    abstract function hasContractSplitApplyCheckPass():bool;

    /**
     * 是否有待审核的出入库平移
     * @return mixed
     */
    abstract function hasStockSplitApplyOnChecking():bool;

    /**
     * 添加商品
     * @param TradeGoods $tradeGoods
     * @throws ZException
     */
    public function addGoodsItem(TradeGoods $tradeGoods){
        if(empty($tradeGoods)){
            throw new ZException("参数TradeGoods对象为空");
        }
        if(isset($this->goods_items[$tradeGoods->goods_id])){
            throw new ZException(BusinessError::Contract_Goods_Is_Exists, ["contract_code" => $this->contract_code, "goods_id" => $tradeGoods->goods_id]);
        }
        $this->goods_items[$tradeGoods->goods_id] = $tradeGoods;
    }

    /**
     * 移除商品
     * @param TradeGoods $tradeGoods
     */
    public function removeGoodsItem(TradeGoods $tradeGoods){
        if(isset($this->goods_items[$tradeGoods->goods_id])){
            unset($this->goods_items[$tradeGoods->goods_id]);
        }
    }

    public function clearGoodsItem(){
        $this->goods_items = [];
    }

    /**
     * 是否还有商品
     * @return bool
     */
    public function hasGoods(){
        if(\Utility::isNotEmpty($this->goods_items)){
            $flag = false;
            foreach($this->goods_items as $goods){
                if($goods->quantity->quantity > 0){
                    $flag = true;
                    break;
                }
            }
            return $flag;
        }

        //没有商品一样可以拆分
        return true;
    }

    /**
     * 是否为拆分后的新合同
     * @return bool
     */
    public function isChild():bool{
        return $this->status == ContractEnum::SPLIT_TYPE_SPLIT && $this->original_id > 0;
    }

    /**
     *合同否未拆分过
     */
    public function isNotSplit():bool{
        return $this->split_type == ContractEnum::SPLIT_TYPE_NOT_SPLIT;
    }

    /**
     * 获取可平移的出/入库单
     * @return   array
     */
    abstract function getCanSplitStockBills():array;

    /**
     * 获取已出入库平移的出入库单
     * @return   array
     */
    abstract function getSplitStockBills():array;

    /**
     * 获取是提交状态的出/入库单
     * @return   array
     */
    abstract function getAllStockBills():array;

    /**
     * 获取有效的可以拆分的出/入库单ids
     * @return array
     */
    public function getCanSplitStockBillIds():array{
        if(\Utility::isEmpty($this->can_split_stock_bills)){
            $this->can_split_stock_bills = $this->getCanSplitStockBills();
            if(\Utility::isEmpty($this->can_split_stock_bills)){
                return [];
            }
        }

        $bill_ids = [];
        foreach($this->can_split_stock_bills as & $stockBillEntity){
            $bill_ids[(string)$stockBillEntity->bill_id] = (string)$stockBillEntity->bill_id;
        }

        return $bill_ids;
    }

    /**
     * 是否可以拆分的出/入单id
     * @param string $billId
     * @return bool
     */
    public function isCanSplitStockBill(string $billId):bool{
        $bill_ids = $this->getCanSplitStockBillIds();

        return isset($bill_ids[(string) $billId]);
    }

    /**
     * 是否虚拟单
     * @param string $billId
     * @return bool
     */
    public function isVirtualStockBill(string $billId):bool{
        $bill_entities = $this->getCanSplitStockBills();
        $bill_entity = $bill_entities[(string) $billId] ?? null;
        if(null === $bill_entity){
            return false;
        }

        return $bill_entity->is_virtual;
    }

    /**
     * 是否采购合同
     * @return bool
     */
    public function isBuyContract(){
        return ContractEnum::BUY_CONTRACT == $this->type;
    }

    /**
     * 是否销售合同
     * @return bool
     */
    public function isSellContract(){
        return ContractEnum::SELL_CONTRACT == $this->type;
    }

    public function getGoodsQuantities():array{
        $goods_quantities = [];
        foreach($this->goods_items as & $tradeGoodsEntity){
            $goods_quantities[$tradeGoodsEntity->goods_id] = $tradeGoodsEntity->quantity->quantity;
        }

        return $goods_quantities;
    }
}
