<?php

/**
 * Created by youyi000.
 * DateTime: 2016/7/5 17:31
 * Describe：
 */
class CheckLog extends BaseActiveRecord
{


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_check_log';
    }

    public function relations()
    {
        return array(
            "checkItem" => array(self::BELONGS_TO, "CheckItem", "check_id"),
            "checkDetail" => array(self::BELONGS_TO, "CheckDetail", "detail_id"),
            "checkNode" => array(self::BELONGS_TO, "FlowNode", array('node_id'=>'node_id')),
            "user" => array(self::BELONGS_TO, "SystemUser", "user_id"),
            "extra" => array(self::HAS_ONE, "CheckExtraLog", array('log_id'=>'id')),
        );
    }



    public static function getCheckLogs($detailId,$checkId)
    {
    	$sql = "select * from t_check_log where detail_id=".$detailId." and check_id=".$checkId;
    	return Utility::query($sql);
    }


}