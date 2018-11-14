<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 14:52
 * Describe：
 */

namespace ddd\application\dto\receipt;


use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\receipt\ReceiptClaim;

class ReceiptClaimDTO extends BaseDTO
{
    public function rules()
    {
        return array();
    }

    public function customAttributeNames()
    {
        return \ReceiveConfirm::model()->attributeNames();
    }

    /**
     * 从实体对象生成DTO对象
     * @param \ddd\Common\Domain\BaseEntity $entity
     */
    public function fromEntity($entity)
    {
        $values = $entity->getAttributes();
        $this->setAttributes($values);
    }

    /**
     * 转换成实体对象
     * @return Used
     */
    public function toEntity()
    {
        $entity = ReceiptClaim::create();

        $entity->setAttributes($this->getAttributes());

        return $entity;
    }
}