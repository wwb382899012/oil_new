<?php
/**
 * User: liyu
 * Date: 2018/8/23
 * Time: 18:32
 * Desc: ProjectPayment.php
 */

namespace ddd\Profit\Domain\Model\Payment;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;
use ddd\Profit\Domain\Model\Project;

class ProjectPayment extends BaseEntity implements IAggregateRoot
{

    /**
     * @var 标识
     */
    public $id;

    /**
     * @var 项目ID
     */
    public $project_id;

    /**
     * @var 付款金额
     */
    public $pay_amount;

    /**
     * @var 杂费
     */
    public $miscellaneous_fee;


    public function getId() {
        return $this->id;
    }

    public function setId($value) {
        $this->id = $value;
    }

    public static function create(Project $project = null) {
        $entity = new static();
        if (!empty($project)) {
            $entity->project_id = $project->project_id;
        }
        return $entity;
    }
}