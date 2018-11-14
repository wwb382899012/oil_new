<?php

/**
 * This is the model class for table "{{contract}}".
 *
 * The followings are the available columns in table '{{contract}}':
 * @property string $contract_id
 * @property string $project_id
 * @property integer $relation_contract_id
 * @property integer $partner_id
 * @property integer $type
 * @property integer $split_type
 * @property string $original_id
 * @property integer $num
 * @property integer $category
 * @property integer $is_main
 * @property string $contract_code
 * @property string $code_out
 * @property string $contract_name
 * @property integer $corporation_id
 * @property integer $agent_id
 * @property integer $agent_type
 * @property integer $currency
 * @property string $exchange_rate
 * @property integer $price_type
 * @property string $formula
 * @property string $amount_cny
 * @property string $amount
 * @property string $agent_amount
 * @property integer $manager_user_id
 * @property string $status_time
 * @property integer $status
 * @property integer $old_status
 * @property string $start_date
 * @property string $end_date
 * @property string $contract_date
 * @property integer $contract_status
 * @property integer $flag
 * @property string $remark
 * @property integer $create_user_id
 * @property string $create_time
 * @property integer $update_user_id
 * @property string $update_time
 * @property integer $settle_type
 * @property string $delivery_term
 * @property integer $days
 * @property integer $delivery_mode
 * @property string $block_hash
 * @property string $tx_hash
 * @property ContractGoods goods
 */
class Contract extends BaseActiveRecord
{

	//const STATUS_BACK 		= -1;//已驳回
    //const STATUS_QUOTAED = 3; // 添加额度
    //const STATUS_TRANSACTION_ROLLBACK = 4; // 业务审核打回

    //const STATUS_CHECKING 	= 1;//审核中
    //const STATUS_CHECKED 	= 2;//已审核


    const STATUS_STOP = -9;//合同作废
    const STATUS_BACK = -1;//风控审核驳回
    const STATUS_TEMP_SAVE = 0;//商务确认已暂存
    const STATUS_SAVED = 1; //商务确认已保存
    const STATUS_RECALL = 2;//合同撤回

    const STATUS_SUBMIT = 10;//商务确认完成提交到风控审核

    const STATUS_RISK_CHECKED = 19;//风控审核通过
    const STATUS_BUSINESS_REJECT = 20; // 业务审核驳回
    const STATUS_CREDIT_CONFIRMED = 21;//风控额度确认并提交业务审核

    const STATUS_BUSINESS_CHECKED = 29; // 业务审核通过

    const STATUS_FILE_SUBMIT=40; //合同最终文件已提交审核
    const STATUS_FILE_BACK=41; //合同最终文件审核驳回
    const STATUS_FILE_UPLOAD=45; //合同最终文件已上传(已审核)

    const STATUS_FILE_SIGNED=50; //合同电子双签文件已上传(已审核)

    const STATUS_FILE_FILED=59; //纸质双签文件已上传(已审核)

    const STATUS_SETTLING= 60;//合同结算中
    const STATUS_SETTLE_INVALIDITY = 65;//合同结算作废
    const STATUS_SETTLE_REVOCATION = 66; //合同结算撤回
    const STATUS_SETTLED_BACK=70;//合同结算审核驳回
    const STATUS_SETTLED_SUBMIT=75;//合同结算审核中
    const STATUS_SETTLED=80;//合同已结算

    const STATUS_TERMINATING=85;//合同终止中
    const STATUS_TERMINATE_BACK=86;//合同终止驳回
    const STATUS_TERMINATED=87;//合同已终止

    const STATUS_COMPLETED = 99; // 合同完成

    /*
     * 合同结算方式
     */
    const SETTLE_TYPE_LADING = 1; //提单方式结算
    const SETTLE_TYPE_BUY_CONTRACT = 2; //采购合同结算
    const SETTLE_TYPE_DELIVERY = 3; //发货单方式结算
    const SETTLE_TYPE_SALE_CONTRACT = 4; //销售合同方式结算

    /**
     * 合同拆分状态
     */
    const SPLIT_TYPE_NOT_SPLIT=0;//合同未拆分
    const SPLIT_TYPE_SPLIT=1;//合同已拆分

