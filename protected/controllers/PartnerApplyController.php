<?php

/**
 * Desc: 合作方管理
 * User: susieh
 * Date: 17/3/29
 * Time: 14:10
 */
class PartnerApplyController extends AttachmentController {
	public function pageInit() {
		$this->attachmentType = Attachment::C_PARTNER_APPLY;
		$this->filterActions = "getCompanies,checkInwhite,attachments,getOwnerships,getKeyNo";
		$this->rightCode = "partnerApply";
	}

	public function actionIndex() {
		$attr = $_GET[search];
		$sql = "select {col} from t_partner_apply a left join t_ownership b on a.ownership=b.id " . $this->getWhereSql($attr) . " order by partner_id desc {limit}";
		$data = $this->queryTablesByPage($sql, "a.*,b.name as ownership_name");
		if (Utility::isNotEmpty($data['data']['rows'])) {
            foreach ($data['data']['rows'] as $key => $row) {
                $type = PartnerApplyService::getPartnerType($row['type']);
                $data['data']['rows'][$key]['type'] = str_replace('&nbsp;', ' ', $type);
            }
        }
		$this->render("index", $data);
	}

	public function actionAdd() {
		$this->pageTitle = "添加合作方信息";
		$this->render("edit");
	}

	public function actionSave() {
		$params = $_POST['data'];
		if (!empty($params["partner_id"])) {
			$obj = PartnerApply::model()->findByPk($params["partner_id"]);
		}

		if (count($params) == 1 && !empty($params['partner_id'])) {
			$filterInjectParams = $params;
		} else {
			$requiredParams = array("contact_person", "contact_phone", "business_type", "user_id", "trade_info", "goods_ids", "describe");
			if (array_key_exists("type", $params) && $params['type'] != 1) {
				// array_push($requiredParams, "apply_amount");
                if(!array_key_exists('apply_amount', $params)) {
                    $this->returnError("*号标注字段不得为空！");
                }
			}
			if (empty($params['partner_id']) || (!empty($obj->partner_id) && $this->checkParamsCanEdit($obj->status))) {
				array_push($requiredParams, "name");
			}

			// print_r($requiredParams);die;
			$paramsCheckInfo = Utility::checkRequiredParams($params, $requiredParams);
			if (!$paramsCheckInfo['isValid']) {
				$this->returnError("*号标注字段不得为空！");
			}
			$filterInjectParams = $paramsCheckInfo['params'];

			$partnerLevel = PartnerApplyService::getPartnerLevel($filterInjectParams);
			if(is_string($partnerLevel) && !empty($partnerLevel)) {
				$this->returnError($partnerLevel);
			} else {
                if(!empty($filterInjectParams['auto_level']) && $filterInjectParams['auto_level'] != $partnerLevel){
                    $this->returnError("系统分级已改变，请重新点击检测系统分级按钮！");
                }
				$filterInjectParams['auto_level'] = $partnerLevel;
				$filterInjectParams['custom_level'] = !empty($filterInjectParams['custom_level']) ? $filterInjectParams['custom_level'] : $partnerLevel;
			}
		}

		//提交需检查附件资料完整性
		if (!$filterInjectParams['is_temp_save']) {
			$checkAttachmentsInfo = PartnerApplyService::checkAttachmentsIntegrity($filterInjectParams);
			if (!empty($checkAttachmentsInfo)) {
				$this->returnError($checkAttachmentsInfo);
			}
		}

		$isNew = 0;
		if (empty($obj->partner_id)) { //新增
			$isNew = 1;
			$obj = new PartnerApply();
			$obj->create_user_id = Utility::getNowUserId();
			$obj->create_time = date('Y-m-d H:i:s');
		} else { //修改
			if (!$this->checkIsCanEdit($obj->status)) { //检查是否可修改
				$this->returnError("该状态下，不允许修改合作方信息！");
			} else {
				if (!$this->checkParamsCanEdit($obj->status)) {
					unset($filterInjectParams['name']);
					unset($filterInjectParams['credit_code']);
					unset($filterInjectParams['registration_code']);
				}
			}
		}

		if (!empty($filterInjectParams['name'])) {
			$oldObj = PartnerApply::model()->find("name='" . $filterInjectParams["name"] . "'");
			if (!empty($oldObj->partner_id) && $oldObj->partner_id != $obj->partner_id) {
				$this->returnError("当前名称的合作方已经存在，请重新填写！");
			}
		}

		if ($filterInjectParams['is_temp_save'] == 1) {
			$filterInjectParams['status'] = PartnerApply::STATUS_NEW;
            $logRemark = ActionLog::getEditRemark($obj->isNewRecord, "合作方准入申请");
        } else {
			$filterInjectParams['status'] = PartnerApply::STATUS_SUBMIT;
            $logRemark = "提交合作方准入申请";
        }

		if (!empty($filterInjectParams['apply_amount'])) {
			$filterInjectParams['apply_amount'] = $filterInjectParams['apply_amount'] * 10000 * 100;
		}
		// print_r($filterInjectParams);die;
		$map = Map::$v;
		foreach ($map['partner_type'] as $key => $value) {
			if(!empty($params['type_'.$key]))
				$type[]= $key;
		}
		if(!empty($type))
			$filterInjectParams['type'] = implode(',',$type);

		unset($filterInjectParams['partner_id']);
		if(empty($filterInjectParams['start_date'])) $filterInjectParams['start_date']=null;
		$obj->setAttributes($filterInjectParams, false);
		$obj->update_user_id = Utility::getNowUserId();
		$obj->update_time = date('Y-m-d H:i:s');


		$trans = Utility::beginTransaction();
		try{
			$obj->save();

			if($isNew==1)
				TaskService::addPartnerTasks(Action::ACTION_1, $obj->partner_id, 0, $obj->create_user_id);
            if ($obj->status == PartnerApply::STATUS_SUBMIT) {
            	FlowService::startFlowForCheck30($obj->partner_id);

            	//TaskService::addPartnerTasks(Action::ACTION_2, $obj->partner_id, ActionService::getActionRoleIds(Action::ACTION_2));
            	TaskService::doneTask($obj->partner_id, Action::ACTION_1);
            }

            $trans->commit();

            Utility::addActionLog(json_encode($obj->oldAttributes), $logRemark, 'PartnerApply', $obj->partner_id);
			$this->returnSuccess($obj->partner_id);
		}catch(Exception $e){
			try{ $trans->rollback(); }catch(Exception $ee){}

			$this->returnError("操作失败！" . $e->getMessage());
		}
		/*$res = $obj->save();
		if ($res === true) {
			if ($obj->status == PartnerApply::STATUS_SUBMIT) {
				$flowRes = FlowService::startFlowForCheck30($obj->partner_id);
                if($flowRes===1)
                {
                    $this->returnSuccess($obj->partner_id);
                }
                else
                    $this->returnError("提交流程失败：" . $flowRes);
			}
			$this->returnSuccess($obj->partner_id);
		} else {
			$this->returnError("保存失败！" . $res);
		}*/
	}

