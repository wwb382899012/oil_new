<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 14:54
 * Describe：
 */


namespace ddd\Split\Dto;

use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\Attachment;

/**
 * 附件DTO
 * Class AttachmentDTO
 * @package ddd\Split\Dto
 */
class AttachmentDTO extends BaseDTO{

    public $id;           //标志id
    public $type;         //类型
    public $name;         //附件名称
    public $file_url;     //文件路径

    public function customAttributeNames(){
        return array();
    }

    /**
     * 从实体对象生成DTO对象
     * @param BaseEntity $entity
     * @throws \Exception
     */
    public function fromEntity(BaseEntity $entity){
        $values = $entity->getAttributes();
        $this->setAttributes($values);
    }

    /**
     * 转换成实体对象
     * @return Attachment
     * @throws \Exception
     */
    public function toEntity(){
        $entity =new Attachment();
        $entity->setAttributes($this->getAttributes());

        return $entity;
    }
}