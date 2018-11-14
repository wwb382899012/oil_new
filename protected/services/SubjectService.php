<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/24 11:18
 * Describe：
 */

class SubjectService
{
    /**
     * 获取所有有效的财务科目
     * @return array
     */
    public static function getActiveSubjects()
    {
        return Subject::getActiveSubjects();//Subject::model()->findAllToArray("status=1");
    }
}