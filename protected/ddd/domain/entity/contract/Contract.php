<?php
/**
 * Created by youyi000.
 * DateTime: 2018/2/28 10:18
 * Describe：
 */

namespace ddd\domain\entity\contract;


use ddd\Common\Domain\BaseEntity;
use ddd\Contract\Domain\Model\Project\Project;
use ddd\domain\entity\stock\LadingBill;
use ddd\domain\event\contract\ContractBusinessRejectEvent;
use ddd\domain\event\contract\ContractDoneEvent;
use ddd\domain\event\contract\ContractRiskRejectEvent;
use ddd\domain\event\contract\ContractSettledEvent;
use ddd\domain\event\contract\ContractSettledRejectEvent;
use ddd\domain\event\contract\ContractSettlingEvent;
use ddd\domain\event\contract\ContractSubmitEvent;
use ddd\domain\event\subscribe\EventSubscribeService;
use ddd\Common\IAggregateRoot;
use ddd\domain\iRepository\contract\IContractRepository;
use ddd\domain\tRepository\contract\ContractRepository;
use ddd\infrastructure\error\BusinessError;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\Utility;
use ddd\Split\Domain\Model\Contract\ContractEnum;


class Contract extends BaseEntity implements IAggregateRoot
{

    #region Event

    /**
     * 合同提交事件
     */
    const EVENT_AFTER_SUBMIT = "onAfterSubmit";
    /**
     * 合同驳回事件
     */
    const EVENT_AFTER_BACK = "onAfterBack";

    /**
     * 结算驳回事件
     */
    const EVENT_AFTER_SETTLED_BACK = "onAfterSettledBack";
    /**
     * 开始结算事件
     */
    const EVENT_AFTER_SETTLING = "onAfterSettling";

    /**
     * 合同结算完成事件
     */
    const EVENT_AFTER_SETTLED = "onAfterSettled";

    /**
     * 合同完结事件
     */
    const EVENT_AFTER_DONE = "onAfterDone";

    #endregion

    #region public property

    /**
     * 合同id
     * @var   int
     */
    public $contract_id;

    /**
     * 合同编号
     * @var   string
     */
    public $contract_code;

    /**
     * 合同名称
     * @var   string
     */
    public $contract_name;

    /**
     * 外部合同编号
     * @var   string
     */
    public $code_out;

    /**
     * 是否是主合同
     * @var   boolean
     */
    public $is_main;

    /**
     * 项目id
     * @var   int
     */
    public $project_id;

    /**
     * 交易主体
     * @var   int
     */
    public $corporation_id;
    /**
     * 代理商id
     * @var   int
     */
    public $agent_id;

    /**
     * 代理模式
     * @var   int
     */
    public $agent_type;

    /**
     * 1：采购合同
     * 2：销售合同
     * 合同类别
     * @var   int
     */
    public $type;

    /**
     * 合作方
     * @var   int
     */
    public $partner_id;

    /**
     * 合同状态
     * @var   int
     */
    public $status;

    /**
     * 状态时间
     * @var   datetime
     */
    public $status_time;

    /**
     * 合同签定日期
     * @var   date
     */
    public $contract_date;

    /**
     * 1：先款后货
     * 2：先货后款
     * 付款方式
     * @var   int
     */
    public $pay_method;

    /**
     * 1：先款后货
     * 2：先货后款
     * 付款说明
     * @var   int
     */
    public $pay_remark;

    /**
     * 交货方式
     * @var   int
     */
    public $delivery_method;

    /**
     * 交货期限
     * @var   date
     */
    public $delivery_term;
    /**
     * 合同类型
     * @var   int
     */
    public $category;
    /**
     * 交易币种
     * @var   int
     */
    public $currency;

    /**
     * 汇率
     * @var   int
     */
    public $exchange_rate;

    /**
     * 交易总金额
     * @var   int
     */
    public $amount;

    /**
     * 人民币总金额
     * @var   int
     */
    public $amount_cny;

    /**
     * 价格方式
     * @var   int
     */
    public $price_type;

    /**
     * 计价公式
     * @var   string
     */
    public $formula;

    /**
     * 合同负责人
     * @var   int
     */
    public $manager_user_id;

    /**
     * 开始日期
     * @var   date
     */
    public $start_date;

    /**
     * 截止日期
     * @var   date
     */
    public $end_date;

    /**
     * 锁价维度
     * @var   int
     */
    public $lock_type;

    /**
     * 结算类型
     * @var   int
     */
    public $settle_type;

