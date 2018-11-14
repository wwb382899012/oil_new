<?php
/**
 * User: liyu
 * Date: 2018/8/9
 * Time: 15:49
 * Desc: PayReceiveAmount.php
 */

namespace ddd\Profit\Domain\Model\Payment;


use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\contract\Contract;
use ddd\infrastructure\DIService;
use ddd\infrastructure\Utility;

class PayReceiveAmount extends BaseEntity
{
    #region property

    /**
     * 合同实付金额
     * @var   Money
     */
    private $pay_confirm_amount = [];

    /**
     * 合同后补认领金额
     * @var   Money
     */
    private $pay_claim_amount = [];

    /**
     * 合同银行流水认领金额
     * @var   Money
     */
    private $receive_confirm_amount = [];

    /**
     * 合同Id
     * @var   int
     */
    public $contract_id;

    /**
     * @var 合同类型
     */
    public $category;


    /**
     * @var 代理模式
     */
    public $agent_type;


    /**
     * 合同类型
     */

    public $type;

    /**
     * @var 用途
     */
    public $subject;

    /**
     * 合同实付详细
     * @var array
     */
    public $receive_confirm_detail = [];

    /**
     * 合同银行流水认领成功事件
     */
    const EVENT_RECEIVE_CONFIRM = "onReceiveConfirm";

    /**
     * 合同后补认领付款成功
     */
    const EVENT_PAY_CLAIM = "onPayClaim";

    /**
     * 合同下付款实付成功
     */
    const EVENT_PAY_CONFIRM = "onPayConfirm";


    #endregion

    /**
     * 事件配置，事件名必须以on开头，否则无效
     * @return array
     */
    protected function events() {
        return [
            static::EVENT_RECEIVE_CONFIRM,
            static::EVENT_PAY_CLAIM,
            static::EVENT_PAY_CONFIRM,
        ];
    }

    /**
     * @desc 杂费Array
     * @var array
     */
    public $miscellaneous_subject = [
        \ContractOverdue::SUBJECT_TYPE_ONE,
//        \ContractOverdue::SUBJECT_TYPE_FOUR,
//        \ContractOverdue::SUBJECT_TYPE_FIVE,
        \ContractOverdue::SUBJECT_TYPE_SEVEN,
        \ContractOverdue::SUBJECT_TYPE_EIGHT
    ];

    public static function create(Contract $contract = null, $subject = 0) {
        if (empty($contract)) {
            ExceptionService::throwArgumentNullException("Contract对象", array('class' => get_called_class(), 'function' => __FUNCTION__));
        }
        $entity = new static();
        $entity->contract_id = $contract->contract_id;
        $entity->category = $contract->category;
        $entity->agent_type = $contract->agent_type;
        $entity->type = $contract->type;
        $entity->subject = $subject;
        $entity->receive_confirm_detail = DIService::getRepository(IReceiveConfirmRepository::class)->findByContract($contract->contract_id);
        return $entity;
    }

    /**
     * 获取合同实付金额
     */
    public function getPayConfirmAmount($subject_list = []) {
        $key = !empty($subject_list) ? 'miscellaneousFee' : 'normalFee';
        if (!$this->pay_confirm_amount[$key]) {
            $amount = 0;
            $contractActualPayAmount = $this->getContractActualPayInfo($subject_list);
            $multiContractActualPayAmount = $this->getMultiContractActualPayInfo($subject_list);
            $this->pay_confirm_amount['miscellaneousFee'] = $contractActualPayAmount['miscellaneousAmount'] + $multiContractActualPayAmount['miscellaneousAmount'];//TODO
            $this->pay_confirm_amount['normalFee'] = $contractActualPayAmount['amount'] + $multiContractActualPayAmount['amount'];//TODO
        }
        return $this->pay_confirm_amount[$key];
    }

    /**
     * @desc 获取多合同付款实付金额
     */
    private function getMultiContractActualPayInfo($subject_list = []) {
        $multiPay = \InterestReportService::getMultiContractActualPayInfo($this->contract_id);
        $amount = 0;
        $miscellaneousAmount = 0;
        if ($this->category == \ConstantMap::BUY_SALE_CONTRACT_TYPE_AGENT_IMPORT and $this->agent_type == \ConstantMap::AGENT_TYPE_BUY_SALE) {
            $this->miscellaneous_subject[] = \ContractOverdue::SUBJECT_TYPE_SIX;
        }
        if (!empty($multiPay)) {
            foreach ($multiPay as $multi_key => $multi_value) {
                if (in_array($multi_value['subject_id'], $this->miscellaneous_subject)) {
                    $miscellaneousAmount += $multi_value['amount_cny'];
                }
                $amount += $multi_value['amount_cny'];
            }
        }
        return ['amount' => $amount, 'miscellaneousAmount' => $miscellaneousAmount];
    }

