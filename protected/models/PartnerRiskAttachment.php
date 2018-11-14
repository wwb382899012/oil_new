<?php

/**
 * Desc: 现场风控附件信息
 * User: susieh
 * Date: 2017/4/10 11:31
 * Time: 11:31
 */
class PartnerRiskAttachment extends BaseActiveRecord {
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 't_partner_risk_attachment';
	}
}