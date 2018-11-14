<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 10:32
 * Describe：商品结算详情   DTO
 */

namespace ddd\application\dto\contractSettlement;

use ddd\Common\Application\BaseDTO;
use ddd\application\dto\AttachmentDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\stock\LadingBill;
use ddd\domain\entity\contractSettlement\GoodsExpenseSettlementItem;
use ddd\domain\entity\contractSettlement\GoodsExpenseItem;
use ddd\domain\entity\contractSettlement\TaxItem;
use ddd\domain\entity\contractSettlement\OtherExpenseItem;
use ddd\domain\entity\value\Attachment;
use ddd\domain\entity\contractSettlement\LadingBillSettlementItem;
use ddd\domain\entity\contractSettlement\DeliveryOrderSettlementItem;
use ddd\repository\stock\LadingBillRepository;
use ddd\repository\stock\DeliveryOrderRepository;
use ddd\repository\GoodsRepository;

class SettlementGoodsDTO extends BaseDTO
{
 
    public $item_id;
    /**
     * @var      int  商品id
     */
    public $goods_id;
    /**
     * @var      string  商品名称
     */
    public $goods_name;
    /**
     * @var      int  入库通知单id
     */
    public $batch_id;
    /**
     * @var      int  发货单id
     */
    public $order_id;
    /**
     * @var      int  发货单id 或入库通知单id
     */
    public $bill_id;

    /**
    * @var      string   入库通知单编号
    */
    public $batch_code;
    /**
     * @var      string   发货单编号
     */
    public $delivery_code;

    /**
    * @var      object   结算数量
    */
    public $quantity;  
   
    /**
     * @var      object   结算数量  子单位
     */
    public $quantity_sub;  
    
    /**
    * @var      object   损耗量
    */
    public $quantity_loss;
    /**
     * @var      object   损耗量 子单位
     */
    public $quantity_loss_sub;
    
    /**
    * @var      float  结算单价
    */
    public $price;  
    
    /**
    * @var      float  结算金额
    */
    public $amount;

    /**
    * @var      object  入库单数量
    */
    public $in_quantity;
    /**
     * @var      object  入库单数量  子单位
     */
    public $in_quantity_sub;
    /**
     * @var      object  出库单数量
     */
    public $out_quantity;
    /**
     * @var      object  出库单数量 子单位
     */
    public $out_quantity_sub;

    /**
    * @var      float  人民币结算单价
    */
    public $price_cny; 
    
    /**
    * @var      float  人民币结算金额
    */ 
    public $amount_cny;
    /**
    * @var      float  结算汇率
    */
    public $unit_rate;
    /**
     * @var      boolean  是否有明细录入
     */
    public $hasDetail=false;
    /**
     * @var      string  备注
     */
    public $remark;
    /**
     * @var      array   入库通知单信息
     */
    public $lading_items;
    /**
     * @var      array   发货单信息
     */
    public $order_items;
    /**
     * @var      array   结算附件
     */
    public $settleFiles;
    /**
     * @var      array   其他单附件
     */
    public $goodsOtherFiles; 
    /**
     * @var      object   计算明细信息
     */
    public $settlementGoodsDetail;


