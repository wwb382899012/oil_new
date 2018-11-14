<?php

use ddd\Split\Application\ContractTerminateService;
use ddd\Split\Domain\Model\Contract\ContractTerminateStatus;

/**
 * User: liyu
 * Date: 2018/6/14
 * Time: 16:19
 * Desc: 合同终止审核
 */
class Check25Controller extends ApiCheckController
{

    public $prefix = "check25_";

    public function initRightCode() {
        $attr = $this->getSearch();
        $checkStatus = $attr["checkStatus"];
        $this->treeCode = $this->prefix . $checkStatus;
        $this->rightCode = $this->prefix;
        $this->filterActions = "list,doCheck,save,detail";
    }

    public function pageInit() {
        parent::pageInit();
        $this->businessId = FlowService::BUSINESS_CONTRACT_TERMINATE_CHECK;
//        $this->indexViewName = "/check25/index";
//        $this->mainUrl = "/check25/";
//        $this->checkViewName = "/check25/check";
//        $this->detailViewName = "/check25/detail";
    }

    /**
     * @api {GET} / [List]合同终止审核列表
     * @apiName List
     * @apiParam (输入字段) {string} contract_code 合同编号
     * @apiParam (输入字段) {string} code_out 外部合同编号
     * @apiParam (输入字段) {int} contract_id 合同ID
     * @apiParam (输入字段) {int} type 合同类型
     * @apiParam (输入字段) {string} project_code 项目编码
     * @apiParam (输入字段) {int} project_type 项目类型
     * @apiParam (输入字段) {string} partner_name 合作方
     * @apiParam (输入字段) {string} corporation_name 交易主体
     * @apiParam (输入字段) {int} check_status 审核状态
     * @apiParam (输入字段) {int} page 页数 <font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     *      "contract_code":'PHP20180321',
     *      "code_out":PHP20180321,
     *      "contract_id":11,
     *      "type":2,
     *      "project_code":'ZPHP1ZJ18032101',
     *      "project_type":1,
     *      "partner_name":"phpdragon合作方有限公司",
     *      "corporation_name":"公司主体phpdragon",
     *      "check_status":1, map映射：contract_terminate_check_status
     *      "page":2,
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回，注意，该接口为异步接口，只返回接收成功：
     *{
     *    "state": 0,
     *    "data": {
     *    "data": {
     *        "pageCount": 1,
     *            "total": 2,
     *            "page": 1,
     *            "rows": [
     *                {
     *                    "contract_id": 1,
     *                    "contract_code": "phptest",
     *                    "type": 1,
     *                    "contract_date": "2018-07-25",
     *                    "amount": "100.00",
     *                    "amount_cny": "100.00",
     *                    "project_code": "phptest",
     *                    "project_type": 1,
     *                    "partner_name": "test_partner",
     *                    "name": "liyu",
     *                    "corporation_name": "test_corporation_name",
     *                    "code_out": "php_test_code_out",
     *                    "terminate_id": 1,
     *                    "checkStatus": 1, map映射：contract_terminate_check_status
     *                    "corporation_id": 1,
     *                    "partner_id": 1,
     *                    "is_can_check":true,
     *                    "is_can_view":false
     *                },
     *                {
     *                    "contract_id": 1,
     *                    "contract_code": "phptest",
     *                    "type": 1,
     *                    "contract_date": "2018-07-25",
     *                    "amount": "100.00",
     *                    "amount_cny": "100.00",
     *                    "project_code": "phptest",
     *                    "project_type": 1,
     *                    "partner_name": "test_partner",
     *                    "name": "liyu",
     *                    "corporation_name": "test_corporation_name",
     *                    "code_out": "php_test_code_out",
     *                    "terminate_id": 1,
     *                    "checkStatus": 1,
     *                    "corporation_id": 1,
     *                    "partner_id": 1,
     *                    "is_can_check":true,
     *                    "is_can_view":false
     *                }
     *            ]
     *        },
     *        "search": {
     *        "e.contract_code": "dddd",
     *            "e.contract_id": null,
     *            "e.type": null,
     *            "cf.code_out*": null,
     *            "f.project_code": null,
     *            "f.type": null,
     *            "c.name*": null,
     *            "g.name*": null,
     *            "checkStatus": null
     *        }
     *    }
     *}
     * 失败返回：
     * {
     *      "code":1,
     *      "data":{}
     * }
     * @apiParam (输出字段) {string} code 错误码
     * @apiParam (输出字段) {array} data 成功时返回交易号，错误时返回错误码
     * @apiGroup Check25
     * @apiVersion 1.0.0
     */

    public function actionList() {
        parent::actionList();
    }

