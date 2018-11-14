<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 10:58
 * Describe：
 */

namespace ddd\application\dto\stock;


use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\stock\LadingBillGoods;
use ddd\domain\entity\value\Quantity;
use ddd\repository\GoodsRepository;

class LadingBillGoodsDTO extends BaseDTO
{
    /**
     * @var      int
     */
    public $goods_id=0;

    public $goodsName;

    /**
     * @var      float
     */
    public $quantity;//发货单数量
    public $quantity_sub;//发货单数量  子单位
    
    public $in_quantity;//总入库数量
    public $in_quantity_sub;//总入库数量  子单位
    
    public $quantity_not;//未入库数量
    public $quantity_not_sub;//未入库数量 子单位
    /**
     * @var      float
     */
   
    public $remark;
    public $store_name;//仓库名称
    public $unit_rate;

    public function init()
    {

    }


    public function fromEntity(BaseEntity $ladingBillGoods)
    {
        $values=$ladingBillGoods->getAttributes();
        $this->setAttributes($values);
        $this->goods_id=$ladingBillGoods->goods_id;
        $this->quantity=$ladingBillGoods->quantity;
        $this->quantity_sub=$ladingBillGoods->quantitySub;
        $this->in_quantity=$ladingBillGoods->in_quantity;
        $this->in_quantity_sub=$ladingBillGoods->in_quantity_sub;
        
        $quantity_not=($ladingBillGoods->quantity->quantity)-($ladingBillGoods->in_quantity->quantity);
        $this->quantity_not= new Quantity($quantity_not,$ladingBillGoods->quantity->unit);
        $quantity_not_sub=($ladingBillGoods->quantitySub->quantity) - ($ladingBillGoods->in_quantity_sub->quantity);
        $this->quantity_not_sub= new Quantity($quantity_not_sub,$ladingBillGoods->quantitySub->unit);
        
        $this->store_name = \StorehouseService::getStoreName($ladingBillGoods->store_id);
        $goods=GoodsRepository::repository()->findByPk($ladingBillGoods->goods_id);
        $this->goodsName=$goods->name;
    }

    public function toEntity()
    {
        $entity = LadingBillGoods::create();
        $entity->goods_id = $this->goods_id;
        $entity->quantity = $this->quantity;
        $entity->quantitySub = $this->quantity_sub;
        $entity->in_quantity = $this->in_quantity;
        $entity->in_quantity_sub = $this->in_quantity_sub;
        //$entity->quantity_not = $this->quantity_not;
        //$entity->quantity_not_sub = $this->quantity_not_sub;
        
        return $entity;
    }




}