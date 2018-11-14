<?php

use ddd\infrastructure\DIService;
use ddd\Split\Domain\Model\ICheckLog;
use ddd\Split\Dto\CheckLogDTO;

abstract class ApiCheckController extends ApiAttachmentController{

    public $businessId = 0;

    public $nowUserRoleId = 0;

    public $nowUserId = 0;

    public $mainRightCode = 'check_';

    protected $check_detail_table_alias = "cd";

    protected $corporation_field_prefix = "c";

    public function pageInit(){
        $check_status = Mod::app()->request->getParam('check_status',1);

        $this->treeCode = $this->mainRightCode.$check_status;
        $this->rightCode = $this->mainRightCode;
        $this->filterActions = "";

        $this->nowUserId = Utility::getNowUserId();
        $this->nowUserRoleId = UserService::getNowUserMainRoleId();
    }

    public function actionList(){
        $data = $this->getListData();

        if(Utility::isEmpty($data['data']['rows'])){
            $this->returnJson([]);
        }

        foreach($data['data']['rows'] as & $datum){
            if(isset($datum['check_status'])){
                $datum['check_status_name'] = Map::getStatusName('flow_check_status_map', $datum['check_status']);
            }
            $datum['is_can_check'] = (boolean) $datum['is_can_check'];
            $datum['is_can_view'] = true;
        }

        $this->formatListData($data['data']['rows']);

        $this->returnJson($data);
    }

    protected function getListData(): array{
        $user = Utility::getNowUser();
        if(empty($user['corp_ids'])){
            $this->returnJson([]);
        }

        $field_and_sql_arr = $this->getMainFieldsAndSql();
        if(empty($field_and_sql_arr[0]) || empty($field_and_sql_arr[1]) || empty($field_and_sql_arr[2])){
            $this->returnJson([]);
        }

        $search = $this->getSearch();

        $check_status=1;
        if(!empty($search["check_status"]))
        {
            $check_status=$search["check_status"];
            unset($search["check_status"]);
        }
        $fields = $field_and_sql_arr[0];
        $mainSql = $field_and_sql_arr[1].$this->getWhereSql($search);
        $subSql =  $field_and_sql_arr[2];

        switch($check_status){
            case 2:
                // 审核通过
                $mainSql .= " AND cd.status=1 AND cd.check_status=1";
                $fields .= ",0 is_can_check, ".$check_status." as check_status ";
                break;
            case 4:
                // 审核驳回
                $mainSql .= " AND cd.status=1 AND cd.check_status=-1";
                $fields .= ",0 is_can_check, ".$check_status." as check_status ";
                break;
            case 1:
            default:
                // 待审核
                $mainSql .= " AND ".$subSql." AND cd.status=0 AND cd.check_status=0";
                $fields .= ",1 is_can_check, ".$check_status." as check_status ";
                break;
        }

        $where = [
            AuthorizeService::getUserDataConditionString($this->corporation_field_prefix),
            "(cd.role_id= {$this->nowUserRoleId} or cd.check_user_id={$this->nowUserId})",
            " cd.business_id={$this->businessId}"
        ];
        $mainSql .= " AND ". implode(' AND ',$where).  " ORDER BY cd.check_id desc {limit}";

        return $this->queryTablesByPage($mainSql, $fields);
    }

