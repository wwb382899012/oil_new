<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 14:52
 * Describe：
 */

namespace ddd\application\dto\contractSettlement;


use ddd\Common\Application\BaseDTO;
use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\contract\Contract;
use ddd\repository\PartnerRepository;
use ddd\repository\CorporationRepository;
use ddd\repository\project\ProjectRepository;
use ddd\repository\SystemUserRepository;


class ContractDTO extends BaseDTO
{
    /**
     * @var      int  合同id
     */
    public $contract_id;
    /**
     * @var      string  合同编号
     */
    public $contract_code;
    /**
     * @var      int  项目id
     */
    public $project_id;
    /**
     * @var      string  项目编号
     */
    public $project_code;
    /**
     * @var      int  交易主体id
     */
    public $corporation_id;
    /**
     * @var      string  交易主体名称
     */
    public $corporation_name;
    /**
     * @var      int  合作方id
     */
    public $partner_id;
    /**
     * @var      string  合作方名称
     */
    public $partner_name;
    /**
     * @var      int  代理商id
     */
    public $agent_id;
    /**
     * @var      string  代理商名称
     */
    public $agent_name;
    /**
     * @var      int  代理模式
     */
    public $agent_type;
    /**
     * @var      int  合同类型
     */
    public $type;
    /**
     * @var      int  合同类别
     */
    public $category;
    /**
     * @var      int  采购/销售币种
     */
    public $currency;
    /**
     * @var      int  价格方式
     */
    public $price_type;
    /**
     * @var      string  计价公式
     */
    public $formula;
    /**
     * @var      string  合同负责人
     */
    public $manager_user_name;
    /**
     * @var      string  状态
     */
    public $status;
    /**
     * @var      string  备注
     */
    public $remark;
    /**
     * @var      array  合同商品
     */
    public $items=array();

    public function rules()
    {
        return array();
    }

    public function customAttributeNames()
    {
        return array();
        //return \Contract::model()->attributeNames();
    }


    /**
     * 从实体对象生成DTO对象
     * @param BaseEntity $entity
     * @throws \Exception
     */
    public function fromEntity(BaseEntity $entity)
    {
        $values=$entity->getAttributes();
        $this->setAttributes($values);

        if(is_array($entity->goods_items))
        {
            foreach ($entity->goods_items as $k=>$v)
            {
                $item=new ContractGoodsDTO();
                $item->fromEntity($v);
                $this->items[]=$item;
            }
        }

        $project =  ProjectRepository::repository()->findByPk($entity->project_id);
        $this->project_code = $project -> project_code;
        if(!empty($entity->partner_id)){
        $partner = PartnerRepository::repository()->findByPk($entity->partner_id);
        $this->partner_name = $partner -> name;
        }
        if(!empty($entity->corporation_id)){
        $corporation = CorporationRepository::repository()->findByPk($entity->corporation_id);
        $this->corporation_name = $corporation -> name;
        }
        if(!empty($entity->manager_user_id)){
        $user = SystemUserRepository::repository()->findByPk($entity->manager_user_id);
        $this->manager_user_name = $user->name;
        }
        if(!empty($entity->agent_id)){
        $agent = PartnerRepository::repository()->findByPk($entity->agent_id);
        $this->agent_name = $agent->name;
        }
    }

    /**
     * 转换成实体对象
     * @return Contract
     * @throws \Exception
     */
    public function toEntity()
    {
        $entity=Contract::create();
        $entity->setAttributes($this->getAttributes());
        if(is_array($this->items))
        {
            foreach ($this->items as $k=>$v)
            {
                $entity->addGoodsItem($v->toEntity());
                //$entity->goodsItems[$v->goods_id]=$v->toEntity();
            }
        }

        return $entity;
    }
}