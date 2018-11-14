<?php
/**
 * Created by wwb.
 * DateTime: 2018/3/21 11:35
 * Describe：结算单
 */

namespace ddd\Profit\Domain\Model\Settlement;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\Domain\Currency\Currency;
use ddd\Common\IAggregateRoot;
use ddd\Common\Domain\Value\Money;
use ddd\infrastructure\error\BusinessError;

abstract class Settlement extends BaseEntity implements IAggregateRoot
{

    #region property

    /**
     * 标识
     * @var   bigint
     */
    public $settle_id;

    /**
     * 合同id
     * @var   int
     */
    public $contract_id;
    /**
     * 项目id
     * @var   int
     */
    public $project_id;
    /**
     * 结算状态
     * @var   int
     */
    public $status;
    /**
     * 结算币种
     * @var   Currency
     */
    public $settle_currency;




    /**
     * 结算明细
     * @var   SettlementItem
     */
    public $settle_items;



    #endregion

    public function __construct()
    {
        parent::__construct();

        $this->settle_currency =Currency::createCNY();
    }




}

