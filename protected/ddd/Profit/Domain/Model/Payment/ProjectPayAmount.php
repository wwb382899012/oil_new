<?php
/**
 * User: liyu
 * Date: 2018/8/10
 * Time: 18:08
 * Desc: ProjectPayAmount.php
 */

namespace ddd\Profit\Domain\Model\Payment;


use ddd\Common\Domain\BaseEntity;
use ddd\infrastructure\error\ExceptionService;

class ProjectPayAmount extends BaseEntity
{
    /**
     * @var 项目ID
     */
    public $project_id;

    /**
     * @var 实付金额
     */
    public $pay_confirm_amount = [];

    /**
     * @var 付款实付ID
     */
    public $payment_id;

    /**
     * 项目 下付款实付成功
     */
    const EVENT_PROJECT_PAY_CONFIRM = "onProjectPayConfirm";


    /**
     * 事件配置，事件名必须以on开头，否则无效
     * @return array
     */
    protected function events() {
        return [
            static::EVENT_PROJECT_PAY_CONFIRM,
        ];
    }

    /**
     * @desc 杂费Array
     * @var array
     */
    public static $miscellaneous_subject = [
        \ContractOverdue::SUBJECT_TYPE_ONE,
//        \ContractOverdue::SUBJECT_TYPE_FOUR,
//        \ContractOverdue::SUBJECT_TYPE_FIVE,
        \ContractOverdue::SUBJECT_TYPE_SEVEN,
        \ContractOverdue::SUBJECT_TYPE_EIGHT
    ];


    public static function create($project_id, $payment_id) {
        if (empty($project_id) || empty($payment_id)) {
            ExceptionService::throwArgumentNullException('project_id,payment_id');
        }
        $entity = new static();
        $entity->project_id = $project_id;
        $entity->payment_id = $payment_id;
        return $entity;
    }

    /**
     * 项目下 付款实付完成
     * @param    boolean $persistent
     * @throws   \Exception
     */
    public function payConfirm() {
        $this->publishEvent(static::EVENT_PROJECT_PAY_CONFIRM, new ProjectPayConfirmEvent($this));
    }


    /**
     * @desc 获取项目下付款实付金额
     */
    public function getPayConfirmAmount($miscellaneousFee = false) {
        $key = $miscellaneousFee ? 'miscellaneousFee' : 'normalFee';
        if (!$this->pay_confirm_amount[$key]) {
            $amount = 0;
            $sql = 'select ifnull(a.amount_cny, 0) total_amount from t_payment a 
                    left join t_pay_application b on b.apply_id = a.apply_id 
                    where a.payment_id=' . $this->payment_id . ' and a.status>=' . \Payment::STATUS_SUBMITED;
            if ($miscellaneousFee) {
                $sql .= ' AND b.subject_id NOT IN (' . implode(',', static::$miscellaneous_subject) . ')';
            }
            $data = \Utility::query($sql);
            if (\Utility::isNotEmpty($data)) {
                $amount = $data[0]['total_amount'];
            }
            $this->pay_confirm_amount[$key] = $amount;//TODO
        }
        return $this->pay_confirm_amount[$key];
    }

}