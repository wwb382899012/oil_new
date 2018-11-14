<?php
/**
 * @Name            预估合同利润
 * @DateTime        2018年8月27日 16:16:30
 * @Author          vector
 */

namespace ddd\Profit\Domain\EstimateProfit;

use ddd\Common\Domain\BaseEntity;

use ddd\Common\Domain\Value\Money;
use ddd\Common\IAggregateRoot;
use ddd\domain\entity\value\Quantity;
use ddd\infrastructure\error\ExceptionService;
use ddd\Profit\Domain\Model\Project;


class EstimateProjectProfit extends BaseEntity implements IAggregateRoot
{
    #region property
    
    /**
     * 标识符 
     * @var   bigint
     */
    public $id;
    
    /**
     * 项目id 
     * @var   bigint
     */
    public $project_id;
    
    /**
     * 交易主体id 
     * @var   int
     */
    public $corporation_id;
    
    /**
     * 项目编号 
     * @var   string
     */
    public $project_code;
    
    /**
     * 项目类型 
     * @var   int
     */
    public $project_type;
    
    /**
     * 业务员 
     * @var   string
     */
    public $salesman;
    
    /**
     * 项目负责人id 
     * @var   int
     */
    public $manger_user_id;
    
    /**
     * 预估销售数量 
     * @var   Quantity
     */
    public $sell_quantity;
    
    /**
     * 预估销售金额 
     * @var   Money
     */
    public $sell_amount;
    
    /**
     * 预估采购单价 
     * @var   Money
     */
    public $buy_price;
    
    /**
     * 预估采购金额 
     * @var   Money
     */
    public $buy_amount;
    
    /**
     * 已收票金额 
     * @var   Money
     */
    public $invoice_amount;
    
    /**
     * 预估毛利 
     * @var   Money
     */
    public $gross_profit;
    
    /**
     * 预估运费 
     * @var   Money
     */
    public $transfer_fee;
    
    /**
     * 预估仓储费 
     * @var   Money
     */
    public $store_fee;
    
    /**
     * 预估杂费 
     * @var   Money
     */
    public $other_fee;
    
    /**
     * 增值税 
     * @var   Money
     */
    public $added_tax;
    
    /**
     * 附加税 
     * @var   Money
     */
    public $surtax;
    
    /**
     * 印花税 
     * @var   Money
     */
    public $stamp_tax;
    
    /**
     * 税后毛利 
     * @var   Money
     */
    public $post_profit;
    
    /**
     * 预估资金成本 
     * @var   Money
     */
    public $fund_cost;
    
    /**
     * 业务净利润 
     * @var   Money
     */
    public $actual_profit;    

    #endregion
    

    public function getId()
    {
        // TODO: Implement getId() method.
        return $this->id;
    }

    public function setId($id)
    {
        // TODO: Implement setId() method.
        $this->id=$id;
    }

    public function __construct()
    {
        parent::__construct();
        $this->sell_amount =new Money(0);
        $this->sell_quantity =new Quantity(0);
        $this->buy_price =new Money(0);
        $this->buy_amount =new Money(0);
        $this->invoice_amount =new Money(0);
        $this->gross_profit =new Money(0);
        $this->transfer_fee =new Money(0);
        $this->store_fee =new Money(0);
        $this->other_fee =new Money(0);
        $this->added_tax =new Money(0);
        $this->surtax =new Money(0);
        $this->stamp_tax =new Money(0);
        $this->post_profit =new Money(0);
        $this->fund_cost =new Money(0);
        $this->actual_profit =new Money(0);
    }
    /**
     * @name:create
     * @desc: 创建项目对象
     * @param:* @param Project $project
     * @throw:
     * @return:static
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
        $entity->project_code = $project->project_code;
        $entity->manger_user_id = $project->manager_user_id;
        $entity->salesman = $project->manager_user_name;
        $entity->project_type = $project->type;
        return $entity;
    }
    
}

