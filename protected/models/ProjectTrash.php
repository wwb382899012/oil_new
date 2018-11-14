<?php

/**
 * Created by youyi000.
 * DateTime: 2017/3/21 16:37
 * Describe：
 */
class ProjectTrash extends BaseActiveRecord
{

    const STATUS_NEW=0;
    const STATUS_CONFIRMED=6;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "t_project_trash";
    }

    /**
     * 获取附件信息
     * @param $id
     * @return array
     */
    public static function getAttachment($id)
    {
        if(empty($id))
            return array();
        $sql="select * from t_project_attachment where project_id=".$id." and status=1 and type>900 and type<999  order by type asc";
        $data=Utility::query($sql);
        $attachments=array();

        foreach($data as $v)
        {
            $attachments[$v["type"]][]=$v;
        }
        return $attachments;
    }

}