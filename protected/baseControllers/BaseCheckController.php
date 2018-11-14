<?php

/**
 * Created by youyi000.
 * DateTime: 2017/3/29 16:01
 * Describe：
 */
abstract class BaseCheckController extends AttachmentController
{
    public $businessId=0;

    public $nowUserRoleId=0;

    public $nowUserId=0;

    //审核界面要引用的显示详细的页面，子类继承时需要，默认传递待审核的model对象
    public $detailPartialFile="";
    //被审核对象详情页面中的model变更名
    public $detailPartialModelName="";
    /**
     * 额外审核项的Map配置项名
     * @var string
     */
    public $extraMapName="";


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

    public $indexViewName="/check/index";

    public $checkPageTitle="审核";

    public $checkedStatement = "没有需要您审核的信息！";

    /**
     * 审核按钮的可视状态，默认全部显示
     *
     * @var array
     */
    public $checkButtonStatus=array("pass"=>1,"back"=>1);//array("pass"=>1,"back"=>1,"reject"=>1);


    public function pageInit()
    {
        $this->nowUserId=Utility::getNowUserId();
        $this->nowUserRoleId=UserService::getNowUserMainRoleId();
        $this->mainUrl="/".$this->getId()."/";
    }

    public function initRightCode()
    {

    }

    public function getFlowConfig()
    {
        return FlowService::$business[$this->businessId];
    }

    public function actionIndex()
    {
//        $search = $_REQUEST["search"];
        $search = $this->getSearch();

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
        $search["checkStatus"]=$checkStatus;
        $data["search"]=$search;
        $data["b"]=$this->businessId;
        $this->render($this->indexViewName,$data);
    }

    public function getMainSql($search)
    {
        $sql="select {col} from t_check_detail a
                 left join t_check_item c on c.check_id=a.check_id and c.node_id>0
                 left join t_flow_node d on d.node_id=c.node_id
                ".$this->getWhereSql($search)." and a.business_id=".$this->businessId."
                and (a.role_id=".$this->nowUserRoleId." or a.check_user_id=".$this->nowUserId.")";
        return $sql;
    }

    public function getFields()
    {
        return "a.*,d.node_name";
    }


    public function actionCheck()
    {
        $id=Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
        {
            $this->renderError("信息异常！", $this->mainUrl);
        }

        //检查审批对象状态
        $this->checkObjectStatus($id, $this->mainUrl);
        //应该替换这个逻辑,start
        $model=$this->getCheckObjectModel($id);

        if(empty($model))
        {
            $this->renderError("当前信息不存在！", $this->mainUrl);
        }
        //应该替换这个逻辑,end

        $checkDetail=CheckDetail::model()->find("obj_id=".$id." and status=".CheckDetail::STATUS_NEW." and business_id=".$this->businessId." and (role_id=".$this->nowUserRoleId." or check_user_id=".$this->nowUserId.")");
        if(empty($checkDetail))
            $this->renderError($this->checkedStatement, $this->mainUrl);
        $data=$checkDetail->getAttributes(array("detail_id","check_id","obj_id", "check_node_id"));
        $this->pageTitle=$this->checkPageTitle;
        $this->render($this->checkViewName,array(
            "data"=>$data,
            "items"=>$this->getExtraItems($checkDetail),
            "model"=>$model
        ));
    }

    /**
     * 获取额外的审核项信息，子类可以继承该方法重写
     * @param $checkDetail
     * @return array
     */
    public function getExtraItems($checkDetail)
    {
       $items=Map::$v[$this->extraMapName];
       if(empty($items))
           $items=array();
       return $items;
    }

    /**
     * 获取被审核对象模型，子类必须继承并生写
     * @param $objId
     */
    abstract function getCheckObjectModel($objId);


    public function actionSave()
    {
        $params = $_POST["data"];

        if (!Utility::checkQueryId($params["check_id"]))
        {
            $this->returnError("非法操作！");
        }

        $checkDetail=CheckDetail::model()->findByPk($params["detail_id"]);
        if(empty($checkDetail->detail_id))
            $this->returnError("审核信息不存在！");

        if($checkDetail->role_id!=$this->nowUserRoleId && $checkDetail->check_user_id!=$this->nowUserId)
            $this->returnError("当前信息无需您审核！");

        if ($this->businessId == FlowService::BUSINESS_PAY_APPLICATION) {
            $this->checkPendingWithdraw($params['detail_id']);
        }

        //检查审批对象状态
        $this->checkObjectStatus($checkDetail->obj_id);

        $checkItem=CheckItem::model()->findByPk($checkDetail["check_id"]);
        if (empty($checkItem->check_id)) {
            $this->returnError("非法操作！");
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
            $this->returnSuccess();
        }
        else
            $this->returnError($res);

    }

    public function actionDetail()
    {
        $detailId=Mod::app()->request->getParam("detail_id");
        if(!Utility::checkQueryId($detailId))
        {
            $this->renderError("信息异常！", $this->mainUrl);
        }

        $checkLog=CheckLog::model()->with("extra")->find("t.detail_id=".$detailId);
        if(empty($checkLog))
        {
            $this->renderError("当前信息不存在！", $this->mainUrl);
        }
        $model=$this->getCheckObjectModel($checkLog->obj_id);

        $this->pageTitle="查看审核详情";
        $this->render($this->detailViewName,
                      array( "model"=>$model, "checkLog"=>$checkLog  ));
    }

    /**
     * 检查是否已经撤回、或其他逻辑，子类覆盖实现即可
     * @param $objId 检查项目ID
     * @param string $backUrl 错误跳转URL
     * @return bool
     */
    protected function checkObjectStatus($objId, $backUrl = ''){
        return false;
    }

}