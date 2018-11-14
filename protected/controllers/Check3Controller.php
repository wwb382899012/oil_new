<?php
/**
*   业务审核,目前1级审核
*/
class Check3Controller extends CheckController
{
    public $businessId = 3;
    public $mainRightCode="check3";
    public $checkedStatement = "当前信息已审核";
    public $extraMapName='transaction_checkitems_config';
    public function pageInit() {
        parent::pageInit();
        $attr= $_REQUEST["search"];
        $checkStatus=empty($attr["checkStatus"])?$attr["checkStatus"]:'_'.$attr["checkStatus"];
        $this->treeCode=$this->mainRightCode.$checkStatus;
        $this->businessId=3;
        $this->rightCode="check3";
        $this->mainUrl = "/check3/";
        $this->checkViewName = "/check3/check";
        $this->detailViewName = "/check3/detail";
        $this->filterActions = "getFileDownload";
        $this->newUIPrefix = 'new_';
    }

    public function actionIndex()
    {
//        $attr = $_GET['search'];
        $attr = $this->getSearch();
        $checkStatus=1;
        if(!empty($attr["checkStatus"])) {
            $checkStatus=$attr["checkStatus"];
            unset($attr['checkStatus']);
        }
        $query="";
        /*$up_partner_name = '';
        if(!empty($attr["up.name*"]))
        {
            $query.=" and ((b.type=1 and up.name like '%".trim($attr['up.name*'])."%') or (b.type=2 and b.is_main=1 and rp.name like '%".trim($attr['up.name*'])."%'))";
            $up_partner_name = $attr["up.name*"];
            unset($attr["up.name*"]);
        }
        $down_partner_name = '';
        if(!empty($attr["dp.name*"]))
        {
            $query.=" and ((b.type=2 and dp.name like '%".trim($attr['dp.name*'])."%') or (b.type=1 and b.is_main=1 and rp.name like '%".trim($attr['dp.name*'])."%'))";
            $down_partner_name = $attr["dp.name*"];
            unset($attr["dp.name*"]);
        }*/
        $userId=Utility::getNowUserId();
        $roleId=UserService::getNowUserMainRoleId();
        $sql = "select {col} from t_check_detail a 
                left join t_check_log d on d.detail_id=a.detail_id 
                left join t_contract_group cg on cg.contract_id=a.obj_id 
                left join t_contract b on b.contract_id=cg.contract_id 
                left join t_project c on c.project_id=cg.project_id
                left join t_project_base e on e.project_id=c.project_id
                left join t_system_user u on u.user_id=c.manager_user_id 
                left join t_corporation co on co.corporation_id=cg.corporation_id 
                left join t_partner up on up.partner_id=cg.up_partner_id 
                left join t_partner dp on dp.partner_id=cg.down_partner_id 
                left join t_system_user su on su.user_id=b.create_user_id 
                ". $this->getWhereSql($attr);
        $fields="";
        switch($checkStatus)
        {
            case 2:
                // 审核通过
                $sql .= " and a.status=1 and a.check_status=1 and a.check_user_id={$userId}";
                $fields.=",0 isCanCheck, ".$checkStatus." as checkStatus ";
                break;
            case 3:
                // 审核驳回
                $sql .= " and a.status=1 and a.check_status=-1 and a.check_user_id={$userId}";
                $fields.=",0 isCanCheck, ".$checkStatus." as checkStatus ";
                break;
            case 1:
            default:
                // 待审核
                /*$corp_ids = UserService::getUserCorpIds(Utility::getNowUserId());
                if($corp_ids) {
                    $sql .= " and b.status=".Contract::STATUS_CREDIT_CONFIRMED." and a.status=0 and a.check_status=0 and c.corporation_id in ($corp_ids)";
                } else {
                    $sql .= " and b.status=".Contract::STATUS_CREDIT_CONFIRMED." and a.status=0 and a.check_status=0";
                }*/
                $sql .= " and b.status=".Contract::STATUS_CREDIT_CONFIRMED." and a.status=0 and a.check_status=0";
                $fields.=",1 isCanCheck, ".$checkStatus." as checkStatus ";
                $checkStatus = 1;
                break;
        }
        $sql .= $query." and ".AuthorizeService::getUserDataConditionString("cg")." and (a.role_id= {$roleId} or a.check_user_id={$userId}) and a.business_id={$this->businessId} order by a.check_id desc {limit}";
        $fields  = "a.detail_id, a.obj_id, b.corporation_id, co.name as corp_name, c.type as project_type, c.project_code, b.status as contract_status, 
                    b.is_main, b.project_id, b.type as contract_type, b.num, u.name, b.create_time, su.name as create_name, 
                    cg.up_partner_id,cg.down_partner_id,up.name as up_partner_name, dp.name as down_partner_name" . $fields.',b.split_type,b.original_id';
        $user = Utility::getNowUser();
        if(!empty($user['corp_ids'])) {
            $data=$this->queryTablesByPage($sql,$fields);
        }else{
            $data = array();
        }
        /*if(!empty($up_partner_name)) {
            $attr["up.name*"] = $up_partner_name;
        }
        if(!empty($down_partner_name)) {
            $attr["dp.name*"] = $down_partner_name;
        }*/
        $attr["checkStatus"]=$checkStatus;
        $data["search"]=$attr;
        $this->render("index", $data);
    }


    public function getCheckData($id)
    {
        $sql = "
              select a.*,c.detail_id
              from t_check_item a
                left join t_contract b on a.obj_id=b.contract_id
                left join t_check_detail c on c.check_id = a.check_id
                where c.check_status = 0 and a.business_id=".$this->businessId." and a.obj_id=".$id;

        $data=Utility::query($sql);
        if(empty($data)) {
            $this->renderError($this->checkedStatement, $this->mainUrl);
        } else {
            $data[0]['items']=$this->getExtraItems();
            return $data;
        }
    }

    public function getExtraItems()
    {
        $items=Map::$v[$this->extraMapName];
        if(empty($items))
            $items=array();
        return $items;
    }

    protected function getExtraCheckItems()
    {
        $items=$_POST["items"];
        if(is_array($items) && count($items)>0)
            return $items;
        else
            return array();
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
        return $status == Contract::STATUS_CREDIT_CONFIRMED;
    }
} 

?>