    /**
     * @api {GET} /api/contract/check25/detail [90020001-detail] 合同平移审核详情
     * @apiName detail
     * @apiGroup Check23
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} check_id 审核对象id <font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     *      "check_id":12,
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回：
     *{
     *    "state": 0,
     *    "data": {
     *        "check_id": "5656",
     *        "remark": "",
     *        "status": "0",
     *        "status_name": "",
     *        "files": [],
     *        "logs": [
     *            {
     *                "result": "审核通过",
     *                "remark": "",
     *                "node_name": "风控审核",
     *                "checker": "李钰",
     *                "check_time": "2018-08-08 14:57:36"
     *            }
     *        ],
     *        "detail": {
     *            "id": "52",
     *            "contract_id": "1282",
     *            "reason": "发的说说地方单独sss",
     *            "files": [],
     *            "audit_log": [
     *                {
     *                    "id": "8347",
     *                    "check_id": "5653",
     *                    "detail_id": "8892",
     *                    "user_id": "109",
     *                    "obj_id": "1282",
     *                    "business_id": "25",
     *                    "flow_id": "32",
     *                    "node_id": "89",
     *                    "check_status": "1",
     *                    "check_time": "2018-08-08 14:57:36",
     *                    "is_countersign": "0",
     *                    "status": "1",
     *                    "remark": "",
     *                    "create_user_id": "109",
     *                    "create_time": "2018-08-08 14:57:36",
     *                    "update_user_id": "109",
     *                    "update_time": "2018-08-08 14:57:36",
     *                    "name": "李钰",
     *                    "node_name": "风控审核",
     *                    "check_status_name": "审核通过"
     *                }
     *            ]
     *        }
     *    }
     *}
     * 失败返回：
     * {
     *      "state":1,
     *      "data": "错误信息"
     * }
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 数据信息
     * @apiParam (输出字段-data) {int} check_id 审核id
     * @apiParam (输出字段-data) {int} remark 审核信息
     * @apiParam (输出字段-data) {int} status 审核状态
     * @apiParam (输出字段-data) {int} status_name 审核状态名称
     * @apiParam (输出字段-data) {int} status_name 审核状态名称
     * @apiParam (输出字段-data) {array} files 审核附件
     * @apiParam (输出字段-data) {array} logs 审核记录
     * @apiParam (输出字段-data) {array} detail 审核对象信息
     * @apiParam (输出字段-data-logs) {string} result 审核结果
     * @apiParam (输出字段-data-logs) {string} remark 审核意见
     * @apiParam (输出字段-data-logs) {array} node_name 审核节点
     * @apiParam (输出字段-data-logs) {array} checker 审核人
     * @apiParam (输出字段-data-logs) {array} check_time 审核时间
     * @apiGroup Check25
     * @apiVersion 1.0.0
     */
    public function actionDetail(){
        parent::actionDetail();
    }

    private function getMockData() {
        $row = [
            'contract_id' => 1,
            'contract_code' => 'phptest',
            'type' => 1,
            'contract_date' => '2018-07-25',
            'amount' => '100.00',
            'amount_cny' => '100.00',
            'project_code' => 'phptest',
            'project_type' => 1,
            'partner_name' => 'test_partner',
            'name' => 'liyu',
            'corporation_name' => 'test_corporation_name',
            'code_out' => 'php_test_code_out',
            'terminate_id' => 1,
            'checkStatus' => 1,
            'corporation_id' => 1,
            'partner_id' => 1,
            'goods_list' => 'test1|test2',
            'is_can_check' => true,
            'is_can_view' => false
        ];
        $data = [
            'pageCount' => 1,
            'total' => 2,
            'page' => 1,
            'rows' => [
                $row,
                $row
            ]
        ];
        return $data;
    }

