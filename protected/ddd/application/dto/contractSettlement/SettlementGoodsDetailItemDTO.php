<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 10:32
 * Describe：结算项费用明细  DTO
 */

namespace ddd\application\dto\contractSettlement;

use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\stock\LadingBill;
use ddd\repository\LadingBillRepository;

class SettlementGoodsDetailItemDTO extends BaseDTO
{
    /**
     * @var      object  费用科目
     */
    public $subject_list;
    /**
     * @var      float   汇率
     */
    public $rate;
    
    /**
     * @var      float   人民币单价
     */
    public $price;   
    
    /**
     * @var      float   人民币总金额
     */
    public $amount;
    
    /**
     * @var      string   备注
     */
    public $remark;




    public function rules()
    {
        return array(
            array('subject_list','validObject','prefix'=>'科目'),
            array("price", "numerical","integerOnly" => true,  "min" => 0, "tooSmall" => "金额{attribute}必须大于0"),
            array("amount", "numerical","integerOnly" => true,  "min" => 0, "tooSmall" => "金额{attribute}必须大于0"),
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
        
       
    }

    /**
     * 转换成实体对象
     * @return LadingBill
     */
    public function toEntity()
    {
        $entity=LadingBill::create();
        $entity->setAttributes($this->getAttributes());
        $entity->id=$this->batch_id;
        $entity->lading_date=$this->batch_date;
        if(is_array($this->items))
        {
            foreach ($this->items as $k=>$v)
            {
                $entity->items[$v->goods_id]=$v->toEntity();
            }
        }
        return $entity;
    }
    //调整方式  验证
    public function validObject($attribute,$params)
    {
        $attr = $this->$attribute;//当前属性
        if(empty($attr->id))
            $this->addError($attribute,$params['prefix'].'不能为空');
    }
    
}