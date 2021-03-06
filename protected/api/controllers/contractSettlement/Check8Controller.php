<?php

/**
 *   入库通知单结算审核
 */
class Check8Controller extends CheckController {
    public $businessId = 8;
    public $mainRightCode = "check8_";

    public function pageInit() {
        parent::pageInit();
        $attr = $_REQUEST["search"];
        $checkStatus = empty($attr["checkStatus"]) ? 1 : $attr["checkStatus"];
        $this->treeCode = $this->mainRightCode . $checkStatus;
        $this->businessId = 8;
        $this->rightCode = "check8";
        $this->mainUrl = "/check8/";
        $this->checkViewName = "/check8/check";
        $this->detailViewName = "/check8/detail";

        $this->filterActions = "list,doCheck,save,detail";
    }

    /**
     * @api {GET} / [90020001-List]入库通知单结算审核列表
     * @apiName List
     * @apiParam (输入字段) {string} code 入库通知单编号
     * @apiParam (输入字段) {string} contract_code 采购合同编号
     * @apiParam (输入字段) {string} code_out 外部合同编号
     * @apiParam (输入字段) {string} project_code 项目编号
     * @apiParam (输入字段) {string} partner_name 合作方名称
     * @apiParam (输入字段) {string} corporation_name 交易主体名称
     * @apiParam (输入字段) {int} delivery_way 发货方式
     * @apiParam (输入字段) {int} check_status 审核状态
     * @apiParam (输入字段) {int} page 页数 <font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     *      "code":"PHP20180321-2",
     *      "contract_code":'PHP20180321',
     *      "code_out":PHP20180321,
     *      "project_code":'ZPHP1ZJ18032101',
     *      "partner_name":"phpdragon合作方有限公司",
     *      "corporation_name":"公司主体phpdragon",
     *      "delivery_way":1,
     *      "check_status":1,
     *      "page":2,
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
     * @apiGroup check8
     * @apiVersion 1.0.0
     */
    
