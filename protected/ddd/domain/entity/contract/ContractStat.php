<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/2 16:00
 * Describe：
 */

namespace ddd\domain\entity\contract;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\domain\iRepository\contract\IContractStatRepository;
use ddd\infrastructure\DIService;

class ContractStat extends BaseEntity implements IAggregateRoot
{

    #region public property

    /**
     * 合同id
     * @var   bigint
     */
    public $contract_id;

    /**
     * 付款总金额
     * @var   int
     */
    public $amount_out;

    /**
     * 收款总金额
     * @var   int
     */
    public $amount_in;

    /**
     * 入库商品金额
     * @var   int
     */
    public $goods_in_amount;

    /**
     * 出库商品金额
     * @var   int
     */
    public $goods_out_amount;

    /**
     * 开票金额
     * @var   int
     */
    public $invoice_out_amount;

    /**
     * 收票金额
     * @var   int
     */
    public $invoice_in_amount;

    /**
     * 开票数量
     * @var   int
     */
    public $invoice_out_quantity;

    /**
     * 收票数量
     * @var   int
     */
    public $invoice_in_quantity;

    /**
     * 更新时间
     * @var   datetime
     */
    public $update_time;

    #endregion

    /**
     * @var IContractStatRepository
     */
    protected $repository;

    #region implements

    function getId()
    {
        // TODO: Implement getId() method.
        return $this->contract_id;
    }

    function setId($value)
    {
        // TODO: Implement setId() method.
        $this->contract_id=$value;
    }

    function getIdName()
    {
        // TODO: Implement getIdName() method.
        return "contract_id";
    }



    #endregion


    /**
     * 创建对象
     * @param int $contractId
     * @return static
     * @throws \ReflectionException
     */
    public function create($contractId)
    {
        // TODO: implement
        return new static(["contract_id"=>$contractId]);
    }

    /**
     * 获取仓储
     * @return IContractStatRepository|object
     * @throws \Exception
     */
    protected function getRepository()
    {
        if (empty($this->repository))
        {
            $this->repository=DIService::getRepository(IContractStatRepository::class);
        }
        return $this->repository;
    }

    /**
     * 增加付款金额
     * @param    int $amount
     */
    public function addAndSavePayAmount($amount)
    {
        // TODO: implement
        $this->amount_out+=$amount;
        $this->repository->addAndSaveAmountOut($this,$amount);
    }

    /**
     * 增加收款金额
     * @param    int $amount
     */
    public function addAndSaveReceiveAmount($amount)
    {
        // TODO: implement
        $this->amount_in+=$amount;
        $this->repository->addAndSaveAmountIn($this,$amount);
    }

    /**
     * 增加商品入库金额
     * @param    int $amount
     */
    public function addAndSaveGoodsInAmount($amount)
    {
        // TODO: implement
        $this->goods_in_amount+=$amount;
        $this->repository->addAndSaveGoodsInAmount($this,$amount);
    }

    /**
     * 增加商品出库金额
     * @param    int $amount
     */
    public function addAndSaveGoodsOutAmount($amount)
    {
        // TODO: implement
        $this->goods_out_amount+=$amount;
        $this->repository->addAndSaveGoodsOutAmount($this,$amount);
    }

    /**
     * 增加开出发票金额
     * @param    int $amount
     */
    public function addAndSaveInvoiceOutAmount($amount)
    {
        // TODO: implement
        $this->invoice_out_amount+=$amount;
        $this->repository->addAndSaveInvoiceOutAmount($this,$amount);
    }

    /**
     * 增加收到发票金额
     * @param    int $amount
     */
    public function addAndSaveInvoiceInAmount($amount)
    {
        // TODO: implement
        $this->invoice_in_amount+=$amount;
        $this->repository->addAndSaveInvoiceInAmount($this,$amount);
    }
}