<?php
/**
 *   销售合同结算审核
 */
class Check22Controller  extends BaseCheckController
{
    public $prefix="check22_";
    public function initRightCode()
    {
        $attr = $_REQUEST["search"];
        $checkStatus = $attr["checkStatus"];
        $this->treeCode = $this->prefix.$checkStatus;
        $this->rightCode = $this->prefix;
        $this->filterActions = "list,doCheck,save,detail";
    }
    
    public function pageInit()
    {
        parent::pageInit();
        $this->businessId =22;
        $this->checkButtonStatus["reject"] = 0;
        $this->detailPartialFile="/common/deliverySettlementList";
        $this->detailPartialModelName="deliveryOrder";
        $this->indexViewName="/check22/index";
        $this->detailViewName="/check22/detail";
        $this->checkViewName="/check22/check";
    }
    /**
     * @api {GET} / [90020001-List]销售合同结算审核列表
     * @apiName List
     * @apiParam (输入字段) {string} contract_code 销售合同编号
     * @apiParam (输入字段) {string} project_code 项目编号
     * @apiParam (输入字段) {string} partner_name 合作方名称
     * @apiParam (输入字段) {string} corporation_name 交易主体名称
     * @apiParam (输入字段) {int} category 合同类型
     * @apiParam (输入字段) {string} manager_user_name 合同负责人
     * @apiParam (输入字段) {int} check_status 审核状态
     * @apiParam (输入字段) {int} page 页数 <font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     *      "contract_code":'PHP20180321',
     *      "project_code":'ZPHP1ZJ18032101',
     *      "partner_name":"phpdragon合作方有限公司",
     *      "corporation_name":"公司主体phpdragon",
     *      "category":1,
     *      "manager_user_name":'张三',
     *      'check_status'=>1,
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
     * @apiGroup check22
     * @apiVersion 1.0.0
     */
    
