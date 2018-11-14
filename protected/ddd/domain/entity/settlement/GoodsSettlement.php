<?php
/**
 * Created by vector.
 * DateTime: 2018/3/22 11:35
 * Describe：货款结算商品明细
 */

namespace ddd\domain\entity\settlement;

use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\contract\Contract;
use ddd\domain\entity\stock\LadingBill;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ZException;
use ddd\domain\entity\value\Quantity;
use ddd\infrastructure\Utility;
use ddd\domain\entity\Attachment;

class GoodsSettlement extends BaseEntity
{

    #region property

    /**
     * 明细id
     * @var   bigint
     */
    public $item_id;

    /**
     * 入库通知单/发货单id
     * @var   bigint
     */
    public $relation_id;

    /**
     * 商品
     * @var   int
     */
    public $goods_id;

    /**
     * 入库或出库数量
     * @var   Quantity
     */
    public $bill_quantity;

    /**
     * 第二单位入库数量
     * @var   Quantity
     */
    public $bill_quantity_sub;

    /**
     * 结算数量
     * @var   Quantity
     */
    public $settle_quantity;

    /**
     * 第二单位结算数量
     * @var   Quantity
     */
    public $settle_quantity_sub;

    /**
     * 损耗量
     * @var   Quantity
     */
    public $loss_quantity;

    /**
     * 第二单位损耗量
     * @var   Quantity
     */
    public $loss_quantity_sub;

    /**
     * 结算单价
     * @var   int
     */
    public $settle_price;

    /**
     * 结算金额
     * @var   int
     */
    public $settle_amount;

    /**
     * 结算汇率
     * @var   float
     */
    public $exchange_rate;

    /**
     * 人民币结算金额
     * @var   int
     */
    public $settle_amount_cny;

    /**
     * 人民币结算单价
     * @var   int
     */
    public $settle_price_cny;

    /**
     * 提单或发货单结算明细
     * @var   array
     */
    public $bill_items;

    /**
     * 结算单据附件
     * @var   array
     */
    public $receipt_attachments;

    /**
     * 其他附件
     * @var   array
     */
    public $other_attachments;

    /**
     * 货款结算明细项
     * @var   GoodsSettlementItem
     */
    public $goods_settlement_item;

    /**
     * 是否有明细
     * @var   boolean
     */
    public $has_detail=false;

    /**
     * 备注
     * @var   text
     */
    public $remark;

    #endregion

    /**
     * 创建对象
     * int goodsId
     * @return GoodsSettlement
     * @throws \Exception
     */
    public static function create($goodsId)
    {
        if(empty($goodsId))
            throw new ZException("货款商品不存在");

        $entity = new GoodsSettlement();
        $entity->generateId();
        $entity->goods_id = $goodsId;

        return $entity;
    }

    /**
     * 货款结算明细添加
     * @param GoodsSettlementItem $item
     * @return bool
     * @throws \Exception
     */
    public function addGoodsSettlementItem(GoodsSettlementItem $item)
    {
        if (empty($item))
        {
            throw new ZException("GoodsSettlementItem对象不存在");
        }

        $this->goods_settlement_item = $item;

        return true;
    }

    /**
     * 货款结算明细取消
     * @param GoodsSettlementItem $item
     * @return bool
     */
    public function removeGoodsSettlementItem(GoodsSettlementItem $item)
    {
        $this->goods_settlement_item = "";
        return true;
    }


    /**
     * 上传货款结算项单据附件
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
     * 移除货款结算项单据附件
     * @param int $fileId
     * @return bool
     */
    public function removeReceiptAttachment($fileId)
    {
        unset($this->receipt_attachments[$fileId]);
        return true;
    }

    /**
     * 上传货款结算项其他附件
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
     * 移除货款结算项其他附件
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
     * @param BillSettlementItem $item
     * @return bool
     * @throws \Exception
     */
    public function addBillSettlementItem(BillSettlementItem $item)
    {
        if(empty($item))
            throw new ZException("BillSettlementItem对象不存在");

        $this->bill_items[]=$item;
        return true;
    }

    /**
     * 生成编号
     */
    public function generateId()
    {
        $this->item_id=\IDService::getGoodsSettlementId();
    }
}