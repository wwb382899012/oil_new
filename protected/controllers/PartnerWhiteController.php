<?php

/**
 * Desc: 合作方白名单管理
 * User: susieh
 * Date: 17/3/27
 * Time: 15:16
 */
class PartnerWhiteController extends Controller {
	public function pageInit() {
		$this->filterActions = "";
		$this->rightCode = "partnerWhite";
	}

	public function actionIndex() {
		$params = $_GET['search'];
		$sql = "select {col} from t_partner_white a left join t_ownership b on a.ownership=b.id " . $this->getWhereSql($params) . " order by a.id desc {limit}";
		$data = $this->queryTablesByPage($sql, 'a.*,b.name as ownership_name');
		$this->render("index", $data);
	}

	public function actionAdd() {
		$this->pageTitle = "添加合作方白名单";
		$this->render("edit");
	}

	public function actionEdit() {
		$id = Mod::app()->request->getParam("id");
		if (!Utility::checkQueryId($id)) {
			$this->renderError("非法参数！", "/partnerWhiter/");
		}

		$obj = PartnerWhite::model()->findByPk($id);
		if (empty($obj->id)) {
			$this->renderError("当前信息不存在！", "/partnerWhiter/");
		}

		$this->pageTitle = "修改合作方白名单";
		$this->render("edit", array("data" => $obj->attributes));
	}

	public function actionSave() {
		$params = $_POST["data"];
		if (empty($params['id']) && empty($params['name'])) {
			$this->returnError("企业名称不得为空！");
		}

		if (!empty($params["id"])) {
			$obj = PartnerWhite::model()->findByPk($params["id"]);
		}

		if (empty($obj->id)) {
			$obj = new PartnerWhite();
			$obj->create_user_id = $this->userId;
			$obj->create_time = date('Y-m-d H:i:s');
		} else {
			unset($params['name']);
			unset($params['corporate']);
			unset($params['registered_capital']);
			unset($params['start_date']);
		}

		if (isset($params['name'])) {
			$params['name'] = trim($params['name']);
			$oldObj = PartnerWhite::model()->find("name='" . $params["name"] . "'");
			if (!empty($oldObj->id) && $obj->id != $oldObj->id) {
				$this->returnError("当前企业名称已经存在，请重新填写！");
			}
		}

		unset($params['id']);
		$obj->setAttributes($params, false);

		$obj->update_user_id = $this->userId;
		$obj->update_time = date('Y-m-d H:i:s');
		$logRemark = ActionLog::getEditRemark($obj->isNewRecord, "合作方白名单");
		$res = $obj->save();
		if ($res === true) {
			//添加操作日志
			Utility::addActionLog(json_encode($obj->oldAttributes), $logRemark, "PartnerWhite", $obj->id);
			$this->returnSuccess($obj->id);
		} else {
			$this->returnError("保存失败！" . $res);
		}
	}

	public function actionDetail() {
		$id = Mod::app()->request->getParam("id");
		if (!Utility::checkQueryId($id)) {
			$this->renderError("非法参数！", "/partnerWhiter/");
		}

		$obj = PartnerWhite::model()->findByPk($id);
		if (empty($obj->id)) {
			$this->renderError("当前信息不存在！", "/partnerWhiter/");
		}

		$sql = "select {col} from t_partner_log where object_id =" . $id . " and table_name ='" . $obj->tableName() . "' order by create_time desc {limit}";
		$logData = PartnerWhite::formatPartnerLog($this->queryTablesByPage($sql, '*'));

		$this->pageTitle = "合作方白名单详情";
		$this->render('detail', array("data" => $obj->attributes, "logData" => $logData['data']));
	}
}