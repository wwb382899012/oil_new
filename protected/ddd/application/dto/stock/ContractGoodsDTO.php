<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 15:48
 * Describe：
 *  入库通知单的可选商品
 */

namespace ddd\application\dto\stock;


use ddd\application\UnitService;
use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\contract\ContractGoods;
use ddd\domain\entity\contract\TradeGoods;
use ddd\repository\GoodsRepository;

class ContractGoodsDTO extends BaseDTO
{
    /**
     * @var      int
     */
    public $goods_id=0;

    public $name;

    /**
     * @var      float
     */
    public $quantity=0;

    public $unit=2;
    public $unitName;

    /**
     * @var      float
     */
    public $quantity_sub;
    public $unit_sub=1;
    public $unit_subName;

    public function init()
    {

    }

    public function fromEntity(BaseEntity $contractGoods)
    {
        $values=$contractGoods->getAttributes();
        $this->setAttributes($values);
        /*$this->goods_id=$contractGoods->goods_id;
        $this->quantity=$contractGoods->quantity;
        $this->unit=$contractGoods->unit;*/

        $this->quantity_sub=$contractGoods->quantity;
        $this->unit_sub=$contractGoods->unit_store;

        $goods=GoodsRepository::repository()->findByPk($contractGoods->goods_id);
        $this->name=$goods->name;
        $this->unitName=UnitService::getName($this->unit);
        $this->unit_subName=UnitService::getName($this->unit_sub);
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