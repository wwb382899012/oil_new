<?php

/**
 * @Name            第三方价格服务
 * @DateTime        2018年5月30日 星期三 19:33:22
 * @Author          wwb
 */

namespace ddd\Profit\Domain\Service;

use CodeService;
use ConstantMap;
use ContractService;
use ddd\Common\Domain\BaseService;
use ddd\domain\entity\contract\ContractGoods;
use ddd\domain\enum\MainEnum;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\error\ZModelSaveFalseException;
use ddd\Profit\Repository\Contract\ContractRepository;
use ddd\Split\Domain\Model\Contract\BuyContract;
use ddd\Split\Domain\Model\Contract\ContractEnum;
use ddd\Split\Domain\Model\Contract\IContractRepository;
use ddd\Split\Domain\Model\Contract\SellContract;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApply;
use ddd\domain\entity\contract\Contract;
use ddd\Split\Domain\Model\Stock\StockIn;
use ddd\Split\Domain\Model\Stock\StockOut;
use ddd\Split\Domain\Model\StockSplit\StockSplitEnum;
use DeliveryOrder;
use IDService;
use StockInService;
use StockNotice;
use StockNoticeService;

class ThirdPriceService extends BaseService
{
    /**
     * @name:getGoodsPrice
     * @desc:  获取合同商品价格
     * @param:* @param $contract_id
     * @param $goods_id
       @throw:
     * @return:int
     */
    public static function getGoodsPrice($contract_id,$goods_id){
        //保留方法，逻辑可能会变
        $contract = ContractRepository::repository()->findByContractId($contract_id);
        $goods_items = $contract->getGoodsItems();
        if(!empty($goods_items)){
            foreach($goods_items as & $value){
                if($value->goods_id == $goods_id){
                    return $value->price->amount;
                }

            }
        }

    }

}
