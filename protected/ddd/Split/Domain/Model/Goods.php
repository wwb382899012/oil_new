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

class Goods extends BaseEntity{

    /**
     * 商品ID
     * @var   int
     */
    public $goods_id;

    public $name;

    public $unit;

}