    /**
     * 相关渠道背对背合同
     * @var
     */
    public $relative;
    public $split;//拆分合同信息

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_contract';
    }

    public function relations()
    {
        return array(
            "project" => array(self::BELONGS_TO, "Project", "project_id"),//项目信息
            "corporation" => array(self::BELONGS_TO, "Corporation", "corporation_id"),
            'partner' => array(self::BELONGS_TO, 'Partner', 'partner_id'),
            'agent' => array(self::BELONGS_TO, 'Partner',array('agent_id'=>'partner_id')),//代理商

            "goods" => array(self::HAS_MANY, "ContractGoods", "contract_id"),//合同商品交易信息
            "contractGoods" => array(self::HAS_MANY, "ContractGoods", "contract_id"),//合同商品交易信息
            "extra" => array(self::HAS_ONE, "ContractExtra", "contract_id"),//合同其他条款信息
            "payments" => array(self::HAS_MANY, "PaymentPlan", "contract_id"),

            'contractSettlement'=>array(self::HAS_ONE, "ContractSettlement", "contract_id"),//合同结算

            "payPlans" => array(self::HAS_MANY, "PaymentPlan", "contract_id","on"=>"payPlans.type=1"),
            "receivePlans" => array(self::HAS_MANY, "PaymentPlan", "contract_id","on"=>"receivePlans.type=2"),

            "files"=>array(self::HAS_MANY, "ContractFile", "contract_id"),
            "filesBase"=>array(self::HAS_MANY, "ContractFile", "contract_id",'on'=>'filesBase.type=1',),
            "filesOnline"=>array(self::HAS_MANY, "ContractFile", "contract_id",'on'=>'filesOnline.type=11'),
            "filesPaper"=>array(self::HAS_MANY, "ContractFile", "contract_id",'on'=>'filesPaper.type=21'),

            "manager"=>array(self::BELONGS_TO, "SystemUser",array('manager_user_id'=>'user_id')), // 负责人

            "agentDetail"=>array(self::HAS_MANY, "ContractAgentDetail", "contract_id"),
            "quotas"=>array(self::HAS_MANY, "ProjectCreditDetail", "contract_id", 'on'=>'quotas.status<>'.ProjectCreditDetail::STATUS_DELETE),
            //"quotas"=>array(self::HAS_MANY, "ProjectCreditDetail", "contract_id"),
            "creator"=>array(self::BELONGS_TO, "SystemUser",array('create_user_id'=>'user_id')), // 创建人
            "relationContract" => array(self::BELONGS_TO, "Contract", array('relation_contract_id'=>'contract_id')),
            "originalContractGoods" => array(self::HAS_MANY, "OriginalContractGoods", 'contract_id'),
            "priceDetails"=>array(self::HAS_MANY, "GoodsPriceDetail", "contract_id", "on"=>"priceDetails.is_settled=0"),
        );
    }

    public function beforeSave()
    {
        if ($this->isNewRecord)
        {
            if (empty($this->create_time))
                $this->create_time = new CDbExpression("now()");
            if (empty($this->create_user_id))
                $this->create_user_id= Utility::getNowUserId();
        }
        if ($this->update_time == $this->getOldAttribute("update_time"))
        {
            $this->update_time = new CDbExpression("now()");
            $this->update_user_id = Utility::getNowUserId();
        }
        return parent::beforeSave(); // TODO: Change the autogenerated stub
    }

    protected function afterSave()
    {
        parent::afterSave(); // TODO: Change the autogenerated stub
        if ($this->status != $this->getOldAttribute("status"))
        {
           $this->addStatusLog();
        }
    }

    /**
     * 查找获取背对背合同
     * @return CActiveRecord
     */
    public function findRelative()
    {
        $condition="project_id=".$this->project_id." and is_main=1 and contract_id<>".$this->contract_id;
        $this->relative= Contract::model()->find($condition);
        return $this->relative;
    }

    /**
     * 根据合同编号查找合同
     * @param $code
     * @return CActiveRecord
     */
    public function findByCode($code)
    {
        return $this->find("contract_code='".$code."'");
    }

    /**
     * 添加状态变更记录
     */
    protected function addStatusLog()
    {
        $obj=new ContractLog();
        $obj->project_id=$this->project_id;
        $obj->contract_id=$this->contract_id;
        $obj->field_name="status";
        $obj->old_value=$this->getOldAttribute("status");
        $obj->new_value=$this->status;
        $obj->timespan=strtotime("now") - strtotime($this->getOldAttribute("status_time"));
        $obj->create_user_id=$this->update_user_id;
        $obj->create_time=new CDbExpression("now()");
        $obj->save();
    }


    protected function beforeDelete()
    {
        $res=$this->extra->delete();
        if(!$res)
            return false;
        $contractGroup = ContractGroup::model()->find('contract_id='.$this->contract_id.' or down_contract_id='.$this->contract_id);
        if (!empty($contractGroup)) {
            $res = $contractGroup->delete();
            if(!$res)
                return false;
        }
        foreach ($this->goods as $model)
        {
            $res=$model->delete();
            if(!$res)
                return false;
        }
        foreach ($this->agentDetail as $model)
        {
            $res=$model->delete();
            if(!$res)
                return false;
        }
        foreach ($this->payments as $model)
        {
            $res=$model->delete();
            if(!$res)
                return false;
        }

        $files=$this->files;

        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    protected function afterDelete()
    {
        parent::afterDelete(); // TODO: Change the autogenerated stub

        foreach ($this->files as $model)
        {
            $model->delete();//附件删除失败不影响整体的删除
        }

    }

    public function isSplit($split_type, $original_id) {
        return $split_type == self::SPLIT_TYPE_SPLIT && $original_id > 0;
    }

    /**
     * 获取合同的类型描述
     * @return string
     */
    public function getContractType()
    {
        if($this->is_main)
        {
            return Map::$v['buy_sell_desc_type'][$this->is_main];
        }
        else
        {
            if($this->isSplit($this->split_type,$this->original_id)){
                return '平移新合同';
            }else{
                return Map::$v['buy_sell_desc_type'][$this->is_main][$this->type];
            }
        }
    }

}