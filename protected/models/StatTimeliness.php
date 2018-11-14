<?php

/**
 * Created by youyi000.
 * DateTime: 2017/4/5 14:41
 * Describe：
 */
class StatTimeliness extends BaseActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "t_stat_timeliness";
    }

}