    public function actionList(){
        
        $batch_code = Mod::app()->request->getParam('code'); //入库通知单编号
        $contract_code = Mod::app()->request->getParam('contract_code');//合同编号
        $code_out = Mod::app()->request->getParam('code_out');//外部合同编号
        $project_code = Mod::app()->request->getParam('project_code');//项目编号
        $partner_name = Mod::app()->request->getParam('partner_name');//合作方项目
        $corporation_name = Mod::app()->request->getParam('corporation_name');
        $delivery_way = Mod::app()->request->getParam('delivery_way');
        $check_status = Mod::app()->request->getParam('check_status');
        $page = Mod::app()->request->getParam('page');
        
        $attr=array(
            'b.code' => $batch_code,
            'e.contract_code' =>$contract_code,
            'cf.code_out*' =>$code_out,
            'f.project_code' =>$project_code,
            'c.name*' => $partner_name,
            'g.name*' => $corporation_name,
            'b.type' => $delivery_way,
            'checkStatus' => $check_status
        );
        $checkStatus = 1;
        if (!empty($attr["checkStatus"])) {
            $checkStatus = $attr["checkStatus"];
            unset($attr['checkStatus']);
        }
        $query = "";
        $userId = Utility::getNowUserId();
        $roleId = UserService::getNowUserMainRoleId();
        $sql = "select {col}
            from t_check_detail a
            left join t_check_log d on d.detail_id=a.detail_id
            join t_stock_in_batch b on a.obj_id=b.batch_id
            left join t_contract e on b.contract_id = e.contract_id
            left join t_project f on e.project_id = f.project_id
            left join t_partner c on c.partner_id = e.partner_id
            left join t_corporation g on g.corporation_id = e.corporation_id
            left join t_contract_file cf on cf.contract_id=e.contract_id and cf.is_main=1 and cf.type=1
            " . $this->getWhereSql($attr);
        $fields = "a.check_id,e.contract_id, e.contract_code, f.project_id, f.project_code, b.type, e.partner_id, c.name as partner_name, f.corporation_id, g.name as corporation_name, b.batch_id, b.code, a.obj_id, a.detail_id, cf.code_out";
        switch ($checkStatus) {
            case 2:
                // 审核通过
                $sql .= " and a.status=1 and a.check_status=1";
                $fields .= ",0 isCanCheck, " . $checkStatus . " as checkStatus ";
                break;
            case 3:
                // 审核驳回
                $sql .= " and a.status=1 and a.check_status=-1";
                $fields .= ",0 isCanCheck, " . $checkStatus . " as checkStatus ";
                break;
            case 1:
            default:
                // 待审核
                $sql .= " and b.status=" . StockNotice::STATUS_SETTLE_SUBMIT . " and a.status=0 and a.check_status=0";
                $fields .= ",1 isCanCheck, " . $checkStatus . " as checkStatus ";
                $checkStatus = 1;
                break;
        }
        
        $sql .= $query . " and " . AuthorizeService::getUserDataConditionString('e') . " and (a.role_id= {$roleId} or a.check_user_id={$userId}) and a.business_id={$this->businessId} order by a.check_id desc {limit}";
        $user = Utility::getNowUser();
        if (!empty($user['corp_ids'])) {
            $data = $this->queryTablesByPage($sql, $fields);
        } else {
            $data = array();
        }
        //数据处理
        if(!empty($data['data']['rows'])){
            foreach ($data['data']['rows'] as $key=>$value){
                if($checkStatus==1)
                    $value['links'][]=array('name'=>'审核','params'=>array(0=>array('keyName'=>'check_id','keyValue'=>$value['check_id']),1=>array('keyName'=>'detail_id','keyValue'=>$value['detail_id']),2=>array('keyName'=>'batch_id','keyValue'=>$value['batch_id'])));
                    else
                        $value['links'][]=array('name'=>'查看','params'=>array(0=>array('keyName'=>'check_id','keyValue'=>$value['check_id']),1=>array('keyName'=>'detail_id','keyValue'=>$value['detail_id']),2=>array('keyName'=>'batch_id','keyValue'=>$value['batch_id'])));
                        $data['data']['rows'][$key]=$value;
            }
        }
        $attr["checkStatus"] = $checkStatus;
        $data["search"] = $attr;
        $data["b"] = $this->businessId;
        $this->returnJson($data);
        
    }
    /**
     * @api {GET} / [90020001-doCheck]入库通知单结算审核：通过/驳回
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
     * @apiGroup check8
     * @apiVersion 1.0.0
     */
    public function actionDoCheck()
    {
        $check_id = Mod::app()->request->getParam('check_id'); //审核id
        $check_status = Mod::app()->request->getParam('check_status');//审核目标状态
        $remark = Mod::app()->request->getParam('remark');//审核意见
        $params=array(
            'check_id'=>$check_id,
            'checkStatus'=>$check_status,
            'remark'=>$remark
        );
       
        if (empty($params["check_id"])) {
            $this->returnJsonError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        if (empty($params["checkStatus"])) {
            $this->returnJsonError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        
        $checkItem=CheckItem::model()->findByPk($params["check_id"]);
        if (empty($checkItem->check_id)) {
            $this->returnJsonError("非法操作！");
        }
        $extras=$this->getExtras();
        $extraCheckItems=$this->getExtraCheckItems();
        if(empty($params["remark"]) && is_array($extraCheckItems))
        {
            $remark="";
            foreach ($extraCheckItems as $v)
            {
                if($v["check_status"]==0)
                    $remark.=$v["remark"].";&emsp;";
            }
            $params["remark"]=$remark;
        }
        
        $res=FlowService::check($checkItem,$params["checkStatus"],$this->nowUserRoleId,$params["remark"],$this->nowUserId,"0",$extras,$extraCheckItems);
      
        if($res==1)
        {
            $this->returnJson('审核成功！');
        }
        else
            $this->returnJsonError($res);
            
    }
    
    public function getCheckData($id) {
        $data = Utility::query("
              select a.*
              from t_check_item a
                left join t_stock_batch_settlement b on a.obj_id=b.settle_id
                left join t_check_detail c on c.check_id = a.check_id
                where c.check_status = 0 and a.business_id=" . $this->businessId . " and a.obj_id=" . $id);

        return $data;
    }

    public function getDetailData($detailId) {
        return $data = Utility::query("
              select b.*
              from t_check_detail a
                left join t_check_log b on b.check_id = a.check_id
                where a.business_id=" . $this->businessId . " and 
                a.detail_id=" . $detailId);
    }

    public function checkIsCanEdit($status) {
        return $status == StockBatchSettlement::STATUS_SUBMIT;
    }
}