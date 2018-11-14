<?php

/**
 * Desc: 合作方货票款信息
 * User: susieh
 * Date: 18/3/28
 * Time: 15:38
 */
class PartnerStat extends BaseActiveRecord {
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return "t_partner_stat";
	}
}