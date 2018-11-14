<?php
/**
*   风控审核,目前1级审核
*/
class Check2Controller  extends CheckController
{
    public $businessId = 2;
    public $mainRightCode="check2";
    public $checkedStatement = "当前信息已审核";
    public function pageInit() {
        parent::pageInit();
        $this->newUIPrefix = 'new_';
        $attr= $_REQUEST["search"];
        $checkStatus=empty($attr["checkStatus"])?$attr["checkStatus"]:'_'.$attr["checkStatus"];
        $this->treeCode=$this->mainRightCode.$checkStatus;
        $this->businessId=2;
        $this->rightCode="check2";
        $this->mainUrl = "/check2/";
        $this->checkViewName = "/check2/check";
        $this->detailViewName = "/check2/detail";
        // $this->filterActions = "detailById";
    }

    /*public function actionIndex()
    {
        $attr = $_GET['search'];
        $checkStatus=1;
        if(!empty($attr["checkStatus"])) {
            $checkStatus=$attr["checkStatus"];
            unset($attr['checkStatus']);
        }
        $query="";
        $projectType = 0;
        if (!empty($attr['project_type'])) {
            switch ($attr["project_type"]) {
                case ConstantMap::SELF_IMPORT_FIRST_SALE_LAST_BUY: //进口自营-先销后采
                    $query .= " and c.type = " . ConstantMap::PROJECT_TYPE_SELF_IMPORT . ' and e.buy_sell_type = ' . ConstantMap::FIRST_SALE_LAST_BUY;
                    break;
                case ConstantMap::SELF_IMPORT_FIRST_BUY_LAST_SALE: //进口自营-先采后销
                    $query .= " and c.type = " . ConstantMap::PROJECT_TYPE_SELF_IMPORT . ' and e.buy_sell_type = ' . ConstantMap::FIRST_BUY_LAST_SALE;
                    break;
                case ConstantMap::SELF_INTERNAL_TRADE_FIRST_SALE_LAST_BUY: //内贸自营-先销后采
                    $query .= " and c.type = " . ConstantMap::PROJECT_TYPE_SELF_INTERNAL_TRADE . ' and e.buy_sell_type = ' . ConstantMap::FIRST_SALE_LAST_BUY;
                    break;
                case ConstantMap::SELF_INTERNAL_TRADE_FIRST_BUY_LAST_SALE: //内贸自营-先采后销
                    $query .= " and c.type = " . ConstantMap::PROJECT_TYPE_SELF_INTERNAL_TRADE . ' and e.buy_sell_type = ' . ConstantMap::FIRST_BUY_LAST_SALE;
                    break;
                default:
                    $query .= " and c.type = " . $attr['project_type'];
                    break;
            }
            $projectType = $attr['project_type'];
            unset($attr['project_type']);
        }
        $up_partner_name = '';
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
        }

        $userId=Utility::getNowUserId();
        $roleId=UserService::getNowUserMainRoleId();
        $sql = "select {col} 
            from t_check_detail a 
            left join t_check_log d on d.detail_id=a.detail_id 
            left join t_contract b on a.obj_id=b.contract_id
            left join t_project c on c.project_id=b.project_id
            left join t_project_base e on e.project_id=c.project_id
            left join t_system_user u on u.user_id=c.manager_user_id 
            left join t_corporation co on co.corporation_id=b.corporation_id 
            left join t_partner up on up.partner_id = b.partner_id and b.type=1 
            left join t_partner dp on dp.partner_id=b.partner_id and b.type=2
            left join t_partner rp on b.is_main=1 and rp.partner_id=(select partner_id from t_contract where project_id=b.project_id and is_main=1 and contract_id<>b.contract_id and type<>b.type)
            left join t_system_user su on su.user_id=b.create_user_id
            ". $this->getWhereSql($attr);
        $fields="";
        switch($checkStatus)
        {
            case 2:
                // 审核通过
                $sql .= " and a.status=1 and a.check_status=1 and d.create_user_id={$userId}";
                $fields.=",0 isCanCheck, ".$checkStatus." as checkStatus ";
                break;
            case 3:
                // 审核驳回
                $sql .= " and a.status=1 and a.check_status=-1";
                $fields.=",0 isCanCheck, ".$checkStatus." as checkStatus ";
                break;
            case 1:
            default:
                // 待审核
                $sql .= " and b.status=".Contract::STATUS_SUBMIT." and a.status=0 and a.check_status=0";
                $fields.=",1 isCanCheck, ".$checkStatus." as checkStatus ";
                $checkStatus = 1;
                break;
        }

        $sql .= $query." and (a.role_id= {$roleId} or a.check_user_id={$userId}) and a.business_id={$this->businessId} order by a.check_id desc {limit}";
        $fields  = "a.detail_id,a.obj_id, b.corporation_id, co.name as corp_name, c.type as project_type, e.buy_sell_type , c.project_code, b.status as contract_status, su.name as create_name,
                    b.is_main, b.project_id, b.type as contract_type, b.num, u.name, b.create_time, 
                    ifnull(up.partner_id,0) as up_partner_id,ifnull(up.name,'') as up_partner_name,
                    ifnull(dp.partner_id,0) as down_partner_id,ifnull(dp.name,'') as down_partner_name,
                    rp.partner_id, rp.name as partner_name" . $fields;
        $data=$this->queryTablesByPage($sql,$fields);
        $map = Map::$v;
        if (Utility::isNotEmpty($data['data']['rows'])) {
            foreach ($data['data']['rows'] as $key => $row) {
                $type_desc= $map['project_type'][$row['project_type']];
                if(!empty($row["buy_sell_type"])){
                    $type_desc .= '-'.$map['purchase_sale_order'][$row["buy_sell_type"]];
                }
                $data['data']['rows'][$key]['project_type_desc'] = $type_desc;
                if($row['is_main']==1){
                    $buy_sell_desc = $map['buy_sell_desc_type'][$row['is_main']];
                }else{
                    $buy_sell_desc = $map['buy_sell_desc_type'][$row['is_main']][$row['contract_type']].$row['num'];
                }
                $data['data']['rows'][$key]['buy_sell_desc'] = $buy_sell_desc;
            }
        }
        if (!empty($projectType)) {
            $attr['project_type'] = $projectType;
        }
        if(!empty($up_partner_name)) {
            $attr["up.name*"] = $up_partner_name;
        }
        if(!empty($down_partner_name)) {
            $attr["dp.name*"] = $down_partner_name;
        }
        $attr["checkStatus"]=$checkStatus;
        $data["search"]=$attr;
        $this->render("index", $data);
    }*/

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
        $projectType = 0;
        if (!empty($attr['project_type'])) {
            switch ($attr["project_type"]) {
                case ConstantMap::SELF_IMPORT_FIRST_SALE_LAST_BUY: //进口自营-先销后采
                    $query .= " and c.type = " . ConstantMap::PROJECT_TYPE_SELF_IMPORT . ' and e.buy_sell_type = ' . ConstantMap::FIRST_SALE_LAST_BUY;
                    break;
                case ConstantMap::SELF_IMPORT_FIRST_BUY_LAST_SALE: //进口自营-先采后销
                    $query .= " and c.type = " . ConstantMap::PROJECT_TYPE_SELF_IMPORT . ' and e.buy_sell_type = ' . ConstantMap::FIRST_BUY_LAST_SALE;
                    break;
                case ConstantMap::SELF_INTERNAL_TRADE_FIRST_SALE_LAST_BUY: //内贸自营-先销后采
                    $query .= " and c.type = " . ConstantMap::PROJECT_TYPE_SELF_INTERNAL_TRADE . ' and e.buy_sell_type = ' . ConstantMap::FIRST_SALE_LAST_BUY;
                    break;
                case ConstantMap::SELF_INTERNAL_TRADE_FIRST_BUY_LAST_SALE: //内贸自营-先采后销
                    $query .= " and c.type = " . ConstantMap::PROJECT_TYPE_SELF_INTERNAL_TRADE . ' and e.buy_sell_type = ' . ConstantMap::FIRST_BUY_LAST_SALE;
                    break;
                default:
                    $query .= " and c.type = " . $attr['project_type'];
                    break;
            }
            $projectType = $attr['project_type'];
            unset($attr['project_type']);
        }
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
                $sql .= " and a.status=1 and a.check_status=1 and d.create_user_id={$userId}";
                $fields.=",0 isCanCheck, ".$checkStatus." as checkStatus ";
                break;
            case 3:
                // 审核驳回
                $sql .= " and a.status=1 and a.check_status=-1";
                $fields.=",0 isCanCheck, ".$checkStatus." as checkStatus ";
                break;
            case 1:
            default:
                // 待审核
                $sql .= " and b.status=".Contract::STATUS_SUBMIT." and a.status=0 and a.check_status=0";
                $fields.=",1 isCanCheck, ".$checkStatus." as checkStatus ";
                $checkStatus = 1;
                break;
        }

        $sql .= $query." and " . AuthorizeService::getUserDataConditionString('cg') . " and (a.role_id= {$roleId} or a.check_user_id={$userId}) and a.business_id={$this->businessId} order by a.check_id desc {limit}";
        $fields  = "a.detail_id,a.obj_id,b.corporation_id, co.name as corp_name, c.type as project_type, 
                    e.buy_sell_type , c.project_code, b.status as contract_status, su.name as create_name,
                    b.is_main, b.project_id, b.type as contract_type, b.num, u.name, b.create_time, 
                    cg.up_partner_id,cg.down_partner_id,up.name as up_partner_name, dp.name as down_partner_name" . $fields.',b.split_type,b.original_id';
        $user = Utility::getNowUser();
        if(!empty($user['corp_ids'])) {
            $data = $this->queryTablesByPage($sql,$fields);
        } else {
            $data = array();
        }
        $map = Map::$v;
        $contractModel=Contract::model();
        if (Utility::isNotEmpty($data['data']['rows'])) {
            foreach ($data['data']['rows'] as $key => $row) {
                $type_desc= $map['project_type'][$row['project_type']];
                if(!empty($row["buy_sell_type"])){
                    $type_desc .= '-'.$map['purchase_sale_order'][$row["buy_sell_type"]];
                }
                $data['data']['rows'][$key]['project_type_desc'] = $type_desc;
                if($row['is_main']==1){
                    $buy_sell_desc = $map['buy_sell_desc_type'][$row['is_main']];
                }else{
                    if($contractModel->isSplit($row['split_type'],$row['original_id'])){
                        $buy_sell_desc = '平移新合同';
                    }else{
                        $buy_sell_desc = $map['buy_sell_desc_type'][$row['is_main']][$row['contract_type']].$row['num'];
                    }
                }
                $data['data']['rows'][$key]['buy_sell_desc'] = $buy_sell_desc;
            }
        }
        if (!empty($projectType)) {
            $attr['project_type'] = $projectType;
        }
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
            return $data;
        }
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
        return $status == Contract::STATUS_SUBMIT;
    }
}