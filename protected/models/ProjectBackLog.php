<?php
/**
 * Created by youyi000.
 * DateTime: 2017/11/29 20:48
 * Describe：
 */

class ProjectBackLog extends BaseActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "t_project_back_log";
    }

}