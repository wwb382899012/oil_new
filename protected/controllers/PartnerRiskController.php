<?php

/**
 * Desc: 现场风控
 * User: susieh
 * Date: 2017/4/7
 * Time: 19:58
 */
class PartnerRiskController extends AttachmentController {
	public function pageInit() {
		$this->attachmentType = Attachment::C_PARTNER_RISK;
		$this->filterActions = "";
		$this->rightCode = "partnerRisk";
	}

	public function actionIndex() {
		$params = $_GET['search'];
		$query = "";
		switch ($params["risk_status"]) {
			case PartnerRisk::RISK_STATUS_PASS:
				$query = " and exists(select 1 from t_partner_risk where a.partner_id=partner_id and status=" . PartnerRisk::STATUS_RISK_SUBMIT . ")";
				break;
			case PartnerRisk::RISK_STATUS_REJECT:
				$query = " and exists(select 1 from t_partner_risk where a.partner_id=partner_id and status=" . PartnerRisk::STATUS_RISK_REJECT . ")";
				break;
			default:
				$query = " and a.status>=" . PartnerApply::STATUS_ON_RISK . " and a.status<" . PartnerApply::STATUS_REVIEW . "";
				break;
		}
		if (empty($params["risk_status"])) {
			$params["risk_status"] = PartnerRisk::RISK_STATUS_WAIT_APPROVAL;
		}

		$status = $params['risk_status'];
		unset($params['risk_status']);

		$type = 0;
        if(!empty($params["a.type"])){
            $type = $params["a.type"];
            unset($params["a.type"]);
            $query .= " and find_in_set(".$type.",a.type) ";
        }

		$sql = "select {col} from t_partner_apply a 
                left join t_ownership b on a.ownership=b.id
                left join t_partner_risk r on a.partner_id=r.partner_id and r.status=".PartnerRisk::STATUS_RISK_NEW."
                " . $this->getWhereSql($params);
		$sql .= $query . " order by a.partner_id desc {limit}";
		// $sql .= "  and a.type in(" . PartnerApply::TYPE_DOWNSTREAM . "," . PartnerApply::TYPE_AGENT . ") " . $query . " order by a.partner_id desc {limit}";

		$data = $this->queryTablesByPage($sql, "a.*,b.name as ownership_name,ifNull(r.status,-1) as risk_status,r.risk_id");

		if (Utility::isNotEmpty($data['data']['rows'])) {
            foreach ($data['data']['rows'] as $key => $row) {
                $typeDesc = PartnerApplyService::getPartnerType($row['type']);
                $data['data']['rows'][$key]['type'] = str_replace('&nbsp;', ' ', $typeDesc);
            }
        }
		if (!empty($status)) {
			$params["risk_status"] = $status;
		}

		if(!empty($type))
            $params["a.type"] = $type;
        
		$data['search'] = $params;
		$this->render("index", $data);
	}

	/**
	 * @desc 判断是否可以修改
	 * @param $status | int
	 * @return bool
	 */
	public function checkIsCanEdit($status) {
		if ($status['r_status'] >= PartnerRisk::STATUS_RISK_SUBMIT || $status['p_status'] != PartnerApply::STATUS_ON_RISK) {
			return false;
		}

		return true;
	}

	/**
	 * @desc 检查是否可驳回(合作方处于现场风控中可驳回)
	 * @param $status | int
	 * @return bool
	 */
	public function checkIsCanReject($status) {
		if ($status == PartnerApply::STATUS_ON_RISK) {
			return true;
		}

		return false;
	}

