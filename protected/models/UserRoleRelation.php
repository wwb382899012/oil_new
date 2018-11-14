<?php

/**
 * Created by PhpStorm.
 * User: youyi000
 * Date: 2015/12/15
 * Time: 10:22
 * Describe：
 */
class UserRoleRelation extends BaseActiveRecord
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_user_role_relation';
    }
}