    /**
     * 0：原始
     * 1：拆分后的
     * 类型
     * @var   int
     */
    public $split_type = 0;

    /**
     * 原始合同ID
     * @var int
     */
    public $original_id = 0;

    /**
     * 合同条款
     * @var   array
     */
    public $contract_items = [];

    /**
     * 交易商品信息
     * @var   ContractGoods[]
     */
    public $goods_items = [];

    /**
     * 代理手续费明细
     * @var array
     */
    public $agent_details=[];

    /**
     * 付款计划
     * @var   array
     */
    public $payment_plans = [];


    /**
     * 备注
     * @var   string
     */
    public $remark;

    /**
     * 创建时间
     * @var   datetime
     */
    public $create_time;

    /**
     * 创建用户
     * @var   int
     */
    public $create_user;

    /**
     * 修改时间
     * @var   datetime
     */
    public $update_time;

    /**
     * 修改用户
     * @var   int
     */
    public $update_user;

    /**
     * 相关合同id
     * @var  int
     */
    public $relation_contract_id;

    public $num;

    #endregion

    use ContractRepository;

    /**
     * @var IContractRepository
     */
    //protected $repository;

    /**
     * 驳回类型 1：风控，2：业务
     * @var int
     */
    protected static $backTypeRisk = 1;
    /**
     * 业务驳回
     * @var int
     */
    protected static $backTypeBusiness = 2;


    public function init() {
        parent::init(); // TODO: Change the autogenerated stub
    }

    /**
     * 获取仓储
     * @return IContractRepository|object
     * @throws \Exception
     */
    /*protected function getRepository()
    {
        if (empty($this->repository))
        {
            $this->repository=DIService::getRepository(IContractRepository::class);
        }
        return $this->repository;
    }*/

    public function rules() {
        return array(array('contract_code', 'length', 'max' => 64),
            array('code_out', 'length', 'max' => 64),);
    }

    /**
     * 验证参数
     * @param null $attributes
     * @param bool $clearErrors
     * @return bool
     */
    public function validate($attributes = null, $clearErrors = true) {
        $res = parent::validate($attributes, $clearErrors); // TODO: Change the autogenerated stub
        if (!$res) {
            return false;
        }
        if (is_array($this->goods_items)) {
            foreach ($this->goods_items as $item) {
                if (!$item->validate($attributes, $clearErrors)) {
                    //$this->addError("goods_items",$this->getErrors());
                    return false;
                    break;
                }
            }
        }

        if (is_array($this->payment_plans)) {
            foreach ($this->payment_plans as $item) {
                if (!$item->validate($attributes, $clearErrors)) {
                    return false;
                    break;
                }
            }
        }

        return true;
    }

    public function getErrors($attribute = null) {
        $errs = parent::getErrors($attribute); // TODO: Change the autogenerated stub
        if (is_array($this->goods_items)) {
            foreach ($this->goods_items as $item) {
                $errs["goods_items"][] = $item->getErrors();
            }
        }
        if (is_array($this->payment_plans)) {
            foreach ($this->payment_plans as $item) {
                $errs["payment_plans"][] = $item->getErrors();
            }
        }
        return $errs;
    }

    public function getId() {
        // TODO: Implement getId() method.
        return $this->contract_id;
    }

    function setId($value) {
        // TODO: Implement setId() method.
        $this->contract_id = $value;
    }

    function getIdName() {
        // TODO: Implement getIdName() method.
        return "contract_id";
    }


    /**
     * @param Project $project
     * @return static
     * @throws \Exception
     */
    public static function create(Project $project = null) {
        $entity = new static();
        if (!empty($project)) {
            $entity->project_id = $project->project_id;
            $entity->corporation_id = $project->corporation_id;
            if ($entity->type == ContractType::BUY_CONTRACT)
                $entity->partner_id = $project->detail->up_partner_id;
            else
                $entity->partner_id = $project->detail->down_partner_id;

            foreach ($project->detail->goods_items as $item) {
                $entity->addGoodsItem(ContractGoods::create($item));
            }
        }

        return $entity;
    }


    #region 交易商品

    /**
     * 添加商品明细
     * @param ContractGoods $item
     * @throws \Exception
     */
    public function addGoodsItem(ContractGoods $item) {
        // TODO: implement
        if (empty($item)) {
            throw new ZException("参数ContractGoods对象为空");
        }

        $goodsId = $item->goods_id;
        if ($this->goodsIsExists($goodsId)) {
            throw new ZException(BusinessError::Contract_Goods_Is_Exists, array("contract_id" => $this->contract_id, "goods_id" => $goodsId,));
        }

        $this->goods_items[$goodsId] = $item;
    }

