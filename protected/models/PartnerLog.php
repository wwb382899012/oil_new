<?php

/**
 * Desc: 合作方白名单变更日志
 * User: susieh
 * Date: 17/3/28
 * Time: 15:38
 */
class PartnerLog extends BaseActiveRecord {
	public function __construct($scenario = 'insert') {
		parent::__construct($scenario);
	}

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return "t_partner_log";
	}
}