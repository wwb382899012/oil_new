<?php
/**
 * Desc:
 * User:  vector
 * Date: 2018/4/24
 * Time: 17:54
 */

namespace ddd\domain\entity\settlement;


use ddd\domain\entity\Attachment;
use ddd\domain\entity\BaseEntity;
use ddd\domain\entity\value\Currency;
use ddd\domain\entity\value\OtherFee;
use ddd\infrastructure\error\ZException;

class OtherSettlement extends BaseEntity
{

    #region property

    /**
     * 明细id
     * @var   bigint
     */
    public $detail_id;

    /**
     * 科目
     * @var   OtherFee
     */
    public $fee;

    /**
     * 币种
     * @var   Currency
     */
    public $currency;

    /**
     * 金额
     * @var   int
     */
    public $amount;

    /**
     * 汇率
     * @var   float
     */
    public $exchange_rate;

    /**
     * 人民币金额
     * @var   int
     */
    public $amount_cny;

    /**
     * 单据附件
     * @var   array
     */
    public $receipt_attachments;

    /**
     * 备注
     * @var   text
     */
    public $remark;

    #endregion

    /**
     * 创建对象
     * @return OtherSettlement
     * @throws \Exception
     */
    public static function create()
    {
        $entity = new OtherSettlement();
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
        $this->detail_id=\IDService::getOtherSettlementId();
    }
}