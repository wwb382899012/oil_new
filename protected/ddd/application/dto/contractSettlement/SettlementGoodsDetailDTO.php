<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 10:32
 * Describe：结算计算明细  DTO
 */

namespace ddd\application\dto\contractSettlement;

use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\stock\LadingBill;
use ddd\domain\entity\contractSettlement\AdjustmentItem;

class SettlementGoodsDetailDTO extends BaseDTO
{
    /**
     * @var      object   计价币种
     */
    public $currency;
    /**
     * @var      float  货款单价
     */
    public $price_goods;
    /**
     * @var      float  计价币种货款金额
     */
    public $amount_currency;
    
    /**
     * @var      float   汇率
     */
    public $exchange_rate;
    
    /**
     * @var      float   人民币货款金额
     */
    public $amount_goods;
    
    /**
     * @var      float  计税汇率
     */
    public $exchange_rate_tax;
    
    /**
     * @var      float  计税人民币货款
     */
    public $amount_goods_tax;
    
    /**
     * @var      object  调整方式
     */
    public $adjust_type;
    /**
     * @var      float   调整金额
     */
    public $amount_adjust;
    
    /**
     * @var      string  调整原因
     */
    public $reason_adjust;
    
    /**
     * @var      object  结算数量
     */
    public $quantity;
    
    /**
     * @var      object  确定结算数量
     */
    public $quantity_actual;
    
    /**
     * @var      float  人民币结算金额
     */ 
    public $amount;
    
    /**
     * @var      float  确定人民币结算金额
     */
    public $amount_actual;
    
    /**
     * @var      float   人民币结算单价
     */
    public $price;
    
    /**
     * @var      int   确定人民币结算单价
     */
    public $price_actual;

    /**
     * @var      array   税收明细
     */
    public $tax_detail_item;
    
    /**
     * @var      array   其他费用明细
     */
    public $other_detail_item;


    public function rules()
    {
        return array(
            array("currency", "validCurrency"),
            array('adjust_type','validObject','prefix'=>'调整方式'),
            array("amount_adjust", "numerical","integerOnly" => true,"allowEmpty"=>false,  "min" => 0, "tooSmall" => "金额{attribute}必须大于0"),
            array("reason_adjust", 'validAdjustReason', 'prefix'=>'调整原因'),
            array("price", "numerical","integerOnly" => true,  "min" => 0, "tooSmall" => "金额{attribute}必须大于0"),
            array("price_actual", "numerical","integerOnly" => true,"allowEmpty"=>false,  "min" => 0, "tooSmall" => "金额{attribute}必须大于0"),
            array("amount", "numerical","integerOnly" => true,  "min" => 0, "tooSmall" => "金额{attribute}必须大于0"),
            array("amount_actual", "numerical","integerOnly" => true,"allowEmpty"=>false,  "min" => 0, "tooSmall" => "金额{attribute}必须大于0"),

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
        $this->adjust_type=isset($entry->adjust_type->id)?$entry->adjust_type->id:0;
        $this->amount_adjust=$entry->adjust_amount;
        $this->reason_adjust=$entry->adjust_reason;
        $this->quantity =$entry->settle_quantity;
        $this->quantity_actual=$entry->confirm_quantity;
        $this->price=$entry->settle_price_cny;
        $this->price_actual=$entry->confirm_price_cny;
        $this->amount=$entry->settle_amount_cny;
        $this->amount_actual=$entry->confirm_amount_cny;
    }

    /**
     * 转换成实体对象
     * @return LadingBill
     */
    public function toEntity()
    {
        $entity= new AdjustmentItem();
        $entity->setAttributes($this->getAttributes());
        $entity->adjust_type = $this->adjust_type;
        $entity->adjust_amount = $this->amount_adjust;
        $entity->adjust_reason = $this->reason_adjust;
        $entity->settle_quantity = $this->quantity;
        $entity->confirm_quantity = $this->quantity_actual;
        $entity->settle_price_cny = $this->price;
        $entity->confirm_price_cny =$this->price_actual;
        $entity->settle_amount_cny =$this->amount;
        $entity->confirm_amount_cny =$this->amount_actual;
        return $entity;
    }
    //计价币种 验证
    public function validCurrency($attribute)
    {
        $attr = $this->$attribute;//当前属性
        if(!empty($this->price_goods)||!empty($this->amount_currency)||!empty($this->exchange_rate)||!empty($this->amount_goods)||!empty($this->exchange_rate_tax)||!empty($this->amount_goods_tax)){
            if(empty($attr->id))
                $this->addError($attribute,'计价币种不能为空');
        }
       
    }
    //调整方式    验证（当调整金额不为0时，需必填）
    public function validObject($attribute,$params)
    {
        $attr = $this->$attribute;//当前属性
        if(!empty($this->amount_adjust)){
            if(empty($attr->id))
                $this->addError($attribute,$params['prefix'].'不能为空');
        }
    }
    //调整原因   验证（当调整金额不为0时，需必填）
    public function validAdjustReason($attribute,$params)
    {
        $attr = $this->$attribute;//当前属性
        if(!empty($this->amount_adjust)){
            if(empty($attr))
                $this->addError($attribute,$params['prefix'].'不能为空');
        }
        
        
    }
    
}