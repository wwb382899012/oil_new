<?php

namespace ddd\Split\Dto\ContractSplit;

use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\Split\Domain\Model\Stock\StockBill;
use ddd\Split\Domain\Model\Stock\StockIn;
use ddd\Split\Dto\TradeGoodsDTO;
use Map;

/**
 * 出入库实体DTO,挂载到StockSplitDetailDTO下面
 * Class StockBillDTO
 * @package ddd\Split\Dto
 */
class StockBillDTO extends BaseDTO{

    /**
     * 出入库id
     * @var big integer
     */
    public $bill_id;

    /**
     * 出入库单号
     * @var string
     */
    public $bill_code;

    /**
     * 状态
     * @var integer
     */
    public $status;

    /**
     * 是否虚拟单
     * @var bool
     */
    public $is_virtual = true;

    /**
     * 是否可拆分
     * @var bool
     */
    public $is_can_split = false;

    /**
     * 是否勾选拆分
     * @var bool
     */
    public $is_split = true;

    /**
     * 商品明细
     * @var array TradeGoodsDTO
     */
    public $goods_items = [];

    public function rules(){
        return [];
    }

    /**
     * 从实体对象生成DTO对象
     * @param BaseEntity $entity
     * @throws \CException
     */
    public function fromEntity(BaseEntity $entity){
        $this->setAttributes($entity->getAttributes());

        $this->goods_items = [];
        foreach($entity->items as & $goods_item){
            $tradeGoodsDto = new TradeGoodsDTO();
            $tradeGoodsDto->fromEntity($goods_item);
            $this->goods_items[] = $tradeGoodsDto;
        }

        $this->is_can_split = $entity->isCanSplit();
        $this->is_split = $this->is_can_split;
    }

    /**
     * 转换成实体对象
     * @return Contract
     */
    public function toEntity(){
        return null;
    }
}