    public function rules()
    {
        return array(
            array("goods_id", "numerical", "integerOnly" => true, "min" => 0, "message" => "商品id必须为大于0的整数"),
            array("price", "numerical","integerOnly" => true, "allowEmpty"=>false, "min" => 0, "tooSmall" => "金额{attribute}必须大于0"),
            array("amount", "numerical","integerOnly" => true, "allowEmpty"=>false, "min" => 0, "tooSmall" => "金额{attribute}必须大于0" ),
            array("unit_rate", "numerical", "min" => 0, "tooSmall" => "结算汇率必须为大于0的数值"),
            array("price_cny", "numerical","integerOnly" => true,  "min" => 0, "tooSmall" => "金额{attribute}必须大于0"),
            array("amount_cny", "numerical","integerOnly" => true,  "min" => 0, "tooSmall" => "金额{attribute}必须大于0"),
            array("quantity", "validQuantity",'prefix'=>'结算数量'),
            
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
    public function fromEntity(BaseEntity $entity)
    {
        $values=$entity->getAttributes();
        $this->setAttributes($values);
        $this->batch_id = $entity->relation_id;
        $this->order_id = $entity->relation_id;
        $this->batch_id = $entity->relation_id;
        $LadingBill = LadingBillRepository::repository()->findByPk($entity->relation_id);
        $this->batch_code = $LadingBill->code;
        $DeliveryOrder = DeliveryOrderRepository::repository()->findByPk($entity->relation_id);
        $this->delivery_code = $DeliveryOrder->code;
        $goods = GoodsRepository::repository()->findByPk($entity->goods_id);
        $this->goods_name = $goods->name; 
        $this->quantity = $entity->settle_quantity;
        $this->quantity_sub = $entity->settle_quantity_sub;
        $this->quantity_loss = $entity->loss_quantity;
        $this->quantity_loss_sub = $entity->loss_quantity_sub;
        $this->price = $entity->settle_price;
        $this->amount = $entity->settle_amount;
        $this->in_quantity =$entity->in_quantity; //入库才有
        $this->in_quantity_sub = $entity->in_quantity_sub; //入库才有
        $this->out_quantity = $entity->out_quantity; //出库才有
        $this->out_quantity_sub =$entity->out_quantity_sub; //出库才有
        $this->price_cny = $entity->settle_price_cny;
        $this->amount_cny = $entity->settle_amount_cny;
        $this->unit_rate = $entity->exchange_rate;
        $this->hasDetail = $entity->isHaveDetail;
        
        //计算明细
        $adjustment_items=new SettlementGoodsDetailDTO();
        if(!empty($entity->adjustment_items))
        $adjustment_items->fromEntity($entity->adjustment_items);
        //$adjustment_items->remark=$entity->remark;
        //货款明细
        $adjustment_items->currency = $entity->goods_expense_items->currency;
        $adjustment_items->price_goods = $entity->goods_expense_items->price;
        $adjustment_items->amount_currency = $entity->goods_expense_items->amount;
        $adjustment_items->exchange_rate = $entity->goods_expense_items->exchange_rate;
        $adjustment_items->amount_goods = $entity->goods_expense_items->amount_cny;
        $adjustment_items->amount_goods_tax =$entity->goods_expense_items->tax_amount_cny;
        $adjustment_items->exchange_rate_tax =$entity->goods_expense_items->tax_exchange_rate;
        //相关税收
        if(is_array($entity->tax_items)){
            foreach ($entity->tax_items as $k=>$v){
                $tax_items = new SettlementGoodsDetailItemDTO();
                $tax_items->subject_list = $v->tax;
                $tax_items->rate = $v->tax_rate;
                $tax_items->price = $v->tax_price;
                $tax_items->amount = $v->tax_amount;
                $tax_items->remark = $v->remark;
                $adjustment_items->tax_detail_item[]=$tax_items;
            }
        }
        //其他费用
        if(is_array($entity->other_expense_items)){
            foreach ($entity->other_expense_items as $k=>$v){
                $other_items = new SettlementGoodsDetailItemDTO();
                $other_items->subject_list = $v->expense;
                $other_items->price = $v->expense_price;
                $other_items->amount = $v->expense_amount;
                $other_items->remark = $v->remark;
                $adjustment_items->other_detail_item[]=$other_items;
            }
        }
        
        //单据附件
        if(is_array($entity->receipt_attachments)&&!empty($entity->receipt_attachments)){
            foreach ($entity->receipt_attachments as $k=>$v){
                $settle_file_items= new AttachmentDTO();
                $settle_file_items->id = $v->id;
                $settle_file_items->name = $v->name;
                $settle_file_items->file_url = $v->file_url;
                $this->settleFiles[] = $settle_file_items;
            }
        }
        //其他附件
        if(is_array($entity->other_attachments)&&!empty($entity->other_attachments)){
            foreach ($entity->other_attachments as $k=>$v){
                $other_file_items= new AttachmentDTO();
                $other_file_items->id = $v->id;
                $other_file_items->name = $v->name;
                $other_file_items->file_url = $v->file_url;
                $this->goodsOtherFiles[] = $other_file_items;
            }
        }
        //lading_items
        $this->lading_items=[];
        if(is_array($entity->lading_items)){
            foreach ($entity->lading_items as $k=>$v){
                $lading_item = new SettlementGoodsDTO();
                
                $lading_item->batch_id = $v->batch_id;
                $lading_item->bill_id = $lading_item->batch_id;
                $LadingBill = LadingBillRepository::repository()->findByPk($lading_item->batch_id);
                $lading_item->batch_code = $LadingBill->code;
                $lading_item->item_id = $v->item_id;
                $lading_item->goods_id = $entity->goods_id;
                $goods = GoodsRepository::repository()->findByPk($v->goods_id);
                $lading_item->goods_name = $goods->name;
                $lading_item->quantity =$v->settle_quantity;
                $lading_item->quantity_sub =$v->settle_quantity_sub;
                $lading_item->quantity_loss =$v->loss_quantity;
                $lading_item->quantity_loss_sub =$v->loss_quantity_sub;
                $lading_item->price =$v->settle_price;
                $lading_item->amount = $v->settle_amount;
                $lading_item->in_quantity = $v->in_quantity;//入库才有
                $lading_item->in_quantity_sub = $v->in_quantity_sub;//入库才有
                $lading_item->price_cny = $v->settle_price_cny;
                $lading_item->amount_cny = $v->settle_amount_cny;
                $lading_item->unit_rate = $v->exchange_rate;
                $this->lading_items[] = $lading_item;
            }
        }
        //order_items
        $this->order_items=[];
        if(is_array($entity->order_items)){
            foreach ($entity->order_items as $k=>$v){
                $order_item = new SettlementGoodsDTO();
                
                $order_item->order_id = $v->order_id;
                $order_item->bill_id =  $order_item->order_id;
                $DeliveryOrder = DeliveryOrderRepository::repository()->findByPk($order_item->order_id);
                $order_item->delivery_code = $DeliveryOrder->code;
                $order_item->item_id = $v->item_id;
                $order_item->goods_id = $v->goods_id;
                $goods = GoodsRepository::repository()->findByPk($v->goods_id);
                $order_item->goods_name = $goods->name;
                $order_item->quantity = $v->settle_quantity;
                $order_item->quantity_sub =$v->settle_quantity_sub;
                $order_item->quantity_loss =$v->loss_quantity;
                $order_item->quantity_loss_sub =$v->loss_quantity_sub;
                $order_item->price = $v->settle_price;
                $order_item->amount = $v->settle_amount;
                $order_item->out_quantity =$v->out_quantity;//出库才有
                $order_item->out_quantity_sub = $v->out_quantity_sub;//出库才有
                $order_item->price_cny = $v->settle_price_cny;
                $order_item->amount_cny = $v->settle_amount_cny;
                $order_item->unit_rate = $v->exchange_rate;
                $this->order_items[] = $order_item;
            }
        }
        
        $this->settlementGoodsDetail=$adjustment_items;
       
    }

    /**
     * 转换成实体对象
     * @return LadingBill
     */
    public function toEntity()
    {
        $entity= new GoodsExpenseSettlementItem();
        $entity->setAttributes($this->getAttributes());
        $entity->relation_id = $this->batch_id;
        $entity->goods_id= $this->goods_id;
        $entity->settle_quantity= $this->quantity;
        $entity->settle_quantity_sub= $this->quantity_sub;
        $entity->loss_quantity = $this->quantity_loss;
        $entity->loss_quantity_sub = $this->quantity_loss_sub;
        $entity->settle_price = $this->price;
        $entity->settle_amount = $this->amount;
        $entity->in_quantity = $this->in_quantity;
        $entity->in_quantity_sub = $this->in_quantity_sub;
        $entity->out_quantity = $this->out_quantity;
        $entity->out_quantity_sub = $this->out_quantity_sub;
        $entity->settle_price_cny = $this->price_cny;
        $entity->settle_amount_cny = $this->amount_cny;
        $entity->exchange_rate = $this->unit_rate; 
        $entity->isHaveDetail = ($this->hasDetail)==1?true:false;
        $entity->remark = $this->remark; 
        //计算明细
        if($this->hasDetail)
        $entity->adjustment_items[] = $this->settlementGoodsDetail->toEntity();
        //货款明细
        $goods_expense_items = new GoodsExpenseItem();
        $goods_expense_items->currency =$this->settlementGoodsDetail->currency;
        $goods_expense_items->price =$this->settlementGoodsDetail->price_goods;
        $goods_expense_items->amount=$this->settlementGoodsDetail->amount_currency;
        $goods_expense_items->exchange_rate = $this->settlementGoodsDetail->exchange_rate;
        $goods_expense_items->amount_cny =$this->settlementGoodsDetail->amount_goods;
        $goods_expense_items->tax_amount_cny = $this->settlementGoodsDetail->amount_goods_tax;
        $goods_expense_items->tax_exchange_rate = $this->settlementGoodsDetail->exchange_rate_tax;
        $cid=isset($goods_expense_items->currency->id)?$goods_expense_items->currency->id:0;
        if($this->hasDetail)
        $entity->goods_expense_items[$cid] = $goods_expense_items;
        //相关税收
        if(is_array($this->settlementGoodsDetail->tax_detail_item))
        {
            foreach ($this->settlementGoodsDetail->tax_detail_item as $k=>$v)
            {
                $tax_items = new TaxItem();
                $tax_items->tax = $v->subject_list;
                $tax_items->tax_rate = $v->rate;
                $tax_items->tax_price = $v->price;
                $tax_items->tax_amount= $v->amount;
                $tax_items->remark = $v->remark;
                $tid=$tax_items->tax->id;
                $entity->tax_items[$tid]=$tax_items;
            }
        } 
       
        //其他费用
        if(is_array($this->settlementGoodsDetail->other_detail_item))
        {
            foreach ($this->settlementGoodsDetail->other_detail_item as $k=>$v)
            {
                $other_items = new OtherExpenseItem();
                $other_items->expense = $v->subject_list;
                $other_items->expense_amount = $v->amount;
                $other_items->expense_price = $v->price;
                $other_items->remark= $v->remark;
                $oid=$other_items->expense->id;
                $entity->other_expense_items[$oid]=$other_items;
            }
        } 
        //单据附件
        if(is_array($this->settleFiles))
        {
            foreach ($this->settleFiles as $k=>$v)
            {
                $receipt_attachments = new Attachment();
                $receipt_attachments->id = $v->id;
                $receipt_attachments->type = $v->type;
                $receipt_attachments->name = $v->name;
                $receipt_attachments->file_url= $v->file_url;
                $fid=$receipt_attachments->id;
                $entity->receipt_attachments[$fid]=$receipt_attachments;
            }
        } 
        //其他附件
        if(is_array($this->goodsOtherFiles))
        {
            foreach ($this->goodsOtherFiles as $k=>$v)
            {
                $other_attachments = new Attachment();
                $other_attachments->id = $v->id;
                $other_attachments->type = $v->type;
                $other_attachments->name = $v->name;
                $other_attachments->file_url= $v->file_url;
                $fid=$receipt_attachments->id;
                $entity->other_attachments[$fid]=$other_attachments;
            }
        } 
        //lading_items
        $entity->lading_items=[];
        if(is_array($this->lading_items))
        {
            foreach ($this->lading_items as $k=>$v)
            {
                $lading_items = new LadingBillSettlementItem();
                $lading_items->batch_id = $v->batch_id;
                $lading_items->goods_id = $v->goods_id;
                $lading_items->item_id = $v->item_id;
                $lading_items->settle_quantity = $v->quantity; 
                $lading_items->settle_quantity_sub = $v->quantity_sub; 
                $lading_items->loss_quantity =  $v->quantity_loss;
                $lading_items->loss_quantity_sub =  $v->quantity_loss_sub;
                $lading_items->settle_price = $v->price;
                $lading_items->settle_amount = $v->amount;
                $lading_items->in_quantity =$v->in_quantity;
                $lading_items->in_quantity_sub = $v->in_quantity_sub;
                $lading_items->settle_price_cny = $v->price_cny;
                $lading_items->settle_amount_cny = $v->amount_cny;
                $lading_items->exchange_rate = $v->unit_rate;
                $goods_id=$v->goods_id;
                $entity->lading_items[]=$lading_items;
            }
        } 
        //order_items
        $entity->order_items=[];
        if(is_array($this->order_items))
        {
            foreach ($this->order_items as $k=>$v)
            { 
                $order_items = new DeliveryOrderSettlementItem();
                $order_items->order_id = $v->order_id;
                $order_items->goods_id = $v->goods_id;
                $order_items->item_id = $v->item_id;
                $order_items->settle_quantity = $v->quantity;
                $order_items->settle_quantity_sub = $v->quantity_sub;
                $order_items->loss_quantity = $v->quantity_loss; 
                $order_items->loss_quantity_sub = $v->quantity_loss_sub; 
                $order_items->settle_price = $v->price;
                $order_items->settle_amount = $v->amount;
                $order_items->out_quantity =$v->out_quantity; 
                $order_items->out_quantity_sub =$v->out_quantity_sub; 
                $order_items->settle_price_cny = $v->price_cny;
                $order_items->settle_amount_cny = $v->amount_cny;
                $order_items->exchange_rate = $v->unit_rate;
                $goods_id=$v->goods_id;
                $entity->order_items[]=$order_items;
            }
        } 
     
        return $entity;
    }
    //验证数量
    public function validQuantity($attribute,$params)
    {
        $attr = $this->$attribute;//当前属性
        if(empty($attr->quantity))
            $this->addError($attribute, $params['prefix'].'不能为空');
    }
    
}