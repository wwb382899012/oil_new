<?php
/**
 * Created by PhpStorm.
 * User: wwb
 * Date: 2018/8/6
 * Time: 10:40
 */

namespace ddd\Profit\Domain\Service;
use ddd\Common\Domain\BaseService;
use ddd\infrastructure\DIService;
use ddd\domain\entity\value\Quantity;
use ddd\domain\entity\value\Price;
use ddd\Profit\Domain\Model\Profit\ProfitDeliverySettleEvent;
use ddd\Profit\Domain\Model\Profit\IDeliveryOrderProfitRepository;
use ddd\Profit\Domain\Model\Stock\DeliveryOrder;
use ddd\Profit\Domain\Model\Stock\IDeliveryOrderDetailRepository;
use ddd\Profit\Domain\Model\Stock\IBuyGoodsCostRepository;
use ddd\Profit\Domain\Model\Stock\BuyGoodsCost;
class ProfitService extends BaseService
{

    /**
     * @desc 获取项目的资金成本->合计利息
     * @param ContractSplitApplySubmittedEvent $event
     * @throws \Exception
     */
    public static  function getFundCost($project_id)
    {
        $sql="
           select  i.project_id,
                    (SELECT IFNULL(sum(ic.interest), 0) from t_payment_interest pi
                    LEFT JOIN t_payment_interest_change ic on pi.contract_id=ic.contract_id
                    where pi.project_id=i.project_id and pi.contract_type=1) as interest_pay,
                     (SELECT IFNULL(sum(ic.interest), 0) from t_payment_interest pi
                    LEFT JOIN t_payment_interest_change ic on pi.contract_id=ic.contract_id
                    where pi.project_id=i.project_id and pi.contract_type=2) as interest_receive
           from
                    t_payment_interest i
          where
                    i.project_id={$project_id} group by i.project_id
        ";
        $result = \Utility::query($sql);
        if(empty($result))
            return 0;
        else
            return $result[0]['interest_pay']-$result[0]['interest_receive'];

    }
}