    public function actionList(){
        
        $contract_code = Mod::app()->request->getParam('contract_code');//合同编号
        $project_code = Mod::app()->request->getParam('project_code');//项目编号
        $partner_name = Mod::app()->request->getParam('partner_name');//合作方项目
        $corporation_name = Mod::app()->request->getParam('corporation_name');
        $category = Mod::app()->request->getParam('category');
        $manager_user_name = Mod::app()->request->getParam('manager_user_name');
        $check_status = Mod::app()->request->getParam('check_status');//审核状态
        $page = Mod::app()->request->getParam('page');
        
        $attr=array(
            'e.contract_code' =>$contract_code,
            'f.project_code' =>$project_code,
            'c.name*' => $partner_name,
            'g.name*' => $corporation_name,
            'e.category' => $category,
            'b.name*'=>$manager_user_name,
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
            left join t_contract e on a.obj_id = e.contract_id
            left join t_system_user as b on b.user_id = e.manager_user_id
            left join t_project f on e.project_id = f.project_id
            left join t_partner c on c.partner_id = e.partner_id
            left join t_corporation g on g.corporation_id = e.corporation_id
            left join t_contract_file cf on cf.contract_id=e.contract_id and cf.is_main=1 and cf.type=1
            " . $this->getWhereSql($attr);
        $fields = "a.check_id,e.contract_id, e.contract_code,e.category, f.project_id, f.project_code, e.partner_id, c.name as partner_name, f.corporation_id, g.name as corporation_name , cf.code_out,b.name as manager_user_name";
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
                $sql .= " and e.status=" . Contract::STATUS_SETTLED_SUBMIT . " and a.status=0 and a.check_status=0";
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
                    $value['links'][]=array('name'=>'审核','params'=>array(0=>array('keyName'=>'check_id','keyValue'=>$value['check_id']),1=>array('keyName'=>'contract_id','keyValue'=>$value['contract_id'])));
                    else
                        $value['links'][]=array('name'=>'查看','params'=>array(0=>array('keyName'=>'check_id','keyValue'=>$value['check_id']),1=>array('keyName'=>'contract_id','keyValue'=>$value['contract_id'])));
                        $data['data']['rows'][$key]=$value;
            }
        }
        $attr["checkStatus"] = $checkStatus;
        $data["search"] = $attr;
        $data["b"] = $this->businessId;
        $this->returnJson($data);
        
    }
    /**
     * @api {GET} / [90020001-doCheck]发货单结算审核通过、驳回
     * @apiName doCheck
     * @apiParam (输入字段) {int} check_id 审核项id
     * @apiParam (输入字段) {int} detail_id
     * @apiParam (输入字段) {int} check_status 审核目标状态，1是审核通过，-1是审核驳回<font color=red>必填</font>
     * @apiParam (输入字段) {string} remark 审核意见
     * @apiExample {json} 输入示例:
     * {
     *      "check_id":955,
     *      "detail_id":1230,
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
     * @apiGroup check22
     * @apiVersion 1.0.0
     */
    public function actionDoCheck()
    {
        $check_id = Mod::app()->request->getParam('check_id'); //审核id
        $detail_id = Mod::app()->request->getParam('detail_id'); //detail_id
        $check_status = Mod::app()->request->getParam('check_status');//审核目标状态,1是审核通过，-1是审核驳回
        $remark = Mod::app()->request->getParam('remark');//审核意见
        $params=array(
            'check_id'=>$check_id,
            'detail_id'=>$detail_id,
            'checkStatus'=>$check_status,
            'remark'=>$remark
        );
        
        if (!Utility::checkQueryId($params["check_id"]))
        {
            $this->returnJsonError("非法操作！");
        }
        
        $checkDetail=CheckDetail::model()->findByPk($params["detail_id"]);
        if(empty($checkDetail->detail_id))
            $this->returnJsonError("审核信息不存在！");
            
        if($checkDetail->role_id!=$this->nowUserRoleId && $checkDetail->check_user_id!=$this->nowUserId)
            $this->returnJsonError("当前信息无需您审核！");
            
        if ($this->businessId == FlowService::BUSINESS_PAY_APPLICATION) {
            $this->checkPendingWithdraw($params['detail_id']);
        }
        
        $checkItem=CheckItem::model()->findByPk($checkDetail["check_id"]);
        if (empty($checkItem->check_id)) {
            $this->returnJsonError("非法操作！");
        }
        
        $extraCheckItems=$params["items"];
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
        
        $res=FlowService::check($checkItem,$params["checkStatus"],$this->nowUserRoleId,$params["remark"],$this->nowUserId,"0",null,$extraCheckItems);
        
        if($res==1)
        {
            $this->returnJson('审核成功！');
        }
        else
            $this->returnJsonError($res);
                    
    }
    
    public function getMainSql($search)
    {
        $sql = "select {col} from t_check_detail a
                left join t_delivery_order b on a.obj_id = b.order_id
                left join t_corporation c on c.corporation_id = b.corporation_id
                left join t_partner p on p.partner_id = b.partner_id
                left join t_stock_in d on d.stock_in_id = b.stock_in_id
                left join t_check_item ci on ci.check_id = a.check_id and ci.node_id > 0
                " . $this->getWhereSql($search) . " and a.business_id = " . $this->businessId . "
                and ".AuthorizeService::getUserDataConditionString("b")."
                and (a.role_id = " . $this->nowUserRoleId . " or a.check_user_id=" . $this->nowUserId . ")";
        return $sql;
    }
    
    public function getFields()
    {
        $fields = "a.detail_id,a.obj_id,a.status,a.check_status,b.order_id,b.code,b.corporation_id,b.partner_id,b.type,
                   b.stock_in_id,c.name as corporation_name,p.name as partner_name,d.code as stock_in_code";
        return $fields;
    }
    
    
    public function getCheckObjectModel($objId)
    {
        return DeliveryOrder::model()->with('settlementDetails', 'settlementDetails.sub')->findByPk($objId);
        
        // return PayApplication::model()->with("details","contract","details.payment","extra","factor")->findByPk($objId);
    }
}