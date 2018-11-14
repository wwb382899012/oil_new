<?php
/**
 * Desc:发货单利润仓储
 * User: wwb
 * Date: 2018/5/31 0031
 * Time: 17:39
 */

namespace ddd\Profit\Repository\Profit;


use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\infrastructure\error\ZModelDeleteFalseException;
use ddd\infrastructure\error\ZModelNotExistsException;
use ddd\infrastructure\error\ZModelSaveFalseException;
use ddd\infrastructure\Utility;
use ddd\Profit\Domain\Model\Stock\IDeliveryOrderRepository;
use ddd\Profit\Domain\Model\Profit\IDeliveryOrderProfitRepository;
use ddd\Profit\Domain\Model\Profit\DeliveryOrderProfit;
use ddd\Profit\Domain\Model\Profit\SellContractProfit;

use ddd\domain\iRepository\contract\IContractRepository;
use ddd\Profit\Domain\Model\Profit\ISellContractProfitRepository;
use ddd\domain\entity\value\Price;
use ddd\domain\entity\value\Quantity;
use ddd\Profit\Domain\Model\Profit\BuyCost;
use ddd\Profit\Domain\Model\Profit\SellProfit;

class SellContractProfitRepository extends EntityRepository implements ISellContractProfitRepository
{

    public function getNewEntity()
    {
        return new SellContractProfit();
    }


    public function init()
    {

    }

    public function getActiveRecordClassName()
    {
        return 'ContractProfit';
    }

    public function dataToEntity($model)
    {
        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes(), false);
        $entity->contract_type=$model->contract->type;
        $sellProfit = new  SellProfit();
        $sell_price = $model->settle_quantity==0?$model->settle_amount:$model->settle_amount/$model->settle_quantity;
        $sellProfit->sell_price = new Price($sell_price,\ConstantMap::CURRENCY_RMB);
        $sellProfit->settle_amount = new Price($model->settle_amount,\ConstantMap::CURRENCY_RMB);
        $sellProfit->settle_quantity = new Quantity($model->settle_quantity,\ConstantMap::UNIT_TON);
        $entity->sell_profit = $sellProfit;

        $buyCost = new  BuyCost();
        $buyCost->buy_price = new Price($model->buy_price,\ConstantMap::CURRENCY_RMB);
        $buyCost->buy_amount = new Price($model->buy_amount,\ConstantMap::CURRENCY_RMB);
        $out_quantity = $model->buy_price==0?0:$model->buy_amount/$model->buy_price;
        $buyCost->out_quantity = new Quantity($out_quantity,\ConstantMap::UNIT_TON);
        $entity->buy_cost = $buyCost;

        //
        $entity->actual_gross_profit = $this->format_price($model->actual_gross_profit);
        $entity->freight = $this->format_price($model->freight);
        $entity->warehouse_fee = $this->format_price($model->warehouse_fee);
        $entity->miscellaneous_fee = $this->format_price($model->miscellaneous_fee);
        $entity->vat = $this->format_price($model->vat);
        $entity->sur_tax = $this->format_price($model->sur_tax);
        $entity->stamp_tax = $this->format_price($model->stamp_tax);
        $entity->after_tax_profit =$this->format_price($model->after_tax_profit);
        $entity->fund_cost = $this->format_price($model->fund_cost);
        $entity->profit = $this->format_price($model->profit);
        $entity->sell_invoice_amount = $this->format_price($model->sell_invoice_amount);
        $entity->buy_invoice_amount = $this->format_price($model->buy_invoice_amount);
        $entity->pay_amount = $this->format_price($model->pay_amount);
        $entity->receive_amount = $this->format_price($model->receive_amount);
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
        //合同利润 持久化
        $model = $this->model()->with($this->with)->find('t.contract_id='.$entity->contract_id);

        if(empty($model)){
            $this->activeRecordClassName = $this->getActiveRecordClassName();
            $model = new $this->activeRecordClassName;
        }
        $id=$model->id;
        $values = $entity->getAttributes();
        $values = \Utility::unsetCommonIgnoreAttributes($values);
        $model->setAttributes($values);

        $model->id=$id;
        $model->settle_quantity=$values['sell_profit']['settle_quantity']['quantity'];
        $model->settle_amount=$values['sell_profit']['settle_amount']['price'];
        $model->buy_price=$values['buy_cost']['buy_price']['price'];
        $model->buy_amount=$values['buy_cost']['buy_amount']['price'];
        $model->actual_gross_profit=$values['actual_gross_profit']['price'];
        $model->freight=$values['freight']['price'];
        $model->warehouse_fee=$values['warehouse_fee']['price'];
        $model->miscellaneous_fee=$values['miscellaneous_fee']['price'];
        $model->vat = $values['vat']['price'];
        $model->sur_tax = $values['sur_tax']['price'];
        $model->stamp_tax = $values['stamp_tax']['price'];
        $model->after_tax_profit = $values['after_tax_profit']['price'];
        $model->fund_cost = $values['fund_cost']['price'];
        $model->profit = $values['profit']['price'];
        $model->buy_invoice_amount = $values['buy_invoice_amount']['price'];
        $model->sell_invoice_amount = $values['sell_invoice_amount']['price'];
        $model->pay_amount = $values['pay_amount']['price'];
        $model->receive_amount = $values['receive_amount']['price'];

        if (!$model->save())
        {
            throw new ZModelSaveFalseException($model);
        }
        //mq事件
        \AMQPService::publishContractProfit($model->contract_id);
        return $model;
    }
    /**
     * @name:format_price
     * @desc: 返回价格 格式数据
     * @param:* @param $price
     * @throw:
     * @return:Price
     */
    protected function format_price($price){
        $price = empty($price)?0:$price;
        return new Price($price,\ConstantMap::CURRENCY_RMB);
    }
    /**
     * 根据合同
     * @param $contract_id
     * @return SellContractProfit
     */
    function findByContractId($contract_id)
    {
        return $this->find('t.contract_id=' . $contract_id);
    }

    /**
     * 根据项目
     * @param $project_id
     * @return SellContractProfit
     */
    function findByProjectId($project_id)
    {
        return $this->findAll('t.project_id=' . $project_id);
    }
}