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
use ddd\domain\entity\stock\LadingBill;
use ddd\repository\contract\ContractRepository;
use ddd\repository\project\ProjectRepository;
use ddd\repository\PartnerRepository;
use ddd\repository\CorporationRepository;

class LadingBillDTO extends BaseDTO
{
    public $batch_code; //入库通知单编号
    public $contract_id; //合同id
    
    public $contract_code; //合同编号
    
    public $project_code;//项目编号
    
    public $partner_id; //合作方id
    
    public $partner_name; //合作方名称
    
    public $corporation_id; //交易主体id
    
    public $corporation_name; //交易主体名称
    
    public $remark; //备注
    
    public $status; //状态
    
    public $type; //类型
    
    public $files; //附件
    /**
     * @var array(goods_id=>LadingBillGoodsDTO)
     */
    public $items;
    public $settleItems;
    public $batch_id;
    public $batch_date;

    public function rules()
    {
        return array(
            array("contract_id", "numerical", "integerOnly" => true, "min" => 1, "tooSmall" => "合同id必须为大于0的整数"),
            array("batch_date", "date", "format" => "yyyy-MM-dd", "allowEmpty" => false),
            array("batch_date", "validateLadingDate", "before" => 30),
            );
    }

  /*   public function customAttributeNames()
    {
        return \StockNotice::model()->attributeNames();
    } */

    /**
     * 从实体对象生成DTO对象
     * @param BaseEntity $ladingBill
     */
    public function fromEntity(BaseEntity $ladingBill)
    {
        $values = $ladingBill->getAttributes();
        unset($values["items"]);
        unset($values['settleItems']);
        unset($values['files']);
        $this->setAttributes($values);
        $this->batch_id = $ladingBill->id;
        $this->batch_code = $ladingBill->code;
        $this->batch_date = $ladingBill->lading_date;
        if (is_array($ladingBill->items))
        {
            foreach ($ladingBill->items as $k => $v)
            {
                $item = new LadingBillGoodsDTO();
                $item->fromEntity($v);
                $this->items[] = $item;
            }
        }
        if (is_array($ladingBill->settleItems))
        {
            foreach ($ladingBill->settleItems as $k => $v)
            {
                $settleItem = new LadingBillSettlementDetailDTO();
                $settleItem->fromEntity($v);
                $this->settleItems[] = $settleItem;
            }
        }
        if (is_array($ladingBill->files))
        {
            foreach ($ladingBill->files as $k => $v)
            {
                $files= new AttachmentDTO();
                $files->fromEntity($v);
                $this->files[] = $files;
            }
        }
        
        $contract =  ContractRepository::repository()->findByPk($ladingBill->contract_id);
        $this->contract_code = $contract -> contract_code;
        $this->partner_id = $contract -> partner_id;
        $this->corporation_id = $contract -> corporation_id;
        $project =  ProjectRepository::repository()->findByPk($contract->project_id);
        $this->project_code = $project -> project_code;
        $partner = PartnerRepository::repository()->findByPk($contract->partner_id);
        $this->partner_name = $partner -> name;
        $corporation = CorporationRepository::repository()->findByPk($contract->corporation_id);
        $this->corporation_name = $corporation -> name;  
      
    }

    /**
     * 转换成实体对象
     * @return LadingBill
     */
    public function toEntity()
    {
        $entity = new LadingBill();

        $entity->setAttributes($this->getAttributes());
        $entity->id = $this->batch_id;
        $entity->lading_date = $this->batch_date;
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

    public function validateLadingDate($attribute, $params)
    {
        $t = strtotime($this->$attribute);
        $createTime = isset($this->create_time) ? strtotime($this->create_time) : strtotime("now");
        $before = isset($params["before"]) ? $params["before"] : 7;
        if ($t < strtotime("- " . $before . " days", $createTime))
        {
            $this->addError($attribute, '日期不得小于创建日期的前' . $before . '天');
        }

        if (isset($params["after"]))
        {
            if ($t > strtotime("+ " . $params["after"] . " days", $createTime))
            {
                $this->addError($attribute, '日期不得晚于创建日期的后' . $params["after"] . '天');
            }
        }

    }
}