<?php

namespace ddd\application\dto\contract;

use DateTime;
use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\contract\ContractPayPlan;

/**
 * @Name            收付款明细DTO
 * @DateTime        2018年5月8日 15:05:43
 * @Author          Administrator
 */
class ContractPayPlanDTO extends BaseDTO
{
#region property

    /**
     * 收付款日期 
     * @var   date
     */
    public $plan_pay_date;

    /**
     * 收付款类别 
     * @var   varchar
     */
    public $expense_type;

    /**
     * 币种 
     * @var   varchar
     */
    public $currency;

    /**
     * 金额 
     * @var   float
     */
    public $amount;

    /**
     * 备注 
     * @var   varchar
     */
    public $remark;

    #endregion

    public function rules() {
        $rules = [
                ['expense_type', 'required', 'message' => '收付款类别不能为空'],
                ['currency', 'required', 'message' => '币种不得为空'],
                ['amount', "numerical", "min" => 0, "tooSmall" => "金额必须为大于0的数值"],
        ];
        return $rules;
    }

    /**
     * 实体对象生成DTO对象
     */
    public function fromEntity(BaseEntity $entity) {
        $values = $entity->getAttributes();
        $this->setAttributes($values);
    }

    /**
     * DTO对象转实体对象
     */
    public function toEntity() {
        $entity = new ContractPayPlan();
        $entity->setAttributes($this->getAttributes());
    }

}
