<?php

namespace ddd\application\dto\contract;

use ddd\application\dto\AttachmentDTO;
use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\repository\contract\ContractRepository;
use ddd\Contract\Repository\Project\ProjectRepository;

/**
 * @Name            合同上传DTO
 * @DateTime        2018年5月11日 18:40:11
 * @Author          Administrator
 */
class ContractFileUploadDTO extends BaseDTO
{
    #region property

    /**
     * 合同ID 
     * @var   int
     */
    public $contract_id;

    /**
     * 合同编号 
     * @var   varchar
     */
    public $contract_code;

    /**
     * 项目ID 
     * @var   int
     */
    public $project_id;

    /**
     * 项目编码 
     * @var   varchar
     */
    public $project_code;

    /**
     * 是否主合同 
     * @var   int
     */
    public $is_main;

    /**
     * 对方合同编号 
     * @var   varchar
     */
    public $code_out;

    /**
     * 合同类型 
     * @var   int
     */
    public $contract_type;

    /**
     * 合同类别 
     * @var   int
     */
    public $category;

    /**
     * 版本类别 
     * @var   int
     */
    public $version_type;

    /**
     * 合同状态 
     * @var   int
     */
    public $status;

    /**
     * 合同文件 
     * @var   AttachmentDTO
     */
    public $files;

    #endregion

    /**
     * 实体对象生成DTO对象
     */
    public function fromEntity(BaseEntity $entity) {
        $values = $entity->getAttributes();
        if ($entity->contract_id) {
            $contract = ContractRepository::repository()->findByPk($entity->contract_id);
            $this->contract_code = $contract->contract_code;
        }
        if ($entity->project_id) {
            $project = ProjectRepository::repository()->findByPk($entity->project_id);
            $this->project_code = $project->project_code;
        }
        $this->setAttributes($values);
    }

    /**
     * DTO对象转实体对象
     */
    public function toEntity() {
        $entity = new ContractFile(); //TODO ContractFile Entity
        $entity->setAttributes($this->getAttributes());
    }

}
