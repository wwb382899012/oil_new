<?php
/**
 * Created by vector.
 * DateTime: 2018/3/22 11:35
 * Describe：货款结算明细项
 */

namespace ddd\domain\entity\settlement;


use ddd\domain\entity\BaseEntity;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ZException;

class GoodsSettlementItem extends BaseEntity
{

    #region property

    /**
     * 货款明细
     * @var   GoodsExpenseItem
     */
    public $goods_expense_item;

    /**
     * 税收明细
     * @var   array
     */
    public $tax_items;

    /**
     * 其他费用明细
     * @var   array
     */
    public $other_expense_items;

    /**
     * 调整结算明细
     * @var   AdjustmentItem
     */
    public $adjustment_item;

    #endregion

    /**
     * 创建对象
     *
     * @return GoodsSettlementItem
     * @throws \Exception
     */
    public static function create()
    {
        $entity = new GoodsSettlementItem();

        return $entity;
    }

    /**
     * 添加货款明细项
     * @param GoodsExpenseItem $item
     * @return bool
     * @throws \Exception
     */
    public function addGoodsExpenseItem(GoodsExpenseItem $item)
    {
        if(empty($item))
            throw new ZException("GoodsExpenseItem对象不存在");

        $this->goods_expense_item=$item;
        return true;
    }

    /**
     * 添加调整明细项
     * @param AdjustmentItem $item
     * @return bool
     * @throws \Exception
     */
    public function addAdjustmentItem(AdjustmentItem $item)
    {
        if(empty($item))
            throw new ZException("AdjustmentItem对象不存在");

        $this->adjustment_item=$item;
        return true;
    }


    /**
     * 添加税收明细项
     * @param TaxItem $item
     * @return bool
     * @throws \Exception
     */
    public function addTaxItem(TaxItem $item)
    {
        if(empty($item))
            throw new ZException("TaxItem对象不存在");

        $taxId= empty($item->tax->id) ? 0 : $item->tax->id;

        if($this->taxSubjectIsExists($taxId))
        {
            throw new ZException(BusinessError::Tax_Subject_Is_Exists,
                array("tax_id"=>$taxId,));
        }
        $this->tax_items[$taxId]=$item;
        return true;
    }

    /**
     * 移除税收明细
     */
    public function removeTaxItem($taxId)
    {
        unset($this->tax_items[$taxId]);
        return true;
    }

    /**
     *计税明细中是否存在相同科目
     * @return   boolean
     */
    public function taxSubjectIsExists($taxId)
    {
        return isset($this->tax_items[$taxId]);
    }

    /**
     * 添加其他费用细项
     * @param OtherExpenseItem $item
     * @return bool
     * @throws \Exception
     */
    public function addOtherExpenseItem(OtherExpenseItem $item)
    {
        if(empty($item))
            throw new ZException("OtherExpenseItem对象不存在");

        $subjectId=empty($item->expense->id) ? 0 : $item->expense->id;
        if($this->otherExpenseSubjectIsExists($subjectId))
        {
            throw new ZException(BusinessError::Other_Expense_Subject_Is_Exists,
                array("subject_id"=>$subjectId,));
        }
        $this->other_expense_items[$subjectId]=$item;
        return true;
    }

    /**
     */
    public function removeOtherExpenseItem($subjectId)
    {
        unset($this->other_expense_items[$subjectId]);
        return true;
    }

    /**
     *其他费用明细中是否存在相同科目
     * @return   boolean
     */
    public function otherExpenseSubjectIsExists($subjectId)
    {
        return isset($this->other_expense_items[$subjectId]);
    }
}