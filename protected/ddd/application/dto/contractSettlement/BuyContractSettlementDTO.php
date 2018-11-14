<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 10:32
 * Describe：采购合同结算   DTO
 */

namespace ddd\application\dto\contractSettlement;

use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\stock\LadingBill;
use ddd\domain\entity\contractSettlement\BuyContractSettlement;
use ddd\repository\contract\ContractRepository;
use ddd\repository\PartnerRepository;
use ddd\repository\CorporationRepository;
use ddd\repository\project\ProjectRepository;


class BuyContractSettlementDTO extends SettlementDTO
{
    /**
     * @var      int  代理商id
     */
    public $agent_id;
    /**
     * @var      string 代理商名称
     */
    public $agent_name;
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
     * @var      array  入库通知单结算列表
     */
    public $lading_bills;


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
    public function fromEntity(BaseEntity $stockContractSettlement)
    {
        $values=$stockContractSettlement->getAttributes();
        unset($values['lading_bills']);
        unset($values['other_expense']);
        $this->setAttributes($values);
        $this->settle_status = $stockContractSettlement->status;
        $this->settle_currency = $stockContractSettlement->settle_currency;
        $this->amount_settle = $stockContractSettlement->total_amount;
        
        $contract =  ContractRepository::repository()->findByPk($stockContractSettlement->contract_id);
        $this->settle_type=$contract->settle_type;
        $this->project_id = $contract->project_id;
        $this->agent_id = $contract->agent_id;
        if(!empty($contract->agent_id)){
        $agent = PartnerRepository::repository()->findByPk($contract->agent_id);
        $this->agent_name = $agent->name;
        }
        $this->contract_code = $contract -> contract_code;
        $this->partner_id = $contract -> partner_id;
        $this->corporation_id = $contract -> corporation_id;
        if(!empty($contract->project_id)){
        $project =  ProjectRepository::repository()->findByPk($contract->project_id);
        }
        $this->project_code = $project -> project_code;
        if(!empty($contract->partner_id)){
        $partner = PartnerRepository::repository()->findByPk($contract->partner_id);
        $this->partner_name = $partner -> name;
        }
        if(!empty($contract->corporation_id)){
        $corporation = CorporationRepository::repository()->findByPk($contract->corporation_id);
        $this->corporation_name = $corporation -> name;  
        }
        
        if(is_array($stockContractSettlement->goods_expense))
        {
            foreach ($stockContractSettlement->goods_expense as $k=>$v)
            {
                $item=new SettlementGoodsDTO();
                $item->fromEntity($v);
                $this->settlementGoods[]=$item;
            }
        }
        if(is_array($stockContractSettlement->lading_bills))
        {
            foreach ($stockContractSettlement->lading_bills as $k=>$v)
            {
                $item=new StockInBatchSettlementDTO();
                $item->fromEntity($v);
                $this->lading_bills[]=$item;
            }
        }
        if(is_array($stockContractSettlement->other_expense))
        {
            foreach ($stockContractSettlement->other_expense as $k=>$v)
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
        $entity= new BuyContractSettlement();
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
       
        $entity->lading_bills=array();
        if(is_array($this->lading_bills)&&$this->settle_type==\ddd\domain\entity\contractSettlement\SettlementMode::LADING_BILL_MODE_SETTLEMENT)
        {
            foreach ($this->lading_bills as $k=>$v)
            {
                $entity->lading_bills[$v->settle_id]=$v->toEntity();
                
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