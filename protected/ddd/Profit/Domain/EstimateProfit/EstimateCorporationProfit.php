<?php
/**
 * @Name            预估合同利润
 * @DateTime        2018年8月27日 16:16:30
 * @Author          vector
 */

namespace ddd\Profit\Domain\EstimateProfit;

use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\infrastructure\error\ExceptionService;
use ddd\Profit\Domain\Model\Corporation;


class EstimateCorporationProfit extends BaseEntity implements IAggregateRoot
{
    #region property
    
    /**
     * 标识符 
     * @var   bigint
     */
    public $id;
    
    /**
     * 交易主体id 
     * @var   int
     */
    public $corporation_id;

    /**
     * 交易主体名称
     * @var   string
     */
    public $corporation_name;
    
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


    /**
     * 创建工厂方法
     */
    public static function create(Corporation $corporation)
    {
        if (empty($corporation))
        {
            ExceptionService::throwArgumentNullException("Corporation对象", array('class' => get_called_class(), 'function' => __FUNCTION__));
        }

        $entity = new static();
        $entity->corporation_id = $corporation->corporation_id;
        $entity->corporation_name = $corporation->corporation_name;

        return $entity;
    }
    
    
    
}

