<?php

/**
 * Created by youyi000.
 * DateTime: 2017/3/24 16:27
 * Describe：
 */
class ActionLog extends BaseActiveRecord
{
    const TYPE_ADD = '添加';
    const TYPE_EDIT = '修改';
    const TYPE_DEL = '删除';

    public function __construct($scenario='insert')
    {
        self::$db=Utility::getDb(Utility::DB_LOG);
        parent::__construct($scenario);
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "t_action_log";
    }

    public static function getEditRemark($isNew, $obj="") {
        if ($isNew)
            return self::TYPE_ADD . $obj;

        return self::TYPE_EDIT . $obj;
    }
}