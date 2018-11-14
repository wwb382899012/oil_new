<?php
/**
 * Desc:预估交易主体利润仓储
 * User: wwb
 * Date: 2018/5/31 0031
 * Time: 17:39
 */

namespace ddd\Profit\Repository\EstimateProfit;


use ddd\Common\Domain\Value\Money;
use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\EntityRepository;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\infrastructure\error\ZModelDeleteFalseException;
use ddd\infrastructure\error\ZModelNotExistsException;
use ddd\infrastructure\error\ZModelSaveFalseException;
use ddd\infrastructure\Utility;
use ddd\domain\iRepository\contract\IContractRepository;
use ddd\domain\entity\value\Price;
use ddd\domain\entity\value\Quantity;
use ddd\Profit\Domain\EstimateProfit\EstimateCorporationProfit;
use ddd\Profit\Domain\EstimateProfit\EstimateProjectProfit;
use ddd\Profit\Domain\EstimateProfit\IEstimateCorporationProfitRepository;
use ddd\Profit\Domain\EstimateProfit\IEstimateProjectProfitRepository;


class EstimateCorporationProfitRepository extends EntityRepository implements IEstimateCorporationProfitRepository
{

    public function getNewEntity()
    {
        return new EstimateCorporationProfit();
    }


    public function init()
    {

    }

    public function getActiveRecordClassName()
    {
        return 'EstimateCorporationProfit';
    }

    public function dataToEntity($model)
    {
        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes(), false);

        $entity->sell_amount = new Money($model->sell_amount,\ConstantMap::CURRENCY_RMB);
        $entity->sell_quantity = new Quantity($model->sell_quantity,\ConstantMap::UNIT_TON);
        $entity->buy_price = new Money($model->buy_price,\ConstantMap::CURRENCY_RMB);
        $entity->buy_amount = new Money($model->buy_amount,\ConstantMap::CURRENCY_RMB);

        //
        $entity->gross_profit = $this->format_price($model->gross_profit);
        $entity->transfer_fee = $this->format_price($model->transfer_fee);
        $entity->store_fee = $this->format_price($model->store_fee);
        $entity->other_fee = $this->format_price($model->other_fee);
        $entity->added_tax = $this->format_price($model->added_tax);
        $entity->surtax = $this->format_price($model->surtax);
        $entity->stamp_tax = $this->format_price($model->stamp_tax);
        $entity->post_profit =$this->format_price($model->post_profit);
        $entity->fund_cost = $this->format_price($model->fund_cost);
        $entity->actual_profit = $this->format_price($model->actual_profit);
        $entity->invoice_amount = $this->format_price($model->invoice_amount);

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
        //预估交易主体利润 持久化
        $model = $this->model()->with($this->with)->find('t.corporation_id='.$entity->corporation_id);

        if(empty($model)){
            $this->activeRecordClassName = $this->getActiveRecordClassName();
            $model = new $this->activeRecordClassName;
        }
        $id=$model->id;
        $values = $entity->getAttributes();
        $values = \Utility::unsetCommonIgnoreAttributes($values);
        $model->setAttributes($values);

        $model->id=$id;
        $model->corporation_name=$values['corporation_name'];
        $model->sell_quantity=$values['sell_quantity']['quantity'];
        $model->sell_amount=$values['sell_amount']['amount'];
        $model->buy_price=$values['buy_price']['amount'];
        $model->buy_amount=$values['buy_amount']['amount'];
        $model->gross_profit=$values['gross_profit']['amount'];
        $model->transfer_fee=$values['transfer_fee']['amount'];
        $model->store_fee=$values['store_fee']['amount'];
        $model->other_fee=$values['other_fee']['amount'];
        $model->added_tax = $values['added_tax']['amount'];
        $model->surtax = $values['surtax']['amount'];
        $model->stamp_tax = $values['stamp_tax']['amount'];
        $model->post_profit = $values['post_profit']['amount'];
        $model->fund_cost = $values['fund_cost']['amount'];
        $model->actual_profit = $values['actual_profit']['amount'];
        $model->invoice_amount = $values['invoice_amount']['amount'];


        if (!$model->save())
        {
            throw new ZModelSaveFalseException($model);
        }
        //mq事件
        //\AMQPService::publishProjectProfit($model->project_id);
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
        return new Money($price,\ConstantMap::CURRENCY_RMB);
    }


    /**
     * 根据交易主体
     * @param $corporation_id
     * @return EstimateProjectProfit
     */
    function findByCorporationId($corporation_id)
    {
        return $this->findAll('t.corporation_id=' . $corporation_id);
    }
}