<?php
/**
 * Created by vector.
 * DateTime: 2018/3/22 11:35
 * Describe：货款结算商品明细
 */

namespace ddd\domain\entity\contractSettlement;

use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\contract\Contract;
use ddd\domain\entity\stock\LadingBill;
use ddd\infrastructure\error\BusinessError;
// use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\error\ZException;
use ddd\domain\entity\value\Quantity;
use ddd\infrastructure\Utility;
use ddd\domain\entity\Attachment;

class GoodsExpenseSettlementItem extends BaseEntity 
{
    
    /**
    * @var      bigint
    */
    public $item_id;

    /**
    * @var      bigint
    */
    public $relation_id;

    /**
    * @var      int
    */
    public $goods_id;
    
    /**
    * @var      Quantity
    */
    public $in_quantity;
    public $in_quantity_sub;
    
    /**
    * @var      Quantity
    */
    public $out_quantity;
    public $out_quantity_sub;
    
    /**
    * @var      Quantity
    */
    public $settle_quantity;
    public $settle_quantity_sub;
    
    /**
    * @var      Quantity
    */
    public $loss_quantity;
    public $loss_quantity_sub;
    
    /**
    * @var      int
    */
    public $settle_price;
    
    /**
    * @var      int
    */
    public $settle_amount;
    
    /**
    * @var      float
    */
    public $exchange_rate;
    
    /**
    * @var      int
    */
    public $settle_amount_cny;
    
    /**
    * @var      int
    */
    public $settle_price_cny;
    
    /**
    * @var      array
    */
    public $lading_items;
    
    /**
    * @var      array
    */
    public $order_items;
    
    /**
    * @var      array
    */
    public $goods_expense_items;
    
    /**
    * @var      array
    */
    public $tax_items;
    
    /**
    * @var      array
    */
    public $other_expense_items;
    
    /**
    * @var      array
    */
    public $adjustment_items;

    /**
     * 货款结算明细单据附件
     *        array(receipt_attachments=>Attachment)
     * @var      array
     */
    public $receipt_attachments;

    /**
     * 货款结算明细其他附件
     *        array(files=>Attachment)
     * @var      array
     */
    public $other_attachments;

    public $remark;

    public $isHaveDetail = false;

    /**
     * 创建对象
     * int goodsId
     * @return GoodsExpenseSettlementItem
     * @throws \Exception
     */
    public static function create($goodsId)
    {
       	$entity = new GoodsExpenseSettlementItem();
        $entity->generateId();
       	$entity->goods_id = $goodsId;
       	return $entity;
    }
    
    /**
     * 上传货款结算商品明细单据附件
     * @param Attachment $file
     * @return bool
     * @throws \Exception
     */
    public function addReceiptAttachment(Attachment $file)
    {
       	if (empty($file))
        {
            throw new ZException("Attachment对象不存在");
        }

        $this->receipt_attachments[$file->id] = $file;

        return true;
    }
    
    /**
     * 移除货款结算商品明细单据附件
     * @param int $fileId
     * @return bool
     */
    public function removeReceiptAttachment($fileId)
    {
       unset($this->receipt_attachments[$fileId]);
       return true;
    }
    
    /**
     * 上传货款结算商品明细其他附件
     * @param Attachment $file
     * @return bool
     * @throws \Exception
     */
    public function addOtherAttachment(Attachment $file)
    {
        if (empty($file))
        {
            throw new ZException("Attachment对象不存在");
        }

        $this->other_attachments[$file->id] = $file;

        return true;
    }
    
    /**
     * 移除货款结算商品明细其他附件
     * @param int $fileId
     * @return bool
     */
    public function removeOtherAttachment($fileId)
    {
       unset($this->other_attachments[$fileId]);
       return true;
    }

    /**
     * 添加提单结算明细
     * @param LadingBillSettlementItem $item
     * @return bool
     * @throws \Exception
     */
    public function addLadingItem(LadingBillSettlementItem $item)
    {
       	if(empty($item))
            throw new ZException("LadingBillSettlementItem对象不存在");

        $this->lading_items[]=$item;
        return true;
    }

    /**
     * 添加发货单结算明细
     * @param DeliveryOrderSettlementItem $item
     * @return bool
     * @throws \Exception
     */
    public function addOrderItem(DeliveryOrderSettlementItem $item)
    {
       	if(empty($item))
            throw new ZException("DeliveryOrderSettlementItem对象不存在");

        $this->order_items[]=$item;
        return true;
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

        $this->goods_expense_items=$item;
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

        $this->adjustment_items=$item;
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
     * 添加计其他费用细项
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


    /**
     * 生成编号
     */
    public function generateId()
    {
        $this->item_id=\IDService::getGoodsExpenseSettlementId();
    }
}