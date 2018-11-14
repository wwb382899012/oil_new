<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/24 9:56
 * Describe：
 */

class AttachmentService
{
    /**
     * 获取所有文件的附件信息
     * @param $attachmentType
     * @param $baseId
     * @param null $fileType
     * @return array
     */
    public static function getAttachments($attachmentType,$baseId,$fileType=null)
    {
        if(empty($baseId) || empty($attachmentType))
            return array();

        $obj=new Attachment($attachmentType);
        return $obj->getAttachments($baseId,$fileType);

    }

    /**
     * 获取附件配置
     * @param $attachmentType
     * @return mixed|null
     */
    public static function getAttachmentConfig($attachmentType)
    {
        if(empty($attachmentType))
            return null;
        $obj=new Attachment($attachmentType);
        if(empty($obj->config))
            return null;
        else
            return $obj->config;
    }
}