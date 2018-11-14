<?php

/**
 * Created by PhpStorm.
 * User: youyi000
 * Date: 2015/12/4
 * Time: 19:19
 * Describe：
 */
class Tag extends BaseActiveRecord
{

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_tag';
    }

    /**
     * 保存，处理父路径，返回1
     *
     * @return int
     */
    public function save($runValidation=true,$attributes=null)
    {
        if(!empty($this->parent_id))
        {
            $obj=Tag::model()->findByPk($this->parent_id);
            if(!isset($obj->id))
            {
                $obj->id=0;
                $obj->parent_ids="0,";
            }
        }

        $this->parent_ids=$obj->parent_ids.$obj->id.",";
        parent::save();

        return 1;
    }


    /**
     * 获取所有的标签，指定父标签则获取所有子标签
     *
     * @param int $parentId
     * @return mixed
     */
    public static function getAllData($parentId=0)
    {
        if($parentId!=0)
        {
            $sql="select * from t_tag where parent_ids like '%," . $parentId . ",%'  order by parent_id asc,order_index asc,id asc";
        }
        else
            $sql="select * from t_tag order by parent_id asc,order_index asc,id asc";
        return Utility::query($sql);
    }

}