    public function clearGoodsItems(){
        return $this->goods_items = [];
    }

    /**
     * 判断当前商品项是否已经存在
     * @param $goodsId
     * @return bool
     */
    public function goodsIsExists($goodsId) {
        return isset($this->goods_items[$goodsId]);
    }

    /**
     * 移除入库的商品项
     * @param    int $goodsId
     */
    public function removeGoodsItem($goodsId) {
        // TODO: implement
        unset($this->goods_items[$goodsId]);
    }

    #endregion

    #region 收付款计划

    /**
     * 增加付款计划
     * @param    ContractPayPlan $payPlan
     */
    public function addPayPlan(ContractPayPlan $payPlan) {
        // TODO: implement
        $this->payment_plans[] = $payPlan;
    }

    /**
     * 移除付款计划
     * @param    ContractPayPlan $payPlan
     */
    public function removePayPlan(ContractPayPlan $payPlan) {
        // TODO: implement
        foreach ($this->payment_plans as $k => $item) {
            if ($payPlan->equals($item)) {
                unset($this->payment_plans[$k]);
                break;
            }
        }
    }

    #endregion

    #region 合同条款

    /**
     * 如果相同key的合同条款已经存在则直接覆盖
     * 增加合同条款
     * @param    ContractItem $item
     */
    public function addItem(ContractItem $item) {
        // TODO: implement
        $this->contract_items[$item->key] = $item;
    }

    /**
     * 移除合同条款
     * @param    ContractItem $item
     */
    public function removeItem(ContractItem $item) {
        // TODO: implement
        unset($this->contract_items[$item->key]);
    }

    /**
     * 增加合同代理费
     * @param ContractAgentDetail $agentDetail
     */
    public function addAgentDetail(ContractAgentDetail $agentDetail){
        $this->agent_details[$agentDetail->goods_id]=$agentDetail;
    }

    /**
     * 移除合同代理费
     * @param ContractAgentDetail $agentDetail
     */
    public function removeAgentDetail(ContractAgentDetail $agentDetail){
        unset($this->agent_details[$agentDetail->goods_id]);
    }

    #endregion

    /**
     * 创建合同提单
     * @return bool|LadingBill
     * @throws \Exception
     */
    public function createLadingBill() {
        if (!$this->isCanLading()) {
            throw new ZException(BusinessError::Contract_Cannot_Lading, array("contract_code" => $this->contract_code));
        }

        return LadingBill::create($this);
    }

    /**
     * 是否可以提货
     * @return bool
     */
    public function isCanLading() {
        return $this->status >= \Contract::STATUS_BUSINESS_CHECKED
            && $this->status < \Contract::STATUS_SETTLING;
    }

    /**
     * 是否可以创建发货单
     * @return bool
     */
    public function isCanCreateDeliveryOrder() {
        return $this->status >= \Contract::STATUS_BUSINESS_CHECKED
            && $this->status < \Contract::STATUS_SETTLING;

    }

    /**
     * @desc 是否可编辑
     * @return bool
     */
    public function isCanEdit() {
        return $this->status > \Contract::STATUS_STOP && $this->status < \Contract::STATUS_SUBMIT;
    }


    #region 合同提交

    /**
     * @desc 是否可提交
     * @return bool
     */
    public function isCanSubmit() {
        return in_array($this->status, array(\Contract::STATUS_BACK, \Contract::STATUS_SAVED, \Contract::STATUS_RECALL));
    }

    /**
     * 合同提交，主要是更新合同状态
     * @param bool $persistent
     * @throws \Exception
     */
    public function submit($persistent = true) {
        $this->status = \Contract::STATUS_SUBMIT;
        $this->status_time = Utility::getNow();
        if ($persistent)
            $this->getContractRepository()->submit($this);

        $this->afterSubmit();
    }

    /**
     * 提交且保存到持久化
     * @throws \Exception
     */
    /*public function submitAndSave()
    {
        // TODO: implement
        $this->submit();
        $this->getContractRepository()->submit($this);
    }*/


    /**
     * 当合同提交后
     * @throws \Exception
     */
    protected function afterSubmit() {
        EventSubscribeService::bind($this, static::EVENT_AFTER_SUBMIT, EventSubscribeService::ContractSubmitEvent);
        if ($this->hasEventHandler(static::EVENT_AFTER_SUBMIT))
            $this->onAfterSubmit(new ContractSubmitEvent($this));
    }

