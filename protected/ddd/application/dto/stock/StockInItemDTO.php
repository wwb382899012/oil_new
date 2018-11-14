<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 14:54
 * Describe：
 */

namespace ddd\application\dto\stock;


use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\stock\LadingBillGoods;
use ddd\domain\entity\stock\StockInItem;
use ddd\repository\GoodsRepository;


class StockInItemDTO extends BaseDTO
{
    public $goods_id;           //商品id
    public $goods_name;         //商品名称
    public $quantity;           //入库单数量
    public $quantity_sub;           //入库单数量 子单位
    public $unit;           //入库单数量
    public $unit_rate;          //换算比例
    public $remark;             //备注


   /*  public function customAttributeNames()
    {
        return \StockInDetail::model()->attributeNames();
    } */

    /**
     * 从实体对象生成DTO对象
     * @param BaseEntity $entity
     */
    public function fromEntity(BaseEntity $entity)
    {
        $values = $entity->getAttributes();
        $this->setAttributes($values);
        $this->unit=$entity->quantity->unit;
        $this->quantity=$entity->quantity; 
        $goods =  GoodsRepository::repository()->findByPk($values['goods_id']);
        $this->goods_name=$goods->name;
    }

    /**
     * 转换成实体对象
     * @return LadingBillGoods
     */
    public function toEntity()
    {
        $entity = StockInItem::create();
        $entity->setAttributes($this->getAttributes());

        return $entity;
    }
}