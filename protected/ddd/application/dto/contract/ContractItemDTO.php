<?php

namespace ddd\application\dto\contract;

use DateTime;
use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\contract\ContractItem;
use ddd\domain\entity\contract\ContractType;

/**
 * @Name            合同条款DTO
 * @DateTime        2018年5月8日 15:05:43
 * @Author          Administrator
 */
class ContractItemDTO extends BaseDTO
{

    public static $BUY_CONTRACT_ITEM_KEY_REQUIRED = ['delivery_period', 'delivery_type', 'pay_type'];
    public static $SELL_CONTRACT_ITEM_KEY_REQUIRED = ['delivery_period', 'delivery_type'];
    #region property

    /**
     * 条款key 
     * @var   int
     */
    public $key;

    /**
     * 条款名称 
     * @var   varchar
     */
    public $name;

    /**
     * 条款内容 
     * @var   varchar
     */
    public $content;

    /**
     * 值类型 
     * @var   int
     */
    public $content_type;

    /**
     * 合同类型 
     * @var   int
     */
    public $contract_type;

    #endregion

    public function rules() {
        return [
                ["key", "validKey"],
        ];
    }

    /**
     * 实体对象生成DTO对象
     */
    public function fromEntity($entity) {
        $values = $entity->getAttributes();
        $this->setAttribues($values);
    }

    /**
     * DTO对象转实体对象
     */
    public function toEntity() {
        $entity = new ContractItem();
        $entity->setAttributes($this->getAttributes());
    }

    //验证key必填项
    public function validKey($attribute, $params) {
        if ($this->contract_type == ContractType::BUY_CONTRACT && in_array($this->$attribute, self::$BUY_CONTRACT_ITEM_KEY_REQUIRED) && empty($this->content)) {
            $this->addError($attribute, $this->name . '不能为空');
        }
        if ($this->contract_type == ContractType::SELL_CONTRACT && in_array($this->$attribute, self::$SELL_CONTRACT_ITEM_KEY_REQUIRED) && empty($this->content)) {
            $this->addError($attribute, $this->name . '不能为空');
        }
    }

}
