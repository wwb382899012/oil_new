<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 14:54
 * Describe：
 */

namespace ddd\application\dto\contract;


use ddd\Common\Application\BaseDTO;
use ddd\domain\entity\contract\TradeGoods;
use ddd\Common\Domain\BaseEntity;

class TradeGoodsDTO extends BaseDTO
{
    public function customAttributeNames()
    {
        return \ContractGoods::model()->attributeNames();
    }


    /**
     * 从实体对象生成DTO对象
     * @param BaseEntity $entity
     * @throws \Exception
     */
    public function fromEntity(BaseEntity $entity)
    {
        $values=$entity->getAttributes();
        $this->setAttributes($values);
    }

    /**
     * 转换成实体对象
     * @return TradeGoods
     * @throws \Exception
     */
    public function toEntity()
    {
        $entity=TradeGoods::create();
        $entity->setAttributes($this->getAttributes());
        return $entity;
    }
}