	public function actionEdit() {
		$partner_id = Mod::app()->request->getParam("partner_id");
		$risk_id = Mod::app()->request->getParam("risk_id");
		if (!Utility::checkQueryId($partner_id) && !Utility::checkQueryId($risk_id)) {
			$this->renderError("非法参数！", "/partnerRisk/");
		}

		$sql = "";
		if (empty($risk_id)) { //添加
			$sql = "select partner_id,name as partner_name,type,auto_level,custom_level,level,apply_amount,credit_amount as o_credit_amount,status as p_status,business_type
                	from t_partner_apply where partner_id=" . $partner_id ." and status=".PartnerApply::STATUS_ON_RISK;
		} else { //修改
			$sql = "select a.name as partner_name,a.type,a.auto_level,a.custom_level,a.status as p_status,a.level,a.apply_amount,a.credit_amount as o_credit_amount,a.business_type,b.*
                	from t_partner_apply a left join t_partner_risk b on a.partner_id=b.partner_id 
                	where a.partner_id=" . $partner_id . " and risk_id=". $risk_id ." and a.status=" .PartnerApply::STATUS_ON_RISK. " order by b.risk_id desc limit 1";
		}

		$data = Utility::query($sql);
		if (Utility::isEmpty($data)) {
			$this->renderError("当前信息不存在！", "/partnerRisk/");
		}

		$riskAttachments = array();
		if (empty($risk_id)) {
			$data[0]['risk_id'] = IDService::getPartnerRiskId();
			$data[0]['status'] = array();
			$data[0]['status']['p_status'] = $data[0]['p_status'];
			$data[0]['status']['r_status'] = -1;

			$title = "添加";
		} else {
			$riskAttachments = PartnerRisk::getPartnerRiskAttachments($data[0]['risk_id']);
			$status = $data[0]['status'];
			unset($data[0]['status']);
			$data[0]['status'] = array();
			$data[0]['content'] = json_decode($data[0]['content']);
			$data[0]['status']['p_status'] = $data[0]['p_status'];
			$data[0]['status']['r_status'] = $status;
			$data[0]['start_time'] = date("Y-m-d", strtotime($data[0]['start_time']));
			$data[0]['end_time'] = date("Y-m-d", strtotime($data[0]['end_time']));

			if (!$this->checkIsCanEdit($data[0]['status'])) {
				$this->renderError("该状态下，不允许修改现场风控信息！");
			}
			$title = "修改";

		}

		$this->pageTitle = $title . "现场风控信息";
		$this->render("edit", array("data" => $data[0], "partnerRiskAttachments" => $riskAttachments));
	}

	public function actionSave() {
		$params = $_POST["obj"];
		$user = $this->getUser();
		if (!empty($params['content']) && is_array($params['content']) && count($params['content']) > 0) {
			$params['content'] = json_encode($params['content']);
		}
		$requiredParams = array("start_time", "end_time", "main_user_id", "uIds", "address");
		$paramsCheckInfo = Utility::checkRequiredParams($params, $requiredParams);
		if (!$paramsCheckInfo['isValid']) {
			$this->returnError("*号标注字段不得为空！");
		}

		$map = include(ROOT_DIR . "/protected/components/Map_old.php");
		if (!PartnerRisk::checkRiskAttachment($params['risk_id'])) {
			$this->returnError("*标注附件必传，请上传" . $map['partner_risk_attachment_type'][PartnerRisk::ATTACHMENT_RISK_REPORT]['name']);
		}
		$params = $paramsCheckInfo['params'];
		if (!empty($params["risk_id"])) {
			$obj = PartnerRisk::model()->findByPk($params["risk_id"]);
		} else {
			$this->returnError("非法参数！");
		}

		if (empty($obj->risk_id)) {
			$obj = new PartnerRisk();
			$obj->create_user_id = $user["user_id"];
			$obj->create_time = date("Y-m-d H:i:s");
		}
		$obj->setAttributes($params, false);
		$obj->status = !empty($params['is_temp_save']) ? PartnerRisk::STATUS_RISK_NEW : PartnerRisk::STATUS_RISK_SUBMIT;
		$obj->user_ids = $params["uIds"];
		$obj->update_user_id = $user["user_id"];
		$obj->update_time = date("Y-m-d H:i:s");
		$trans = Utility::beginTransaction();

		$logRemark = ActionLog::getEditRemark($obj->isNewRecord, "现场风控");
		try{
			$obj->save();
			if($obj->status == PartnerRisk::STATUS_RISK_SUBMIT) {
				PartnerService::updateApplyPartnerStatus($obj->partner_id, $obj->status, $obj->credit_amount); //更新合作方状态
				// 发送消息
				$partner = PartnerApply::model()->findByPk($obj->partner_id);
		        $typeStr = array();
		        $types = explode(',', $partner->type);
		        foreach ($types as $thisType) {
		            $typeStr[] = Map::$v['partner_type'][$thisType];
		        }
		        $typeStr = implode(',', $typeStr);
		        $taskParams = array('name'=>$partner->name, 'typeName'=>$typeStr);
				TaskService::addPartnerTasks(Action::ACTION_4, $obj->partner_id, ActionService::getActionRoleIds(Action::ACTION_4), "0", $taskParams);
            	TaskService::doneTask($obj->partner_id, Action::ACTION_3);
			}

			$trans->commit();

			Utility::addActionLog(json_encode($obj->oldAttributes), $logRemark, 'PartnerRisk', $obj->risk_id);
			$this->returnSuccess($obj->risk_id);

		}catch(Exception $e){
			try{ $trans->rollback(); }catch(Exception $ee){}

			$this->returnError("操作失败！" . $e->getMessage());
		}
		/*$res = $obj->save();
		if ($res === true) {
			if($obj->status == PartnerRisk::STATUS_RISK_SUBMIT) {
				PartnerService::updateApplyPartnerStatus($obj->partner_id, $obj->status, $obj->credit_amount); //更新合作方状态
			}
			$this->returnSuccess($obj->risk_id);
		} else {
			$this->returnError("保存失败" . $res);
		}*/
	}

