<?php

/**
 * Created by vector.
 * DateTime: 2018/3/21 11:35
 * Describe：非货款结算科目明细
 */

namespace ddd\domain\entity\contractSettlement;

use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\Attachment;
// use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\error\ZException;

class OtherExpenseSettlementItem extends BaseEntity
{
    
    /**
    * @var      int
    */
    public $detail_id;

    /**
    * @var      OtherFee
    */
    public $fee;
    
    /**
    * @var      Currency
    */
    public $currency;
    
    /**
    * @var      int
    */
    public $amount;
    
    /**
    * @var      float
    */
    public $exchange_rate;
    
    /**
    * @var      int
    */
    public $amount_cny;

    /**
    * @var      array
    */
    public $receipt_attachments;

    /**
    * @var      text
    */
    public $remark;

    /**
     * 创建对象
     * @return OtherExpenseSettlementItem
     * @throws \Exception
     */
    public static function create()
    {
       $entity = new OtherExpenseSettlementItem();
       $entity->generateId();

        return $entity;
    }
    
    /**
     * 上传非货款结算科目明细单据附件
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
     * 移除非货款结算科目明细单据附件
     * @param int $fileId
     * @return bool
     */
    public function removeReceiptAttachment($fileId)
    {
       unset($this->receipt_attachments[$fileId]);
       return true;
    }


    /**
     * 生成编号
     */
    public function generateId()
    {
        $this->detail_id=\IDService::getOtherExpenseSettlementId();
    }
}