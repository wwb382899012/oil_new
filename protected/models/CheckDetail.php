<?php

/**
 * Created by youyi000.
 * DateTime: 2016/7/5 11:12
 * Describe：
 */
class CheckDetail extends BaseActiveRecord
{

    const STATUS_NEW=0;//待审核
    const STATUS_CHECKED=1;//已审核
    const STATUS_IGNORED=2;//已忽略，无需审核

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_check_detail';
    }

    public function relations()
    {
        return array(
            "checkItem" => array(self::BELONGS_TO, "CheckItem", "check_id"),
            "checkLog" => array(self::HAS_ONE, "CheckLog", "detail_id"),
            "checkNode" => array(self::BELONGS_TO, "FlowNode", array('check_node_id'=>'node_id')),
            "user" => array(self::BELONGS_TO, "SystemUser", array('check_user_id'=>'user_id')),
        );
    }

}