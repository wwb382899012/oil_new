<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/14 15:49
 * Describe：
 */

namespace ddd\Profit\Repository\EstimateProfit;


use ddd\Common\Domain\Value\Money;
use ddd\Common\Domain\Value\Quantity;
use ddd\Common\IAggregateRoot;
use ddd\Common\Repository\BaseRepository;
use ddd\Profit\Domain\Contract\IContractRepository;
use ddd\Profit\Domain\EstimateProfit\EstimateBuyGoodsItem;
use ddd\Profit\Domain\EstimateProfit\EstimateContractProfit;
use ddd\Profit\Domain\EstimateProfit\EstimateCostInfo;
use ddd\Profit\Domain\EstimateProfit\IEstimateContractProfitRepository;
use ddd\Profit\Domain\Service\EventService;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\error\ZModelSaveFalseException;

class EstimateContractProfitRepository extends BaseRepository implements IEstimateContractProfitRepository
{

    /**
     * 根据合同id查找合同信息
     * @param $contractId
     * @return EstimateContractProfit|null
     */
    public function findByContractId($contractId)
    {
        $model=\EstimateContractProfit::model()->with("goodsItems")->find("t.contract_id=".$contractId);
        if(empty($model))
            return null;
        
        return $this->dataToEntity($model);
    }


    /**
     * 根据合同id查找合同信息
     * @param $projectId
     * @return EstimateContractProfit[]|null
     */
    public function findAllByProjectId($projectId)
    {
        $model=\EstimateContractProfit::model()->with("goodsItems")->findAll("t.project_id=".$projectId);
        if(empty($model))
            return null;
        $result = array();
        foreach ($model as $v) {
            $result[] = $this->dataToEntity($v);
        }
        return $result;
    }

    /**
     *
     * @param \EstimateContractProfit $model
     * @return EstimateContractProfit
     */
    protected function dataToEntity(\EstimateContractProfit $model)
    {
        $entity = new EstimateContractProfit();
        $entity->setAttributes($model->getAttributes());
        $out_quantity           = new Quantity($model->sell_quantity, $model->unit);
        $sell_amount            = new Money($model->sell_amount, $model->currency);
        $buy_amount             = new Money($model->buy_amount, $model->currency);
        $entity->sell_income    = new EstimateCostInfo($out_quantity, $sell_amount);
        $entity->buy_cost       = new EstimateCostInfo($out_quantity, $buy_amount);
        $entity->invoice_amount = new Money($model->invoice_amount);
        if(is_array($model->goodsItems))
        {
            foreach ($model->goodsItems as $goods)
            {
                $item = new EstimateBuyGoodsItem();
                $item->goods_id     = $goods->goods_id;
                $item->buy_price    = new Money($goods->buy_price, $goods->currency);
                $item->sell_price   = new Money($goods->sell_price, $goods->currency);
                $item->out_quantity = new Quantity($goods->out_quantity, $goods->unit);
                $item->buy_quantity = new Quantity($goods->buy_quantity,$goods->unit);
                
                $entity->addGoods($item);
            }
        }
        return $entity;
    }


    /**
     * 把对象持久化到数据
     * @param IAggregateRoot $entity
     * @return EstimateContractProfit
     * @throws \Exception
     */
    public function store(IAggregateRoot $entity)
    {
        if(empty($entity))
            throw new ZException("EstimateContractProfit对象不存在");

        $model = array();
        $id = $entity->getId();
        if(!empty($id))
            $model = \EstimateContractProfit::model()->with("goodsItems")->findByPk($id);

        if(empty($model)){
            $model = new \EstimateContractProfit();
            $model->contract_id     = $entity->contract_id;
            $model->project_id      = $entity->project_id;
            $model->corporation_id  = $entity->corporation_id;
            $model->check_pass_time = $entity->check_pass_time;
            $model->currency        = $entity->sell_income->amount->currency->id;
            $model->unit            = $entity->sell_income->quantity->unit->id;
        }

        $isNew = $model->isNewRecord;

        $items = $entity->goods_items;
        if (!is_array($items))
            $items = array();
        
        $model->sell_amount     = $entity->sell_income->amount->amount;
        $model->buy_amount      = $entity->buy_cost->amount->amount;
        $model->sell_quantity   = $entity->sell_income->quantity->quantity;
        $model->buy_price       = $entity->buy_cost->price->amount;
        // $model->gross_profit    = $model->sell_amount - $model->buy_amount;
        // $model->transfer_fee    = 0;
        // $model->store_fee       = 0;
        // $model->other_fee       = 0;
        // $model->added_tax       = round($model->gross_profit/1.16*0.16);    // 预估毛利/1.16*16%-运费/1.1*10%-(仓储费+杂费)/1.06*6%
        // $model->surtax          = round($model->added_tax * 0.12); //增值税*12%
        // $model->stamp_tax       = round($model->sell_amount * 0.0003);//未完结预估销售金额*万分之三
        // $model->post_profit     = $model->gross_profit - $model->added_tax - $model->surtax - $model->stamp_tax; //预估毛利-运费-仓储费-其他费用-增值税-附加税-印花税
        // $model->fund_cost       = 0; //通过领域服务获取资金成本 （销售预估数/销售合同数）*资金成本
        // $model->actual_profit   = $model->post_profit - $model->fund_cost; // 税后毛利-资金成本
         $model->invoice_amount   = $entity->invoice_amount->amount;

        $res = $model->save();
        if (!$res)
            throw new ZModelSaveFalseException($model);

        if (!$isNew)
        {
            if (is_array($model->goodsItems) && !empty($model->goodsItems))
            {
                foreach ($model->goodsItems as $p)
                {
                    $item = $items[$p->goods_id];
                    $p->buy_price    = $item->buy_price->amount;
                    $p->sell_price   = $item->sell_price->amount;
                    $p->out_quantity = $item->out_quantity->quantity;
                    $p->buy_quantity = $item->buy_quantity->quantity;

                    $res = $p->save();
                    if (!$res)
                        throw new ZModelSaveFalseException($p);

                    unset($items[$p->goods_id]);
                }
            }
        }


        if (is_array($items) && count($items) > 0)
        {
            foreach ($items as $item)
            {
                $buyItem = new \EstimateGoodsBuyDetail();
                $buyItem->project_id   = $entity->project_id;
                $buyItem->contract_id  = $entity->contract_id;
                $buyItem->goods_id     = $item->goods_id;
                $buyItem->buy_price    = $item->buy_price->amount;
                $buyItem->sell_price   = $item->sell_price->amount;
                $buyItem->currency     = $item->buy_price->currency;
                $buyItem->out_quantity = $item->out_quantity->quantity;
                $buyItem->buy_quantity = $item->buy_quantity->quantity;
                $buyItem->unit         = $item->buy_quantity->unit;

                $res = $buyItem->save();
                if (!$res)
                    throw new ZModelSaveFalseException($buyItem);
            }
        }

        return $entity;
    }


    function findByPk($id, $condition = '', $params = array())
    {
        // TODO: Implement findByPk() method.
    }

    function find($condition = '', $params = array())
    {
        // TODO: Implement find() method.
    }

    function findAll($condition = '', $params = array())
    {
        // TODO: Implement findAll() method.
    }
}