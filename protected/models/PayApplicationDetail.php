<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/23 17:23
 * Describe：
 */

class PayApplicationDetail extends BaseBusinessActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "t_pay_application_detail";
    }

    public function relations()
    {
        return array(
            "project" => array(self::BELONGS_TO, "Project", "project_id"),
            "application" => array(self::BELONGS_TO, "PayApplication", "apply_id"),
            "contract" => array(self::BELONGS_TO, "Contract", "contract_id"),
            "payment" => array(self::BELONGS_TO, "PaymentPlan", "plan_id"),
        );
    }

}