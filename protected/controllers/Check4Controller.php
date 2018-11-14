<?php
/**
 * Describe：合同审核
 */
class Check4Controller extends CheckController
{

    public function pageInit()
    {
        parent::pageInit();
        $this->filterActions="saveFile,getFile";
        $this->businessId=4; 
        $this->rightCode = "check4_";
        $this->checkButtonStatus["back"]=0;

        $this->checkViewName="/check4/checkItems";
        $this->newUIPrefix = 'new_';
    }

    public function initRightCode()
    {
        $attr= $_REQUEST["search"];
        $checkStatus=$attr["checkStatus"];
        $this->treeCode="check4_".$checkStatus;

    }

    protected function getExtraCheckItems()
    {
        $items=json_decode($_POST["items"],true);
        return $items;
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

        $query = '';
        $projectType = 0;
        if (!empty($attr['project_type'])) {
            switch ($attr["project_type"]) {
                case ConstantMap::SELF_IMPORT_FIRST_SALE_LAST_BUY: //进口自营-先销后采
                    $query .= " and p.type = " . ConstantMap::PROJECT_TYPE_SELF_IMPORT . ' and base.buy_sell_type = ' . ConstantMap::FIRST_SALE_LAST_BUY;
                    break;
                case ConstantMap::SELF_IMPORT_FIRST_BUY_LAST_SALE: //进口自营-先采后销
                    $query .= " and p.type = " . ConstantMap::PROJECT_TYPE_SELF_IMPORT . ' and base.buy_sell_type = ' . ConstantMap::FIRST_BUY_LAST_SALE;
                    break;
                case ConstantMap::SELF_INTERNAL_TRADE_FIRST_SALE_LAST_BUY: //内贸自营-先销后采
                    $query .= " and p.type = " . ConstantMap::PROJECT_TYPE_SELF_INTERNAL_TRADE . ' and base.buy_sell_type = ' . ConstantMap::FIRST_SALE_LAST_BUY;
                    break;
                case ConstantMap::SELF_INTERNAL_TRADE_FIRST_BUY_LAST_SALE: //内贸自营-先采后销
                    $query .= " and p.type = " . ConstantMap::PROJECT_TYPE_SELF_INTERNAL_TRADE . ' and base.buy_sell_type = ' . ConstantMap::FIRST_BUY_LAST_SALE;
                    break;
                default:
                    $query .= " and p.type = " . $attr['project_type'];
                    break;
            }
            $projectType = $attr['project_type'];
            unset($attr['project_type']);
        }

        $sql="
                 select {col} from t_check_detail a
                 left join t_contract_file t on a.obj_id=t.file_id
                 left join t_project p on t.project_id=p.project_id
                 left join t_project_base base on p.project_id=base.project_id
                 left join t_system_user s on base.manager_user_id=s.user_id
                 left join t_check_item c on c.check_id=a.check_id and c.node_id>0 
                 left join t_partner up on up.partner_id=base.up_partner_id 
                 left join t_partner dp on dp.partner_Id=base.down_partner_id 
                 left join t_corporation co on co.corporation_id=p.corporation_id 
                 left join t_system_user su on su.user_id=p.create_user_id 
                ".$this->getWhereSql($attr).$query." and a.business_id=".$this->businessId."
                and (a.role_id=".$this->nowUserRoleId." or a.check_user_id=".$this->nowUserId.")";

        $fields="a.detail_id,p.project_id,p.project_name,p.status as project_status,p.type,p.project_code,base.buy_sell_type,s.name as manager_name,p.corporation_id,
                 co.name as corp_name,base.up_partner_id,base.down_partner_id,up.name as up_partner_name,dp.name as down_partner_name,su.name as create_name,p.create_time";

        switch($checkStatus)
        {
            case 2:
                $sql .= " and a.status=1 and a.check_status=1 ";
                $fields.=",0 isCanCheck, 2 as checkStatus ";
                break;
            case 3:
                $sql .= " and a.status=1 and a.check_status=0";
                $fields.=",0 isCanCheck, 3 as checkStatus ";
                break;
            case 4:
                $sql .= " and a.status=1 and a.check_status=-1";
                $fields.=",0 isCanCheck, 4 as checkStatus ";
                break;
            default:
                $sql .= " and a.status=0";
                $fields.=",1 isCanCheck, 1 as checkStatus ";
                break;
        }

        $sql .= " and p.corporation_id in (".$user['corp_ids'].") group by p.project_id order by p.project_id desc";
       /* echo $sql."<br/>";
        echo $fields;die;*/
        $countSql=str_replace("{col}","count(*) as total",$sql);
        $rows = Utility::query($countSql);

        $data = $this->queryTablesByPage($sql,$fields,count($rows));
        $map = Map::$v;
        if (Utility::isNotEmpty($data['data']['rows'])) {
            foreach ($data['data']['rows'] as $key => $row) {
                $type_desc= $map['project_type'][$row['type']];
                if(!empty($row["buy_sell_type"])){
                    $type_desc .= '-'.$map['purchase_sale_order'][$row["buy_sell_type"]];
                }
                $data['data']['rows'][$key]['project_type_desc'] = $type_desc;
            }
        }

        if (!empty($projectType))
            $attr['project_type'] = $projectType;

        $attr["checkStatus"]=$checkStatus;
        $data["search"]=$attr;
        $data["b"]=$this->businessId;
        $this->render('index',$data);
    }


