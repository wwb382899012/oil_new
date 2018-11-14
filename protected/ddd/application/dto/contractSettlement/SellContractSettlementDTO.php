<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 10:32
 * Describe：销售合同结算   DTO
 */

namespace ddd\application\dto\contractSettlement;

use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\stock\LadingBill;
use ddd\domain\entity\contractSettlement\SaleContractSettlement;
use ddd\repository\contract\ContractRepository;
use ddd\repository\PartnerRepository;
use ddd\repository\CorporationRepository;
use ddd\repository\project\ProjectRepository;

class SellContractSettlementDTO extends SettlementDTO
{

    /**
     * @var      int
     */
    //public $status;
    /**
     * @var      float  非货款金额
     */
    public $other_amount;
    /**
     * @var      float  结算总金额 = 货款金额 + 非货款金额
     */
    public $amount_settle;

    /**
     * @var      array  非货款结算信息
     */
    public $other_expense;
    /**
     * @var      object 发货单结算结算信息（按发货单结算）
     */
    public $delivery_orders;



    public function rules()
    {
        return array(
            array("contract_id", "numerical", "integerOnly" => true, "min" => 0, "tooSmall" => "合同id必须为大于0的整数"),
            array("settle_date","date","format"=>"yyyy-MM-dd","allowEmpty"=>false,"message"=>'结算日期传入错误'),
            array("settle_status", "numerical", "integerOnly" => true, "allowEmpty"=>false,"message" => "结算状态传入错误"),
        );
    }

   public function customAttributeNames()
    {
        return array();
    }

    /**
     * 从实体对象生成DTO对象
     * @param LadingBill $ladingBill
     */
    public function fromEntity(BaseEntity $deliveryBatchSettlement)
    {
        $values=$deliveryBatchSettlement->getAttributes();
        unset($values['delivery_orders']);
        unset($values['other_expense']);
        $this->setAttributes($values);
        $this->settle_status = $deliveryBatchSettlement->status;
        $this->settle_currency = $deliveryBatchSettlement->settle_currency;
        $this->amount_settle = $deliveryBatchSettlement->total_amount;
        
        $contract =  ContractRepository::repository()->findByPk($deliveryBatchSettlement->contract_id);
        $this->settle_type=$contract->settle_type;
        $this->project_id = $contract->project_id;
        $this->contract_code = $contract -> contract_code;
        $this->partner_id = $contract -> partner_id;
        $this->corporation_id = $contract -> corporation_id;
        $project =  ProjectRepository::repository()->findByPk($contract->project_id);
        $this->project_code = $project -> project_code;
        $partner = PartnerRepository::repository()->findByPk($contract->partner_id);
        $this->partner_name = $partner -> name;
        $corporation = CorporationRepository::repository()->findByPk($contract->corporation_id);
        $this->corporation_name = $corporation -> name;
        
        if(is_array($deliveryBatchSettlement->goods_expense))
        {
            foreach ($deliveryBatchSettlement->goods_expense as $k=>$v)
            {
                $item=new SettlementGoodsDTO();
                $item->fromEntity($v);
                $this->settlementGoods[]=$item;
            }
        }
        if(is_array($deliveryBatchSettlement->delivery_orders))
        {
            foreach ($deliveryBatchSettlement->delivery_orders as $k=>$v)
            {
                $item=new DeliveryOrderSettlementDTO();
                $item->fromEntity($v);
                $this->delivery_orders[]=$item;
            }
        }
        if(is_array($deliveryBatchSettlement->other_expense))
        {
            foreach ($deliveryBatchSettlement->other_expense as $k=>$v)
            {
                $item=new SettlementGoodsSubjectDTO();
                $item->fromEntity($v);
                $this->other_expense[]=$item;
            }
        }
    }

    /**
     * 转换成实体对象
     * @return LadingBill
     */
    public function toEntity()
    {
        $entity= new SaleContractSettlement();
        $entity->setAttributes($this->getAttributes());
        $entity->status=$this->settle_status;
        $entity->settle_currency = $this->settle_currency;
        $entity->total_amount = $this->amount_settle;
        if(is_array($this->settlementGoods))
        {
            foreach ($this->settlementGoods as $k=>$v)
            {
                $entity->goods_expense[$v->goods_id]=$v->toEntity();
            }
        }
        
        $entity->delivery_orders=array();
        if(is_array($this->delivery_orders)&&$this->settle_type==\ddd\domain\entity\contractSettlement\SettlementMode::DELIVERY_ORDER_MODE_SETTLEMENT)
        {
            foreach ($this->delivery_orders as $k=>$v)
            {
                $entity->delivery_orders[$v->settle_id]=$v->toEntity();
                
            }
        }
        $entity->other_expense=array();
        if(is_array($this->other_expense))
        {
            foreach ($this->other_expense as $k=>$v)
            {
                $entity->other_expense[$v->fee->id]=$v->toEntity();
                
            }
        }
        
        return $entity;
    }

    
}