	/**
	 * @desc 根据name获取合作方信息
	 */
	public function actionGetCompanies() {
		// $map = include(ROOT_DIR . "/protected/components/Map_old.php");
		$map = Map::$v;
		$name = $_GET['name'];
		$name = Utility::filterInject($name);
		if (empty($name)) {
			$this->returnError("企业名称不得为空！");
		}

		$partnerInfo = PartnerService::getPartnersInfo($name);
		if (count($partnerInfo) > 0) {
			foreach ($partnerInfo as $key => $row) {
				$partnerInfo[$key]['runs_state_desc'] = $map['runs_state'][$row['runs_state']];
			}
		}

		$ownerships = Ownership::getOwnerships();
		$this->returnSuccess(array("partnerInfo" => $partnerInfo, "ownerships" => $ownerships));
	}

	public function actionCheckLevel() {
		$params = $_POST['data'];
		$requiredParams = array("contact_person", "contact_phone", "business_type", "user_id", "trade_info", "goods_ids");
		$paramsCheckInfo = Utility::checkRequiredParams($params, $requiredParams);
		if (!$paramsCheckInfo['isValid']) {
			$this->returnError("*号标注字段不得为空！");
		}
		$systemCheckLevel = PartnerApplyService::getPartnerLevel($paramsCheckInfo['params']);
		// $map = include(ROOT_DIR . "/protected/components/Map_old.php");
		$map = Map::$v;
		$levelInfo = array('system_level' => $systemCheckLevel, 'level_desc' => $map['partner_level'][$systemCheckLevel]);
		$this->returnSuccess($levelInfo);
	}

