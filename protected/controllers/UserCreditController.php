<?php

/**
 * Desc: 业务员额度
 * User: susiehuang
 * Date: 2017/4/13 0013
 * Time: 17:13
 */
class UserCreditController extends AttachmentController {
	public function pageInit() {
		$this->attachmentType = Attachment::C_USER_CREDIT;
//		$this->filterActions = "saveFile,getFile";
		$this->filterActions = "";
		$this->rightCode = "userCredit";
	}

	public function actionIndex() {
		$attr = $_GET['search'];

//		$sql = "select {col} from t_user_extra a left join t_user_credit b on a.user_id=b.user_id left join t_user_credit_detail as c on b.user_id=c.user_id" . $this->getWhereSql($attr) . " and a.status=1 order by a.user_id desc {limit}";

		$sql = "select {col} from t_system_user a
				left join t_user_role_relation d on d.user_id = a.user_id 
				left join t_user_credit b on a.user_id=b.user_id 
				left join t_user_credit_detail c on b.user_id=c.user_id" .
		        $this->getWhereSql($attr) . " and a.status=1 and d.role_id=3 
		        order by a.user_id desc {limit}";
		$fields = "a.user_id, a.name, b.credit_amount, b.use_amount, b.frozen_amount, (b.credit_amount - b.use_amount - b.frozen_amount) as balance_amount, c.credit_id";
		$data = $this->queryTablesByPage($sql, $fields);
		$this->render("index", $data);
	}

	public function actionEdit() {
		$user_id = Mod::app()->request->getParam('user_id');
		$credit_id = Mod::app()->request->getParam('credit_id');

		if (!Utility::checkQueryId($user_id) && !Utility::checkQueryId($credit_id)) {
			$this->renderError("非法参数！", "/userCredit/");
		}

		/*if (!$this->checkIsCanOperate()) {
			$this->renderError("对不起，您无权执行该操作！", "/userCredit/");
		}*/

		$model = UserExtra::model()->with("credit")->findByPk($user_id);
		if(empty($model->user_id)) {
			$this->renderError("信息不完善，请先到“业务负责人”完善业务负责人信息！");
		}

		if (empty($model->credit->credit_id)) {
			$this->pageTitle = "录入个人额度信息";
			$data['user_id'] = $model->user_id;
		} else {
			$this->pageTitle = "调整个人额度信息";
			$data = Utility::numberFormatAttributes($model->credit->attributes, array('bank_amount', 'jyb_amount', 'credit_amount'));
			if (!empty($data['other_json'])) {
				$data["other_json"] = Utility::numberFormatAttributes(json_decode($data['other_json'], true), array('stock', 'equity', 'property', 'vehicle', 'liquid_assets', 'fixed_assets'));
			}
			$data['start_time'] = $data['start_time'] != '0000-00-00 00:00:00' ? date("Y-m-d", strtotime($data['start_time'])) : '';
			$data['end_time'] = $data['end_time'] != '0000-00-00 00:00:00' ? date("Y-m-d", strtotime($data['end_time'])) : '';
		}

		/*$data = array();
		if (!empty($user_id)) {
			$userData = UserExtra::model()->findAllToArray('user_id=' . $user_id);
			if (Utility::isEmpty($userData)) {
				$this->renderError("当前信息不存在！", "/userCredit/");
			}
			$data[0]['user_id'] = $userData[0]['user_id'];
			$this->pageTitle = "录入个人额度信息";
		} else {
			if (!empty($credit_id)) {
				$data = UserCreditDetail::model()->findAllToArray($credit_id);
				if (Utility::isEmpty($data)) {
					$this->renderError("当前信息不存在！", "/userCredit/");
				}
		
				if (!$this->checkIsCanEdit($data[0]['status'])) {
					$this->renderError("该状态下，不允许执行该操作！");
				}
				$this->pageTitle = "调整个人额度信息";
				if (!empty($data[0]['other_json'])) {
					$data[0]['other_json'] = json_decode($data[0]['other_json'], true);
				}
				$data[0]['start_time'] = date("Y-m-d", strtotime($data[0]['start_time']));
				$data[0]['end_time'] = date("Y-m-d", strtotime($data[0]['end_time']));
			}
		}*/

		$userAttachments = SystemUser::getUserAttachments($user_id, $this->attachmentType);
		$this->render("edit", array("obj" => $data, "attachments" => $userAttachments));
	}

