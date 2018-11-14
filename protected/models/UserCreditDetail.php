<?php

/**
 * Desc: 个人信用额度明细
 * User: susiehuang
 * Date: 2017/4/14 0014
 * Time: 12:06
 */
class UserCreditDetail extends BaseActiveRecord {
	const STATUS_SUBMIT = 1; //已提交

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return "t_user_credit_detail";
	}

	/**
	 * @desc 保存，同时修改user_credit表中额度信息，并添加log
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
			parent::save();
			//更新user_credit额度信息
			$this->updateUserCreditInfo();

			//添加log
			if (empty($this->credit_id)) {
				$logRemark = '录入个人额度信息';
			} else {
				$logRemark = '调整个人额度信息';
			}
			$this->addUserCreditLog($logRemark, $this);
			if (!$isInDbTrans) {
				$trans->commit();
			}

			return 1;
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
	 * @desc 更新system_user表信息
	 * @return array|int
	 */
	public function updateUserCreditInfo() {
		if (!empty($this->credit_amount) && !empty($this->user_id)) {
			$currUserId = Utility::getNowUserId();
			$sql = "update t_user_credit set credit_amount=" . $this->credit_amount . ", update_user_id=" . $currUserId . ",update_time='" . date('Y-m-d H:i:s') . "' where user_id=" . $this->user_id;
			$userCreditObj = UserCredit::model()->findByPk($this->user_id);
			if (empty($userCreditObj->user_id)) {
				$sql = "insert into t_user_credit(user_id, credit_amount, create_user_id, create_time, update_user_id, update_time)
						values(" . $this->user_id . "," . $this->credit_amount . "," . $currUserId . ",'" . date('Y-m-d H:i:s') . "'," . $currUserId . ",'" . date('Y-m-d H:i:s') . "')";
			}
			Utility::executeSql($sql);
		}
	}

	/**
	 * @desc 添加操作日志
	 * @param $logRemark | string 日志备注信息
	 * @param $object | object 操作对象
	 */
	public static function addUserCreditLog($logRemark, $object) {
		$create_user_id = isset($object->update_user_id) ? $object->update_user_id : $object->create_user_id;
		$create_time = isset($object->update_time) ? $object->update_time : $object->create_time;
		if (doubleval($object->getOldAttribute("credit_amount")) != doubleval($object->credit_amount)) {
			$oldVal = $object->getOldAttribute("credit_amount");
			$oldVal = !empty($oldVal) ? $oldVal : 0;
			$sql = "insert into t_user_credit_log(user_id,field_name,old_value,new_value,remark,create_user_id,create_time)
              values (" . $object->user_id . ",'credit_amount'," . $oldVal . "," . $object->credit_amount . ",'" . $logRemark . "'," . $create_user_id . ",'" . $create_time . "')";
			Utility::executeSql($sql);
		}
	}

	public static function formatLog($logData) {
		if (count($logData['data']['rows']) > 0) {
			foreach ($logData['data']['rows'] as $key => $row) {
				$userInfo = SystemUser::getUser($row["create_user_id"]);
				$logData['data']['rows'][$key]['create_user_name'] = $userInfo['user_name'];
				$operation_content = "调整前：￥" . number_format($row['old_value']/1000000, 2) . "万元  调整后：￥" . number_format($row['new_value']/1000000, 2) . '万元；';
				$logData['data']['rows'][$key]['operation_content'] = $operation_content;
			}
		}

		return $logData;
	}

	/**
	 * @desc 检查个人信用额度是否正确
	 * @param $params | array
	 * @return string
	 */
	public static function checkCreditAmountIsValid($params) {
		if (!empty($params['other_json']) && is_array($params['other_json']) && count($params['other_json']) > 0) {
			foreach ($params['other_json'] as $key => $row) {
				$params[$key] = $row;
			}

			/*$requiredParams = array("bank_amount", "jyb_amount", "credit_amount", "stock", "equity", "property", "vehicle", "liquid_assets", "fixed_assets");
			$paramsCheckInfo = Utility::checkRequiredParams($params, $requiredParams);
			if (!$paramsCheckInfo['isValid']) {
				return "*号标注字段不得为空！";
			}
			$params = $paramsCheckInfo['params'];*/

			$calculateVal = (floatval($params['bank_amount']) + floatval($params['jyb_amount']) + floatval($params['stock']) + floatval($params['equity']) + floatval($params['property']) + floatval($params['vehicle']) + floatval($params['liquid_assets']) + floatval($params['fixed_assets'])) * 8;
			if ($calculateVal != $params['credit_amount']) {
				return "确认额度需要重新计算，请点击计算！";
			}
		} else {
			return "参数传入错误，请检查！";
		}

		return "";
	}
}