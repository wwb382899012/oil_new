<?php

namespace ddd\application\dto\contract;

use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\contract\ContractAgentDetail;
use ddd\repository\GoodsRepository;

/*
 * Created By: yu.li
 * DateTime:2018-5-10 15:55:38.
 * Desc:代理费明细
 */

class ContractAgentDetailDTO extends BaseDTO
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
     * 计费方式
     * @var   int
     */
    public $type;

    /**
     * 计费单价
     * @var   float
     */
    public $price;

    /**
     * 计费单位
     * @var   int
     */
    public $unit;

    /**
     * 代理手续费率
     * @var   float
     */
    public $fee_rate;

    /**
     * 代理手续费
     * @var   float
     */
    public $amount;

    #endregion

    /**
     * 实体对象生成DTO对象
     */
    public function fromEntity(BaseEntity $entity) {
        $this->setAttributes($entity->getAttributes());
        $goods = GoodsRepository::repository()->findByPk($entity->goods_id);
        $this->goods_name = $goods->name;
    }

    /**
     * DTO对象转实体对象
     */
    public function toEntity() {
        $entity = ContractAgentDetail::create();
        $entity->setAttributes($this->getAttributes());
        return $entity;
    }

}
