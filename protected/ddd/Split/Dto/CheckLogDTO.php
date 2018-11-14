<?php

/**
 * Created by IntelliJ IDEA.
 * User: Administrator
 * Date: 2018/6/8
 * Time: 15:20
 */

namespace ddd\Split\Dto;


use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;

/**
 * 审核记录DTO
 * Class CheckLogDTO
 * @package ddd\Split\Dto
 */
class CheckLogDTO extends BaseDTO
{

    /**
     * 审核结果
     * @var string
     */
    public $result;

    /**
     * 审核备注
     * @var string
     */
    public $remark;

    /**
     * 审核节点
     * @var string
     */
    public $node_name;

    /**
     * 审核人
     * @var string
     */
    public $checker;

    /**
     * 审核时间
     * @var string
     */
    public $check_time;


    public function rules() {
        return [];
    }

    /**
     * 从实体对象生成DTO对象
     * @param BaseEntity $entity
     * @return $this|void
     * @throws \Exception
     */
    public function fromEntity(BaseEntity $entity) {
        $this->setAttributes($entity->getAttributes());
        return $this;
    }
}