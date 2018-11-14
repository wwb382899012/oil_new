<?php
/**
*   还货审核
*/
class Check12Controller  extends CheckController
{
    public $businessId = 12;
    public $mainRightCode="check12_";
    public function pageInit() {
        parent::pageInit();
        $attr= $_REQUEST["search"];
        $checkStatus=$attr["checkStatus"];
        $this->treeCode=$this->mainRightCode.$checkStatus;
        $this->businessId=12;
        $this->rightCode="check12";
        $this->mainUrl = "/check12/";
        $this->checkViewName = "/check12/check";
        $this->detailViewName = "/check12/detail";

        $this->filterActions = "index,check,save,detail";
    }

    public function actionIndex()
    {
        $attr = $_GET['search'];
        $checkStatus=1;
        if(!empty($attr["checkStatus"])) {
            $checkStatus=$attr["checkStatus"];
            unset($attr['checkStatus']);
        }
        $query="";

        // $user = SystemUser::getUser(Utility::getNowUserId());
        $userId=Utility::getNowUserId();
        $roleId=UserService::getNowUserMainRoleId();
        $sql = "select {col} 
            from t_check_detail a 
            left join t_cross_order b on b.cross_id=a.obj_id
            left join t_contract e on e.contract_id=b.contract_id 
            left join t_contract_file bcf on bcf.contract_id=e.contract_id and bcf.is_main=1 and bcf.type=1 
            left join t_cross_detail c on c.detail_id=b.detail_id
            left join t_cross_order d on d.cross_id=b.relation_cross_id 
            left join t_contract f on f.contract_id=d.contract_id 
            left join t_contract_file scf on scf.contract_id=f.contract_id and scf.is_main=1 and scf.type=1
            left join t_cross_detail g on g.cross_id=b.cross_id
            left join t_goods h on h.goods_id=b.goods_id 
            ". $this->getWhereSql($attr);
        $fields="";
        switch($checkStatus)
        {
            case 2:
                // 审核通过
                $sql .= " and a.status=1 and a.check_status=1 ";
                $fields.=",0 isCanCheck, ".$checkStatus." as checkStatus ";
                break;
            case 4:
                // 审核驳回
                $sql .= " and a.status=1 and a.check_status=-1 ";
                $fields.=",0 isCanCheck, ".$checkStatus." as checkStatus ";
                break;
            case 1:
            default:
                // 待审核
                $sql .= " and b.status=".CrossOrder::STATUS_CHECKING." and a.status=0 and b.type in (".ConstantMap::ORDER_BACK_TYPE . ", " . ConstantMap::ORDER_BUY_TYPE . ")";
                $fields.=",1 isCanCheck, ".$checkStatus." as checkStatus ";
                $checkStatus = 1;
                break;
        }

        $sql .= $query." and ".AuthorizeService::getUserDataConditionString("e")." and (a.role_id= {$roleId} or a.check_user_id={$userId}) and a.business_id={$this->businessId} order by a.check_id desc {limit}";
        $fields  = " a.obj_id, a.detail_id, b.cross_code as return_code, d.cross_code as borrow_code, e.contract_code as buy_contract_code, f.contract_code as sell_contract_code, a.status as status, b.type, bcf.code_out as buy_code_out, scf.code_out as sell_code_out, h.name as goods_name " . $fields;
        $user = Utility::getNowUser();
        if(!empty($user['corp_ids'])) {
            $data=$this->queryTablesByPage($sql,$fields);
        } else {
            $data = array();
        }
        $attr["checkStatus"]=$checkStatus;
        $data["search"]=$attr;
        $this->render("index", $data);
    }

    public function actionCheck()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
        {
            $this->renderError("信息异常！", $this->mainUrl);
        }

        $data=$this->getCheckData($id);
        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", $this->mainUrl);
        }

        $order = CrossOrder::model()->findByPk($id);
        $head = CrossOrderService::getCrossHead($order->relation_cross_id, $order->contract_id);
        $crossDetail = CrossOrderService::getCrossDetail($order->relation_cross_id, $order->contract_id);
        
        $returnDetail['type'] = $order->type;
        if($order->type==ConstantMap::DISTRIBUTED_RETURN){
            $returnDetail['details'] = CrossOrderService::getReturnDetailById($id);
        }else{
            $returnDetail['cross_remark'] = $order->remark;
            $returnDetail['quantity'] = $order->quantity;
            $returnDetail['unit'] = $crossDetail['unit'];
        }

        $returnDetail['remark'] = $order->remark;
        $returnDetail['goods_name'] = $head['goods_name'];
        
        // $crossReturn = CrossOrderService::getCrossDetailById($order->relation_cross_id, $contractId);
        // print_r($crossReturn);die;
        

        $this->pageTitle=$this->checkPageTitle;
        $this->render($this->checkViewName,array(
            "data"=>$data[0],
            "head"=>$head,
            "crossDetail"=>$crossDetail,
            "returnDetail"=>$returnDetail
        ));
    }


    public function getCheckData($id)
    {
         $data=Utility::query("
              select a.*
              from t_check_item a
                left join t_cross_order b on a.obj_id=b.cross_id
                left join t_check_detail c on c.check_id = a.check_id
                where c.check_status = 0 and a.business_id=".$this->businessId." and a.obj_id=".$id);
         return $data;
    }


    public function actionDetail()
    {
        $detailId=Mod::app()->request->getParam("detail_id");
        if(!Utility::checkQueryId($detailId))
        {
            $this->renderError("信息异常！", $this->mainUrl);
        }

        $data=$this->getDetailData($detailId);

        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", $this->mainUrl);
        }

        $order = CrossOrder::model()->findByPk($data[0]['obj_id']);
        $head = CrossOrderService::getCrossHead($order->relation_cross_id, $order->contract_id);
        $crossDetail = CrossOrderService::getCrossDetail($order->relation_cross_id, $order->contract_id);
        
        $returnDetail['type'] = $order->type;
        if($order->type==ConstantMap::DISTRIBUTED_RETURN){
            $returnDetail['details'] = CrossOrderService::getReturnDetailById($order->cross_id);
        }else{
            $returnDetail['cross_remark'] = $order->remark;
            $returnDetail['quantity'] = $order->quantity;
            $returnDetail['unit'] = $crossDetail['unit'];
        }

        $returnDetail['remark'] = $order->remark;
        $returnDetail['goods_name'] = $head['goods_name'];

        $this->pageTitle="查看审核详情";
        $this->render($this->detailViewName,array(
            "data"=>$data[0],
            "head"=>$head,
            "crossDetail"=>$crossDetail,
            "returnDetail"=>$returnDetail
        ));
    }

    public function getDetailData($detailId)
    {
        return $data=Utility::query("
              select b.*
              from t_check_detail a
                left join t_check_log b on b.check_id = a.check_id
                where a.business_id=".$this->businessId." and 
                a.detail_id=".$detailId);
    }

    public function checkIsCanEdit($status) {
        return $status == StockBatchSettlement::STATUS_SUBMIT;
    }
}