<?php

/**
 * Created by PhpStorm.
 * User: Don
 * Date: 2016/11/9
 * Time: 17:31
 */
class PartnerAttachment extends BaseActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_partner_attachment';
    }


    /**
     * 删除成功返回1，否则返回错误信息或0
     * @return int|string
     */
    public static function del($id)
    {
        if(empty($id))
            return "id不能为空！";
        if(!Utility::isIntString($id))
            return "非法Id";

        $obj=PartnerAttachment::model()->findByPk($id);
        if(empty($obj->id))
            return "信息不存在";

        $sql="delete from t_partner_attachment where id=".$id." ";
        $res=Utility::execute($sql);
        if($res==1)
        {
            try{
                if(file_exists($obj->file_path)) {
                    unlink($obj->file_path);
                }
            }
            catch(Exception $e)
            {

            }
            return 1;
        }
        else
            return "操作失败！";
    }
}