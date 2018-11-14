<?php
/**
 * User: liyu
 * Date: 2018/6/14
 * Time: 13:14
 * Desc: ContractAgentDetail.php
 */

namespace ddd\domain\entity\contract;


use ddd\Common\Domain\BaseEntity;

class ContractAgentDetail extends BaseEntity
{
    public $goods_id;

    public $type;

    public $price;

    public $unit;

    public $fee_rate;

    public $amount;

    public static function create(){
        return new static();
    }
}