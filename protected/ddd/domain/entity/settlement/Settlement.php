<?php
/**
 * Created by vector.
 * DateTime: 2018/3/21 11:35
 * Describe：结算单
 */

namespace ddd\domain\entity\settlement;


use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\value\Currency;
use ddd\Common\IAggregateRoot;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ZException;

abstract class Settlement extends BaseEntity implements IAggregateRoot
{

    #region property

    /**
     * 标识
     * @var   bigint
     */
    public $settle_id;

    /**
     * 合同id
     * @var   int
     */
    public $contract_id;

    /**
     * 结算币种
     * @var   Currency
     */
    public $settle_currency;

    /**
     * 货款金额
     * @var   int
     */
    public $goods_amount;

    /**
     * 货款结算
     * @var   array(goods_id=>GoodsSettlement)
     */
    public $goods_settle_items;

    /**
     * 非货款金额
     * @var   int
     */
    public $other_amount;

    /**
     * 非货款结算
     * @var   array(subject_id=>OtherSettlement)
     */
    public $other_settle_items;

    /**
     * 总金额
     * @var   int
     */
    public $total_amount;

    /**
     * 结算日期
     * @var   date
     */
    public $settle_date;

    /**
     * 结算状态
     * @var   int
     */
    public $status;

    /**
     * 结算状态时间
     * @var   datetime
     */
    public $status_time;

    /**
     * 备注
     * @var   text
     */
    public $remark;

    #endregion

    /**
     * 是否可以编辑
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
     * 是否可以提交
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
     * 是否可以作废
     * @return   boolean
     */
    public function isCanTrash()
    {
        // TODO: implement
    }

    /**
     * 作废
     */
    public function trash()
    {
        // TODO: implement
    }

    /**
     * 是否可以撤回
     * @return   boolean
     */
    public function isCanWithdraw()
    {
        // TODO: implement
    }

    /**
     * 撤回
     */
    public function withdraw()
    {
        // TODO: implement
    }

    /**
     * 添加非货款结算项
     * @param OtherSettlement $item
     * @return bool
     * @throws \Exception
     */
    public function addOtherSettlement(OtherSettlement $item)
    {
        if(empty($item))
            throw new ZException("OtherSettlement对象不存在");

        $subjectId=empty($item->fee->id) ? 0 : $item->fee->id;

        if($this->otherSettlementSubjectIsExists($subjectId))
        {
            throw new ZException(BusinessError::Other_Expense_Settlement_Subject_Is_Exists,
                array("subject_id"=>$subjectId));
        }
        $this->other_settle_items[$subjectId]=$item;
        return true;
    }

    /**
     * 移除非货款结算项
     */
    public function removeOtherSettlement($subjectId)
    {
        unset($this->other_settle_items[$subjectId]);
        return true;
    }

    /**
     *非货款结算项中是否存在相同科目
     * @return   boolean
     */
    public function otherSettlementSubjectIsExists($subjectId)
    {
        return isset($this->other_settle_items[$subjectId]);
    }

    /**
     * 添加货款结算项
     * @param GoodsSettlement $item
     * @return bool
     * @throws \Exception
     */
    public function addGoodsSettlement(GoodsSettlement $item)
    {
        if(empty($item))
            throw  new ZException("GoodsSettlement对象不存在");

        $goodsId=$item->goods_id;

        $this->goods_settle_items[$goodsId]=$item;
        return true;
    }


    public function getId(){
        return $this->settle_id;
    }

    function getIdName(){
        return "settle_id";
    }

    function setId($value){
        $this->settle_id = $value;
    }


}

