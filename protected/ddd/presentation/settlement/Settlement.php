<?php
/**
 * Created by youyi000.
 * DateTime: 2018/5/3 11:29
 * Describe：
 */

namespace ddd\presentation\settlement;

use ddd\presentation\BaseObject;

class Settlement extends BaseObject
{
    /**
     * @var      int
     */
    public $settle_id;
    /**
     * @var      int
     */
    public $bill_id;
    /**
     * @var      string
     */
    public $bill_code;
    /**
     * @var      int
     */
    public $contract_id;
    
    /**
     * @var      string
     */
    public $contract_code;
    
    /**
     * @var      int
     */
    public $project_id;
    
    /**
     * @var      string
     */
    public $project_code;
    
    /**
     * @var      int
     */
    public $partner_id;
    
    /**
     * @var      string
     */
    public $partner_name;
    
    /**
     * @var      int
     */
    public $corporation_id;
    
    /**
     * @var      string
     */
    public $corporation_name;
    
    /**
     * @var      int
     */
    public $agent_id;
    
    /**
     * @var      string
     */
    public $agent_name;
    
    /**
     * @var      int
     */
    public $settle_date;
    
    /**
     * @var      int
     */
    public $settle_status;
    /**
     * @var      object  结算币种
     */
    public $settle_currency;
    /**
     * @var      int  结算方式
     */
    public $settle_type;
    /**
     * @var      float  货款结算金额
     */
    public $goods_amount;
    /**
     * @var      float  非货款金额
     */
    public $other_amount; 
    /**
     * @var      float 货款+非货款
     */
    public $amount_settle;
    /**
     * @var      string 备注
     */
    public $remark; 
    /**
     * @var      array  商品结算详情
     */
    public $settlementGoods;
    
    /**
     * @var      array  非货款金额
     */
    public $other_expense;
    
    /**
     * @var      array 按入库通知单结算 
     */
    public $bill_settlements;
}