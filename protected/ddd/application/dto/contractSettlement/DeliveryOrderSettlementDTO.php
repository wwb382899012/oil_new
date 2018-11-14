<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 10:32
 * Describe：发货单结算   DTO
 */

namespace ddd\application\dto\contractSettlement;

use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\stock\LadingBill;
use ddd\domain\entity\contractSettlement\DeliveryOrderSettlement;
use ddd\repository\project\ProjectRepository;
use ddd\repository\stock\DeliveryOrderRepository;
use ddd\repository\contract\ContractRepository;
use ddd\repository\PartnerRepository;
use ddd\repository\CorporationRepository;

class DeliveryOrderSettlementDTO extends SettlementDTO
{
    
   /**
    * @var      int  发货单id
    */
    public $order_id;
    
    /**
    * @var      string  发货单编号
    */
    public $order_code;
    

    public function rules()
    {
        return array(
            array("order_id", "numerical", "integerOnly" => true, "min" => 0, "tooSmall" => "发货单id必须为大于0的整数"),
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
    public function fromEntity(BaseEntity $entry)
    {
        $values=$entry->getAttributes();
        $this->setAttributes($values);
        $this->settle_status = $entry->status;
        $this->settle_currency = $entry->settle_currency;
        
        $DeliveryOrder = DeliveryOrderRepository::repository()->findByPk($entry->order_id);
        $this->order_code = $DeliveryOrder->code;
        $contract =  ContractRepository::repository()->findByPk($entry->contract_id);
        $this->settle_type = $contract->settle_type;
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
        
        if(is_array($entry->goods_expense))
        {
            foreach ($entry->goods_expense as $k=>$v)
            {
                $item=new SettlementGoodsDTO();
                $item->fromEntity($v);
                $this->settlementGoods[]=$item;
            }
        }
    }

    /**
     * 转换成实体对象
     * @return LadingBill
     */
    public function toEntity()
    {
        $entity= new DeliveryOrderSettlement();
        $entity->setAttributes($this->getAttributes());
        $entity->settle_currency = $this->settle_currency;
        $entity->status=$this->settle_status;
        if(is_array($this->settlementGoods))
        {
            foreach ($this->settlementGoods as $k=>$v)
            {
                $entity->goods_expense[$v->goods_id]=$v->toEntity();
            }
        }
        return $entity;
    }

    
}