<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/19 14:37
 * Describe：
 */

class HomeController extends Controller
{
    public function actionIndex() {
        echo "OK";
    }

    /**
     * @api {GET} / [90020001-map] MAP文件
     * @apiName map
     * @apiExample {json} 输入示例:
     * {
     *
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回，注意，该接口为异步接口，只返回接收成功：
     * {
     *      "code":0,
     *      "data":{}
     * }
     * 失败返回：
     * {
     *      "code":1,
     *      "data":{}
     * }
     * @apiParam (输出字段) {string} code 错误码
     * @apiParam (输出字段) {array} data 成功时返回交易号，错误时返回错误码
     * @apiGroup home
     * @apiVersion 1.0.0
     */
    public function actionMap() {
        $this->returnJson(Map::$v);
    }

    /**
     * @api {GET} / [90020001-getMenu] 系统菜单树
     * @apiName getMenu
     * @apiExample {json} 输入示例:
     * {
     *
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回，注意，该接口为异步接口，只返回接收成功：
     * {
     *      "code":0,
     *      "data":{}
     * }
     * 失败返回：
     * {
     *      "code":1,
     *      "data":{}
     * }
     * @apiParam (输出字段) {string} code 错误码
     * @apiParam (输出字段) {array} data 成功时返回交易号，错误时返回错误码
     * @apiGroup home
     * @apiVersion 1.0.0
     */
    public function actionGetMenu() {
        $treeData = SystemUser::getFormattedRightCodes($this->userId);
        $this->returnJson($treeData["tree"]);
    }

    /**
     * @api {GET} / [90020001-task] 代办事项
     * @apiName task
     * @apiExample {json} 输入示例:
     * {
     *
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回，注意，该接口为异步接口，只返回接收成功：
     * {
     *      "code":0,
     *      "data":{}
     * }
     * 失败返回：
     * {
     *      "code":1,
     *      "data":{}
     * }
     * @apiParam (输出字段) {string} code 错误码
     * @apiParam (输出字段) {array} data 成功时返回交易号，错误时返回错误码
     * @apiGroup home
     * @apiVersion 1.0.0
     */
    public function actionTask() {
        $tasks = $this->getUserTasks();
        $this->returnJson($tasks);
    }

    /**
     * @api {GET} / [90020001-getUserInfo] 获取用户基本信息
     * @apiName getUserInfo
     * @apiExample {json} 输入示例:
     * {
     *
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回，注意，该接口为异步接口，只返回接收成功：
     *{
     *    "state": 0,
     *    "data": {
     *        "user_id": "1",
     *        "user_name": "admin",
     *        "name": "管理员",
     *        "email": "",
     *        "phone": "0",
     *        "main_role_id": "1",
     *        "role_ids": "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26",
     *        "corp_ids": "18,17,16,15,14,13,9,8,7,6,5,4,3,2,1",
     *        "identity": "",
     *        "weixin": "",
     *        "now_main_role_id": "3"  //用户当前角色ID
     *    }
     *}
     * 失败返回：
     * {
     *      "code":1,
     *      "data":{}
     * }
     * @apiParam (输出字段) {string} code 错误码
     * @apiParam (输出字段) {array} data 成功时返回交易号，错误时返回错误码
     * @apiGroup home
     * @apiVersion 1.0.0
     */
    public function actionGetUserInfo() {
        $user = Utility::getNowUser();
        $nowRoleId = UserService::getNowUserMainRoleId();
        $user['now_main_role_id'] = $nowRoleId;
        $this->returnJson($user);
    }


    /**
     * @api {GET} / [90020001-getUserRoles] 获取用户的所有角色
     * @apiName getUserRoles
     * @apiExample {json} 输入示例:
     * {
     *
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回，注意，该接口为异步接口，只返回接收成功：
     *{
     *    "state": 0,
     *    "data": [
     *        {
     *            "role_id": "1",
     *            "role_name": "系统管理员"
     *        },
     *        {
     *            "role_id": "2",
     *            "role_name": "业务助理"
     *        },
     *        {
     *            "role_id": "3",
     *            "role_name": "业务主管"
     *        },
     *        {
     *            "role_id": "4",
     *            "role_name": "商务导入"
     *        }
     *    ]
     *}
     * 失败返回：
     * {
     *      "code":1,
     *      "data":{}
     * }
     * @apiParam (输出字段) {string} code 错误码
     * @apiParam (输出字段) {array} data 成功时返回交易号，错误时返回错误码
     * @apiGroup home
     * @apiVersion 1.0.0
     */
    public function actionGetUserRoles() {
        $roles = UserService::getUserRoles();
        $this->returnJson(array_values($roles));
    }

    /**
     * @api {GET} / [90020001-setRole] 切换用户的角色
     * @apiName setRole
     * @apiExample {json} 输入示例:
     * ?id=11
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回，注意，该接口为异步接口，只返回接收成功：
     *{
     *    "state": 0,
     *    "data": [
     *        {
     *            "role_id": "1",
     *            "role_name": "系统管理员"
     *        },
     *        {
     *            "role_id": "2",
     *            "role_name": "业务助理"
     *        },
     *        {
     *            "role_id": "3",
     *            "role_name": "业务主管"
     *        },
     *        {
     *            "role_id": "4",
     *            "role_name": "商务导入"
     *        }
     *    ]
     *}
     * 失败返回：
     * {
     *      "code":1,
     *      "data":{}
     * }
     * @apiParam (输出字段) {string} code 错误码
     * @apiParam (输出字段) {array} data 成功时返回交易号，错误时返回错误码
     * @apiGroup home
     * @apiVersion 1.0.0
     */
    public function actionSetRole() {
        $id = Mod::app()->request->getParam("id");
        $roles = UserService::getUserRoles();
        $role = $roles[$id];
        if (empty($role))
            $this->returnJsonError("您没有所选角色的权限！");
        else {
            UserService::setMainRoleId($id);
            $this->returnJson($id);
        }
    }

    /**
     * @api {GET} / [90020001-userTasks] 获取小铃铛用户的待办
     * @apiName userTasks
     * @apiExample {json} 输入示例:
     * ?id=11
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回，注意，该接口为异步接口，只返回接收成功：
     *{
     *    "state": 0,
     *    "data": [
     *        {
     *            "action_id": "42",
     *            "action_name": "商务驳回项目",
     *            "icon": null,
     *            "list_url": "/project/?search[a.status]=1",
     *            "order_index": "0",
     *            "n": "1"
     *        },
     *        {
     *            "action_id": "60",
     *            "action_name": "合同平移审核驳回",
     *            "icon": null,
     *            "list_url": null,
     *            "order_index": "0",
     *            "n": "1"
     *        },
     *        {
     *            "action_id": "21",
     *            "action_name": "付款审核",
     *            "icon": "<i class=\"fa fa-fw fa-check\"></i>",
     *            "list_url": "/check13/?search[checkStatus]=1",
     *            "order_index": "21",
     *            "n": "12"
     *        }
     *    ]
     *}
     * 失败返回：  
     * {
     *      "code":1,
     *      "data":{}
     * }
     * @apiParam (输出字段) {string} code 错误码
     * @apiParam (输出字段) {array} data 成功时返回交易号，错误时返回错误码
     * @apiGroup home
     * @apiVersion 1.0.0
     */
    public function actionUserTasks() {
        $tasks = TaskService::getUserActions(Utility::getNowUserId());
        $this->returnJson($tasks);
    }

}