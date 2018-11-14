<?php
/**
 * Created by youyi000.
 * DateTime: 2017/11/23 10:59
 * Describe：
 */

class ProjectProfit extends BaseBusinessActiveRecord
{
    const TYPE_CONFIRM=1;//可分配
    const TYPE_SETTLED=2;//可计算

    const CATEGORY_PROJECT = 1; //项目分配统计
    const CATEGORY_CORPORATION = 2; //交易主体分配统计
    const CATEGORY_PROJECT_LEADER = 3; //项目负责人分配统计

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "t_project_profit";
    }

    public function relations()
    {
        return array(
            "project" => array(self::BELONGS_TO, "Project", "project_id"),//项目信息
            "corporation" => array(self::BELONGS_TO, "Corporation", "corporation_id"),
        );
    }
}