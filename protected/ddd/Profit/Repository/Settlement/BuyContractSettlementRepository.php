<?php
/**
 * Desc:采购合同结算仓储
 * User: wwb
 * Date: 2018/5/31 0031
 * Time: 17:39
 */

namespace ddd\Profit\Repository\Settlement;


use ddd\Common\Domain\Currency\Currency;
use ddd\Common\Domain\Currency\CurrencyService;
use ddd\Common\Domain\Value\Money;
use ddd\Common\Domain\Value\Quantity;
use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\domain\entity\value\Price;

use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\infrastructure\error\ZModelDeleteFalseException;
use ddd\infrastructure\error\ZModelNotExistsException;
use ddd\infrastructure\error\ZModelSaveFalseException;
use ddd\infrastructure\Utility;
use ddd\Profit\Domain\Model\Settlement\BuyContractSettlement;
use ddd\Profit\Domain\Model\Settlement\DeliveryOrderSettlement;
use ddd\Profit\Domain\Model\Settlement\IDeliveryOrderSettlementRepository;
use ddd\Profit\Domain\Model\Settlement\ILadingBillSettlementRepository;
use ddd\Profit\Domain\Model\Settlement\LadingBillSettlement;
use ddd\Profit\Domain\Model\Settlement\SettlementItem;
use ddd\Profit\Domain\Model\Settlement\IBuyContractSettlementRepository;


class BuyContractSettlementRepository extends EntityRepository implements IBuyContractSettlementRepository
{

    public function getNewEntity()
    {
        return new BuyContractSettlement();
    }


    public function init()
    {

    }

    public function getActiveRecordClassName()
    {
        return 'ContractSettlement';
    }

    public function dataToEntity($model)
    {
        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes(), false);
        $entity->contract_id = $model->contract_id;
        $entity->project_id = $model->project_id;
        $entity->settle_currency = $model->currency== CurrencyService::CNY ?  Currency::createCNY() : Currency::createUSD();


        if(!empty($model->contractSettlementGoods)){
            foreach($model->contractSettlementGoods as &$value){
                $item = new SettlementItem();
                $item->goods_id  = $value->goods_id;
                $item->exchange_rate = $value->exchange_rate;
                $item->price = new Money($value->price,$model->currency);
                $item->price_cny = new Money($value->price_cny,$model->currency);


                $entity->addSettleItem($item);
            }
        }
        return $entity;
    }



    /**
     * 把对象持久化到数据
     * @param IAggregateRoot $entity
     * @return int
     * @throws \Exception
     */
    public function store(IAggregateRoot $entity)
    {

    }

    /**
     * 根据采购合同
     * @param $contractId
     * @return ContractSettlement
     */
    function findByContractId($contractId)
    {
        return $this->find('t.contract_id=' . $contractId);
    }


}