<?php

/**
 * Created by youyi000.
 * DateTime: 2017/3/27 17:50
 * Describe：
 */
class PartnerApply extends BaseActiveRecord {
	const STATUS_NEW = 0; //已保存
	const STATUS_BACK = 9;//风控初审驳回
	const STATUS_SUBMIT = 10;//提交风控初审中
	const STATUS_ON_RISK_BACK = 15;//现场风控驳回
	const STATUS_ON_RISK = 25;//现场风控中
	const STATUS_REVIEW = 30;//会议评审中
	const STATUS_ADD_INFO_NEED_REVIEW = 40;//补充资料需再评审
	const STATUS_ADD_INFO_NOT_REVIEW = 45;//补充资料无需再评
	const STATUS_REJECT = -1;//评审否决
	const STATUS_PASS = 99;//评审通过

	// const TYPE_UPDOWNSTREAM = 0;
	const TYPE_UPSTREAM = 1;
	const TYPE_DOWNSTREAM = 2;
	const TYPE_AGENT=3;

	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 't_partner_apply';
	}

	/**
	 * @desc 保存，添加操作日志
	 * @return bool|string
	 * @throws Exception
	 */
	public function save($runValidation=true,$attributes=null)
    {
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
			PartnerService::addPartnerLog($logRemark, $this);
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

	protected function afterSave()
    {
		if ($this->status != $this->oldAttributes["status"])
		{
			$timeSpan = strtotime("now") - strtotime($this->oldAttributes["status_time"]);
			if (!empty($this->oldAttributes["status"])) {
				$this->addStatusLog($this->oldAttributes["status"], $timeSpan);
			}
		}

        if($this->type!=$this->getOldAttribute("type"))
        {

            if(empty($this->type))
            {
                PartnerTypeRelation::model()->deleteAll("partner_id=".$this->partner_id."");
            }
            else
            {
                $types=explode(",",$this->type);

                $models=PartnerTypeRelation::model()->findAll("partner_id=".$this->partner_id."");
                $relations=ModelService::modelsToKeyModels($models,"type");
                if(is_array($types))
                {
                    foreach ($types as $v)
                    {
                        if(!isset($relations[$v]))
                        {
                            $model=new PartnerTypeRelation();
                            $model->partner_id=$this->partner_id;
                            $model->type=$v;
                            $model->update_time=new CDbExpression("now()");
                            $model->save();
                        }
                    }
                }
                PartnerTypeRelation::model()->deleteAll("partner_id=".$this->partner_id." and type not in(".$this->type.")");
            }
        }

	}

	/**
	 * 重写父方法，处理金额的问题
	 * @return array
	 */
	public function getUpdateLog() {
		$data = parent::getUpdateLog();
		if (key_exists("credit_amount", $data)) {
			if (doubleval($data["credit_amount"]["oldValue"]) == doubleval($data["credit_amount"]["newValue"])) {
				unset($data["credit_amount"]);
			}
		}
		if (key_exists("apply_amount", $data)) {
			if (doubleval($data["apply_amount"]["oldValue"]) == doubleval($data["apply_amount"]["newValue"])) {
				unset($data["apply_amount"]);
			}
		}

		return $data;
	}

	/**
	 * 添加项目状态变更记录
	 * @param $oldStatus
	 * @param $timeSpan
	 */
	public function addStatusLog($oldStatus, $timeSpan) {
		$sql = "insert into t_partner_apply_log(partner_id,field_name,old_value,new_value,timespan,remark,create_user_id,create_time)
              values (" . $this->partner_id . ",'status'," . $oldStatus . "," . $this->status . "," . $timeSpan . ",'','" . $this->update_user_id . "',now());";
		Utility::executeSql($sql);
	}


	/**
	 * @desc 格式化额度调整操作日志输出
	 * @param $logData | array 分页结果数组
	 * @return array
	 */
	public static function formatPartnerLog($logData) {
		if (count($logData['data']['rows']) > 0) {
			foreach ($logData['data']['rows'] as $key => $row) {
				$userInfo = SystemUser::getUser($row["create_user_id"]);
				$logData['data']['rows'][$key]['create_user_name'] = $userInfo['user_name'];
				$contentArray = json_decode($row["content"], true);
				$operation_content = $row["remark"] . "。";
				if (count($contentArray) > 0) {
					foreach ($contentArray as $k => $v) {
						if ($k == 'apply_amount') {
							$operation_content .= "调整前：" . number_format($v['oldValue'] / 1000000, 2) . "  调整后：" . number_format($v['newValue'] / 1000000, 2) . '；';
						}
					}
				}
				$logData['data']['rows'][$key]['operation_content'] = $operation_content;
			}
		}

		return $logData;
	}

	/**
	 * @desc 格式化额度调整操作日志输出
	 * @param $logData | array 分页结果数组
	 * @return array
	 */
	public static function formatPartnerApplyLog($logData) {
		if (count($logData['data']['rows']) > 0) {
			foreach ($logData['data']['rows'] as $key => $row) {
				$temp = array();
				$logInfo = array();
				$userInfo = SystemUser::getUser($row["create_user_id"]);
				$logData['data']['rows'][$key]['create_user_name'] = $userInfo['user_name'];
				$logData['data']['rows'][$key]['content'] = json_decode($logData['data']['rows'][$key]['content'], true);
				$contentArray = json_decode($row["content"], true);
				if (count($contentArray) > 0) {
					foreach ($contentArray as $k => $r) {
						$map = Map::$v;
						$temp['field'] = $k;
						$temp['field_name'] = $map['partner_apply_fields_name'][$k];
						$temp['oldValue'] = $r['oldValue'];
						$temp['newValue'] = $r['newValue'];
						if ($k == "ownership") {
							$temp['oldValue'] = !empty($temp['oldValue']) ? Ownership::getOwnershipNameById($temp['oldValue']) : "无";
							$temp['newValue'] = !empty($temp['newValue']) ? Ownership::getOwnershipNameById($temp['newValue']) : "无";
						}
						if ($k == "runs_state") {
							$temp['oldValue'] = !empty($temp['oldValue']) ? $map['runs_state'][$temp['oldValue']] : "无";
							$temp['newValue'] = !empty($temp['newValue']) ? $map['runs_state'][$temp['newValue']] : "无";
						}
						if ($k == "is_stock") {
							$temp['oldValue'] = !empty($temp['oldValue']) ? "是" : "否";
							$temp['newValue'] = !empty($temp['newValue']) ? "是" : "否";
						}
						if ($k == "business_type") {
							$temp['oldValue'] = !empty($temp['oldValue']) ? $map['business_type'][$temp['oldValue']] : "无";
							$temp['newValue'] = !empty($temp['newValue']) ? $map['business_type'][$temp['newValue']] : "无";
						}
						if ($k == "type") {
							$temp['oldValue'] = !empty($temp['oldValue']) ? str_replace('&nbsp;', " ", PartnerApplyService::getPartnerType($temp['oldValue'])) : "无";
							$temp['newValue'] = !empty($temp['newValue']) ? str_replace('&nbsp;', " ", PartnerApplyService::getPartnerType($temp['newValue'])): "无";
						}
						if ($k == "apply_amount" || $k == "credit_amount") {
							$temp['oldValue'] = "￥" . number_format($temp['oldValue'] / 1000000, 2) . "万元";
							$temp['newValue'] = "￥" . number_format($temp['newValue'] / 1000000, 2) . "万元";
						}
						if ($k == "user_id" || $k == "update_user_id") {
							$temp['oldValue'] = !empty($temp['oldValue']) ? SystemUser::getUserNameById($temp['oldValue']) : "无";
							$temp['newValue'] = !empty($temp['newValue']) ? SystemUser::getUserNameById($temp['newValue']) : "无";
						}
						if ($k == "goods_ids" && !empty($temp['oldValue']) && !empty($temp['newValue'])) {
							$temp['oldValue'] = !empty($temp['oldValue']) ? GoodsService::getSpecialGoodsNames($temp['oldValue']) : "无";
							$temp['newValue'] = !empty($temp['newValue']) ? GoodsService::getSpecialGoodsNames($temp['newValue']) : "无";
						}
						if ($k == "custom_level" || $k == "auto_level" || $k == "level") {
							$temp['oldValue'] = !empty($temp['oldValue']) ? $map['partner_level'][$temp['oldValue']] : "无";
							$temp['newValue'] = !empty($temp['newValue']) ? $map['partner_level'][$temp['newValue']] : "无";
						}
						if ($k == "status") {
							$temp['oldValue'] = !empty($temp['oldValue']) ? $map['partner_status_log'][$temp['oldValue']] : "无";
							$temp['newValue'] = !empty($temp['newValue']) ? $map['partner_status_log'][$temp['newValue']] : "无";
						}
						$logInfo[] = $temp;
					}
					$logData['data']['rows'][$key]['content'] = $logInfo;
				}
			}
		}

		return $logData;
	}

	/**
	 * @desc 获取合作方名
	 * @param $partner_id
	 * @return string
	 */
	public static function getPartnerNameById($partner_id) {
		$obj = PartnerApply::model()->findByPk($partner_id);
		if(!empty($obj->partner_id)) {
			return $obj->name;
		}else{
			return "";
		}
	}
}