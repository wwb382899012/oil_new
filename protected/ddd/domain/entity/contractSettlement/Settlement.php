<?php
/**
 * Created by vector.
 * DateTime: 2018/3/21 11:35
 * Describe：结算单
 */

namespace ddd\domain\entity\contractSettlement;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ExceptionService;

abstract class Settlement extends BaseEntity implements IAggregateRoot
{
    
    /**
    * @var      bigint
    */
    public $settle_id;

    /**
    * @var      bigint
    */
    public $code;

    /**
    * @var      bigint
    */
    public $contract_id;
    
    /**
    * @var      Currency
    */
    public $settle_currency;
    
    /**
    * @var      int
    */
    public $goods_amount;
    
    /**
     * 货款结算明细信息
     *        array(goods_id=>GoodsExpenseSettlementItem)
     * @var      array
     */
    public $goods_expense;
    
    /**
    * @var      int
    */
    public $other_amount;

    /**
    * @var      int
    */
    public $total_amount;
    
    /**
     * 非货款结算明细信息
     *        array(subject_id=>OtherExpenseSettlementItem)
     * @var      array
     */
    public $other_expense;
    
    /**
    * @var      date
    */
    public $settle_date;
    
    /**
    * @var      int
    */
    public $status;
    
    /**
    * @var      datetime
    */
    public $status_time;

    /**
    * @var      text
    */
    public $remark;
    
    /**
    * @return   boolean
    */
    public function isCanEdit()
    {
        $isBool = false;
        if($this->status>=SettlementStatus::STATUS_RECALL && $this->status<=SettlementStatus::STATUS_SAVED)
            $isBool = true;
        
        return $isBool;
    }
    
    /**
    * @return   boolean
    */
    public function isCanSubmit()
    {   
        return in_array($this->status, array(SettlementStatus::STATUS_RECALL, SettlementStatus::STATUS_BACK, SettlementStatus::STATUS_SAVED));
    }

    /**
    * 结算单提交
    */
    abstract public function submit();

    /**
    * 审核通过
    */
    abstract public function checkPass();

    /**
    *审核驳回
    */
    abstract public function checkBack();
    
    
    /**
    * @return   boolean
    */
    public function isCanTrash()
    {
       // TODO: implement
    }
    
    /**
    */
    public function trash()
    {
       // TODO: implement
    }
    
    /**
    * @return   boolean
    */
    public function isCanWithdraw()
    {
       // TODO: implement
    }
    
    /**
    */
    public function withdraw()
    {
       // TODO: implement
    }
    
    /**
     * 添加非货款结算科目明细项
     * @param OtherExpenseSettlementItem $item
     * @return bool
     * @throws \Exception
     */
    public function addOtherExpenseSettlementItem(OtherExpenseSettlementItem $item)
    {
        if(empty($item))
            ExceptionService::throwArgumentNullException("OtherExpenseSettlementItem对象",array('class'=>get_class($this), 'function'=>__FUNCTION__));

        $subjectId=empty($item->fee->id) ? 0 : $item->fee->id;

        if($this->otherExpenseSettlementSubjectIsExists($subjectId))
        {
            ExceptionService::throwBusinessException(BusinessError::Other_Expense_Settlement_Subject_Is_Exists,
                                                     array("subject_id"=>$subjectId,));
        }
        $this->other_expense[$subjectId]=$item;
        return true;
    }
    
    /**
    * 移除非货款结算科目明细项
    */
    public function removeOtherExpenseSettlementItem($subjectId)
    {
    	unset($this->other_expense[$subjectId]);
        return true;
    }
    
    /**
    *非货款明细中是否存在相同科目
    * @return   boolean
    */
    public function otherExpenseSettlementSubjectIsExists($subjectId)
    {
       return isset($this->other_expense[$subjectId]);
    }
    
    /**
     * 添加货款结算商品明细项
     * @param GoodsExpenseSettlementItem $item
     * @return bool
     * @throws \Exception
     */
    public function addGoodsExpenseSettlementItem(GoodsExpenseSettlementItem $item)
    {
       if(empty($item))
            ExceptionService::throwArgumentNullException("GoodsExpenseSettlementItem对象",array('class'=>get_class($this), 'function'=>__FUNCTION__));

        $goodsId=$item->goods_id;

        $this->goods_expense[$goodsId]=$item;
        return true;
    }


    public function getId(){
    	return $this->settle_id;
    }

    function getIdName(){
    	return "settle_id";
    }

    function setId($value)
    {
        $this->settle_id=$value;
    }

    
}

