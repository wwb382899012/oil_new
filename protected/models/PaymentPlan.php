<?php
/**
 * Created by youyi000.
 * DateTime: 2017/9/5 14:45
 * Describe：
 */

class PaymentPlan extends BaseActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_payment_plan';
    }

    public function relations() {
        return array(
            "project" => array(self::BELONGS_TO, "Project", "project_id"),//项目信息
            "contract" => array(self::BELONGS_TO, "Contract", "contract_id"),

        );
    }
}