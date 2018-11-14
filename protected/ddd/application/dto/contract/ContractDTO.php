<?php

/**
 * Created by youyi000.
 * DateTime: 2018/3/1 14:52
 * Describe：
 */

namespace ddd\application\dto\contract;

use ddd\application\dto\AttachmentDTO;
use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\contract\Contract;
use ddd\domain\entity\contract\ContractAgentDetail;
use ddd\repository\CorporationRepository;
use ddd\repository\PartnerRepository;
use ddd\Contract\Repository\Project\ProjectRepository;
use ddd\repository\SystemUserRepository;
use Faker\Provider\Base;

class ContractDTO extends BaseDTO
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
     * 项目编号
     * @var   varchar
     */
    public $project_code;

    /**
     * 合作方ID
     * @var   int
     */
    public $partner_id;

    /**
     * 合作方名称
     * @var   carchar
     */
    public $partner_name;

    /**
     * 交易主体ID
     * @var   int
     */
    public $corporation_id;

    /**
     * 交易主体名称
     * @var   varchar
     */
    public $corporation_name;

    /**
     * 代理商ID
     * @var   int
     */
    public $agent_id;

    /**
     * 代理商名称
     * @var   int
     */
    public $agent_name;

    /**
     * 代理模式
     * @var   int
     */
    public $agent_type;

    /**
     * 合同负责人ID
     * @var   int
     */
    public $manager_user_id;

    /**
     * 合同负责人
     * @var   varchar
     */
    public $manager_user_name;

    /**
     * 1：采购合同
     * 2：销售合同
     * 采销合同类别
     * @var   int
     */
    public $type;

    /**
     * 合同类型
     * @var   int
     */
    public $category;

    /**
     * 合同状态
     * @var   int
     */
    public $status;

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
     * 价格方式
     * @var   int
     */
    public $price_type;

    /**
     * 是否主合同
     * @var   boolean
     */
    public $is_main;

    /**
     * 计价公式
     * @var   varchar
     */
    public $formula;

    /**
     * 合同商品
     * @var   ContractGoodsDTO
     */
    public $goodsItems = [];

    /**
     * 收付款明细
     * @var   ContractPayPlanDTO
     */
    public $paymentPlans = [];

    /**
     * 合同条款
     * @var   ContractItemDTO
     */
    public $contractItems = [];

    /**
     * 代理手续费明细
     * @var   ContractAgentDetailDTO
     */
    public $agentDetails = [];

    /**
     * 合同文件
     * @var   AttachmentDTO
     */
    public $contractFiles = [];

    #endregion

    public function rules() {
        return [
            ['goodsItems', "required", "message" => "请填写交易明细"],
            ['paymentPlans', "required", "message" => "请填写收付款明细"],
            ['project_id', "numerical", "integerOnly" => true, "min" => 1, "tooSmall" => "项目id必须为大于0的整数"],
            ['corporation_id', "numerical", "integerOnly" => true, "min" => 1, "tooSmall" => "交易主体id必须为大于0的整数"],
            ['partner_id', "numerical", "integerOnly" => true, "min" => 1, "tooSmall" => "合作方id必须为大于0的整数"],
            ['currency,exchange_rate,price_type,manager_user_id', "required", "message" => "{attribute}字段不得为空！"],
            ["agent_type", "validAgentType", "prefix" => "代理模式"],
            ["formula", "validFormula", "prefix" => "计价公式"],
        ];
    }

//    public function customAttributeNames() {
//        return \Contract::model()->attributeNames();
//    }

    /**
     * 从实体对象生成DTO对象
     * @param BaseEntity $entity
     */
    public function fromEntity(BaseEntity $entity) {
        $values = $entity->getAttributes();
        $this->setAttributes($values);
        $project = ProjectRepository::repository()->findByPk($entity->project_id);
        $this->project_code = $project->project_code;
        $partner = PartnerRepository::repository()->findByPk($entity->partner_id);
        $this->partner_name = $partner->name;
        if ($entity->agent_id) {
            $agent = PartnerRepository::repository()->findByPk($entity->agent_id);
            $this->agent_name = $agent->name;
        }
        $corporation = CorporationRepository::repository()->findByPk($entity->corporation_id);
        $this->corporation_name = $corporation->name;
        $managerUser = SystemUserRepository::repository()->findByPk($entity->manager_user_id);
        $this->manager_user_name = $managerUser->name;
        if (is_array($entity->goods_items)) {
            foreach ($entity->goods_items as $k => $v) {
                $item = new ContractGoodsDTO();
                $item->fromEntity($v);
                $this->goodsItems[$k] = $item;
            }
        }
        if (is_array($entity->payment_plans)) {
            foreach ($entity->payment_plans as $k => $v) {
                $item = new ContractPayPlanDTO();
                $item->fromEntity($v);
                $this->paymentPlans[] = $item;
            }
        }
        if (is_array($entity->contract_items)) {
            foreach ($entity->contract_items as $k => $v) {
                $item = new ContractItemDTO();
                $item->fromEntity($v);
                $this->contractItems[$k] = $item;
            }
        }
        if (\Utility::isNotEmpty($entity->agent_details)) {
            foreach ($entity->agent_details as $k => $agent_detail) {
                $item = new ContractAgentDetailDTO();
                $item->fromEntity($agent_detail);
                $this->agentDetails[$k] = $item;
            }
        }
    }

    /**
     * 转换成实体对象
     * @return Contract
     */
    public function toEntity() {
        $entity = Contract::create();

        $entity->setAttributes($this->getAttributes());
        $entity->goods_items = array();
        if (is_array($this->goodsItems)) {
            foreach ($this->goodsItems as $k => $v) {
                $entity->addGoodsItem($v->toEntity());
//                $entity->goods_items[$v->goods_id]=$v->toEntity();
            }
        }
        if (is_array($this->paymentPlans)) {
            foreach ($this->paymentPlans as $k => $v) {
                $entity->addPayPlan($v->toEntity());
            }
        }
        if (is_array($this->contractItems)) {
            foreach ($this->contractItems as $k => $v) {
                $entity->addItem($v->toEntity());
            }
        }
        return $entity;
    }

    //代理模式  验证
    public function validAgentType($attribute, $params) {
        if ($this->agent_id && empty($this->$attribute))
            $this->addError($attribute, $params['prefix'] . '不能为空');
    }

    //计价公式 验证
    public function validFormula($attribute, $params) {
        if ($this->price_type == \ConstantMap::PRICE_TYPE_TEMPORARY && empty($this->$attribute))
            $this->addError($attribute, $params['prefix'] . '不能为空');
    }

}
