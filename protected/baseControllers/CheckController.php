<?php

/**
 * Created by youyi000.
 * DateTime: 2016/7/6 17:26
 * Describe：
 */
class CheckController extends AttachmentController
{
    public $businessId=0;

    //public $roleId=0;

    public $nowUserRoleId=0;

    public $nowUserId=0;

    //审核界面要引用的显示详细的页面，为空则默认
    public $checkDetailFile="";

    //审核界面额外需要填写的内容文件
    public $extraCheckItemFile="";

    public $contractDetailFile="";


    /**
     * 审核页面的视图
     * @var string
     */
    public $checkViewName="/check/check";

    /**
     * 审核详细页面的视图
     * @var string
     */
    public $detailViewName="/check/detail";

    public $checkPageTitle="审核";

    /**
     * 审核按钮的可视状态，默认全部显示
     *
     * @var array
     */
    public $checkButtonStatus=array("pass"=>1,"back"=>1,"reject"=>1);


    public function pageInit()
    {
        $this->nowUserId=Utility::getNowUserId();
        $this->nowUserRoleId=UserService::getNowUserMainRoleId();
        //$this->roleId = UserService::getUserMainRoleId(Utility::getNowUserId());

        //$this->filterActions="detail";
        //$this->filterActions="check,save";
    }

    public function getFlowConfig()
    {
        return FlowService::$business[$this->businessId];
    }

    public function initRightCode()
    {

    }

    public function actionIndex()
    {
        $attr = $_REQUEST[search];

        $checkStatus=1;
        if(!empty($attr["checkStatus"]))
        {
            $checkStatus=$attr["checkStatus"];
            unset($attr["checkStatus"]);
        }
        $user = SystemUser::getUser($this->nowUserId);

        $sql="
                 select {col} from t_check_detail a
                 left join t_project p on a.obj_id=p.project_id
                 left join t_partner b on p.up_partner_id=b.partner_id
                 left join t_partner s on p.down_partner_id=s.partner_id
                 left join t_check_item c on c.check_id=a.check_id and c.node_id>0
                 left join t_flow_node d on d.node_id=c.node_id
                ".$this->getWhereSql($attr)." and a.business_id=".$this->businessId."
                and (a.role_id=".$this->nowUserRoleId." or a.check_user_id=".$this->nowUserId.")";

        $fields="a.*,p.project_name,p.project_id,p.up_partner_id,p.down_partner_id,p.status as project_status,b.name as up_name,s.name as down_name,d.node_name";

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

        $sql .= " and p.corporation_id in (".$user['corp_ids'].") order by a.obj_id desc {limit}";
        //echo $sql;die;
        //echo $fields;die;
        $data = $this->queryTablesByPage($sql,$fields);
        $attr["checkStatus"]=$checkStatus;
        $data["search"]=$attr;
        $data["b"]=$this->businessId;
        $this->render('/check/index',$data);
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
        $extraItems=$this->getExtraItems();
        $this->pageTitle=$this->checkPageTitle;
        $this->render($this->checkViewName,array(
            "data"=>$data[0],
            "extraItems"=>$extraItems
        ));
    }

    public function getExtraItems()
    {
        return [];
    }

    public function getCheckData($id)
    {
        return $data=Utility::query("
              select
                  a.*,b.name as up_name,s.name as down_name,c.business_id,c.check_id,
                  u.price as up_price,u.quantity as up_quantity,u.amount as up_amount,
                  d.price as down_price,d.quantity as down_quantity,d.amount as down_amount,
                  d.deposit_amount,d.fee_amount,u.pay_date,d.pay_date as return_date,
                  uu.name as manager_name
              from t_project a
                left join t_partner b on a.up_partner_id=b.partner_id
                left join t_partner s on a.down_partner_id=s.partner_id
                left join t_project_detail u on a.project_id=u.project_id and u.type=1
                left join t_project_detail d on a.project_id=d.project_id and d.type=2
                left join t_system_user uu on uu.user_id=a.manager_user_id
                left join t_check_item c on a.project_id=c.obj_id and c.node_id>0 and c.business_id=".$this->businessId."
                where a.project_id=".$id."");
    }


    public function actionSave()
    {
        $params = $_POST["obj"];
        if (empty($params["check_id"])) {
            $this->returnError("非法操作！");
        }
        $checkItem=CheckItem::model()->findByPk($params["check_id"]);
        if (empty($checkItem->check_id)) {
            $this->returnError("非法操作！");
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
            $this->returnSuccess();
        }
        else
            $this->returnError($res);

    }

    /**
     * 获取要保存的额外信息，数组格式array(array("type"=>1,"display_name"=>"XXX","key"=>"item1","value"=>"XXXXX",))
     * @return array
     */
    protected function getExtras()
    {
        return array();
    }

    /**
     * 获取额外的审核明细信息，数组格式array(array("type"=>1,"check_status"=>1,"remark"=>"XXXXX",))
     * @return array
     */
    protected function getExtraCheckItems()
    {
        $items=json_decode($_POST["items"],true);
        if(is_array($items) && count($items)>0)
            return $items;
        else
            return array();
    }


    public function actionDetail()
    {
       /* $id=Mod::app()->request->getParam("id");
        if(empty($id))
        {
            $this->renderError("信息异常！", $this->mainUrl);
        }*/
        $detailId=Mod::app()->request->getParam("detail_id");
        if(!Utility::checkQueryId($detailId))
        {
            $this->renderError("信息异常！", $this->mainUrl);
        }

        $data=$this->getDetailData($detailId);

        if(!empty($this->extraCheckItemFile))
        {
            $extraItems=FlowService::getExtraItems($detailId);
        }

        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", $this->mainUrl);
        }

        $this->pageTitle="查看审核详情";
        $this->render($this->detailViewName,array(
            "data"=>$data[0],
            "extraItems"=>$extraItems
        ));
    }

    public function getDetailData($detailId)
    {
        return $data=Utility::query("
              select
                  a.*,log.obj_id,log.business_id,log.detail_id,log.check_status,log.remark check_remark,
                  b.name as up_name,s.name as down_name,
                  u.price as up_price,u.quantity as up_quantity,u.amount as up_amount,
                  d.price as down_price,d.quantity as down_quantity,d.amount as down_amount,
                  d.deposit_amount,d.fee_amount,u.pay_date,d.pay_date as return_date,
                  uu.name as manager_name
              from t_project a
                left join t_partner b on a.up_partner_id=b.partner_id
                left join t_partner s on a.down_partner_id=s.partner_id
                left join t_project_detail u on a.project_id=u.project_id and u.type=1
                left join t_project_detail d on a.project_id=d.project_id and d.type=2
                left join t_system_user uu on uu.user_id=a.manager_user_id
                left join t_check_log log on a.project_id=log.obj_id
                where log.detail_id=".$detailId."");
    }


}