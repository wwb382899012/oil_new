<?php

/**
 * Desc: 合作方附件信息
 * User: susieh
 * Date: 2017/4/5 11:31
 * Time: 11:31
 */
class PartnerApplyAttachment extends BaseActiveRecord {
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 't_partner_apply_attachment';
	}
}