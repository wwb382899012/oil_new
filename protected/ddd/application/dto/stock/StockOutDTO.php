<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 10:32
 * Describe：
 */

namespace ddd\application\dto\stock;

use ddd\application\dto\AttachmentDTO;
use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\stock\StockOut;

class StockOutDTO extends BaseDTO
{
    public $items = array();
    public $files;
    public $store_name;

    public function rules()
    {
        return array();
    }

    public function customAttributeNames()
    {
        return \StockOutOrder::model()->attributeNames();
    }

    /**
     * 从实体对象生成DTO对象
     * @param BaseEntity $stockOut
     */
    public function fromEntity(BaseEntity $stockOut)
    {
        $values = $stockOut->getAttributes();
        unset($values['files']);
        unset($values['items']);
        $this->setAttributes($values);
        $this->store_name = \StorehouseService::getStoreName($stockOut->store_id);

        if (is_array($stockOut->items))
        {
            foreach ($stockOut->items as $k => $v)
            {
                $item = new StockOutItemDTO();
                $item->fromEntity($v);
                $this->items[] = $item;
            }
        }
        if (is_array($stockOut->files))
        {
            foreach ($stockOut->files as $k => $v)
            {
                $files = new AttachmentDTO();
                $files->fromEntity($v);
                $this->files[] = $files;
            }
        }
    }

    /**
     * 转换成实体对象
     * @params Contract $contractEntity
     * @return LadingBill
     */
    public function toEntity()
    {
        $entity = new StockOut();

        $entity->setAttributes($this->getAttributes());
        $entity->items = array();
        if (is_array($this->items))
        {
            foreach ($this->items as $k => $v)
            {
                $entity->items[$v->goods_id] = $v->toEntity();
            }
        }

        return $entity;
    }

    public function validateLadingDate($attribute, $params)
    {
        $t = strtotime($this->$attribute);
        $createTime = isset($this->create_time) ? strtotime($this->create_time) : strtotime("now");
        $before = isset($params["before"]) ? $params["before"] : 7;
        if ($t < strtotime("- " . $before . " days", $createTime))
        {
            $this->addError($attribute, '日期不得小于创建日期的前' . $before . '天');
        }

        if (isset($params["after"]))
        {
            if ($t > strtotime("+ " . $params["after"] . " days", $createTime))
            {
                $this->addError($attribute, '日期不得晚于创建日期的后' . $params["after"] . '天');
            }
        }

    }
}