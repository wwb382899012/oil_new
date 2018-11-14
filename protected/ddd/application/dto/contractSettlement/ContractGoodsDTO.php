<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 14:54
 * Describe：
 */

namespace ddd\application\dto\contractSettlement;


use ddd\Common\Application\BaseDTO;
use ddd\domain\entity\contract\TradeGoods;
use ddd\domain\entity\contract\ContractGoods;
use ddd\Common\Domain\BaseEntity;
use ddd\repository\GoodsRepository;

class ContractGoodsDTO extends BaseDTO
{
    
    /**
     * @var      int 商品id
     */
    public $goods_id;
    
    /**
     * @var      string 商品名称
     */
    public $goods_name;
    /**
     * @var      string  计价标的
     */
    public $refer_target;
    /**
     * @var      object  数量 {quantity:,unit:}
     */
    public $quantity;

    /**
     * @var      float  单价
     */
    public $price;
    
    /**
     * @var      float  总价
     */
    public $amount;
    
    /**
     * @var      float  采购溢短装比例
     */
    public $more_or_less_rate;
    


    /**
     * 从实体对象生成DTO对象
     * @param BaseEntity $entity
     */
    public function fromEntity(BaseEntity $entity)
    {
        $values=$entity->getAttributes();
        $this->setAttributes($values);
        $goods = GoodsRepository::repository()->findByPk($entity->goods_id);
        $this->goods_name = $goods->name;
    }

    /**
     * 转换成实体对象
     * @return TradeGoods
     */
    public function toEntity()
    {
        $entity=ContractGoods::create();
        $entity->setAttributes($this->getAttributes());
        return $entity;
    }
}