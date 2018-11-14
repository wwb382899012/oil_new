<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/5/29 0029
 * Time: 10:13
 */

namespace ddd\Split\Domain\Model;


use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\value\Quantity;

class TradeGoods extends BaseEntity
{

    /**
     * 商品
     * @var   int
     */
    public $goods_id;

    /**
     * 数量
     * @var   Quantity
     */
    public $quantity;

    /**
     * 创建对象
     * @param    int $goodsId
     * @return   TradeGoods
     * @throws   \Exception
     */
    public static function create($goodsId = 0)
    {
        return new static(array("goods_id" => $goodsId));
    }
}

?>