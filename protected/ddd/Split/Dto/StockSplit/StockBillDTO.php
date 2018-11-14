<?php

/**
 *
 */
namespace ddd\Split\Dto\StockSplit;

use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\Split\Domain\Model\Stock\StockIn;
use ddd\Split\Dto\TradeGoodsDTO;
use Map;

/**
 * 出入库实体DTO
 * Class StockBillDTO
 * @package ddd\Split\Dto
 */
class StockBillDTO extends BaseDTO{

    /**
     * 申请标识
     * @var
     */
    public $apply_id = 0;

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
     * @var
     */
    public $status;

    /**
     * 状态别名
     * @var string
     */
    public $status_name;

    /**
     * 是否虚拟单
     * @var bool
     */
    public $is_virtual = true;

    /**
     * 是否勾选平移
     * @var bool
     */
    public $is_split = false;

    /**
     * 是否可拆分
     * @var bool
     */
    public $is_can_split = false;

    /**
     * 是否可查看详情
     * @var bool
     */
    public $is_can_view = false;

    /**
     * 是否可以提交
     * @var bool
     */
    public $is_can_submit = false;

    /**
     * @var bool
     */
    public $is_can_save = false;

    /**
     * 是否可以审核
     * @var bool
     */
    public $is_can_check = false;

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
     * @throws \Exception
     */
    public function fromEntity(BaseEntity $entity){
        $this->setAttributes($entity->getAttributes());

        if($entity instanceof StockIn){
            $this->status_name = Map::getStatusName('stock_bill_split_status',$entity->status);
        }else{
            $this->status_name = Map::getStatusName('stock_bill_split_status',$entity->status);
        }

        $this->goods_items = [];
        foreach($entity->items as & $goods_item){
            $tradeGoodsDto = new TradeGoodsDTO();
            $tradeGoodsDto->fromEntity($goods_item);
            $this->goods_items[] = $tradeGoodsDto;
        }

        $this->is_can_split = $entity->isCanSplit();
    }

    /**
     * 转换成实体对象
     * @return Contract
     */
    public function toEntity(){
        //nobody
    }
}