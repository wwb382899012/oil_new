<?php

use ddd\infrastructure\DIService;
use ddd\infrastructure\error\BusinessError;
use ddd\Split\Application\ContractTerminateService;
use ddd\Split\Domain\Model\Contract\ContractEnum;
use ddd\Split\Domain\Model\Contract\ContractTerminate;
use ddd\Split\Domain\Model\Contract\ContractTerminateStatus;
use ddd\Split\Domain\Model\Contract\IContractTerminateRepository;
use ddd\Split\Domain\Model\ContractSplit\ContractSplitApplyEnum;
use ddd\Split\Domain\Model\StockSplit\StockSplitEnum;

/**
 * User: liyu
 * Date: 2018/6/12
 * Time: 15:37
 * Desc: 合同终止Controller
 */
class ContractTerminateController extends ApiAttachmentController
{
    public function init() {
        parent::pageInit();
        $this->attachmentType = Attachment::C_CONTRACT_TERMINATE;
//        $this->authorizedActions = array("list", "save", "submit", "detail", "saveFile", "delFile", "getFile","uncommittedDetail");
    }


    /**
     * @api {GET} /api/contract/ContractTerminate/list [List]合同终止列表
     * @apiName List
     * @apiParam (输入字段) {string} contract_code 合同编号
     * @apiParam (输入字段) {string} code_out 外部合同编号
     * @apiParam (输入字段) {int} contract_id 合同ID
     * @apiParam (输入字段) {int} type 合同类型
     * @apiParam (输入字段) {string} project_code 项目编码
     * @apiParam (输入字段) {int} project_type 项目类型
     * @apiParam (输入字段) {string} partner_name 合作方
     * @apiParam (输入字段) {string} corporation_name 交易主体
     * @apiParam (输入字段) {int} terminate_status 合同终止状态
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
     *      "terminate_status":1,
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
     *                    "type": 1, map映射：buy_sell_type
     *                    "contract_date": "2018-07-25",
     *                    "amount": "100.00",
     *                    "amount_cny": "100.00",
     *                    "project_code": "phptest",
     *                    "project_type": 1, map映射：project_type
     *                    "partner_name": "test_partner",
     *                    "name": "liyu",
     *                    "corporation_name": "test_corporation_name",
     *                    "code_out": "php_test_code_out",
     *                    "terminate_id": 1,
     *                    "terminate_status": 1, map映射：contract_terminate_status
     *                    "corporation_id": 1,
     *                    "partner_id": 1,
     *                    "is_can_view":true,
     *                    "is_can_terminate":true
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
     *                    "terminate_status": 1,
     *                    "corporation_id": 1,
     *                    "partner_id": 1,
     *                    "is_can_view":false,
     *                    "is_can_terminate":true,
     *                    "is_can_edit":false,
     *                }
     *            ]
     *        },
     *        "search": {
     *            "a.contract_code": "dddd",
     *            "a.contract_id": null,
     *            "a.type": null,
     *            "cf.code_out*": null,
     *            "f.project_code": null,
     *            "f.type": null,
     *            "c.name*": null,
     *            "g.name*": null,
     *            "e.status": null
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
     * @apiGroup ContractTerminate
     * @apiVersion 1.0.0
     */
    public function actionList() {
        $search = $this->getSearch();
        $contract_code = $search['contract_code'];
        $code_out = $search['code_out'];
        $contract_id = $search['contract_id'];
        $type = $search['type'];
        $project_code = $search['project_code'];
        $project_type = $search['project_type'];
        $partner_name = $search['partner_name'];
        $corporation_name = $search['corporation_name'];
        $terminate_status = $search['terminate_status'];
        $page = Mod::app()->request->getParam('page');
        $attr = array(
            'a.contract_code' => $contract_code,
            'a.contract_id' => $contract_id,
            'a.type' => $type,
            'cf.code_out*' => $code_out,
            'f.project_code' => $project_code,
            'f.type' => $project_type,
            'c.name*' => $partner_name,
            'g.name*' => $corporation_name,
        );
        //TODO
//        $mockData['data'] = $this->getMockData();
//        $mockData['search'] = $attr;
//        $this->returnJson($mockData);
//        exit;
        //TODO end
        $query = '';
        if (isset($search["terminate_status"]) && $search["terminate_status"] == "-10") {
            $query .= " and a.status<=" . Contract::STATUS_TERMINATING;
        } else {
            $attr['e.status'] = $terminate_status;
        }
        $sql = "select {col}
            from t_contract a
            left join t_contract_terminate e on e.contract_id=a.contract_id
            left join t_project f on a.project_id = f.project_id
            left join t_partner c on c.partner_id = a.partner_id
            left join t_system_user u on a.manager_user_id=u.user_id
            left join t_corporation g on g.corporation_id = a.corporation_id
            left join t_contract_file cf on cf.contract_id=a.contract_id and cf.is_main=1 and cf.type=1 "
            . $this->getWhereSql($attr);
        $query .= " and a.split_type=" . ContractEnum::SPLIT_TYPE_SPLIT . " 
            and a.original_id=0
            and not exists (select contract_id from t_contract_split_apply where status<>" . ContractSplitApplyEnum::STATUS_PASS . " and contract_id=a.contract_id)
            and not exists (select contract_id from t_stock_split_apply where status<>" . StockSplitEnum::STATUS_PASS . " and contract_id=a.contract_id)
            and a.status<>" . Contract::STATUS_TERMINATED . " 
            and ((SELECT sum(quantity) FROM t_contract_goods t2 where t2.contract_id=a.contract_id)=0) 
            and 
            (IF(a.type=1,(SELECT count(1) sum_quantity FROM t_stock_in t4 WHERE t4.is_virtual=0 AND t4.split_status<2 AND t4.status=20 AND t4.contract_id=a.contract_id),
            (SELECT count(1) sum_quantity FROM t_stock_out_order t6 WHERE t6.is_virtual=0 AND t6.split_status<2 AND t6.status=1 AND t6.contract_id=a.contract_id))=0)
            ";
        $fields = "a.contract_id, a.contract_code,a.type,a.contract_date,a.amount,a.amount_cny,a.partner_id,a.currency,
         f.project_id, f.project_code,f.type project_type, a.partner_id,u.name,c.name as partner_name, f.corporation_id,
         g.name as corporation_name, cf.code_out,
         ifNull(e.id,0) terminate_id,ifNull(e.status," . ContractTerminateStatus::STATUS_NOT_EDIT . ") terminate_status
         ";
        $orderBy=' ORDER BY a.update_time DESC {limit}';
        $sql = $sql . $query . $orderBy;
        $data = $this->queryTablesByPage($sql, $fields);
        //echo "##".(time()-$s);
        //数据处理
        if (!empty($data['data']['rows'])) {
            foreach ($data['data']['rows'] as $key => &$value) {
                //链接
                $links = [];
                $value['is_can_terminate'] = false;
                $value['is_can_edit'] = false;
                $value['is_can_view'] = false;
                if (empty($value["terminate_id"])) {
                    $value['is_can_terminate'] = true;
                } else {
                    if (ContractTerminateService::terminateIsCanEdit($value["terminate_status"]))
                        $value['is_can_edit'] = true;
                    if (!empty($value['terminate_id']))
                        $value['is_can_view'] = true;
                }
                $value['goods_list'] = GoodsService::getSpecialGoodsNames(ContractService::getContractAllGoodsId($value['contract_id']));
                $value['amount_cny'] = '￥' . Utility::numberFormatFen2Yuan($value['amount_cny']);
                $value['amount'] = Map::$v['currency'][$value['currency']]['ico'] . Utility::numberFormatFen2Yuan($value['amount']);
            }

            //echo "##".(time()-$s);
        }
        $data['search'] = $attr;
        $this->returnJson($data);
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
            'terminate_status' => 1,
            'corporation_id' => 1,
            'partner_id' => 1,
            'goods_list' => 'test1|test2',
            'is_can_view' => true,
            'is_can_terminate' => true,
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
     * @api {POST} /api/contract/ContractTerminate/save [90020001-save]  暂存、保存
     * @apiName save
     * @apiParam (输入字段) {arr} contractTerminate 合同终止信息
     * @apiExample {json} 输入示例:
     * "contractTerminate": {
     *      "id":null,
     *      "contract_id": 11,
     *      "reason": null,
     *      "files": [],
     *      "audit_log": []
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
     * @apiGroup ContractTerminate
     * @apiVersion 1.0.0
     */
    public function actionSave() {
        $contractTerminate = Mod::app()->request->getParam('contractTerminate');
        $contractTerminate = json_decode($contractTerminate, true);
        if (empty($contractTerminate)) {
            $this->returnJsonBusinessError(BusinessError::Argument_Required, array('name' => 'contractTerminate'));
        }
        $contract_id = $contractTerminate['contract_id'];
        if (!Utility::checkQueryId($contract_id)) {
            $this->returnJsonBusinessError(BusinessError::Argument_Required, array('name' => 'contract_id'));
        }
        $contract = \ddd\repository\contract\ContractRepository::repository()->findByPk($contract_id);
        if (empty($contract)) {
            $this->returnJsonError(OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $contract_id));
        }

        $contractTerminateService = new ContractTerminateService();
        $contractTerminateDTO = $contractTerminateService->assignDTO($contractTerminate);
        $res = $contractTerminateService->save($contractTerminateDTO, $contract);
        if (is_array($res)) {
            $this->returnApiValidateError($res);
        } else {
            if (is_string($res))
                $this->returnJsonError($res);
            else
                $this->returnJson('保存成功');
        }

    }


    /**
     * @api {GET} /api/contract/ContractTerminate/submit [90020001-submit]  提交
     * @apiName submit
     * @apiParam (输入字段) {string} contract_id 合同ID
     * @apiExample {json} 输入示例:
     * "contractTerminate": {
     *      "id":null,
     *      "contract_id": 11,
     *      "reason": null,
     *      "files": [],
     *      "audit_log": []
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
     * @apiGroup ContractTerminate
     * @apiVersion 1.0.0
     */
    public function actionSubmit() {
        $contractTerminate = Mod::app()->request->getParam('contractTerminate');
        $contractTerminate = json_decode($contractTerminate, true);
        if (empty($contractTerminate)) {
            $this->returnJsonBusinessError(BusinessError::Argument_Required, array('name' => 'contractTerminate'));
        }
        $contract_id = $contractTerminate['contract_id'];
        if (!Utility::checkQueryId($contract_id)) {
            $this->returnJsonBusinessError(BusinessError::Argument_Required, array('name' => 'contract_id'));
        }
        $contract = \ddd\repository\contract\ContractRepository::repository()->findByPk($contract_id);
        if (empty($contract)) {
            $this->returnJsonError(OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $contract_id));
        }
        try {
            //save
            $contractTerminateService = new ContractTerminateService();
            $contractTerminateDTO = $contractTerminateService->assignDTO($contractTerminate);
            $res = $contractTerminateService->save($contractTerminateDTO, $contract);
            if (is_array($res)) {
                $this->returnApiValidateError($res);
            }
            if (is_string($res)) {
                $this->returnJsonError($res);
            }
            //submit
            $contractTerminateEntity = DIService::getRepository(IContractTerminateRepository::class)->findByContractId($contract_id);
            if (empty($contractTerminateEntity)) {
                throw new \ddd\infrastructure\error\ZEntityNotExistsException($contract_id, ContractTerminate::class);
            }
            $res = ContractTerminateService::service()->submit($contractTerminateEntity);
            if ($res !== true) {
                throw new Exception($res);
            }
            $this->returnJson('提交成功！');
        } catch (Exception $e) {
            $this->returnJsonBusinessError(BusinessError::Operate_Error, array('reason' => $e->getMessage()));
        }
    }


