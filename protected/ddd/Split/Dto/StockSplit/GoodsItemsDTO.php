<?php


namespace ddd\Split\Dto\StockSplit;

use ddd\Common\Application\BaseDTO;

/**
 * 提交、保存使用
 * Class GoodsItemsDTO
 * @package ddd\Split\Dto\StockSplit
 */
class GoodsItemsDTO extends BaseDTO{

    public $goods_id = 0;

    public $goods_name = '';

    public $quantity = 0;

    public $unit = 0;

    public function rules(){
        return [];
    }

}