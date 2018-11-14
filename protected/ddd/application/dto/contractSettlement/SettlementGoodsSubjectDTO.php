<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 10:32
 * Describe：结算项费用明细  DTO
 */

namespace ddd\application\dto\contractSettlement;

use ddd\Common\Application\BaseDTO;
use ddd\application\dto\AttachmentDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\value\Attachment;
use ddd\domain\entity\stock\LadingBill;
use ddd\domain\entity\contractSettlement\OtherExpenseSettlementItem;

class SettlementGoodsSubjectDTO extends BaseDTO
{
    
    /**
     * 标志id(文件上传)
     * @var      int
     */
    public $detail_id;
    
    /**
     * 财务科目
     * @var      object
     */
    public $fee;
    
    /**
     * 币种
     * @var      object
     */
    public $currency;
    
    /**
     * 金额
     * @var      float
     */
    public $amount;
    
    /**
     * 人民币金额
     * @var      float
     */
    public $amount_cny;
    
    /**
     * 汇率
     * @var      float
     */
    public $exchange_rate;
    
    /**
     * 备注
     * @var      string
     */
    public $remark;

    /**
     * 非货款附件
     * @var      array
     */
    public $otherFiles;



    public function rules()
    {
        return array(
            //array("detail_id", "numerical","allowEmpty"=>false, "integerOnly" => true, "min" => 0,"tooSmall" => "detail_id必须为大于0的数值", "message" => "detail_id必须为大于0的整数"),
            array("fee", "validObject", "prefix" => "非货款类科目"),
            array("currency", "validObject", "prefix" => "非货款类币种"),
            array("amount", "numerical","integerOnly" => true,  "min" => 0, "tooSmall" => "金额{attribute}必须大于0"),
            array("amount_cny", "numerical","integerOnly" => true,  "min" => 0, "tooSmall" => "金额{attribute}必须大于0"),
            array("exchange_rate", "required", "message" => "非货款类结算汇率不能为空"),

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
        unset($values['receipt_attachments']);
        $this->setAttributes($values);
        //结算附件
        if(is_array($entity->receipt_attachments)){
            foreach ($entity->receipt_attachments as $k=>$v){
                $receipt_attachments= new AttachmentDTO();
                $receipt_attachments->id = $v->id;
                $receipt_attachments->name = $v->name;
                $receipt_attachments->file_url = $v->file_url;
                $this->otherFiles[] = $receipt_attachments;
            }
        }
        
       
    }

    /**
     * 转换成实体对象
     * @return LadingBill
     */
    public function toEntity()
    {
       
        $entity=new OtherExpenseSettlementItem();
        $entity->setAttributes($this->getAttributes());
        $entity->fee = $this->fee;
        $entity->currency = $this->currency;
        
        //结算附件
        if(is_array($this->otherFiles))
        {
            foreach ($this->otherFiles as $k=>$v)
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
        return $entity;
    }
    //验证
    public function validObject($attribute,$params)
    {
        $attr = $this->$attribute;//当前属性
        if(empty($attr->id))
            $this->addError($attribute,$params['prefix'].'不能为空');
    }
    
}