    /**
     * @api {GET} /api/contract/ContractTerminate/detail [90020001-detail]  合同终止详情
     * @apiName detail
     * @apiParam (输入字段) {string} contract_id 合同ID
     * @apiExample {json} 输入示例:
     * {
     *      "contract_id":997,
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回，注意，该接口为异步接口，只返回接收成功：
     *{
     *    "state": 0,
     *    "data": {
     *        "contractTerminate": {
     *            "id": 1,
     *            "contract_id": 1,
     *            "reason": "test",
     *            "files": [
     *                {
     *                    "id": 1,
     *                    "name": "某某附件",
     *                    "file_url": "http://www.text.com/test.jpg"
     *                },
     *                {
     *                    "id": 2,
     *                    "name": "某某附件",
     *                    "file_url": "http://www.text.com/test.jpg"
     *                }
     *            ],
     *            "audit_log": [
     *                {
     *                    "status": 1, 1:通过 -1:驳回
     *                    "remark": "审核意见1",
     *                    "node_name": "审核节点1",
     *                    "name": "liyu",
     *                    "check_time": "2018-7-26"
     *                },
     *                {
     *                    "status": 1,
     *                    "remark": "审核意见1",
     *                    "node_name": "审核节点1",
     *                    "name": "liyu",
     *                    "check_time": "2018-7-26"
     *                }
     *            ]
     *        },
     *        "isCanSubmit": true,
     *        "isCanEdit": true
     *    }
     *}
     * 失败返回：
     * {
     *      "code":1,
     *      "data":{}
     * }
     * @apiParam (输出字段) {string} code 错误码
     * @apiParam (输出字段) {array} data 成功时返回交易号，错误时返回错误码
     * @apiGroup ContractTerminate
     * @apiVersion 1.0.0
     */
    public
    function actionDetail() {
        $contractId = Mod::app()->request->getParam('contract_id');
        if (!Utility::checkQueryId($contractId)) {
            $this->returnJsonError(OilError::$PARAMS_PASS_ERROR);
        }
        $contract = \ddd\repository\contract\ContractRepository::repository()->findByPk($contractId);
        if (empty($contract)) {
            $this->returnJsonError(OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $contractId));
        }
//        $contractInfo = \ddd\Split\Application\ContractService::service()->getContract($contractId);
//        $data['contract'] = $contractInfo;
        $contractTerminateService = new ContractTerminateService();
        $contractTerminate = $contractTerminateService->getContractTerminate($contractId);
        $data['contractTerminate'] = $contractTerminate;
        $isCanSubmit = false;
        $isCanEdit = false;
        $contractTerminateEntity = DIService::getRepository(IContractTerminateRepository::class)->findByContractId($contractId);
        if (empty($contractTerminateEntity)) {
            $contractTerminateEntity = ContractTerminate::create();
        }
        if ($contractTerminateService->isCanSubmit($contractTerminateEntity)) {
            $isCanSubmit = true;
        }
        if ($contractTerminateService->isCanEdit($contractTerminateEntity)) {
            $isCanEdit = true;
        }
        $data['isCanSubmit'] = $isCanSubmit;
        $data['isCanEdit'] = $isCanEdit;