	/**
	 * @desc 检查是是否可编辑
	 * @param $status | int
	 * @return bool
	 */
	public function checkIsCanEdit($status) {
		return true;
	}

	/**
	 * @desc 检查是否可执行操作
	 * @return bool
	 */
	/*public function checkIsCanOperate() {
		$roleId = UserService::getNowUserMainRoleId();
		$busId = array(UserService::getRiskRoleId(), UserService::getRiskManagerRoleId());
		if (in_array($roleId, $busId)) {
			return true;
		}

		return false;
	}*/

	public function actionSubmit() {
		$params = json_decode($_POST["data"],true);

		/*if (!$this->checkIsCanOperate()) {
			$this->renderError("对不起，您无权执行该操作！", "/userCredit/");
		}*/

		//检查个人信用额度计算是否正确
		$creditCheckInfo = UserCreditDetail::checkCreditAmountIsValid($params);
		if (is_string($creditCheckInfo) && !empty($creditCheckInfo)) {
			$this->returnError($creditCheckInfo);
		}

		if(isset($params['other_json']['errors'])) {
			unset($params['other_json']['errors']);
		}

		$params['other_json'] = json_encode($params['other_json']);

//		$requiredParams = array("user_id", "bank_amount", "jyb_amount", "credit_amount", "start_time", "end_time");
		$requiredParams = array("credit_amount");
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

		$obj = UserCreditDetail::model()->findByPk($params['credit_id']);
		if (empty($obj->credit_id)) {
			$obj = new UserCreditDetail();
			$obj->create_time = date("Y-m-d H:i:s");
			$obj->create_user_id = Utility::getNowUserId();

		}

		$obj->setAttributes($params, false);
		$obj->update_time = date("Y-m-d H:i:s");
		$obj->update_user_id = Utility::getNowUserId();

		$res = $obj->save();
		if ($res === 1) {
			$this->returnSuccess($obj->user_id);
		} else {
			$this->returnError("保存失败:" . $res);
		}
	}

	public function actionDetail() {
		$credit_id = Mod::app()->request->getParam('credit_id');
		if (!Utility::checkQueryId($credit_id)) {
			$this->renderError("非法参数！", "/userCredit/");
		}

		$model = UserCreditDetail::model()->findByPk($credit_id);
		if (empty($model->credit_id)) {
			$this->renderError("当前信息不存在！", "/userCredit/");
		}
        $data=$model->attributes;
		if (!empty($model['other_json'])) {
			$data['other_json'] = json_decode($data['other_json'], true);
			$otherArr = $data['other_json'];
			$data['other_amount'] = $otherArr['stock'] + $otherArr['equity'] + $otherArr['property'] + $otherArr['vehicle'] + $otherArr['liquid_assets'] + $otherArr['fixed_assets'];
		}

		$attachments = SystemUser::getUserAttachments($data['user_id'], $this->attachmentType);

		$sql = "select {col} from t_user_credit_log where user_id =" . $data['user_id'] . " order by create_time desc {limit}";
		$logData = UserCreditDetail::formatLog($this->queryTablesByPage($sql, '*'));

		$this->pageTitle = "查看个人额度信息";
		$this->render('detail', array("data" => $data, "attachments" => $attachments, "logData" => $logData['data']));
	}

