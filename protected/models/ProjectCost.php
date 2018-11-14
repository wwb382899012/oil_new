<?php
/**
 * Created by youyi000.
 * DateTime: 2017/11/23 14:50
 * Describe：
 */

class ProjectCost extends BaseBusinessActiveRecord
{


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "t_project_cost";
    }

    public function relations()
    {
        return array(
            "project" => array(self::BELONGS_TO, "Project", "project_id"),
            "corporation" => array(self::BELONGS_TO, "Corporation", "corporation_id"),
        );
    }
}