    /**
     * 响应合同提交事件
     * @param $event
     * @throws \Exception
     */
    protected function onAfterSubmit($event) {
        $this->raiseEvent(static::EVENT_AFTER_SUBMIT, $event);
    }

    #endregion

    #region 审核驳回相关

    public function isCanRiskBack() {
        return $this->status == \Contract::STATUS_SUBMIT;
    }

    public function isCanBusinessBack() {
        return $this->status == \Contract::STATUS_CREDIT_CONFIRMED;
    }

    /**
     * 风控驳回
     * @param bool $persistent 是否持久化，默认为true
     * @throws \Exception
     */
    public function riskBack($persistent = true) {
        $this->back(static::$backTypeRisk, $persistent);
    }

    /**
     * 风控驳回并持久化
     * @throws \Exception
     */
    /*public function riskBackAndSave()
    {
        $this->backAndSave(static::$backTypeRisk);
    }*/

    /**
     * 业务驳回
     * @param bool $persistent 是否持久化，默认为true
     * @throws \Exception
     */
    public function businessBack($persistent = true) {
        $this->back(static::$backTypeBusiness, $persistent);
    }

    /**
     * 业务驳回并持久化
     * @throws \Exception
     */
    /*public function businessBackAndSave()
    {
        $this->backAndSave(static::$backTypeBusiness);
    }*/

    /**
     * 合同驳回
     * @param $type
     * @param bool $persistent 是否持久化，默认为true
     * @throws \Exception
     */
    protected function back($type, $persistent = true) {
        $this->status = \Contract::STATUS_BACK;
        $this->status_time = Utility::getNow();
        if ($persistent)
            $this->getContractRepository()->back($this);
        $this->afterBack($type);
    }

    /**
     * @param $type
     * @throws \Exception
     */
    /*protected function backAndSave($type)
    {
        $this->status=\Contract::STATUS_BACK;
        $this->status_time=Utility::getNow();
        $this->back($type);
        $this->getContractRepository()->back($this);
    }*/

    /**
     * @param $type
     * @throws \Exception
     */
    protected function afterBack($type) {
        EventSubscribeService::bind($this, static::EVENT_AFTER_BACK, EventSubscribeService::ContractBackEvent);
        $event = $this->getBackEvent($type);
        if ($this->hasEventHandler(static::EVENT_AFTER_BACK))
            $this->onAfterBack($event);
    }

    /**
     * 获取驳回事件
     * @param $type
     * @return ContractBusinessRejectEvent|ContractRiskRejectEvent
     */
    protected function getBackEvent($type) {
        if ($type == static::$backTypeRisk)
            $event = new ContractRiskRejectEvent($this);
        else
            $event = new ContractBusinessRejectEvent($this);
        return $event;
    }

    /**
     * @desc 响应合同驳回事件
     * @param $event
     * @throws \Exception
     */
    protected function onAfterBack($event) {
        $this->raiseEvent(static::EVENT_AFTER_BACK, $event);
    }

    #endregion

    #region 结算及完结相关

    /**
     * 设为结算驳回
     * @param bool $persistent 是否持久化，默认为true
     * @throws \Exception
     */
    public function setSettledBack($persistent = true) {
        // TODO: implement
        $this->status = \Contract::STATUS_SETTLED_BACK;
        $this->status_time = Utility::getNow();
        if ($persistent)
            $this->getContractRepository()->setSettledBack($this);

        $this->afterSettledBack();
    }

    /**
     * @throws \Exception
     */
    protected function afterSettledBack() {
        EventSubscribeService::bind($this, static::EVENT_AFTER_SETTLED_BACK, EventSubscribeService::ContractSettledBackEvent);
        $event = new ContractSettledRejectEvent();
        if ($this->hasEventHandler(static::EVENT_AFTER_SETTLED_BACK))
            $this->raiseEvent(static::EVENT_AFTER_SETTLED_BACK, $event);
    }

    /**
     * 设为结算中
     * @param bool $persistent 是否持久化，默认为true
     * @throws \Exception
     */
    public function setOnSettlingAndSave($persistent = true) {
        // TODO: implement
        $this->status = \Contract::STATUS_SETTLED_SUBMIT;
        $this->status_time = Utility::getNow();
        if ($persistent)
            $this->getContractRepository()->setOnSettling($this);

        $this->afterSettling();
    }

