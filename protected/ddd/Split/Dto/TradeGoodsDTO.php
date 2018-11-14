<?php

/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2018/6/8
 * Time: 15:20
 */

namespace ddd\Split\Dto;

use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\value\Quantity;
use ddd\infrastructure\DIService;
use ddd\Split\Domain\Model\IGoods;
use ddd\Split\Domain\Model\TradeGoods;
use Map;

/**
 * 商品明细DTO
 * Class TradeGoodsDTO
 * @package ddd\Split\Dto
 */
class TradeGoodsDTO extends BaseDTO{

    /**
     * 商品id
     * @var integer
     */
    public $goods_id;

    /**
     * 商品名称
     * @var string
     */
    public $goods_name;

    /**
     * 数量
     * @var float
     */
    public $quantity;

    /**
     * 单位
     * @var int
     */
    public $unit;

    /**
     * 单位名称
     * @var string
     */
    public $unit_name;


    public function rules(){
        return [
            array("goods_id", "numerical", "integerOnly" => true, "min" => 1, "tooSmall" => "商品id必须为大于0"),
            array("quantity","numerical", "min" => 1, "tooSmall" => "商品数量必须为大于0"),
        ];
    }

    /**
     * 从实体对象生成DTO对象
     * @param BaseEntity $entity
     * @return TradeGoodsDTO|void
     * @throws \Exception
     */
    public function fromEntity(BaseEntity $entity){
        $goods_entity = DIService::getRepository(IGoods::class)->findByPK($entity->goods_id);
        $this->goods_id = $entity->goods_id;
        $this->goods_name = $goods_entity->name;
        $this->quantity = $entity->quantity->quantity;
        $this->unit = $entity->quantity->unit;
        $this->unit_name = Map::getStatusName('goods_unit_enum',$this->unit);
    }

    /**
     * 转换成实体对象
     * @return TradeGoods
     * @throws \Exception
     */
    public function toEntity(){
        $entity = new TradeGoods();
        $entity->goods_id = $this->goods_id;
        $entity->quantity = new Quantity($this->quantity,$this->unit);
        return $entity;
    }
}