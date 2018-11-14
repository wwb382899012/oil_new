<?php


namespace ddd\Split\Dto\StockSplit;

use ddd\Common\Application\BaseDTO;

/**
 * 提交、保存使用
 * Class StockSplitApplyDetailDTO
 * @package ddd\Split\Dto\StockSplit
 */
class StockSplitApplyDetailDTO extends BaseDTO{

    public $contract_id = 0;

    public $contract_code = '';

    public $goods_items = [];

    public function rules(){
        return [];
    }

    public function setGoodsItems(array $goods_items = []){
        $this->goods_items = $goods_items;
    }

    public function addGoodsItems(GoodsItemsDTO $dto){
        $this->goods_items[] = $dto;
    }

}