        //TODO  测试mock数据
//        $data['contractTerminate'] = $this->mockDetail();
        $this->returnJson($data);
    }

    /**
     * @api {GET} /api/contract/ContractTerminate/uncommittedDetail [90020001-uncommittedDetail]  合同终止详情
     * @apiName uncommittedDetail
     * @apiParam (输入字段) {string} contract_id 合同ID
     * @apiExample {json} 输入示例:
     * {
     *      "contract_id":997,
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回，注意，该接口为异步接口，只返回接收成功：
     *{
     *    "state": 0,
     *    "data": {
     *        "contractTerminate": {
     *            "id": 1,
     *            "contract_id": 1,
     *            "reason": "test",
     *            "files": [
     *                {
     *                    "id": 1,
     *                    "name": "某某附件",
     *                    "file_url": "http://www.text.com/test.jpg"
     *                },
     *                {
     *                    "id": 2,
     *                    "name": "某某附件",
     *                    "file_url": "http://www.text.com/test.jpg"
     *                }
     *            ],
     *            "audit_log": [
     *                {
     *                    "status": 1, 1:通过 -1:驳回
     *                    "remark": "审核意见1",
     *                    "node_name": "审核节点1",
     *                    "name": "liyu",
     *                    "check_time": "2018-7-26"
     *                },
     *                {
     *                    "status": 1,
     *                    "remark": "审核意见1",
     *                    "node_name": "审核节点1",
     *                    "name": "liyu",
     *                    "check_time": "2018-7-26"
     *                }
     *            ]
     *        },
     *        "isCanSubmit": true,
     *        "isCanEdit": true
     *    }
     *}
     * 失败返回：
     * {
     *      "code":1,
     *      "data":{}
     * }
     * @apiParam (输出字段) {string} code 错误码
     * @apiParam (输出字段) {array} data 成功时返回交易号，错误时返回错误码
     * @apiGroup ContractTerminate
     * @apiVersion 1.0.0
     */
    public function actionUncommittedDetail() {
        $contractId = Mod::app()->request->getParam('contract_id');
        if (!Utility::checkQueryId($contractId)) {
            $this->returnJsonError(OilError::$PARAMS_PASS_ERROR);
        }
        $contract = \ddd\Split\Repository\Contract\ContractRepository::repository()->findByPk($contractId);
        if (empty($contract)) {
            $this->returnJsonError(OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $contractId));
        }
        $contractTerminateService = new ContractTerminateService();
        $contractTerminate = $contractTerminateService->getContractTerminate($contractId);
        $data['contractTerminate'] = $contractTerminate;
        $isCanSubmit = false;
        $isCanEdit = false;
        $contractTerminateEntity = DIService::getRepository(IContractTerminateRepository::class)->findByContractId($contractId);
        if (empty($contractTerminateEntity)) {
            $contractTerminateEntity = ContractTerminate::create();
        }
        $isCanTerminate = $contractTerminateService->isCanTerminate($contractTerminateEntity, $contract);
        if ($isCanTerminate !== true) {
            if (is_string($isCanTerminate)) {
                $this->returnJsonError($isCanTerminate);
            }
        }
        if ($contractTerminateService->isCanSubmit($contractTerminateEntity)) {
            $isCanSubmit = true;
        }
        if ($contractTerminateService->isCanEdit($contractTerminateEntity)) {
            $isCanEdit = true;
        }
        $data['isCanSubmit'] = $isCanSubmit;
        $data['isCanEdit'] = $isCanEdit;

        //TODO  测试mock数据
