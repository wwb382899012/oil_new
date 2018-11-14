<?php

/**
 * Created by youyi000.
 * DateTime: 2016/11/18 14:58
 * Describe：
 */
class ProgressController extends Controller
{
    public $nowUserId=0;
    public function pageInit()
    {
        $this->mainUrl="/".$this->getId()."/";
        $this->nowUserId=Utility::getNowUserId();
        $this->rightCode = "projectProgress";
    }

    /*public function initRightCode()
    {
        $attr= $_REQUEST["search"];
        $status=$attr["project_progress"];
        $this->treeCode="projectProgress_".$status;
    }*/

    public function actionIndex()
    {
        $data =$this->getIndexData();
        $this->render('index',$data);
    }

    protected function getIndexData()
    {
        $attr = $_GET[search];

        $where="";
        $projectProgress=$attr["project_progress"];
        if(!empty($attr["project_progress"]))
        {
            if($attr["project_progress"]<10) {
                $item = ProjectService::$projectProgress[$attr["project_progress"]];
                if (!empty($item))
                    $where = " and a.status>=" . $item["startStatus"] . " and a.status<" . $item["endStatus"] . "";
            }
            else
                $where = " and a.status=" . Project::STATUS_STOP . "";
            unset($attr["project_progress"]);
        }

        $user = SystemUser::getUser($this->nowUserId);

        $sql="select {col}
            from t_project a 
            left join t_partner b on a.up_partner_id=b.partner_id
            left join t_partner s on a.down_partner_id=s.partner_id
            left join t_check_item c on a.project_id=c.obj_id and c.node_id>0 and c.business_id not in(5,11,12)
            left join t_flow_node n on c.node_id=n.node_id"
            .$this->getWhereSql($attr).$where;
        $sql .= "  and (a.create_user_id=".$this->nowUserId." or a.manager_user_id=".$this->nowUserId." or a.corporation_id in(".$user['corp_ids'].")) order by a.project_id desc {limit}";

        $data= $this->queryTablesByPage($sql,"a.*,b.name as up_name,s.name as down_name,c.check_id,n.node_name");
        $attr["project_progress"]=$projectProgress;
        $data["search"]=$attr;
        return $data;
    }
}