    /**
     * @desc 合同下付款
     */
    private function getContractActualPayInfo($subject_list = []) {
        $actualPay = \InterestReportService::getActualPayInfo($this->contract_id);
        $amount = 0;
        $miscellaneousAmount = 0;
        if ($this->category == \ConstantMap::BUY_SALE_CONTRACT_TYPE_AGENT_IMPORT and $this->agent_type == \ConstantMap::AGENT_TYPE_BUY_SALE) {
            $this->miscellaneous_subject[] = \ContractOverdue::SUBJECT_TYPE_SIX;
        }
        if (!empty($actualPay)) {
            foreach ($actualPay as $actual_key => $actual_value) {
                if (in_array($actual_value['subject_id'], $this->miscellaneous_subject)) {
                    $miscellaneousAmount += $actual_value['amount_cny'];
                }
                $amount += $actual_value['amount_cny'];
            }
        }
        return ['amount' => $amount, 'miscellaneousAmount' => $miscellaneousAmount];
    }

    /**
     * 获取合同后补认领金额
     */
    public function getPayClaimAmount($subject_list = []) {
        $key = !empty($subject_list) ? 'miscellaneousFee' : 'normalFee';
        if ($this->category == \ConstantMap::BUY_SALE_CONTRACT_TYPE_AGENT_IMPORT and $this->agent_type == \ConstantMap::AGENT_TYPE_BUY_SALE) {
            $this->miscellaneous_subject[] = \ContractOverdue::SUBJECT_TYPE_SIX;
        }
        if (!$this->pay_claim_amount[$key]) {
            $amount = 0;
            $miscellaneousAmount = 0;
            $payClaim = \InterestReportService::getClaimInfo($this->contract_id);
            if (!empty($payClaim)) {
                foreach ($payClaim as $claim_key => $claim_value) {
                    if (in_array($claim_value['subject_id'], $this->miscellaneous_subject)) {
                        $miscellaneousAmount += $claim_value['amount_cny'];
                    }
                    $amount += $claim_value['amount_cny'];
                }
            }
            $this->pay_claim_amount['miscellaneousFee'] = $miscellaneousAmount;//TODO
            $this->pay_claim_amount['normalFee'] = $amount;//TODO
        }
        return $this->pay_claim_amount[$key];
    }

    /**
     * 获取合同银行流水认领金额
     */
    public function getReceiveConfirmAmount($subject_list = []) {
        $key = !empty($subject_list) ? 'miscellaneousFee' : 'normalFee';
        if ($this->category == \ConstantMap::BUY_SALE_CONTRACT_TYPE_AGENT_IMPORT and $this->agent_type == \ConstantMap::AGENT_TYPE_BUY_SALE) {
            $this->miscellaneous_subject[] = \ContractOverdue::SUBJECT_TYPE_SIX;
        }
        if (!$this->receive_confirm_amount[$key]) {
            $amount = 0;
            $miscellaneousAmount = 0;
            if (\Utility::isNotEmpty($this->receive_confirm_detail)) {
                foreach ($this->receive_confirm_detail as $item) {
                    if ($item->isSubmitted()) {
                        if (in_array($item->subject, $this->miscellaneous_subject)) {
                            $miscellaneousAmount += $item->amount_cny;
                        }
                        $amount += $item->amount_cny;
                    }
                }
            }
            $this->receive_confirm_amount['normalFee'] = $amount;
            $this->receive_confirm_amount['miscellaneousFee'] = $miscellaneousAmount;
        }
        return $this->receive_confirm_amount[$key];
    }


    /**
     * 合同银行流水认领成功  事件
     * @param    boolean $persistent
     * @throws   \Exception
     */
    public function receiveConfirm() {
        $this->publishEvent(static::EVENT_RECEIVE_CONFIRM, new ReceiveConfirmEvent($this));
    }

    /**
     * 合同后补认领付款成功  事件
     * @param    boolean $persistent
     * @throws   \Exception
     */
    public function payClaim() {
        $this->publishEvent(static::EVENT_PAY_CLAIM, new PayClaimEvent($this));
    }

    /**
     * 合同后补认领付款成功  事件
     * @param    boolean $persistent
     * @throws   \Exception
     */
    public function payConfirm() {
        $this->publishEvent(static::EVENT_PAY_CONFIRM, new PayConfirmEvent($this));
    }


}