	public function actionSubmit() {
		$partner_id = Mod::app()->request->getParam("partner_id");
		$status = Mod::app()->request->getParam("status");
		if (!Utility::checkQueryId($partner_id) || !Utility::checkQueryId($status)) {
			$this->returnError("非法参数！");
		}
  
		$obj = PartnerRisk::model()->find("partner_id=" . $partner_id . " order by risk_id desc");
		//$obj = PartnerRisk::model()->find($risk_id);
		if (empty($obj->risk_id)) { //未添加过现场风控
			if ($status == PartnerRisk::STATUS_RISK_SUBMIT) {
				$this->returnError("当前信息不存在！");
			} else {
				if($status == PartnerRisk::STATUS_RISK_REJECT) {
					PartnerService::updateApplyStatus($partner_id,PartnerApply::STATUS_ON_RISK_BACK);
            		TaskService::doneTask($partner_id, Action::ACTION_3);
					$flowRes = FlowService::startFlowForCheck30($partner_id);
					if (is_string($flowRes) && !empty($flowRes)) {
						$this->returnError($flowRes);
					} else {
						if ($flowRes === 1) {
							$this->returnSuccess($partner_id);
						} else {
							$this->returnError("操作失败！" . $flowRes);
						}
					}
				}
			}
		}

		$user = $this->getUser();
		$oldStatus =       $obj->status;
		$obj->status = $status;
		$obj->update_user_id = $user["user_id"];
		$obj->update_time = date("Y-m-d H:i:s");
		$res = $obj->save();
		if ($res === true) {
			PartnerService::updateApplyPartnerStatus($obj->partner_id, $status, $obj->credit_amount); //更新合作方状态
			if ($obj->status == PartnerApply::STATUS_ON_RISK_BACK) {
            	TaskService::doneTask($partner_id, Action::ACTION_3);
				$flowRes = FlowService::startFlowForCheck30($obj->partner_id);
				if (is_string($flowRes) && !empty($flowRes)) {
					$this->returnError($flowRes);
				} else {
					if ($flowRes === 1) {
						$this->returnSuccess($partner_id);
					} else {
						$this->returnError("操作失败！" . $flowRes);
					}
				}
			}
			Utility::addActionLog(json_encode(array('oldStatus' => $oldStatus)), "提交现场风控", "PartnerRisk", $obj->risk_id);
			$this->returnSuccess($obj->risk_id);
		} else {
			$this->returnError("操作失败！" . $res);
		}
	}

	/*public function actionReject() {
		$partner_id = Mod::app()->request->getParam("partner_id");
		if (!Utility::checkQueryId($partner_id)) {
			$this->returnError("非法参数！");
		}
		$res = PartnerService::updateApplyPartnerStatus($partner_id, PartnerApply::STATUS_ON_RISK_BACK); //更新合作方状态

		if ($res === 1) {
			FlowService::startFlowForCheck30($partner_id);
			$this->redirect("/partnerRisk/");
		} else {
			$this->renderError("操作失败！" . $res);
		}
	}*/

	public function actionDetail() {
		$partner_id = Mod::app()->request->getParam("partner_id");
		if (!Utility::checkQueryId($partner_id)) {
			$this->renderError("非法参数！", "/partnerRisk/");
		}

		$sql = "select partner_id,name as partner_name,type,auto_level,custom_level,level,apply_amount,credit_amount as o_credit_amount,status as p_status,business_type
              	from t_partner_apply where partner_id=" . $partner_id;
		$partnerApply = Utility::query($sql);

		$data = PartnerRisk::model()->findAllToArray(array("condition" => "partner_id=" . $partner_id, "order" => "risk_id desc"));
		/*if (Utility::isEmpty($data)) {
			$this->renderError("当前信息不存在！", "/partnerRisk/");
		}*/

		$riskAttachments = array();
		if (count($data) > 0) {
			foreach ($data as $key => $row) {
				$riskAttachments[$key] = PartnerRisk::getPartnerRiskAttachments($row['risk_id']);
				$status = $row['status'];
				unset($row['status']);
				$data[$key]['status'] = array();
				$data[$key]['content'] = json_decode($row['content']);
				$data[$key]['status']['p_status'] = $partnerApply[0]['p_status'];
				$data[$key]['status']['r_status'] = $status;
				$data[$key]['start_time'] = date("Y-m-d", strtotime($row['start_time']));
				$data[$key]['end_time'] = date("Y-m-d", strtotime($row['end_time']));
			}

		}

		$this->pageTitle = "查看现场风控详情";
		$this->render('detail', array("partner" => $partnerApply[0], "data" => $data, "attachments" => $riskAttachments));
	}

	/**
	 * @desc 重写文件上传获取额外参数的方法
	 * @return array
	 */
	protected function getFileExtras() {
		return array();
	}
}