    /**
     * @api {GET} / [doCheck]合同终止审核：通过/驳回
     * @apiName doCheck
     * @apiParam (输入字段) {int} check_id 审核项id <font color=red>必填</font>
     * @apiParam (输入字段) {int} check_status 审核目标状态，1是审核通过，-1是审核驳回 <font color=red>必填</font>
     * @apiParam (输入字段) {string} remark 审核意见 <font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     *      "check_id":932,
     *      "check_status":1,
     *      "remark":'同意',
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
     * @apiGroup Check25
     * @apiVersion 1.0.0
     */
    public function actionDoCheck() {
        $check_id = Mod::app()->request->getParam('check_id'); //审核id
        $check_status = Mod::app()->request->getParam('check_status');//审核目标状态
        $remark = Mod::app()->request->getParam('remark');//审核意见
        $params = array(
            'check_id' => $check_id,
            'checkStatus' => $check_status,
            'remark' => $remark
        );

        if (empty($params["check_id"])) {
            $this->returnJsonError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        if (empty($params["checkStatus"])) {
            $this->returnJsonError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $checkItem = CheckItem::model()->findByPk($params["check_id"]);
        if (empty($checkItem->check_id)) {
            $this->returnJsonBusinessError("非法操作！");
        }
        $extras = $this->getExtras();
        $extraCheckItems = $this->getExtraCheckItems();
        if (empty($params["remark"]) && is_array($extraCheckItems)) {
            $remark = "";
            foreach ($extraCheckItems as $v) {
                if ($v["check_status"] == 0)
                    $remark .= $v["remark"] . ";&emsp;";
            }
            $params["remark"] = $remark;
        }
        if (empty($params["remark"])) {
            $this->returnJsonBusinessError(\ddd\infrastructure\error\BusinessError::Argument_Required, ['name' => '审核意见']);
        }
        $res = FlowService::check($checkItem, $params["checkStatus"], $this->nowUserRoleId, $params["remark"], $this->nowUserId, "0", $extras, $extraCheckItems);
        if ($res == 1) {
            $this->returnJson('审核成功！');
        } else
            $this->returnJsonError($res);

    }

    public function getCheckData($id) {
        $data = Utility::query("
              select a.*
              from t_check_item a
                left join t_contract_terminate b on a.obj_id=b.contract_id
                left join t_check_detail c on c.check_id = a.check_id
                where c.check_status = 0 and a.business_id=" . $this->businessId . " and a.obj_id=" . $id);

        //合同信息
        if (!empty($data)) {
            $contractTerminateService = new ContractTerminateService();
            $contractTerminate = $contractTerminateService->getContractTerminate($id);
            $data[0]['contractTerminate'] = $contractTerminate;
        }
        return $data;
    }

    function getDetailData(\CheckDetail & $checkDetail): array {
        if (!Utility::checkQueryId($checkDetail->obj_id)) {
            $this->returnJsonError(OilError::$PARAMS_PASS_ERROR);
        }
        $contract = \ddd\repository\contract\ContractRepository::repository()->findByPk($checkDetail->obj_id);
        if (empty($contract)) {
            $this->returnJsonError(OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $checkDetail->obj_id));
        }
        $contractTerminateService = new ContractTerminateService();
        $contractTerminate = $contractTerminateService->getContractTerminate($checkDetail->obj_id);
        return $contractTerminate;
    }

    public function checkIsCanEdit($status) {
        return $status == ContractTerminateStatus::STATUS_SUBMIT;
    }

    function formatListData(array & $data): void {
        //数据处理
        if (Utility::isNotEmpty($data)) {
            return;
        }

        foreach ($data as $row) {
            $row['is_can_check'] = (boolean)$row['is_can_check'];
            $row['is_can_view'] = false;
            $row['goods_list'] = GoodsService::getSpecialGoodsNames(ContractService::getContractAllGoodsId($row['contract_id']));
            $row['amount_cny'] = '￥' . Utility::numberFormatFen2Yuan($row['amount_cny']);
            $row['amount'] = Map::$v['currency'][$row['currency']]['ico'] . Utility::numberFormatFen2Yuan($row['amount']);
        }
    }

    function getMainFieldsAndSql(): array {
        $mainSql = "select {col}
            from t_check_detail cd
            left join t_check_log d on cd.detail_id=d.detail_id
            join t_contract_terminate b on cd.obj_id=b.contract_id
            left join t_contract e on b.contract_id = e.contract_id
            left join t_project f on e.project_id = f.project_id
            left join t_partner c on c.partner_id = e.partner_id
            left join t_system_user u on e.manager_user_id=u.user_id
            left join t_corporation g on g.corporation_id = e.corporation_id
            left join t_contract_file cf on cf.contract_id=e.contract_id and cf.is_main=1 and cf.type=1
            ";
        $fields = "cd.check_id,e.contract_id, e.contract_code,e.type,e.contract_date,e.amount,e.amount_cny,e.currency,
        f.project_id,f.project_code,f.type project_type, e.partner_id,u.name,c.name as partner_name, f.corporation_id,
         g.name as corporation_name,cd.obj_id, cd.detail_id, cf.code_out,b.id terminate_id";
        //设置审核明细表别名
        $this->check_detail_table_alias = 'cd';
        //设置交易主体字段前缀
        $this->corporation_field_prefix = "g";

        return [$fields, $mainSql, "b.status=" . ContractTerminateStatus::STATUS_SUBMIT];
    }
}