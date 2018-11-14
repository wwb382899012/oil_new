<?php

/**
 * Desc: 保理明细
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
class FactorDetail extends BaseBusinessActiveRecord {
    const STATUS_BACK = -1; //已驳回
    const STATUS_NEW = 0;//新增加
    const STATUS_SUBMIT = 10;//提交待审核
    const STATUS_PASS = 20;//审核通过
    const STATUS_IN_RETURN = 25;//回款中
    const STATUS_RETURNED = 30;//已回款

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 't_factoring_detail';
    }

    public function relations() {
        return array(
            'factor' => array(self::BELONGS_TO, 'Factor', 'factor_id'),
            'payApply' => array(self::BELONGS_TO, 'PayApplication', 'apply_id'),
            'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
            'contract' => array(self::BELONGS_TO, 'Contract', 'contract_id'),
            "factorReturn" => array(self::HAS_MANY, "FactorReturn", "detail_id"),
            "attachments" => array(self::HAS_MANY, "FactoringAttachment", "", "on" => "t.detail_id=attachments.detail_id"),
            "create_user" => array(self::BELONGS_TO, "SystemUser", array('create_user_id' => 'user_id')),
        );
    }

    /**
     * @desc 是否可以修改
     * @param null $status
     * @return bool
     */
    public function isCanEdit($status = null) {
        if ($status == null) {
            $status = $this->status;
        }

        return $status < FactorDetail::STATUS_SUBMIT;
    }

    protected function beforeDelete()
    {
        $returns = $this->factorReturn;
        if(Utility::isNotEmpty($returns)) {
            foreach ($returns as $model)
            {
                $res=$model->delete();
                if(!$res)
                    return false;
            }
        }

        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    protected function afterDelete()
    {
        parent::afterDelete(); // TODO: Change the autogenerated stub

        $attachs = $this->attachments;
        if(Utility::isNotEmpty($attachs)) {
            foreach ($attachs as $model)
            {
                $model->delete();//附件删除失败不影响整体的删除
            }
        }
    }
}