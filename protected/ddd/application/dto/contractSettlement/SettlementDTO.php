<?php
/**
 * Created by youyi000.
 * DateTime: 2018/5/3 14:56
 * Describe：
 */

namespace ddd\application\dto\contractSettlement;


use ddd\Common\Application\BaseDTO;

class SettlementDTO extends BaseDTO
{

    public $settle_id;
    /**
     * @var      int  项目id
     */
    public $project_id;

    /**
     * @var      string  项目编号
     */
    public $project_code;

    /**
     * @var      int   合同id
     */
    public $contract_id;

    /**
     * @var      string  合同编号
     */
    public $contract_code;

    /**
     * @var      int 合作方id
     */
    public $partner_id;

    /**
     * @var      string 合作方名称
     */
    public $partner_name;

    /**
     * @var      int  交易主体id
     */
    public $corporation_id;

    /**
     * @var      string 交易状态名称
     */
    public $corporation_name;

    /**
     * @var      date   结算日期
     */
    public $settle_date;
    /**
     * @var      int 结算方式
     */
    public $settle_type;
    /**
     * @var      int    结算状态
     */
    public $settle_status;
    /**
     * @var      float  货款结算金额
     */
    public $goods_amount;
    /**
     * @var      object  结算币种
     */
    public $settle_currency;
    /**
     * @var      string  备注
     */
    public $remark;
    /**
     * @var      array  商品结算信息
     */
    public $settlementGoods;
}