    /**
     * @throws \Exception
     */
    protected function afterSettling() {
        EventSubscribeService::bind($this, static::EVENT_AFTER_SETTLING, EventSubscribeService::ContractSettlingEvent);
        $event = new ContractSettlingEvent();
        if ($this->hasEventHandler(static::EVENT_AFTER_SETTLING))
            $this->raiseEvent(static::EVENT_AFTER_SETTLING, $event);
    }

    /**
     * 设为结算完成
     * @param bool $persistent 是否持久化，默认为true
     * @throws \Exception
     */
    public function setSettledAndSave($persistent = true) {
        // TODO: implement
        $this->status = \Contract::STATUS_SETTLED;
        $this->status_time = Utility::getNow();
        if ($persistent)
            $this->getContractRepository()->setSettled($this);

        $this->afterSettled();
    }

    /**
     * @throws \Exception
     */
    protected function afterSettled() {
        EventSubscribeService::bind($this, static::EVENT_AFTER_SETTLED, EventSubscribeService::ContractSettledEvent);
        $event = new ContractSettledEvent();
        if ($this->hasEventHandler(static::EVENT_AFTER_SETTLED))
            $this->raiseEvent(static::EVENT_AFTER_SETTLED, $event);
    }

    /**
     * 设为已完结
     * @param bool $persistent 是否持久化，默认为true
     * @throws \Exception
     */
    public function setDone($persistent = true) {
        // TODO: implement
        $this->status = \Contract::STATUS_COMPLETED;
        $this->status_time = Utility::getNow();
        if ($persistent)
            $this->getContractRepository()->setDone($this);

        $this->afterDone();
    }

    /**
     * @throws \Exception
     */
    protected function afterDone() {
        EventSubscribeService::bind($this, static::EVENT_AFTER_DONE, EventSubscribeService::ContractDoneEvent);
        $event = new ContractDoneEvent();
        if ($this->hasEventHandler(static::EVENT_AFTER_DONE))
            $this->raiseEvent(static::EVENT_AFTER_DONE, $event);
    }


    #endregion

    #region 合同文本相关

    /**
     * 设置合同文本已上传
     */
    public function setFileUploaded() {
        // TODO: implement
        $this->status = \Contract::STATUS_FILE_UPLOAD;
        $this->status_time = Utility::getNow();
        $this->getContractRepository()->setFileUploaded($this);
    }

    /**
     * 设置电子双签合同上传
     */
    public function setSignedFileUploaded() {
        // TODO: implement
        $this->status = \Contract::STATUS_FILE_SIGNED;
        $this->status_time = Utility::getNow();
        $this->getContractRepository()->setSignedFileUploaded($this);
    }

    /**
     * 设置纸质合同上传
     */
    public function setPaperUploaded() {
        // TODO: implement
        $this->status = \Contract::STATUS_FILE_FILED;
        $this->status_time = Utility::getNow();
        $this->getContractRepository()->setPaperUploaded($this);
    }

    /**
     * 设为已经拆分
     * @param bool $persistent 是否持久化，默认为true
     * @throws \Exception
     */
    public function setSplit($persistent = true) {
        // TODO: implement
        $this->split_type = ContractEnum::SPLIT_TYPE_SPLIT;
        if ($persistent)
            $this->getContractRepository()->setSplit($this);
//        $this->afterSplit();
    }

    /**
     * 设置合同终止中
     * @param bool $persistent
     * @throws \Exception
     */

    public function setTerminating($persistent = true) {
        $this->status = \Contract::STATUS_TERMINATING;
        $this->status_time = Utility::getNow();
        if ($persistent) {
            $this->getContractRepository()->setTerminating($this);
        }
    }

    /**
     * 设置合同为终止驳回
     * @param bool $persistent
     * @throws \Exception
     */
    public function setTerminateBack($persistent = true) {
        $this->status = \Contract::STATUS_TERMINATE_BACK;
        $this->status_time = Utility::getNow();
        if ($persistent) {
            $this->getContractRepository()->setTerminateBack($this);
        }
    }

    /**
     * 设置合同为已终止
     * @param bool $persistent
     * @throws \Exception
     */
    public function setTerminated($persistent = true) {
        $this->status = \Contract::STATUS_TERMINATED;
        $this->status_time = Utility::getNow();
        if ($persistent) {
            $this->getContractRepository()->setTerminated($this);
        }
    }


    #endregion


}