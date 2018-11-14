<?php

/**
 * Desc: 现场风控
 * User: susieh
 * Date: 2017/4/7
 * Time: 19:58
 */
class PartnerRisk extends BaseActiveRecord {
	const RISK_STATUS_WAIT_APPROVAL = 1; //待审核
	const RISK_STATUS_REJECT = 2; //已驳回
    const RISK_STATUS_PASS = 3; //已通过

	const STATUS_RISK_NEW = 0; //已保存
	const STATUS_RISK_SUBMIT = 30; //提交
	const STATUS_RISK_REJECT = 15; //驳回

	const ATTACHMENT_RISK_REPORT = 2003;

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 't_partner_risk';
	}

	/**
	 * @desc 获取现场风控最新一条记录的状态
	 * @param $partner_Id | int
	 * @return array
	 */
	public static function getLatestPartnerRisk($partner_Id) {
		$data = PartnerRisk::model()->findAllToArray(array("condition" => "partner_id=" . $partner_Id, "order" => "risk_id desc limit 1"));
		if (Utility::isNotEmpty($data) && count($data) > 0) {
			return $data[0];
		}

		return array();
	}


	/**
	 * @desc 获取附件信息
	 * @param $id | int
	 * @return array
	 */
	public static function getPartnerRiskAttachments($id) {
		if (empty($id)) {
			return array();
		}
		$sql = "select * from t_partner_risk_attachment where base_id=" . $id . " and status=1 and type>2000 and type<2100  order by type asc";
		$data = Utility::query($sql);
		if (Utility::isEmpty($data)) {
			return array();
		}
		$attachments = array();
		foreach ($data as $v) {
			$attachments[$v["type"]][] = $v;
		}

		return $attachments;
	}

	public static function checkRiskAttachment($risk_id) {
		$obj = PartnerRiskAttachment::model()->find("base_id=" . $risk_id . " and type=" . self::ATTACHMENT_RISK_REPORT . " and status=1");
		if (empty($obj->id)) {
			return false;
		}
		return true;
	}
}