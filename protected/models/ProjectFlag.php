<?php

/**
 * Created by youyi000.
 * DateTime: 2016/6/27 16:36
 * Describe：
 */
class ProjectFlag extends BaseActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_project_flag';
    }

    public static function getFlagInfo($projectId)
    {
    	$sql = "select project_id,check_content from t_project_flag where project_id=".$projectId;
    	return Utility::query($sql);
    }
}