	public function actionEdit() {
		// $map = include(ROOT_DIR . "/protected/components/Map_old.php");
		$map = Map::$v;
		$partner_id = Mod::app()->request->getParam("partner_id");
		if (!Utility::checkQueryId($partner_id)) {
			$this->renderError("非法参数！", "/partnerApply/");
		}

		$obj = PartnerApply::model()->findByPk($partner_id);
		if (empty($obj->partner_id)) {
			$this->renderError("当前信息不存在！", "/partnerApply/");
		}

		if (!$this->checkIsCanEdit($obj->status)) {
			$this->renderError("该状态下，不允许修改合作方信息！");
		}
		$isCanEditName = 1;
		if(!$this->checkParamsCanEdit($obj->status)) {
			$isCanEditName = 0;
		}

		$data = $obj->getAttributes(true, array("create_user_id", "create_time", "update_user_id", "update_time",));
		if (empty($data['is_stock'])) {
			unset($data['is_stock']);
		}

        $whiteModel = PartnerWhite::model()->find("name='" . $obj->name . "'");
        if (!empty($whiteModel->id)) {
            $data["white_level"] = $whiteModel->level;
            $data["auto_level"] = $whiteModel->level;
        } else {
            $data["white_level"] = 0;
        }

        if(!empty($obj->auto_level)) {
            $data['auto_level_desc'] = $map['partner_level'][$data["auto_level"]];
        }

        if(!empty($data['type'])){
			$typeArr = explode(",", $data['type']);
			foreach ($typeArr as $key => $value) {
				$data['type_'.$value]=1;
			}
		}
		unset($data['type']);

		$this->pageTitle = "修改合作方信息";
		$this->render("edit", array("data" => $data, "isCanEditName" => $isCanEditName));
	}

	public function actionDetail() {
		$partner_id = Mod::app()->request->getParam("id");
		if (!Utility::checkQueryId($partner_id)) {
			$this->renderError("非法参数！", "/partnerApply/");
		}

		$obj = PartnerApply::model()->findByPk($partner_id);
		if (empty($obj->partner_id)) {
			$this->renderError("当前信息不存在！", "/partnerApply/");
		}
		$attachments = PartnerApplyService::getAttachment($partner_id);

		$sql = "select {col} from t_partner_log where object_id =" . $partner_id . " and table_name ='" . $obj->tableName() . "' order by create_time desc {limit}";
		$logData = PartnerApply::formatPartnerApplyLog($this->queryTablesByPage($sql, '*'));

		$this->pageTitle = "合作方详情";
		$this->render('detail', array("data" => $obj->attributes, "attachments" => $attachments, "logData" => $logData['data']));
	}

	public function checkIsCanEdit($status) {
		if ($status >= PartnerApply::STATUS_SUBMIT && $status < PartnerApply::STATUS_PASS) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * @desc 检查字段是否可修改
	 * @param $status
	 * @return bool
	 */
	public function checkParamsCanEdit($status) {
		if (in_array($status, array(PartnerApply::STATUS_BACK, PartnerApply::STATUS_REJECT, PartnerApply::STATUS_PASS))) {
			return false;
		} else {
			return true;
		}
	}

	public function actionCheckInwhite() {
		$name = $_GET['name'];
		$name = Utility::filterInject($name);
		if (empty($name)) {
			$this->returnError("企业名称不得为空！");
		}
		$obj = PartnerWhite::model()->find("name='" . $name . "'");
		$this->returnSuccess($obj->attributes);
	}

	public function actionAttachments() {
		$partner_id = Mod::app()->request->getParam("partner_id");
		if (!Utility::checkQueryId($partner_id)) {
			$this->renderError("非法参数！", "/partnerApply/");
		}

		$obj = PartnerApply::model()->findByPk($partner_id);
		if (empty($obj->partner_id)) {
			$this->renderError("当前信息不存在！", "/partnerApply/");
		}
		if (!$this->checkIsCanEdit($obj->status)) {
			$this->renderError("当前状态不允许修改附件", "/partnerApply/");
		}
		$attachments = PartnerApplyService::getAttachment($partner_id);
		$this->pageTitle = "合作方附件上传";
		$this->render('attachments', array("data" => $obj->attributes, "attachments" => $attachments,));
	}

	public function getLevelAttach($map) {
		$res = array();
		$res["1"] = count($map["partner_required_attachment_config"]["1"]["1"]) || count($map["partner_required_attachment_config"]["2"]["1"]);
		$res["2"] = count($map["partner_required_attachment_config"]["1"]["2"]) || count($map["partner_required_attachment_config"]["2"]["2"]);
		$res["3"] = count($map["partner_required_attachment_config"]["1"]["3"]) || count($map["partner_required_attachment_config"]["2"]["3"]);
		$res["4"] = count($map["partner_required_attachment_config"]["1"]["4"]) || count($map["partner_required_attachment_config"]["2"]["4"]);

		return $res;
	}

	/*public function actionGetOwnerships() {
		$this->returnSuccess(Ownership::getOwnerships());
	}*/

	public function actionGetKeyNo() {
		$name = Mod::app()->request->getParam('name');
		$name = Utility::filterInject($name);
		if (empty($name)) {
			$this->returnError("企业名称不得为空！");
		}
		$this->returnSuccess(PartnerService::getKeyNo($name));
	}

	/**
	 * @desc 重写文件上传获取额外参数的方法
	 * @return array
	 */
	protected function getFileExtras() {
		return array();
	}
}