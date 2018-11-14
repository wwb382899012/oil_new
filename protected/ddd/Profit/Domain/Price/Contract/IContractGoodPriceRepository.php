<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/13 16:12
 * Describe：
 */

namespace ddd\Profit\Domain\Price\Contract;


use ddd\Common\Domain\IRepository;

interface IContractGoodPriceRepository extends IRepository
{
    /**
     * 根据合同id和商品id查找对象
     * @param $contractId
     * @param $goodsId
     * @return ContractGoodsPrice|null
     */
    public function findByContractIdAndGoodsId($contractId,$goodsId);
}