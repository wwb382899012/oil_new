<?php
/**
 * Created by youyi000.
 * DateTime: 2018/6/1 17:04
 * Describe：
 */

namespace ddd\Split\Domain\Model\Stock;

use ddd\Common\IAggregateRoot;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApplyEnum;
use ddd\Split\Domain\Model\StockSplit\StockSplitEnum;

class StockOut extends StockBill implements IAggregateRoot
{

    /**
     * 发货单id
     * @var
     */
    public $order_id;

    use StockOutRepository;

    /**
     * 保存
     * @param bool $persistent
     * @return $this
     * @throws \Exception
     */
    public function save($persistent = true){
        if($persistent){
            $this->getStockOutRepository()->store($this);
        }

        return $this;
    }

    public function getType()
    {
        $this->type = ContractSplitApplyEnum::STOCK_TYPE_OUT;
        return $this->type;
    }

    /**
     * 是否可平移
     * @return bool
     */
    public function isCanSplit():bool {
        $flag = false;
        if ($this->status != \StockOutOrder::STATUS_SUBMITED) {
            return $flag;
        }
        return parent::isCanSplit();
    }

    /**
     * 出库单是否 待审核和待提交
     * @return bool
     */
    public function isOnChecking() {
        //TODO: 怎么个待审核和待提交？ 下面的逻辑判断应该不对
        return $this->status == \StockOutOrder::STATUS_SAVED || $this->status==\StockOutOrder::STATUS_SUBMIT;
    }

}