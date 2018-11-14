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
use ddd\domain\entity\stock\StockIn;


class StockInDTO extends BaseDTO
{
    public $items = array();
    public $check_remark;//审核意见
    public $store_name;//入库的仓库
    public $files;
    public function rules()
    {
        return array(
            array("stock_in_id","numerical","integerOnly"=>true,"min"=>1,"tooSmall"=>"入库单id必须为大于0的整数"),
            array("entry_date","date","format"=>"yyyy-MM-dd","allowEmpty"=>false),

        );
    }

   public function customAttributeNames()
    {
        return \StockIn::model()->attributeNames();
    }

    /**
     * 从实体对象生成DTO对象
     * @param BaseEntity $stockIn
     */
    public function fromEntity(BaseEntity $stockIn)
    {
        $values=$stockIn->getAttributes();
        unset($values['items']);
        unset($values['files']);
        $this->setAttributes($values);
        $checkLog = \FlowService::getCheckLog($values['stock_in_id'], "7");
        $this->check_remark = $checkLog[0]['remark'];
        $this->store_name = \StorehouseService::getStoreName($stockIn->store_id);
        if(is_array($stockIn->items))
        {
            foreach ($stockIn->items as $k=>$v)
            {
                $item=new StockInItemDTO();
                $item->fromEntity($v);
                $this->items[]=$item;
               
            }
        }
        if(is_array($stockIn->files))
        {
            foreach ($stockIn->files as $k=>$v)
            {
                $files=new AttachmentDTO();
                $files->fromEntity($v);
                $this->files[]=$files;
                
            }
        }
        
    }

    /**
     * 转换成实体对象
     * @params Contract $contractEntity
     * @return LadingBill
     */
    public function toEntity()
    {
        $entity = StockIn::create();

        $entity->setAttributes($this->getAttributes());
        $entity->items = array();
        if (is_array($this->items))
        {
            foreach ($this->items as $k => $v)
            {
                $entity->items[$v->goods_id] = $v->toEntity();
            }
        }

        return $entity;
    }

    public function validateLadingDate($attribute,$params)
    {
        $t=strtotime($this->$attribute);
        $createTime=isset($this->create_time)?strtotime($this->create_time):strtotime("now");
        $before=isset($params["before"])?$params["before"]:7;
        if($t<strtotime("- ".$before." days",$createTime))
        {
            $this->addError($attribute,'日期不得小于创建日期的前'.$before.'天');
        }

        if(isset($params["after"]))
        {
            if($t>strtotime("+ ".$params["after"]." days",$createTime))
                $this->addError($attribute,'日期不得晚于创建日期的后'.$params["after"].'天');
        }

    }
}