//        $data['contractTerminate'] = $this->mockDetail();
        $this->returnJson($data);
    }


    /**
     * @api {GET} /api/contract/ContractTerminate/saveFile [90020001-saveFile] 附件上传
     * @apiName saveFile
     * @apiGroup ContractTerminate
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} id 标志id
     * @apiParam (输入字段) {int} type 类型，1是附件
     * @apiParam (输入字段) {arr} files 文件信息
     * @apiExample {json} 输入示例:
     * {
     *      "id":779,
     *      "type"=>1,
     *      "files"=>[]
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     *      "state":0,
     *      "data":1
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data":{}
     * }
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 成功时返回附件id
     */
    public function actionSaveFile() {
        parent::actionSaveFile();
    }

    /**
     * @api {GET} /api/contract/ContractTerminate/delFile [90020001-delFile] 附件删除
     * @apiName delFile
     * @apiGroup ContractTerminate
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} id 文件id
     * @apiExample {json} 输入示例:
     * {
     *      "id":779,
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     *      "state":0,
     *      "data":1
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data":{}
     * }
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 成功时返回附件id
     */
    public function actionDelFile() {
        parent::actionDelFile();
    }

    /**
     * @api {GET} /api/contract/ContractTerminate/getFile [90020001-getFile] 获取附件
     * @apiName getFile
     * @apiGroup ContractTerminate
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} id 文件id
     * @apiParam (输入字段) {string} name 文件名称
     * @apiExample {json} 输入示例:
     * {
     *      "id":779,
     *      "name"=>'ddd',
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     *      "state":0,
     *      "data":1
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data":{}
     * }
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 成功时返回附件id
     */
    public function actionGetFile() {
        parent::actionGetFile();
    }

    private function mockDetail() {
        $data = [
            'id' => 1,
            'contract_id' => 1,
            'reason' => 'test',
            'files' => [
                [
                    'id' => 1,
                    'name' => '某某附件',
                    'file_url' => 'http://www.text.com/test.jpg',
                ],
                [
                    'id' => 2,
                    'name' => '某某附件',
                    'file_url' => 'http://www.text.com/test.jpg',
                ]
            ],
            'audit_log' => [
                [
                    'status' => 1,
                    'remark' => '审核意见1',
                    'node_name' => '审核节点1',
                    'name' => 'liyu',
                    'check_time' => '2018-7-26',
                ],
                [
                    'status' => 1,
                    'remark' => '审核意见1',
                    'node_name' => '审核节点1',
                    'name' => 'liyu',
                    'check_time' => '2018-7-26',
                ]
            ],
        ];
        return $data;
    }
}