<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/26 14:31
 * Describe：
 */

class PayApplicationExtra extends BaseBusinessActiveRecord
{
    const STATUS_TRASH=-9;//止付作废
    const STATUS_BACK=-1;//止付驳回
    const STATUS_CHECKING=1;//止付审核中
    const STATUS_PASS=2;//止付审核通过



    public $items=array();

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "t_pay_application_extra";
    }

    public function relations()
    {
        return array(
            "application" => array(self::BELONGS_TO, "PayApplication", "apply_id"),
        );
    }

    public function findByApplyId($applyId)
    {
        return $this->find("apply_id=".$applyId);
    }


    protected function afterFind()
    {
        parent::afterFind(); // TODO: Change the autogenerated stub
        $this->items=json_decode($this->content,true);
    }


}