	public function actionCreditDetail() {
		$user_id = Mod::app()->request->getParam('user_id');
		if (!Utility::checkQueryId($user_id)) {
			$this->renderError("非法参数！", "/userCredit/");
		}
		
		$model = UserExtra::model()->with("credit")->findByPk($user_id);
		if(empty($model->user_id)) {
			$this->renderError("信息不完善，请先到“业务负责人”完善业务负责人信息！");
		}

		/*$sql = "select {col} from t_project p
                left join t_project_detail u on p.project_id=u.project_id and u.type=1
                left join t_settlement su on p.project_id=su.project_id and su.type=1
                left join t_project_detail d on p.project_id=d.project_id and d.type=2
                left join t_settlement sd on p.project_id=sd.project_id and sd.type=2
                left join t_system_user uu on uu.user_id=p.manager_user_id
              	left join t_project_credit_apply_detail b on p.project_id=b.project_id
             	where p.status>=" . Project::STATUS_SUBMIT . "
             	and (p.manager_user_id=" . $this->userId . " 
                or exists(select project_id from t_project_credit_apply_detail where project_id=p.project_id and user_id=" . $user_id . " and status=" . ProjectCreditApplyDetail::STATUS_USED . ") )";*/

		$sql = "select {col} from t_user_credit_use_detail a 
				left join t_project p on a.project_id = p.project_id
				left join t_project_detail u on p.project_id = u.project_id and u.type = 1
				left join t_settlement su on p.project_id = su.project_id and su.type = 1
				left join t_project_detail d on p.project_id = d.project_id and d.type = 2
				left join t_settlement sd on p.project_id = sd.project_id and sd.type = 2
				left join t_system_user uu on uu.user_id = a.user_id
				where p.status >= " . Project::STATUS_SUBMIT . " and a.user_id = " . $user_id . "
				order by a.create_time desc";

		/*$fields = "p.*,b.amount-b.amount_free as used_amount,uu.name as manager_name,
                  u.price as up_price,u.quantity as up_quantity,u.amount as up_amount,
                  d.price as down_price,d.quantity as down_quantity,d.amount as down_amount,
                  su.price as su_price,su.quantity as su_quantity,su.amount as su_amount,
                  sd.price as sd_price,sd.quantity as sd_quantity,sd.amount as sd_amount";*/

		$fields = "a.detail_id,a.amount-a.amount_free as used_amount,a.project_id,a.user_id,
					p.project_name,p.trade_type,p.status as p_status, uu.name as user_name,
                    u.price as up_price,u.quantity as up_quantity,u.amount as up_amount,
                    d.price as down_price,d.quantity as down_quantity,d.amount as down_amount,
                    su.price as su_price,su.quantity as su_quantity,su.amount as su_amount,
                    sd.price as sd_price,sd.quantity as sd_quantity,sd.amount as sd_amount";

		$creditDetailData = $this->queryTablesByPage($sql, $fields);
		if (!empty($creditDetailData['data']['rows']) && is_array($creditDetailData['data']['rows']) && count($creditDetailData['data']['rows']) > 0) {
			foreach ($creditDetailData['data']['rows'] as $key => $row) {
			    if($row['p_status'] < Project::STATUS_SETTLE_CONFIRM) { //未结算以项目信息为主查询价格和数量
				    $creditDetailData['data']['rows'][$key]['actual_up_amount'] = $row['up_amount'];
				    $creditDetailData['data']['rows'][$key]['actual_up_quantity'] = $row['up_quantity'];
				    $creditDetailData['data']['rows'][$key]['actual_down_quantity'] = $row['down_quantity'];
			    } else {
				    $creditDetailData['data']['rows'][$key]['actual_up_amount'] = $row['su_amount'];
				    $creditDetailData['data']['rows'][$key]['actual_up_quantity'] = $row['su_quantity'];
				    $creditDetailData['data']['rows'][$key]['actual_down_quantity'] = $row['sd_quantity'];
			    }
			}
		}

		$sql = "select a.user_id, a.name, b.credit_amount, b.use_amount, (b.credit_amount - b.use_amount) as balance_amount 
				from t_user_extra a left join t_user_credit b on a.user_id=b.user_id where a.user_id=" . $user_id . " and a.status=1";
		$userCreditData = Utility::query($sql);
		if (Utility::isEmpty($userCreditData)) {
			$this->renderError("当前信息不存在！", "/userCredit/");
		}

		$this->pageTitle = "查看个人额度明细";
		$this->render("creditDetail", array("userCreditData" => $userCreditData[0], 'creditDetailData' => $creditDetailData));
	}
}