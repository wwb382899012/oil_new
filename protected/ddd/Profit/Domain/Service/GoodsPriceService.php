<?php

/**
 * @Name            商品单价服务
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
use ddd\Profit\Domain\Model\Stock\IStockNoticeRepository;
use ddd\Profit\Domain\Model\Stock\IStockNoticeCostRepository;
use ddd\Profit\Domain\Model\Stock\StockNoticeCost;
use ddd\domain\entity\contractSettlement\SettlementStatus;

class GoodsPriceService extends BaseService
{
    /**
     * @name:getGoodsPrice
     * @desc: 获取商品的价格
     * @param:* @param $goods_id
     * @param $batch_id
       @throw:
     * @return: Price对象
     */
    public static function getGoodsPrice($goods_id,$batch_id){
            $stockNotice = DIService::getRepository(IStockNoticeRepository::class)->findByPk($batch_id);
            $stockNoticeCost = DIService::getRepository(IStockNoticeCostRepository::class)->findByBatchId($batch_id);
            if(empty($stockNoticeCost)){
                $stockNoticeCost = new StockNoticeCost();
                $stockNoticeCost = $stockNoticeCost->create($stockNotice);
            }

            if(!empty($stockNoticeCost->items)){
                foreach ($stockNoticeCost->items as & $value) {
                    if($value->goods_id==$goods_id){
                        if($stockNoticeCost->settle_status == SettlementStatus::STATUS_PASS) //已结算，结算价；未结算，合同价
                            return $value->settle_price;
                        else
                            return $value->contract_price;
                    }
                }
            }
    }

}
