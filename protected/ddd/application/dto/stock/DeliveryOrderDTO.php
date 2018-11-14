<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 10:32
 * Describe：
 */

namespace ddd\application\dto\stock;

use ddd\application\dto\AttachmentDTO;
use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\stock\DeliveryOrder;
use ddd\domain\entity\value\Quantity;
use ddd\repository\contract\ContractRepository;
use ddd\repository\PartnerRepository;
use ddd\repository\CorporationRepository;

class DeliveryOrderDTO extends BaseDTO
{
    public $items;
    public $settleItems;
    public $order_id;
    public $delivery_date;
    public $settle_date;
    public $files;
    public $contract_code;//销售合同编号
    public $corporation_name;
    public $partner_name; //合作方名称
    public $check_remark;//审核意见

    public function rules()
    {
        return array();
    }

    public function customAttributeNames()
    {
        return \DeliveryOrder::model()->attributeNames();
    }

    /**
     * 从实体对象生成DTO对象
     * @param BaseEntity $deliveryOrder
     */
    public function fromEntity(BaseEntity $deliveryOrder)
    {
        $values = $deliveryOrder->getAttributes();
        unset($values['items']);
        unset($values['settleItems']);
        unset($values['files']);
        $this->setAttributes($values);
        $checkLog = \FlowService::getCheckLog($values['order_id'], "9");
        $this->check_remark = $checkLog[0]['remark'];
        $contract = ContractRepository::repository()->findByPk($deliveryOrder->contract_id);
        $this->contract_code = $contract->contract_code;
        $partner = PartnerRepository::repository()->findByPk($contract->partner_id);
        $this->partner_name = $partner -> name;
        $corporation = CorporationRepository::repository()->findByPk($contract->corporation_id);
        $this->corporation_name = $corporation -> name;  
        if (is_array($deliveryOrder->items))
        {
            foreach ($deliveryOrder->items as $k => $v)
            {
                $item = new DeliveryOrderGoodsDTO();
                $item->fromEntity($v);
                $item->delivery_items = \DeliveryOrderService::getGoodsStockDelivery($values['order_id'], $v['detail_id']);
                //var_dump($item->delivery_items);die; 
                /*  $item->stock_in_id = $values['stock_in_id'];
                //配货数量
                $stockDelivery = \DeliveryOrderService::getGoodsStockDelivery($values['order_id'], $v['detail_id']);
                $item->stock_delivery_quantity=new Quantity($stockDelivery['stock_delivery_quantity'],$v->quantity->unit);
                $item->store_name=$stockDelivery['store_name'];
                //总出库数量
                $item->out_quantity=new Quantity(\DeliveryOrderService::getGoodsOutQuantity($values['order_id'], $v['detail_id']),$v->quantity->unit);
                $no_out_quantity=($item->stock_delivery_quantity) - ($item->out_quantity) ;
                $item->no_out_quantity = new Quantity($no_out_quantity,$v->quantity->unit);  */
                if(!empty($item->delivery_items)){
                    foreach ($item->delivery_items as $m=>$n){
                        $n['stock_delivery_quantity']=new Quantity($n['stock_delivery_quantity'],$v->quantity->unit);
                        $n['out_quantity']=new Quantity($n['out_quantity'],$v->quantity->unit);
                        $n['no_out_quantity']=new Quantity($n['no_out_quantity'],$v->quantity->unit);
                        $item->delivery_items[$m]=$n;
                    }
                }
                
                $this->items[] = $item;
            }
        }
        if (is_array($deliveryOrder->settleItems))
        {
            foreach ($deliveryOrder->settleItems as $k => $v)
            {
                $settleItem = new DeliveryOrderSettlementDetailDTO();
                $settleItem->fromEntity($v);
                $this->settleItems[] = $settleItem;
            }
        }
        if (is_array($deliveryOrder->files))
        {
            foreach ($deliveryOrder->files as $k => $v)
            {
                $files = new AttachmentDTO();
                $files->fromEntity($v);
                $this->files[] = $files;
            }
        }
       
    }

    /**
     * 转换成实体对象
     * @return LadingBill
     */
    public function toEntity()
    {
        $entity = new DeliveryOrder();
        $entity->setAttributes($this->getAttributes());
        $entity->order_id = $this->order_id;
        $entity->delivery_date = $this->delivery_date;
        $entity->items = array();
        $entity->settleItems = array();
        if (is_array($this->items))
        {
            foreach ($this->items as $v)
            {
                $entity->items[$v->goods_id] = $v->toEntity();
            }
        }
        if(is_array($this->settleItems)) {
            foreach ($this->settleItems as $s) {
                $entity->settleItems[$s->goods_id] = $s->toEntity();
            }
        }

        return $entity;
    }
}