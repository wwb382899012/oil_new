<?php
/**
 * Created by youyi000.
 * DateTime: 2018/6/1 17:04
 * Describe：
 */

namespace ddd\Split\Domain\Model\Stock;

use ddd\Common\IAggregateRoot;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApplyEnum;

class StockIn extends StockBill implements IAggregateRoot{

    /**
     * 入库通知单/提单id
     * @var
     */
    public $batch_id;

    use StockInRepository;

    /**
     * 保存
     * @param bool $persistent
     * @return $this
     * @throws \Exception
     */
    public function save($persistent = true){
        if($persistent){
            $this->getStockInRepository()->store($this);
        }

        return $this;
    }

    public function getType(){
        $this->type = ContractSplitApplyEnum::STOCK_TYPE_IN;
        return $this->type;
    }

    /**
     * 是否可平移
     * @return bool
     */
    public function isCanSplit():bool{
        $flag = false;
        if($this->status != \StockIn::STATUS_PASS){
            return $flag;
        }

        return parent::isCanSplit();
    }

    /**
     * 入库单是否 待审核和待提交
     */
    public function isOnChecking(){
        //TODO: 怎么个待审核和待提交？ 下面的逻辑判断应该不对
        return $this->status == \StockIn::STATUS_NEW || $this->status == \StockIn::STATUS_SUBMIT;
    }
}