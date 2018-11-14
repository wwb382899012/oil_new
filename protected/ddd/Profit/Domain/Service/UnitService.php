<?php

/**
 * @Name            单位转换服务
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
use ddd\domain\entity\value\Quantity;
use ddd\domain\entity\contract\Contract;

class UnitService extends BaseService
{
    /**
     * @name:settleUnitTon
     * @desc: 结算单位转为吨
     * @param:quantity 数量对象，goods_id 商品id，contract 合同对象
     * @throw:
     * @return: Quantity
     */
    static public function settleUnitTon(Quantity $quantity,$goods_id,Contract $contract){
        $contractGoods  = $contract->goods_items;
        if(!empty($contractGoods)) {
            foreach ($contractGoods as $key => $value) {
                if($value['goods_id']==$goods_id){
                    if($quantity->unit == ConstantMap::UNIT_TON)
                        return $quantity;
                    else{
                        $quantity_ton  = ($quantity->quantity)/$value['unit_convert_rate'];
                        return new Quantity($quantity_ton,ConstantMap::UNIT_TON);
                    }

                }
            }
        }
    }

    /*public function convertToQuantityByT($contractId,$goodsId,\ddd\Common\Domain\Value\Quantity $quantity)
    {

    }*/

}
