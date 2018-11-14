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
use ddd\domain\entity\stock\StockOutItem;
use ddd\repository\GoodsRepository;

class StockOutItemDTO extends BaseDTO
{
    
    /**
     * @var      int
     */
    public $goods_id;
    
    /**
     * @var      varchar
     */
    public $goods_name;
    
    /**
     * @var      int  发货数量
     */
    public $quantity;
    /**
     * @var      int  配货数量
     */
    public $delivery_quantity;
    
    /**
     * @var      string
     */
    public $stock_in_code;
    
    /**
     * @var      text
     */
    public $remark;
    
    
    /* public function customAttributeNames()
    {
        return \StockOutDetail::model()->attributeNames();
    } */

    /**
     * 从实体对象生成DTO对象
     * @param BaseEntity $entity
     */
    public function fromEntity(BaseEntity $entity)
    {
        $values = $entity->getAttributes();
        $this->setAttributes($values);
        $goods = GoodsRepository::repository()->findByPk($entity->goods_id);
        $this->goods_name = $goods->name;
       
    }

    /**
     * 转换成实体对象
     * @return LadingBillGoods
     */
    public function toEntity()
    {
        $entity = StockOutItem::create();
        $entity->setAttributes($this->getAttributes());

        return $entity;
    }
}