<?php

/**
 * Created by youyi000.
 * DateTime: 2016/8/15 15:46
 * Describe：
 */
class Task extends BaseActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_task';
    }

    public function relations()
    {
        return array(
            "action" => array(self::BELONGS_TO, "Action", "action_id"),

        );
    }


}