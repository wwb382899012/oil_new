<?php

/**
 * This is the model class for table "{{project_payment}}".
 *
 * The followings are the available columns in table '{{project_payment}}':
 * @property integer $id
 * @property string $project_id
 * @property string $pay_amount
 * @property integer $create_user_id
 * @property string $create_time
 * @property integer $update_user_id
 * @property string $update_time
 */
class ProjectPayment extends BaseBusinessActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return '{{project_payment}}';
    }


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'project_id' => '项目id',
            'pay_amount' => '付款金额',
            'create_user_id' => 'Create User',
            'create_time' => 'Create Time',
            'update_user_id' => 'Update User',
            'update_time' => 'Update Time',
        );
    }


    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return ProjectPayment the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
}
