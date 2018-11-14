<?php
/**
 * Created by PhpStorm.
 * User: liyu
 * Date: 2018/6/11
 * Time: 16:57
 * Desc:合同终止DTO
 */

namespace ddd\Split\Dto\ContractTerminate;

use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\Split\Domain\Model\Contract\ContractTerminate;
use ddd\Split\Dto\AttachmentDTO;
use ddd\Split\Dto\CheckLogDTO;

class ContractTerminateDTO extends BaseDTO
{
    #region property

    public $id=0;

    /**
     * 合同ID
     * @var   int
     */
    public $contract_id;

    /**
     * 终止理由
     * @var   string
     */
    public $reason;

    /**
     * 附件
     * @var   array
     */
    public $files = [];


    #endregion

    /**
     * rules
     */
    public function rules() {
        return [
            ['reason', "required", "message" => "请填写合同终止理由"]
        ];
    }

    /**
     * 从实体对象生成DTO对象
     */
    public function fromEntity(BaseEntity $entity) {
        $values = $entity->getAttributes();
        unset($values['files']);
        $this->setAttributes($values);
        if (\Utility::isNotEmpty($entity->files)) {
            foreach ($entity->files as $k => $file) {
                $item = new AttachmentDTO();
                $item->fromEntity($file);
                $this->files[] = $item;
            }
        }
    }

    /**
     * 转换成实体对象
     */
    public function toEntity() {
        $entity = ContractTerminate::create();
        $entity->setAttributes($this->getAttributes());
        if (\Utility::isNotEmpty($this->files)) {
            foreach ($this->files as $k => $file) {
                $entity->addFiles($file->toEntity());
            }
        }
        return $entity;
    }

}