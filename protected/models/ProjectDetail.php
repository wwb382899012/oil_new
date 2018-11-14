<?php

/**
 * Created by youyi000.
 * DateTime: 2016/11/8 16:18
 * Describe：
 */
class ProjectDetail extends BaseActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_project_detail';
    }


}