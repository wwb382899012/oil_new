<?php

class Payment extends BaseActiveRecord 
{
	const STATUS_SAVED = 1; //已保存
    const STATUS_SUBMITED = 2; //已提交

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 't_payment';
	}

	public function relations()
    {
        return array(
            "apply" => array(self::BELONGS_TO, "PayApplication", "apply_id"),
        );
    }

}