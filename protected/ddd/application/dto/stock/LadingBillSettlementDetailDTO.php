<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 10:58
 * Describe：
 */

namespace ddd\application\dto\stock;


use ddd\application\UnitService;
use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\contractSettlement\LadingBillSettlementDetail;
use ddd\domain\entity\value\Quantity;
use ddd\repository\GoodsRepository;

class LadingBillSettlementDetailDTO extends BaseDTO
{
    public $settle_id;          //结算单id
    public $batch_id;           //入库通知单id
    public $settle_date;        //结算日期
    public $goods_id;           //结算商品id
    public $goods_name;         //结算商品名称
    public $settle_quantity;           //结算数量
    public $settle_quantity_sub;           //结算数量
    public $loss_quantity;      //损耗量
    public $loss_quantity_sub;      //损耗量
    public $settle_price;              //结算单价
    public $settle_amount;             //结算金额

    public $in_quantity;  //入库单数量
    public $in_quantity_sub;  //入库单数量
    public $price_cny;          //人民币结算单价
    public $settle_amount_cny;         //人民币结算金额
    public $unit_rate;          //结算汇率
    public $status;             //状态
    public $unit;           //单位
    public $unit_sub;           //单位
    public $unit_name;       //单位名
    public $unit_sub_name;       //单位名

    public function fromEntity(BaseEntity $ladingBillSettlementDetail)
    {
        $values = $ladingBillSettlementDetail->getAttributes();
        $this->setAttributes($values);

        $goods = GoodsRepository::repository()->findByPk($ladingBillSettlementDetail->goods_id);
        $this->goods_name = $goods->name;
        $this->unit_name = UnitService::getName($this->unit);
        $this->unit_sub_name = UnitService::getName($this->unit_sub);
    }

    public function toEntity()
    {
        $entity = LadingBillSettlementDetail::create();
        $values = $this->getAttributes();
        $entity->setAttributes($values);

        $entity->settle_quantity = new Quantity($values['settle_quantity'], $values['unit']);
        $entity->settle_quantity_sub = new Quantity($values['settle_quantity_sub'], $values['unit_sub']);
        $entity->loss_quantity = new Quantity($values['loss_quantity'], $values['unit']);
        $entity->loss_quantity_sub = new Quantity($values['loss_quantity_sub'], $values['unit_sub']);

        return $entity;
    }
}