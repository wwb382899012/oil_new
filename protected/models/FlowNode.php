<?php

/**
 * Created by youyi000.
 * DateTime: 2016/7/5 11:28
 * Describe：
 */
class FlowNode extends BaseActiveRecord
{


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_flow_node';
    }

    public function relations()
    {
        return array(
            "nextNode" => array(self::HAS_ONE, "FlowNode", array('node_id'=>'next_id')),
        );
    }

}