    /**
     * @api {GET} /api/xxxxxx/detail [check] 审核详情
     * @apiName detail
     * @apiGroup ApiCheck
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} check_id 审核对象id <font color=red>必填</font>
     * @apiExample {FromData} 输入示例:
     * {
     *      "check_id":12,
     * }
     * @apiSuccessExample {json} 输出示例:
     * 接收成功返回：
     * {
     * "state": 0,
     * "data": {
     *      "check_id":1,
     *      "check_id": "5632",
     *       "remark": "",
     *      "status": "0",
     *      "status_name": "待审核",
     *      "files": [],
     *      "logs": [
     *          {
     *          "result": "审核通过",
     *          "remark": "同意",
     *          "node_name": "商务主管审核",
     *          "checker": "李贤配",
     *          "check_time": "2017-11-17 17:04:37"
     *          }
     *      ],
     *      "detail":{
     *        "origin_contract": {
     *          "contract_id": 1,
     *          "type": 1,
     *          "contract_code": "Z998102NQ12",
     *          "partner_name": "无锡市中港石化集团有限公司",
     *          "goods_items": [{
     *              "goods_id": 1,
     *              "goods_name": "普通柴油",
     *              "quantity": 30000,
     *              "unit": "吨"
     *          }],
     *          "stock_bill_items": [{
     *              "bill_id": 2,
     *              "bill_code": "NO.009144-2-2",
     *              "is_virtual": true,
     *              "is_can_split": true,
     *              "goods_items": [{
     *                  "goods_id": 1,
     *                  "goods_name": "普通柴油",
     *                  "quantity": 15000,
     *                  "unit": "吨"
     *              }]
     *          }]
     *      }
     * }
     * }
     * }
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
     * @apiParam (输出字段-data-logs) {string} result 审核结果
     * @apiParam (输出字段-data-logs) {string} remark 审核意见
     * @apiParam (输出字段-data-logs) {array} node_name 审核节点
     * @apiParam (输出字段-data-logs) {array} checker 审核人
     * @apiParam (输出字段-data-logs) {array} check_time 审核时间
     */
    public function actionDetail(){
        $check_id = Mod::app()->request->getParam('check_id');
        if(!Utility::checkQueryId($check_id) || $check_id <= 0){
            $this->returnJsonBusinessError(\ddd\infrastructure\error\BusinessError::Argument_Required, array('name' => 'id'));
        }

        $checkDetail = CheckDetail::model()->find('check_id='.$check_id.' and business_id='.$this->businessId);
        if(empty($checkDetail)){
            $this->returnJsonBusinessError(\ddd\infrastructure\error\BusinessError::Obj_Check_Detail_Not_Exist, array('obj_id' => $checkDetail->obj_id));
        }

        $detail_data = $this->getDetailData($checkDetail);

        $checkLogEntities = DIService::getRepository(ICheckLog::class)->findAllByObjIdAndBusinessId($checkDetail->obj_id, $this->businessId);
        //$checkLogEntities = DIService::getRepository(ICheckLog::class)->findAllByObjIdAndBusinessId('2017111700001', '13');

        $audit_remark = '';
        $checkLogDtos = [];

        if(\Utility::isNotEmpty($checkLogEntities)){
            foreach($checkLogEntities as & $logEntity){
                $log_dto =new CheckLogDTO();
                $log_dto->fromEntity($logEntity);
                $checkLogDtos[] = $log_dto;

                if($logEntity->detail_id == $checkDetail->detail_id){
                    $audit_remark = $logEntity->remark;
                }
            }
        }

        $data['check_id'] = $checkDetail->check_id;
        $data['remark'] = $audit_remark;
        $data['status'] = $checkDetail->check_status;
        $data['status_name'] = Map::getStatusName('flow_check_status_map', $checkDetail->check_status);
        $data['files'] = [];
        $data['logs'] = $checkLogDtos;
        $data['detail'] = $detail_data;
        $data['is_can_check'] = (1 == $checkDetail->check_status);

        $this->returnJson($data);
    }

    /**
     * @api {POST} /api/xxxxx/check [check] 审核：通过/驳回
     * @apiName check
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
     *      "data":"审核成功!"
     * }
     * 失败返回：
     * {
     *      "code":1,
     *      "data":"失败原因"
     * }
     * @apiParam (输出字段) {string} code 错误码
     * @apiParam (输出字段) {array} data 消息
     * @apiGroup Check24
     * @apiVersion 1.0.0
     */
    public function actionCheck(){
        $check_id = $this->getRestParam('check_id'); //审核id
        $check_status = $this->getRestParam('check_status');//审核目标状态
        $remark = $this->getRestParam('remark');//审核意见

        if (empty($check_id)) {
            $this->returnJsonError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        if (empty($check_status)) {
            $this->returnJsonError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $checkItem = CheckItem::model()->findByPk($check_id);
        if (empty($checkItem->check_id)) {
            $this->returnJsonError("非法操作！");
        }
        $extras = $this->getExtras();
        $extraCheckItems = $this->getExtraCheckItems();
        if (empty($remark) && is_array($extraCheckItems)) {
            $remark = "";
            foreach ($extraCheckItems as $v) {
                if ($v["check_status"] == 0)
                    $remark .= $v["remark"] . ";&emsp;";
            }
        }

        $res = FlowService::check($checkItem, $check_status, $this->nowUserRoleId, $remark, $this->nowUserId, "0", $extras, $extraCheckItems);

        if ($res == 1) {
            $this->returnJson('审核成功！');
        } else{
            $this->returnJsonError($res);
        }
    }

    abstract function getMainFieldsAndSql():array;

    abstract function formatListData(array & $data):void;

    abstract function getDetailData(\CheckDetail & $checkDetail):array;

    /**
     * 获取要保存的额外信息，数组格式array(array("type"=>1,"display_name"=>"XXX","key"=>"item1","value"=>"XXXXX",))
     * @return array
     */
    protected function getExtras(){
        return array();
    }

    /**
     * 获取额外的审核明细信息，数组格式array(array("type"=>1,"check_status"=>1,"remark"=>"XXXXX",))
     * @return array
     */
    protected function getExtraCheckItems(){
        $items = json_decode($this->getRestParam('items'),true);
        return is_array($items) && count($items)>0 ? $items : [];
    }

}