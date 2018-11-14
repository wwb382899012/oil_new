<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/9 16:15
 * Describe：
 */

namespace ddd\domain\entity\risk;


use ddd\Common\Domain\BaseEntity;
use ddd\infrastructure\DIService;
use ddd\repository\risk\PartnerAmountRepository;

abstract class RiskAmount extends BaseEntity
{
    /**
     * @var      bigint
     */
    public $id;

    /**
     * @var      int
     */
    public $amount = 0;

    /**
     * @var      int
     */
    public $credit_amount = 0;

    /**
     * @var      int
     */
    public $frozen_amount = 0;

    /**
     * @var      int
     */
    public $available_amount = 0;

    protected $oldValues=array();

    /**
     * 仓储类名
     * @var string
     */
    protected $repositoryClassName;

    /**
     * @var PartnerAmountRepository
     */
    protected $repository;

    public function __construct()
    {
        $this->frozen_amount = 0;
        $this->available_amount = 0;
        $this->amount = 0;
        $this->getRepository();
        parent::__construct();
    }

    /**
     * @return PartnerAmountRepository|object
     * @throws \Exception
     */
    protected function getRepository()
    {
        if (empty($this->repository))
        {
            $this->repository=DIService::getRepository($this->repositoryClassName);
        }
        return $this->repository;
    }

    /**
     * @param    int $amount
     */
    public function addAmount($amount)
    {
        // TODO: implement
        $this->repository->addAmount($this, $amount);
        $this->amount+=$amount;
    }

    /**
     * @param    int $amount
     */
    public function subtractAmount($amount)
    {
        // TODO: implement
        $this->repository->subtractAmount($this, $amount);
        $this->amount-=$amount;
    }

    /**
     * @param    int $amount
     */
    public function freezeAmount($amount)
    {
        // TODO: implement
        $this->repository->freezeAmount($this, $amount);
        $this->frozen_amount+=$amount;
    }

    /**
     * @param    int $amount
     */
    public function unfreezeAmount($amount)
    {
        // TODO: implement
        $this->repository->unfreezeAmount($this, $amount);
        $this->frozen_amount-=$amount;
    }

    /**
     * @return   int
     */
    public function getAvailableAmount()
    {
        // TODO: implement
        return $this->credit_amount-$this->getAllAmount();
    }

    /**
     * @return   int
     */
    public function getAllAmount()
    {
        // TODO: implement
        return $this->amount+$this->frozen_amount;
    }
}