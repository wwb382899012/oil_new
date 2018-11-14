<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/10 15:43
 * Describe：
 */

namespace ddd\Profit\Domain\EstimateProfit;


use ddd\Common\Domain\BaseService;
use ddd\Common\Domain\Value\Money;
use ddd\domain\entity\contract\Contract;

use ddd\Common\Domain\Value\Quantity;
use ddd\domain\entity\value\Price;
use ddd\infrastructure\DIService;
use ddd\Profit\Domain\EstimateProfit\EstimateContractProfitRepository;
use ddd\Profit\Domain\EstimateProfit\EstimateProjectProfitRepository;
use ddd\Profit\Domain\Model\Project;
use ddd\Profit\Domain\Service\ProfitService;
use ddd\Profit\Domain\Contract\IContractRepository;

class EstimateProjectProfitService extends BaseService
{

    use EstimateContractProfitRepository;
    use EstimateProjectProfitRepository;

    /**
     * @name:createProjectProfit
     * @desc: 创建合同利润 对象
     * @param:* @param Project $project
     * @param bool $persistent
       @throw: * @throws \Exception
     * @return:static
     */
    public function createProjectProfit(Project $project,$persistent=false)
    {

        $estimateProjectProfitEntity=EstimateProjectProfit::create($project);

        $estimateContractProfitList = $this->getEstimateContractProfitRepository()->findAllByProjectId($project->project_id);

        $estimateProjectProfitEntity = $this->computeTotalPrice($estimateContractProfitList,$estimateProjectProfitEntity);

        if($persistent)
            $this->getEstimateProjectProfitRepository()->store($estimateProjectProfitEntity);

        return $estimateProjectProfitEntity;
    }

    /**
     * @name:computeTotalPrice
     * @desc: 计算合计
     * @param:* @param $profitList
     * @param $entity
       @throw:
     * @return:mixed
     */
    protected function computeTotalPrice($profitList,$entity){
        $sell_quantity=$sell_amount=$buy_price=$buy_amount=0;
        $invoice_amount=0;
        $gross_profit = $added_tax=$surtax=$stamp_tax=$post_profit=$fund_cost=$actual_profit=0;

        if(!empty($profitList)){
            foreach($profitList as $key=>$item){

                $sell_quantity += $item->sell_income->quantity->quantity;
                $sell_amount += $item->sell_income->amount->amount;
                /*$buy_price += $item->buy_cost->buy_price->price;*/
                $buy_amount += $item->buy_cost->amount->amount;

                $invoice_amount+=$item->invoice_amount->amount;
            }

        }
        $sell_contract_quantity = $this->getProjectSellQuantity($entity->project_id);//项目的销售合同数量、吨

        /*$projectPaymentEntity = ProjectPaymentRepository::repository()->findByProjectId($entity->project_id);

        if(!empty($projectPaymentEntity)) {
            $pay_amount += $projectPaymentEntity->pay_amount->price;//付款金额，要加上“项目下付款”
            $miscellaneous_fee += $projectPaymentEntity->miscellaneous_fee->price; //杂费，要加上“项目下付款”的杂费
        }*/


        $entity->sell_quantity=new Quantity($sell_quantity,\ConstantMap::UNIT_TON);
        $entity->sell_amount=new Money($sell_amount,\ConstantMap::CURRENCY_RMB);

        $buy_price = $sell_quantity==0?$buy_amount:$buy_amount/$sell_quantity;//采购金额总和 除以 结算数量总和
        $entity->buy_price = new Money($buy_price,\ConstantMap::CURRENCY_RMB);
        $entity->buy_amount = new Money($buy_amount,\ConstantMap::CURRENCY_RMB);


        $entity->invoice_amount = new Money($invoice_amount,\ConstantMap::CURRENCY_RMB);

        /*$entity->miscellaneous_fee = new Price($miscellaneous_fee,\ConstantMap::CURRENCY_RMB);*/

        //预估实际毛利
        $gross_profit = ($entity->sell_amount->amount) - ($entity->buy_amount->amount);
        $entity->gross_profit = new Money($gross_profit,\ConstantMap::CURRENCY_RMB);


        //增值税、附加税
        $added_tax = ($entity->gross_profit->amount / 1.16 * 0.16)-($entity->transfer_fee->amount /1.1 * 0.1)- (($entity->store_fee->amount + $entity->other_fee->amount)/1.06*0.06);
        $entity->added_tax = new Money($added_tax,\ConstantMap::CURRENCY_RMB);

        $surtax=$added_tax*0.12;
        $entity->surtax = new Money($surtax,\ConstantMap::CURRENCY_RMB);

        $stamp_tax = $entity->sell_amount->amount * 0.0003;
        $entity->stamp_tax = new Money($stamp_tax,\ConstantMap::CURRENCY_RMB);
        $post_profit = $gross_profit-($entity->transfer_fee->amount)-($entity->store_fee->amount)-($entity->other_fee->amount)-($entity->added_tax->amount)-($entity->surtax->amount)-($entity->stamp_tax->amount);
        $entity->post_profit = new Money($post_profit,\ConstantMap::CURRENCY_RMB);

        //资金成本
        \InterestReportService::addInterestInfo();
        \InterestReportService::addDayInterest($entity->project_id);

        $fund_cost = ProfitService::getFundCost($entity->project_id);
        if($sell_contract_quantity==0)
            $fund_cost = $fund_cost;
        else
            $fund_cost = $fund_cost * ($sell_quantity/$sell_contract_quantity);

        $entity->fund_cost = new Money($fund_cost,\ConstantMap::CURRENCY_RMB);
        $entity->actual_profit = new Money($entity->post_profit->amount - $entity->fund_cost->amount,\ConstantMap::CURRENCY_RMB);

        return $entity;
    }

    protected function getProjectSellQuantity($project_id){
        $sell_contract_quantity=0;
        $contractGoods = \Utility::query('select quantity,unit_convert_rate from t_contract_goods where type=2 and project_id='.$project_id);
        if(!empty($contractGoods)){
            foreach($contractGoods as $key=>$value){
                $sell_contract_quantity += $value['quantity']/$value['unit_convert_rate'];
            }
        }
        return $sell_contract_quantity;
    }

}