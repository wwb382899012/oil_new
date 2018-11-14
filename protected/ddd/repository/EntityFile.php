<?php
/**
 * Created by youyi000.
 * DateTime: 2018/2/27 16:07
 * Describe：
 */

namespace ddd\repository;


use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\Attachment;
use ddd\Common\IAggregateRoot;
use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\error\ZModelNotExistsException;
use ddd\infrastructure\error\ZModelSaveFalseException;
use system\components\base\Object;

trait EntityFile
{
    /**
     * 获取文件对象
     * @param $fileModel
     * @return Attachment
     */
    public function getAttachmentEntity($fileModel)
    {
        $attachment = Attachment::create();
        if (!empty($attachment))
        {
            $attachment->setAttributes($fileModel->getAttributes(), false);
        }
        return $attachment;
    }
}