<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/12 16:04
 * Describe：
 */

namespace ddd\domain\entity\contract;


use ddd\domain\entity\base\BaseContractGoods;
use ddd\Contract\Domain\Model\Project\ProjectGoods;

class ContractGoods extends BaseContractGoods
{

    /**
     * @var 合同ID
     */
    public $contract_id;

    /**
     * @var 项目ID
     */
    public $project_id;

    public $type;

    public $unit_store;

    public $unit_price;

    /**
     * @var 单位换算比
     */
    public $unit_convert_rate;

    /**
     * @var 人名币金额
     */
    public $amount_cny;
    /**
     * 由项目交易商品创建合同交易商品信息
     * @param \ddd\Contract\Domain\Model\Project\ProjectGoods|null $goods
     * @return ContractGoods
     * @throws \Exception
     */
    public static function create(ProjectGoods $goods = null) {
        $params = [];
        if (!empty($goods))
            $params = $goods->getAttributes();
        return new static($params);
    }
}