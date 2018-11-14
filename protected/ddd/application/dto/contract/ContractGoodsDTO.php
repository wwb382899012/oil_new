<?php

/**
 * Created by youyi000.
 * DateTime: 2018/3/1 14:54
 * Describe：
 */

namespace ddd\application\dto\contract;

use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\contract\ContractGoods;
use ddd\repository\GoodsRepository;

class ContractGoodsDTO extends BaseDTO
{
    #region property

    /**
     * 商品ID 
     * @var   int
     */
    public $goods_id;

    /**
     * 商品名称 
     * @var   varchar
     */
    public $goods_name;

    /**
     * 计价标的 
     * @var   int
     */
    public $refer_target;

    /**
     * 溢短装比 
     * @var   float
     */
    public $more_or_less_rate;

    /**
     * 数量 
     * @var   int
     */
    public $quantity;

    /**
     * 单位 
     * @var   int
     */
    public $unit;

    /**
     * 单价 
     * @var   float
     */
    public $price;

    /**
     * 总价 
     * @var   float
     */
    public $amount;

    /**
     * 人民币总金额 
     * @var   float
     */
    public $amount_cny;

    #endregion

    public function rules() {
        $rules = [
                ["goods_id", "numerical", "integerOnly" => true, "min" => 0, "message" => "商品id必须为大于0的整数"],
                ['quantity', "numerical", "min" => 0, "tooSmall" => "数量必须为大于0的数值"],
                ['unit', 'required', 'message' => '单位不得为空'],
                ['price', "numerical", "min" => 0, "tooSmall" => "单价必须为大于0的数值"],
        ];
        return $rules;
    }

    /**
     * 实体对象生成DTO对象
     */
    public function fromEntity(BaseEntity $entity) {
        $values = $entity->getAttributes();
        $this->setAttributes($values);
        $goods = GoodsRepository::repository()->findByPk($entity->goods_id);
        $this->unit = $entity->quantity->unit;
        $this->goods_name = $goods->name;
    }

    /**
     * DTO对象转实体对象
     */
    public function toEntity() {
        $entity = ContractGoods::create();
        $entity->setAttributes($this->getAttributes());
    }

}
