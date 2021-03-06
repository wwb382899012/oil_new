<?php

/**
 * Desc: 保理
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
class Factor extends BaseBusinessActiveRecord {
    const STATUS_TRASHED=-10;//已作废
//    const STATUS_STOP = - 2;//已止付
    const STATUS_CONFIRM_BACK = - 1;//已驳回(付款申请驳回同时驳回对接保理)
    const STATUS_NEW = 0;//新增加
    const STATUS_SUBMIT = 2;//待确认（保理财务会计）
    const STATUS_CONFIRMED = 3;//已确认（保理财务会计）待申请
//    const STATUS_CONFIRM = 5;//待审核
//    const STATUS_BACK = 10;//已驳回

//    const STATUS_PASS = 20;//审核通过
//    const STATUS_IN_RETURN = 25;//回款中
//    const STATUS_RETURNED = 30;//已回款

    //可确认状态
    public static $canConfirmStatus = array(
        Factor::STATUS_SUBMIT,
    );

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 't_factoring';
    }

    public function relations() {
        return array(
            'payApply' => array(self::BELONGS_TO, 'PayApplication', 'apply_id'),
            'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
            'contract' => array(self::BELONGS_TO, 'Contract', 'contract_id'),
            'corporation' => array(self::BELONGS_TO, 'Corporation', 'corporation_id'),
            "factorDetail" => array(self::HAS_MANY, "FactorDetail", "factor_id"),
            "factorReturn" => array(self::HAS_MANY, "FactorReturn", "factor_id"),
            "create_user" => array(self::BELONGS_TO, "SystemUser", array('create_user_id' => 'user_id')),
            "factorFundCode" => array(self::HAS_ONE, "FactorFundCode", array("code" => "contract_code_fund")),
        );
    }

    public function beforeSave() {
        if ($this->isNewRecord) {
            if (empty($this->create_time)) {
                $this->create_time = new CDbExpression("now()");
            }
            if (empty($this->create_user_id)) {
                $this->create_user_id = Utility::getNowUserId();
            }
        }
        if ($this->update_time == $this->getOldAttribute("update_time")) {
            $this->update_time = new CDbExpression("now()");
            $this->update_user_id = Utility::getNowUserId();
        }

        return parent::beforeSave(); // TODO: Change the autogenerated stub
    }

    protected function beforeDelete() {
        $res=$this->factorFundCode->delete();
        if(!$res)
            return false;
        foreach ($this->factorDetail as $model) {
            $res = $model->delete();
            if (!$res) {
                return false;
            }
        }

        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    public function isCanTrash()
    {
        return ($this->status<self::STATUS_SUBMIT);
    }

    /**
     * @desc 获取附件信息
     * @param $id
     * @param $type
     * @return array
     */
    public static function getAttachments($id, $type = '') {
        if (empty($id)) {
            return array();
        }
        if (!empty($type)) {
            $type = ' and type=' . $type;
        }

        $sql = "select * from t_factoring_attachment where base_id = " . $id . " and status = 1" . $type . " order by type asc";
        $data = Utility::query($sql);
        $attachments = array();
        foreach ($data as $v) {
            $attachments[$v["type"]][] = $v;
        }

        return $attachments;
    }

    /**
     * @desc 保理对接审核中金额
     * @param int $factorId
     * @return float
     */
    public function checkingAmount($factorId = 0) {
        if(!Utility::checkQueryId($factorId))
            $factorId = $this->factor_id;
        return FactoringDetailService::getFactorAmountById($factorId, ' and status = '. FactorDetail::STATUS_SUBMIT);
    }

    /**
     * @desc 已对接金额
     * @param int $factorId
     * @return float
     */
    public function buttedAmount($factorId = 0) {
        if(!Utility::checkQueryId($factorId))
            $factorId = $this->factor_id;
        return FactoringDetailService::getFactorAmountById($factorId, ' and status >= '. FactorDetail::STATUS_PASS);
    }
}