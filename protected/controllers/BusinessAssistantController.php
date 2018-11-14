<?php

/**
 * Desc: 业务负责人
 * User: susieh
 * Date: 2017/4/12
 * Time: 14:49
 */
class BusinessAssistantController extends AttachmentController {
	public function pageInit() {
		$this->attachmentType = Attachment::C_USER_EXTRA;
		$this->filterActions = "saveFile,getFile";
		$this->rightCode = "businessAssistant";
	}

	public function actionIndex() {
		$attr = $_GET[search];
		$sql = "select {col} from t_system_user a 
				left join t_user_extra b on a.user_id=b.user_id 
				left join t_user_role_relation c on a.user_id=c.user_id" . $this->getWhereSql($attr) .
		       " and c.role_id=3 and a.status=1 order by a.user_id desc {limit}";
		$data = $this->queryTablesByPage($sql, "a.user_id as main_user_id, a.name as main_name, a.phone, a.email, a.status, b.code, b.sex, b.id_code");
		$this->render("index", $data);
	}

	public function actionEdit() {
		$user_id = Mod::app()->request->getParam("user_id");
		if (!Utility::checkQueryId($user_id)) {
			$this->renderError("非法参数！", "/businessAssistant/");
		}

		$sql = "select a.user_id, a.name, b.code, a.phone, a.status, a.remark, b.sex, b.id_code, b.email, b.address, b.contact_person, b.contact_phone, b.contact_id_code
 				from t_system_user a left join t_user_extra b on a.user_id=b.user_id
				where a.user_id=" . $user_id . " order by a.user_id desc limit 1";

		$data = Utility::query($sql);
		if (Utility::isEmpty($data)) {
			$this->renderError("当前信息不存在！", "/businessAssistant/");
		}

		$userAttachments = SystemUser::getUserAttachments($user_id, $this->attachmentType);
		$this->pageTitle = "修改业务负责人信息";
		$this->render("edit", array("data" => $data[0], "attachments" => $userAttachments, "attachmentType" => $this->attachmentType));
	}

	public static function checkIsCanEdit() {
		/*$roleId = UserService::getNowUserMainRoleId();
		$busId = UserService::getAssistantsRoleId();
		if ($roleId == $busId) {
			return true;
		}*/

		return true;
	}

	public function actionSave() {
		$params = Mod::app()->request->getParam("data");
		$requiredParams = array("user_id", "name", "sex", "id_code", "address");
		$paramsCheckInfo = Utility::checkRequiredParams($params, $requiredParams);
		if (!$paramsCheckInfo['isValid']) {
			$this->returnError("*号标注字段不得为空！");
		}
		$params = $paramsCheckInfo['params'];

		$attachCheckInfo = Utility::checkRequiredAttachments($this->attachmentType, $params['user_id']);
		if (is_string($attachCheckInfo) && !empty($attachCheckInfo)) {
			$this->returnError($attachCheckInfo);
		}

		$userObj = SystemUser::model()->findByPk($params["user_id"]);
		if (empty($userObj->user_id)) {
			$this->returnError("用户不存在！");
		}

		$oldUserObj = SystemUser::model()->find("name='" . $params["name"] . "'");
		if (!empty($oldUserObj->user_id) && $userObj->user_id != $oldUserObj->user_id) {
			$this->returnError("当前姓名已经存在，请重新填写！");
		}

		$userExtraObj = UserExtra::model()->find("user_id=:userId", array("userId" => $params['user_id']));
		if (empty($userExtraObj->user_id)) {
			$userExtraObj = new UserExtra();
			$userExtraObj->create_time = date("Y-m-d H:i:s");
			$userExtraObj->create_user_id = Utility::getNowUserId();
		}

		$oldUserExtraObj = UserExtra::model()->find("name='" . $params["name"] . "'");
		if (!empty($oldUserExtraObj->user_id) && $userExtraObj->user_id != $oldUserExtraObj->user_id) {
			$this->returnError("当前姓名已经存在，请重新填写！");
		}

		$userExtraObj->setAttributes($params, false);
		$userExtraObj->update_time = date("Y-m-d H:i:s");
		$userExtraObj->update_user_id = Utility::getNowUserId();

		$logRemark = ActionLog::getEditRemark($userExtraObj->isNewRecord,'业务负责人');
		$res = $userExtraObj->save();
		if ($res === 1) {
			SystemUser::clearUserCache($userExtraObj->user_id);
			Utility::addActionLog(json_encode($userExtraObj->oldAttributes), $logRemark, "UserExtra", $userExtraObj->user_id);
			$this->returnSuccess($userExtraObj->user_id);
		} else {
			$this->returnError("保存失败:" . $res);
		}
	}

	public function actionDetail() {
		$user_id = Mod::app()->request->getParam('user_id');
		if (!Utility::checkQueryId($user_id)) {
			$this->renderError("非法参数！", "/businessAssistant/");
		}

		$sql = "select a.user_id, a.name as name, b.code, a.phone, a.status, a.remark, b.sex, b.id_code, b.email, b.address, b.contact_person, b.contact_phone, b.contact_id_code
 				from t_system_user a left join t_user_extra b on a.user_id=b.user_id
				where a.user_id=" . $user_id . " order by a.user_id desc limit 1";

		$data = Utility::query($sql);
		if (Utility::isEmpty($data)) {
			$this->renderError("当前信息不存在！", "/businessAssistant/");
		}

		$userAttachments = SystemUser::getUserAttachments($user_id, $this->attachmentType);
		$this->pageTitle = "查看业务负责人信息";
		$this->render("detail", array("data" => $data[0], "attachments" => $userAttachments, "attachmentType" => $this->attachmentType));
	}
}