    public function actionEdit()
    {
        $id=Mod::app()->request->getParam("id");
        $this->getCheckData($id);
    }

    public function actionCheck()
    {
        $id    = Mod::app()->request->getParam("id");
        $type  = Mod::app()->request->getParam("type");
        $compare_id  = Mod::app()->request->getParam("compare_id");
        if(!Utility::checkQueryId($id) || !Utility::checkQueryId($type))
        {
            $this->renderError("参数有误！", "/check4/");
        }

        $checkObj = CheckDetail::model()->find("obj_id=".$id." and business_id=".$this->businessId." and check_status=0 and status=0");
        if(empty($checkObj->detail_id))
            $this->renderError("待审合同信息不存在！", "/check4/");

        $check = $checkObj->getAttributes(true, array("create_time", "create_user_id", "update_time", "update_user_id"));

        /*if($type==2 && !Utility::checkQueryId($compare_id))
            $this->renderError("参数有误！", "/check4/");*/

        $map = Map::$v;

        if($type==1 || ($type==2 && !Utility::checkQueryId($compare_id))){
            $contractFile = ContractFile::model()->with('contract')->findByPk($id);
            $files = $contractFile->getAttributes(true, array("create_time", "create_user_id", "update_time", "update_user_id"));
            $contracts = $contractFile->contract->getAttributes(true, array("create_time", "create_user_id", "update_time", "update_user_id"));
            if(Utility::isEmpty($files) || Utility::isEmpty($contracts)){
                $this->renderError("待审合同信息不存在！", "/check4/");
            }

            if($files['status']!=ContractFile::STATUS_CHECKING)
                $this->renderError("当前状态不允许审核！", $this->mainUrl);
            $check['project_id'] = $files['project_id'];
            $check['category'] = $files['category'];
            $check['contract_type'] = $contracts['type'];
            $data = $files;
            $data['type'] = $contractFile->contract->type;
            $data['id'] = $data['file_id'];
            $data["num"] = $contracts["num"]+1;
            $data["contract_name"] = $contracts["contract_code"]."-".$map["contract_file_categories"][$data['type']][$data['category']]["name"];
            $data['title']="&nbsp;待审合同:&nbsp;".$data["contract_name"]."&nbsp;<span class='text-red'>".$map["contract_standard_type"][$data['version_type']]["name"]."</span>";
            unset($data["remark"]);

            $contractDetailFile     = ROOT_DIR.DIRECTORY_SEPARATOR."protected/views/check4/otherCheck.php";
            /*$extraCheckItems=array(
                array("display_name"=>"合同编号、签订日期、签订地点审核？","type"=>1),
                array("display_name"=>"月息≥ 1.35%/月？","type"=>2),
                array("display_name"=>"上游交提货方式（送货/自提）与下游交提货方式（送货/自提）是否一致？","type"=>3),
                array("display_name"=>"上游交货地点与下游交货地点是否一致？","type"=>4)
            );*/
        }else{
            //待审合同
            $contractFile = ContractFile::model()->with('contract')->findByPk($id);
            $files = $contractFile->getAttributes(true, array("create_time", "create_user_id", "update_time", "update_user_id"));
            $contracts = $contractFile->contract->getAttributes(true, array("create_time", "create_user_id", "update_time", "update_user_id"));
            if(Utility::isEmpty($files) || Utility::isEmpty($contracts)){
                $this->renderError("待审合同信息不存在！", "/check4/");
            }
            if($files['status']!=ContractFile::STATUS_CHECKING)
                $this->renderError("当前状态不允许审核！", $this->mainUrl);

            $check['project_id'] = $files['project_id'];
            $check['category'] = $files['category'];
            $check['contract_type'] = $contracts['type'];
            $data["up"] = $files;
            $data["up"]['id'] = $files['file_id'];
            $data["up"]["num"] = $contracts["num"]+1;
            // $data["up"]["contract_name"] = $map["buy_sell_type"][$data["up"]['type']].$data["up"]["num"]."-".$map["contract_file_categories"][$data["up"]['type']][$data["up"]['category']]["name"];
            $data["up"]["contract_name"] = $contracts["contract_code"]."-".$map["contract_file_categories"][$data["up"]['type']][$data["up"]['category']]["name"];
            $data["up"]['title'] = "&nbsp;待审合同:&nbsp;".$data["up"]["contract_name"]."&nbsp;<span class='text-red'>".$map["contract_standard_type"][$data["up"]['version_type']]["name"]."</span>"; 
            unset($data["up"]["remark"]);
            //参考合同
            $compareContract = ContractFile::model()->with('contract')->findByPk($compare_id);
            $files = $compareContract->getAttributes(true, array("create_time", "create_user_id", "update_time", "update_user_id"));
            $contracts = $compareContract->contract->getAttributes(true, array("create_time", "create_user_id", "update_time", "update_user_id"));
            if(Utility::isEmpty($files) || Utility::isEmpty($contracts)){
                $this->renderError("参考合同信息不存在！", "/check4/");
            }
            $data["down"] = $files;
            $data["down"]['id'] = $files['file_id'];
            $data["down"]['type'] = $compareContract->contract->type;
            $data["down"]["num"] = $contracts["num"]+1;
            // $data["down"]["contract_name"] = $map["buy_sell_type"][$data["down"]['type']].$data["down"]["num"]."-".$map["contract_file_categories"][$data["down"]['type']][$data["down"]['category']]["name"];
            $data["down"]["contract_name"] = $contracts["contract_code"]."-".$map["contract_file_categories"][$data["down"]['type']][$data["down"]['category']]["name"];
            $data["down"]['title'] = "参考合同:&nbsp;".$data["down"]["contract_name"]."&nbsp;<span class='text-red'>".$map["contract_standard_type"][$data["down"]['version_type']]["name"]."</span>"; 
            unset($data["down"]["remark"]);

            $contractDetailFile     = ROOT_DIR.DIRECTORY_SEPARATOR."protected/views/check4/compareCheck.php";
            /*$extraCheckItems=array(
                array("display_name"=>"签订日期及货物所属合同对应下游合同并与上游合同中要求一致？","type"=>51),
                array("display_name"=>"产品名称，销售单价，运输方式，收货数量，交货地点是否一致？","type"=>52),
            );*/
        }
        // print_r($check);die;
        if(($check['contract_type']==ConstantMap::BUY_TYPE && in_array($check['category'], ConstantMap::$upload_contract_type)) || 
           ($check['contract_type']==ConstantMap::SALE_TYPE && $check['category']==ConstantMap::SELL_SALE_CONTRACT_TYPE_INTERNAL)){
            $extraCheckItems = $map['contract_check_items'][1];
        }else{
            $extraCheckItems = $map['contract_check_items'][2];
        }


        // $extraCheckItems = array();

        // print_r($data[0]);die;

        $this->pageTitle="合同审核";
        $this->render($this->checkViewName,array(
            "data"=>$check,
            "contract"=>$data,
            "extraCheckItems"=>$extraCheckItems,
            "contractDetailFile"=>$contractDetailFile
        ));
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

        $res=FlowService::check($checkItem,$params["checkStatus"],$this->nowUserRoleId,$params["remark"],$this->nowUserId,"0",$extras,$extraCheckItems);

        if($res==1)
        {
            $this->returnSuccess();
        }
        else
            $this->returnError($res);

    }


