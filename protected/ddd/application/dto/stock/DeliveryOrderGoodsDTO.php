<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 10:58
 * Describe：
 */

namespace ddd\application\dto\stock;


use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\stock\DeliveryOrderGoods;
use ddd\repository\GoodsRepository;

class DeliveryOrderGoodsDTO extends BaseDTO
{
    public $detail_id;
    /**
    * @var      int
    */
    public $goods_id;
    
    /**
    * @var      varchar
    */
    public $goods_name;
    
    
    /**
    * @var      float   发货数量
    */
    public $quantity;
    
    /**
    * @var      varchar
    */
    //public $stock_in_id;
    
    /**
    * @var      int 配货数量
    */
    //public $stock_delivery_quantity;
    
    /**
    * @var      varchar 仓库名称
    */
    //public $store_name;
    
    /**
    * @var      int   总出库数量
    */
    //public $out_quantity;
    
    /**
    * @var      int  未出库数量
    */
    //public $no_out_quantity;
    
    /**
    * @var      int
    */
    //public $remark;
    /**
     * @var      array  配货单列表
     */
    public $delivery_items;

    public function init()
    {

    }


    public function fromEntity(BaseEntity $deliveryOrderGoods)
    {
        $values=$deliveryOrderGoods->getAttributes();
        $this->setAttributes($values);
        $goods = GoodsRepository::repository()->findByPk($deliveryOrderGoods->goods_id);
        $this->goods_name = $goods->name;
        
        //print_r($values);
       
    }

    public function toEntity()
    {
        $entity = DeliveryOrderGoods::create();
        $entity->goods_id = $this->goods_id;
        //$entity->out_quantity = $this->out_quantity;
        $entity->quantity = $this->quantity;
        //$entity->remark = $this->remark;
        return $entity;
    }




}