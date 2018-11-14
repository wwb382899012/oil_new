<?php
/**
*   发货单结算审核
*/
class Check10Controller  extends BaseCheckController
{
    public $prefix="check10_";
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
        $this->businessId =10;
        $this->checkButtonStatus["reject"] = 0;
        $this->detailPartialFile="/common/deliverySettlementList";
        $this->detailPartialModelName="deliveryOrder";
        $this->indexViewName="/check10/index";
        $this->detailViewName="/check10/detail";
        $this->checkViewName="/check10/check";
    }
    /**
     * @api {GET} / [90020001-List]发货单结算审核列表
     * @apiName List
     * @apiParam (输入字段) {string} order_code 发货单编号
     * @apiParam (输入字段) {string} partner_name 合作方
     * @apiParam (输入字段) {string} corporation_name 交易主体
     * @apiParam (输入字段) {int} delivery_way 发货方式
     * @apiParam (输入字段) {int} check_status 审核状态
     * @apiParam (输入字段) {int} page 页数 <font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     *      "order_code":"PHP20180321-2",
     *      "partner_name":'中国林业物资总公司',
     *      "corporation_name":'中油海化石油化工',
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
     * @apiGroup check10
     * @apiVersion 1.0.0
     */
    public function actionList()
    {
        $order_code = Mod::app()->request->getParam('order_code'); //发货单编号
        $partner_name = Mod::app()->request->getParam('partner_name');//合作方
        $corporation_name = Mod::app()->request->getParam('corporation_name');
        $delivery_way = Mod::app()->request->getParam('delivery_way');//发货方式
        $check_status = Mod::app()->request->getParam('check_status');//审核状态
        $page = Mod::app()->request->getParam('page');
        
        $search = array(
            'b.code' => $order_code,
            'c.name*' => $partner_name,
            'p.name*' => $corporation_name,
            'checkStatus' => $check_status,
            'b.type' => $delivery_way
        );
        $checkStatus=1;
        if(!empty($search["checkStatus"]))
        {
            $checkStatus=$search["checkStatus"];
            unset($search["checkStatus"]);
        }
        
        $sql=$this->getMainSql($search);
        
        $fields=$this->getFields();
        
        //对应 Map::$v["search_check_status"]
        switch($checkStatus)
        {
            case 2:
                $sql .= " and a.status=1 and a.check_status=1";
                $fields.=",0 isCanCheck ";
                break;
            case 3:
                $sql .= " and a.status=1 and a.check_status=0";
                $fields.=",0 isCanCheck ";
                break;
            case 4:
                $sql .= " and a.status=1 and a.check_status=-1";
                $fields.=",0 isCanCheck ";
                break;
            default:
                $sql .= " and a.status=0";
                $fields.=",1 isCanCheck ";
                break;
        }
        
        $sql .= " order by a.detail_id desc {limit}";
        
        $data = $this->queryTablesByPage($sql,$fields);
        //数据处理
        if(!empty($data['data']['rows'])){
            foreach ($data['data']['rows'] as $key=>$value){
                if($checkStatus==1)
                    $value['links'][]=array('name'=>'审核','params'=>array(0=>array('keyName'=>'check_id','keyValue'=>$value['check_id']),1=>array('keyName'=>'detail_id','keyValue'=>$value['detail_id']),2=>array('keyName'=>'order_id','keyValue'=>$value['order_id'])));
                    else
                        $value['links'][]=array('name'=>'查看','params'=>array(0=>array('keyName'=>'check_id','keyValue'=>$value['check_id']),1=>array('keyName'=>'detail_id','keyValue'=>$value['detail_id']),2=>array('keyName'=>'order_id','keyValue'=>$value['order_id'])));
                        $data['data']['rows'][$key]=$value;
            }
        }
        $search["checkStatus"]=$checkStatus;
        $data["search"]=$search;
        $data["b"]=$this->businessId;
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
     * @apiGroup check10
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
            $this->returnJson('审核成功');
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
                left join t_contract as e on e.contract_id = b.contract_id
                left join t_project as f on f.project_id = e.project_id
                left join t_delivery_settlement as s on s.order_id = b.order_id
                " . $this->getWhereSql($search) . " and a.business_id = " . $this->businessId . "
                and ".AuthorizeService::getUserDataConditionString("b")."
                and (a.role_id = " . $this->nowUserRoleId . " or a.check_user_id=" . $this->nowUserId . ")";
        return $sql;
    }

    public function getFields()
    {
        $fields = "a.detail_id,a.check_id,a.obj_id,a.check_status,b.order_id,b.code,b.corporation_id,b.partner_id,b.type,
                   b.stock_in_id,c.name as corporation_name,p.name as partner_name,d.code as stock_in_code,
                   e.contract_id,e.contract_code,e.project_id,f.project_code,s.settle_date";
        return $fields;
    }


    public function getCheckObjectModel($objId)
    {
        return DeliveryOrder::model()->with('settlementDetails', 'settlementDetails.sub')->findByPk($objId);
        
        // return PayApplication::model()->with("details","contract","details.payment","extra","factor")->findByPk($objId);
    }
}