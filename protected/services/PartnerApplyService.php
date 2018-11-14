<?php

/**
 * Desc: 合作方准入申请
 * User: susieh
 * Date: 17/4/1
 * Time: 14:10
 */
class PartnerApplyService {
	const PARTNER_LEVEL_A = 1;
	const PARTNER_LEVEL_B = 2;
	const PARTNER_LEVEL_C = 3;
	const PARTNER_LEVEL_D = 4;

	/**
	 * @desc 获取合作方分类级别（先根据白名单判断；不在白名单根据商务强制分类判断；无强制分类根据填写资料判断）
	 * @param $partnerApply | array 合作方信息
	 * @return int|string
	 */
	public static function getPartnerLevel($partnerApply) {
		$map = Map::$v;
		if (count($partnerApply) == 1 && !empty($partnerApply['partner_id'])) {
			$partnerInfo = PartnerApply::model()->findByPk($partnerApply['partner_id']);
			$partnerApply = $partnerInfo->attributes;
		}

		if(!empty($partnerApply['partner_id']) && empty($partnerApply['name'])) {
			$partnerApply['name'] = PartnerApply::getPartnerNameById($partnerApply['partner_id']);
		}

		$name = Utility::filterInject($partnerApply['name']);
		if (empty($name)) {
			return "企业名称不得为空！";
		}
		$partnerWhiterObj = PartnerWhite::model()->find("name='" . $name . "' and status=1");
		if (!empty($partnerWhiterObj->id)) {
			return intval($partnerWhiterObj->level);
		}

		/*if (!empty($partnerApply['custom_level'])) {
			return $partnerApply['custom_level'];
		}*/

        $level_d_runs_state = array(array_search("注销", $map['runs_state']), array_search("吊销", $map['runs_state']));
        if (empty($partnerApply['business_scope']) || in_array($partnerApply['runs_state'], $level_d_runs_state)) {
            return self::PARTNER_LEVEL_D;
        }


        if (!empty($partnerApply['is_stock'])) {
            return self::PARTNER_LEVEL_B;
        }

		$customOwnershipNames=array("有限责任公司（国有独资）","有限责任公司(国有独资)","全民所有制");
        $customOwnershipKeys=array();
        $ownerships = Ownership::getOwnerships();


		/*$level_b_ownershop_name_1 = "有限责任公司（国有独资）";
		$level_b_ownershop_name_2 = "全民所有制";
		$level_b_ownershops = array();*/

		if (count($ownerships) > 0) {
			foreach ($ownerships as $key => $row)
			{
			    if(in_array($row["name"],$customOwnershipNames))
			        $customOwnershipKeys[]=$row["id"];
				/*if ($row['name'] == $level_b_ownershop_name_1 || $row['name'] == $level_b_ownershop_name_2) {
					$level_b_ownershops[] = $key;
				}*/
			}
			if (in_array($partnerApply['ownership'], $customOwnershipKeys)) {
				return self::PARTNER_LEVEL_B;
			}
		}



		return self::PARTNER_LEVEL_C;
	}

	/**
	 * @desc 合作方附件完整性校验
	 * @param $partnerApply | array 合作方信息
	 * @return string
	 */
	public static function checkAttachmentsIntegrity($partnerApply) {
		$map = Map::$v;
		if ((count($partnerApply) == 1 && !empty($partnerApply['partner_id']))) {
			$partnerInfo = PartnerApply::model()->findAllToArray('partner_id=' . $partnerApply['partner_id']);
			$partnerApply = $partnerInfo[0];
		}
		if(!empty($partnerApply['partner_id']) && empty($partnerApply['name'])) {
			$partnerApply['name'] = PartnerApply::getPartnerNameById($partnerApply['partner_id']);
		}
		if (!empty($partnerApply['custom_level'])) {
			$level = $partnerApply['custom_level'];
		} else {
			$level = PartnerApplyService::getPartnerLevel($partnerApply);
		}
		$requiredAttachments = array();
		if (!empty($level) && !empty($partnerApply['business_type'])) {
		    $partnerApply['apply_amount'] = isset($partnerApply['apply_amount']) ? $partnerApply['apply_amount'] : 0;
		    $amountType = PartnerService::getAttachmentAmountType($partnerApply['business_type'], $level, $partnerApply['apply_amount']/1000000);
		    if($amountType > 0) {
                $requiredAttachments = $map['partner_required_attachment_config'][$partnerApply['business_type']][$level][$amountType];
            }
		}

		if (count($requiredAttachments) > 0) {
			foreach ($requiredAttachments as $v) {
				$obj = PartnerApplyAttachment::model()->find("partner_id=" . $partnerApply['partner_id'] . " and type=" . $v . " and status=1");
				if (empty($obj->id)) {
					return "请上传：" . $map['partner_apply_attachment_type'][$v]['name'];
				}
			}
		}

		return "";
	}

	/**
	 * @desc 获取附件信息
	 * @param $partner_id | int
	 * @return array
	 */
	public static function getAttachment($partner_id) {
		if (empty($partner_id)) {
			return array();
		}
		$sql = "select * from t_partner_apply_attachment where partner_id=" . $partner_id . " and status=1 and type>1100 and type<1599  order by type asc";
		$data = Utility::query($sql);
		$attachments = array();

		foreach ($data as $v) {
			$attachments[$v["type"]][] = $v;
		}

		return $attachments;
	}


	//输出合作方类型
	public static function getPartnerType($type)
	{
		$typeDesc = "";
		if(!empty($type)){
			$map = Map::$v;
            $tArr = explode(',', $type);
            foreach ($tArr as $key => $value) {
                $types[] = $map["partner_type"][$value];
            }
            $typeDesc = implode('&nbsp;|&nbsp;', $types);
        }
        return $typeDesc;
	}
}