    public function actionDetail()
    {
        $id           = Mod::app()->request->getParam("id");
        $check_status = Mod::app()->request->getParam("check_status");
        $this->getCheckData($id, $check_status);
    }


    public function getCheckData($projectId, $checkStatus=0)
    {
        if($checkStatus==2){
            $checkStatus=1;
        }else if($checkStatus==4){
            $checkStatus=-1;
        }
        $query   = "";
        $groupBy = "";
        if(!empty($checkStatus))
            $query   = " and a.check_status=".$checkStatus;
        else
            $groupBy = " having a.detail_id=(select max(detail_id) from t_check_detail where business_id=a.business_id and obj_id=a.obj_id)"; //审核通过去掉重复

        if(!Utility::checkQueryId($projectId))
        {
            $this->renderError("信息异常！", $this->mainUrl);
        }
        $sql = "select a.*,p.project_id,p.status as project_status,p.project_code,p.type as prject_type,c.name as partner_name, 
                b.buy_sell_type,t.status as contract_status,t.num,t.contract_id,t.contract_code,t.type,t.partner_id, 
                f.file_id,f.category,f.version_type,f.code,f.code_out,f.name as file_name,f.status as file_status,
                l.remark,ex.content, t.corporation_id, t.amount_cny, t.amount, t.currency
                from t_check_detail a
                left join t_contract_file f on a.obj_id=f.file_id
                left join t_contract t on f.contract_id=t.contract_id 
                left join t_partner c on c.partner_id=t.partner_id
                left join t_project p on t.project_id=p.project_id
                left join t_project_base b on p.project_id=b.project_id
                left join t_check_log l on a.detail_id=l.detail_id
                left join t_check_extra_log ex on a.detail_id=ex.detail_id
                where a.business_id=".$this->businessId." and p.project_id=".$projectId." ".$query.
                " and f.type=1 and f.status!=".ContractFile::STATUS_DELETED." ".
                " and (f.status=".ContractFile::STATUS_BACK." or f.status>=".ContractFile::STATUS_CHECKING.") 
                ".$groupBy." order by f.type ,t.num ,f.file_id asc ";
        $check = Utility::query($sql);
        if(Utility::isEmpty($check))
        {
            $this->renderError("当前信息不存在！", $this->mainUrl);
        }
        // print_r($check);die;

        $map = Map::$v;

        foreach ($check as $key => $row) {
            $type_desc= $map['project_type'][$row['prject_type']];
            if(!empty($row["buy_sell_type"])){
                $type_desc .= '-'.$map['purchase_sale_order'][$row["buy_sell_type"]];
            }
            $check[$key]['project_type_desc'] = $type_desc;
        }


        $data['project_id']        = $check[0]['project_id'];
        $data['project_code']      = $check[0]['project_code'];
        $data['project_status']    = $check[0]['project_status'];
        $data['contract_status']   = $check[0]['contract_status'];
        $data['prject_type']       = $check[0]['prject_type'];
        $data['buy_sell_type']     = $check[0]['buy_sell_type'];
        $data['corporation_id']     = $check[0]['corporation_id'];
        $data['project_type_desc'] = $check[0]['project_type_desc'];

        $infoArr = array();
        $count   = 0;
        

        foreach ($check as $key => $value) {
            $infoArr[$value['type']][$value['contract_code']][$key] = $value;
            $infoArr[$value['type']][$value['contract_code']][$key]['amount'] = Map::$v['currency'][$value['currency']]['ico'] . Utility::numberFormatFen2Yuan($value['amount']);
            $infoArr[$value['type']][$value['contract_code']][$key]['goods'] = GoodsService::getSpecialGoodsNames(ContractService::getContractAllGoodsId($value['contract_id']));
            $num = 0;
            if(!empty($value['content'])){
                $contents = json_decode($value['content'], true);
                $infoArr[$value['type']][$value['contract_code']][$key]['content'] = $contents;
                if($value['check_status']==-1){
                    foreach ($contents as $k => $v) {
                        if($v['value']==0)
                            $num++;
                    }
                }
                $infoArr[$value['type']][$value['contract_code']][$key]['count'] = $num;
                
            }
            $count++;
        }

        // print_r($infoArr);die;
        $this->pageTitle="合同审核";
        $this->render("edit",array(
            "data"=>$data,
            "count"=>$count,
            "infoArr"=>$infoArr
        ));
    }

}