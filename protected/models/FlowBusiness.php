<?php

/**
 * Created by youyi000.
 * DateTime: 2016/7/4 16:30
 * Describe：
 */
class FlowBusiness extends BaseActiveRecord
{


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_flow_business';
    }

}