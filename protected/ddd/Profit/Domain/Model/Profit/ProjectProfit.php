<?php
/**
 * Desc: 项目利润
 * User: wwb
 * Date: 2018/5/28 0028
 * Time: 15:39
 */

namespace ddd\Profit\Domain\Model\Profit;

use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Value\Quantity;
use ddd\Common\IAggregateRoot;
use ddd\domain\entity\Attachment;
use ddd\domain\entity\value\Price;
use ddd\infrastructure\DIService;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\Utility;
use ddd\Profit\Domain\Model\Profit\BaseProfit;
use ddd\Profit\Domain\Model\Profit\DeliveryOrderProfitRepository;
use ddd\Profit\Domain\Model\Project;
use ddd\Profit\Domain\Model\Stock\BuyGoodsCost;
use ddd\Profit\Domain\Model\Stock\IDeliveryOrderRepository;
use ddd\Profit\Domain\Model\Stock\IDeliveryOrderDetailRepository;
use ddd\Profit\Domain\Model\Stock\IBuyGoodsCostRepository;
use ddd\Profit\Domain\Model\Stock\DeliveryOrder;
use ddd\domain\iRepository\contract\IContractRepository;
use ddd\Profit\Domain\Model\Profit\BuyCost;
use ddd\Profit\Domain\Model\Profit\SellProfit;
use ddd\Profit\Domain\Service\ProfitService;

class ProjectProfit extends BaseProfit
{

    /**
     * 标识id
     * @var      int
     */
    public $id;

    /**
     * 项目id
     * @var      int
     */
    public $project_id;

    /**
     * 交易主体id
     * @var      int
     */
    public $corporation_id;

    /**
     * 销售利润
     * @var      SellProfit
     */
    public $sell_profit;

    /**
     * 采购成本
     * @var      BuyCost
     */
    public $buy_cost;


    /**
     * 获取id
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 设置id
     * @param $value
     */
    public function setId($value)
    {
        $this->id = $value;
    }


    /**
     * 创建对象
     * @param    $project_id
     * @return   static
     * @throws   \Exception
     */
    public static function create(Project $project)
    {
        if (empty($project))
        {
            ExceptionService::throwArgumentNullException("Project对象", array('class' => get_called_class(), 'function' => __FUNCTION__));
        }

        $entity = new static();
        $entity->project_id = $project->project_id;
        $entity->corporation_id = $project->corporation_id;
        return $entity;
    }




}
