<?php

/**
 * Desc: 合作方白名单partner_white model
 * User: susie
 * Date: 17/3/27
 * Time: 15:45
 */
class PartnerWhite extends BaseActiveRecord {
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return "t_partner_white";
	}

	/**
	 * @desc 保存，同时进行验重，添加操作日志
	 * @return bool|string
	 * @throws Exception
	 */
	public function save($runValidation=true,$attributes=null) {
		$isInDbTrans = Utility::isInDbTrans();
		if (!$isInDbTrans) {
			$db = Mod::app()->db;
			$trans = $db->beginTransaction();
		}

		try {
			if ($this->isNewRecord) {
				$logRemark = '新增';
			} else {
				$logRemark = '修改';
			}
			parent::save();
			$this->addPartnerLog($logRemark);
			if (!$isInDbTrans) {
				$trans->commit();
			}

			return true;
		} catch (Exception $e) {
			if (!$isInDbTrans) {
				try {
					$trans->rollback();
				} catch (Exception $ee) {
				}

				return $e->getMessage();
			} else {
				throw $e;
			}
		}
	}

	/**
	 * @desc 添加合作方白名单管理操作日志
	 * @param $logRemark | string 日志备注信息
	 */
	public function addPartnerLog($logRemark) {
		$create_user_id = isset($this->update_user_id) ? $this->update_user_id : $this->create_user_id;
		$create_time = isset($this->update_time) ? $this->update_time : $this->create_time;
		$content = count($this->getUpdateLog()) > 0 ? json_encode($this->getUpdateLog()) : '';

		$sql = "insert into t_partner_log(object_id,table_name,content,remark,create_user_id,create_time)
              values (" . $this->id . ",'" . $this->tableName() . "','" . $content . "','" . $logRemark . "'," . $create_user_id . ",'" . $create_time . "')";
		Utility::executeSql($sql);
	}

	/**
	 * @desc 格式化白名单操作日志输出
	 * @param $logData | array 分页结果数组
	 * @return array
	 */
	public static function formatPartnerLog($logData) {
		$map = Map::$v;
		if (count($logData['data']['rows']) > 0) {
			foreach ($logData['data']['rows'] as $key => $row) {
				$userInfo = SystemUser::getUser($row["create_user_id"]);
				$logData['data']['rows'][$key]['create_user_name'] = $userInfo['user_name'];
				$contentArray = json_decode($row["content"], true);
				$operation_content = $row["remark"] . "。";
				if (count($contentArray) > 0) {
					foreach ($contentArray as $k => $v) {
						if ($k == 'level') {
							$operation_content .= $map['partner_white_field_name'][$k] . "从" . $map['partner_level'][$v['oldValue']] . "到" . $map['partner_level'][$v['newValue']] . '；';
						}
						if ($k == "status") {
							$operation_content .= $map['partner_white_field_name'][$k] . "从" . $map['partner_white_status'][$v['oldValue']] . "到" . $map['partner_white_status'][$v['newValue']] . '；';
						}
					}
				}
				$logData['data']['rows'][$key]['operation_content'] = $operation_content;
			}
		}

		return $logData;
	}
}