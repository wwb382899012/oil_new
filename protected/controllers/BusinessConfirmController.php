<?php

/**
 * Created by vector.
 * DateTime: 2017/08/31 15:58
 * Describe：
 */
class BusinessConfirmController  extends AttachmentController
{
    /**
     * 是否显示全览链接
     * @var int
     */
    public $isShowAllLink = 1;
    public $isCanAdd = 0;

    public function pageInit()
    {
        $this->attachmentType=Attachment::C_BUDGET;
        $this->newUIPrefix="new_";
        $this->filterActions="getFile";
        $this->rightCode="businessConfirm";
        $this->isCanAdd = 1;
    }


    /*public function actionIndex()
    {
        $attr = $_GET[search];
        if(!is_array($attr) || !array_key_exists("a.status",$attr))
        {
            $attr["a.status"]="-2";
        }
        $search=$attr;

        $statusArr = array(Contract::STATUS_BACK, Contract::STATUS_TEMP_SAVE, Contract::STATUS_SAVED);

        $query="";
        $status="";
        if($attr["a.status"]=="-2"){
            $status="-2";
            unset($attr["a.status"]);
            $query=" and (a.status in(".implode(',', $statusArr).") or a.status is null)";
        }else if(isset($attr["a.status"]) && $attr["a.status"]=="0"){
            $status="0";
            unset($attr["a.status"]);
            $query=" and (a.status is null or a.status=0)";
        }else if($attr["a.status"]=="19"){
            $status="19";
            unset($attr["a.status"]);
            $query=" and a.status>=19";
        }

        $projectType = 0;
        if (!empty($attr['project_type'])) {
            switch ($attr["project_type"]) {
                case ConstantMap::SELF_IMPORT_FIRST_SALE_LAST_BUY: //进口自营-先销后采
                    $query .= " and p.type = " . ConstantMap::PROJECT_TYPE_SELF_IMPORT . ' and b.buy_sell_type = ' . ConstantMap::FIRST_SALE_LAST_BUY;
                    break;
                case ConstantMap::SELF_IMPORT_FIRST_BUY_LAST_SALE: //进口自营-先采后销
                    $query .= " and p.type = " . ConstantMap::PROJECT_TYPE_SELF_IMPORT . ' and b.buy_sell_type = ' . ConstantMap::FIRST_BUY_LAST_SALE;
                    break;
                case ConstantMap::SELF_INTERNAL_TRADE_FIRST_SALE_LAST_BUY: //内贸自营-先销后采
                    $query .= " and p.type = " . ConstantMap::PROJECT_TYPE_SELF_INTERNAL_TRADE . ' and b.buy_sell_type = ' . ConstantMap::FIRST_SALE_LAST_BUY;
                    break;
                case ConstantMap::SELF_INTERNAL_TRADE_FIRST_BUY_LAST_SALE: //内贸自营-先采后销
                    $query .= " and p.type = " . ConstantMap::PROJECT_TYPE_SELF_INTERNAL_TRADE . ' and b.buy_sell_type = ' . ConstantMap::FIRST_BUY_LAST_SALE;
                    break;
                default:
                    $query .= " and p.type = " . $attr['project_type'];
                    break;
            }
            $projectType = $attr['project_type'];
            unset($attr['project_type']);
        }


        if(!empty($attr["up.name*"]))
        {
            $query.=" and (case when a.contract_id is null then pup.name like '%".trim($attr["up.name*"])."%' 
                           when a.type=1 then cp.name like '%".trim($attr["up.name*"])."%'
                           when a.type=2 then false
                        else true end ) ";
            unset($attr["up.name*"]);
        }

        if(!empty($attr["dp.name*"]))
        {
            $query.=" and (case when a.contract_id is null or (a.is_main=1 and a.type=2 and p.type not in(".implode(",",ConstantMap::$self_support_project_type).")) 
                        then pdp.name like '%".trim($attr["dp.name*"])."%' 
                        when a.type=2 then cp.name like '%".trim($attr["dp.name*"])."%'
                         when a.type=1 then false
                        else true end) ";
            unset($attr["dp.name*"]);
        }

        $user = SystemUser::getUser(Utility::getNowUserId());

        $sql = "select {col}
            from t_project p 
            left join t_project_base b on p.project_id=b.project_id
            left join t_contract a on p.project_id=a.project_id
            left join t_system_user u on p.manager_user_id=u.user_id 
            left join t_corporation co on co.corporation_id = p.corporation_id 
            left join t_partner pup on pup.partner_id = b.up_partner_id 
            left join t_partner pdp on pdp.partner_id = b.down_partner_id 
            left join t_partner cp on cp.partner_id=a.partner_id 
            left join t_system_user su on su.user_id=p.create_user_id 
            ". $this->getWhereSql($attr)." and (case when a.is_main=1 and a.contract_id>0 and p.type in (".implode(",", array_merge(ConstantMap::$buy_select_contract_type, ConstantMap::$buy_static_contract_type)).") then a.type=1 else true end) ";
        $sql.="";
        $sql    .= $query." and p.status>=".Project::STATUS_SUBMIT." 
                    and p.corporation_id in (".$user['corp_ids'].") 
                    order by p.project_id desc,a.contract_id asc {limit}";
        $fields  = "p.*,b.buy_sell_type,IFNULL(a.contract_id, 0) as contract_id,case when IFNULL(a.status,0)=0 then 0 when a.status>=19 then 19 else a.status end as contract_status,a.type as contract_type,
                    IFNULL(a.is_main, 1) as is_main,a.num,u.name,p.corporation_id,co.name as corp_name, 
                    a.partner_id as c_partner_id,cp.name as partner_name, su.name as create_name, p.create_time,
                    b.up_partner_id,b.down_partner_id,pup.name as p_up_partner_name,pdp.name as p_down_partner_name";
        $data=$this->queryTablesByPage($sql,$fields);
        $map = Map::$v;
        if (Utility::isNotEmpty($data['data']['rows'])) {
            foreach ($data['data']['rows'] as $key => $row) {
                $type_desc= $map['project_type'][$row['type']];
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

        if($status=="0" || $status=="-2" || $status=="19")
            $attr["a.status"]=$status;
        if (!empty($projectType)) 
            $attr['project_type'] = $projectType;

        $data["search"]=$search;
        $this->render("index", $data);
    }*/

    public function actionIndex()
    {
        $attr = $this->getSearch();//$_GET[search];
        if(!is_array($attr) || !array_key_exists("a.status",$attr))
        {
            $attr["a.status"]="-2";
        }
        $search=$attr;

        $statusArr = array(Contract::STATUS_BACK, Contract::STATUS_TEMP_SAVE, Contract::STATUS_SAVED);

        $query="";
        $status="";
        if($attr["a.status"]=="-2"){
            $status="-2";
            unset($attr["a.status"]);
            $query=" and (a.status in(".implode(',', $statusArr).") or a.status is null)";
        }else if(isset($attr["a.status"]) && $attr["a.status"]=="0"){
            $status="0";
            unset($attr["a.status"]);
            $query=" and (a.status is null or a.status=0)";
        }else if($attr["a.status"]=="19"){
            $status="19";
            unset($attr["a.status"]);
            $query=" and a.status>=19";
        }

        $projectType = 0;
        if (!empty($attr['project_type'])) {
            switch ($attr["project_type"]) {
                case ConstantMap::SELF_IMPORT_FIRST_SALE_LAST_BUY: //进口自营-先销后采
                    $query .= " and p.type = " . ConstantMap::PROJECT_TYPE_SELF_IMPORT . ' and b.buy_sell_type = ' . ConstantMap::FIRST_SALE_LAST_BUY;
                    break;
                case ConstantMap::SELF_IMPORT_FIRST_BUY_LAST_SALE: //进口自营-先采后销
                    $query .= " and p.type = " . ConstantMap::PROJECT_TYPE_SELF_IMPORT . ' and b.buy_sell_type = ' . ConstantMap::FIRST_BUY_LAST_SALE;
                    break;
                case ConstantMap::SELF_INTERNAL_TRADE_FIRST_SALE_LAST_BUY: //内贸自营-先销后采
                    $query .= " and p.type = " . ConstantMap::PROJECT_TYPE_SELF_INTERNAL_TRADE . ' and b.buy_sell_type = ' . ConstantMap::FIRST_SALE_LAST_BUY;
                    break;
                case ConstantMap::SELF_INTERNAL_TRADE_FIRST_BUY_LAST_SALE: //内贸自营-先采后销
                    $query .= " and p.type = " . ConstantMap::PROJECT_TYPE_SELF_INTERNAL_TRADE . ' and b.buy_sell_type = ' . ConstantMap::FIRST_BUY_LAST_SALE;
                    break;
                default:
                    $query .= " and p.type = " . $attr['project_type'];
                    break;
            }
            $projectType = $attr['project_type'];
            unset($attr['project_type']);
        }

        $user = SystemUser::getUser(Utility::getNowUserId());

        $sql = 'select {col} from t_contract_group cg
                left join t_project p on p.project_id = cg.project_id 
                left join t_project_base b on b.project_id = p.project_id 
                left join t_contract a on a.contract_id = cg.contract_id 
                left join t_system_user u on u.user_id = p.manager_user_id 
                left join t_system_user su on su.user_id = p.create_user_id 
                left join t_corporation co on co.corporation_id = p.corporation_id 
                left join t_partner up on up.partner_id = cg.up_partner_id 
                left join t_partner dp on dp.partner_id = cg.down_partner_id '.$this->getWhereSql($attr).$query.' 
                and p.status >= '.Project::STATUS_SUBMIT.' and p.corporation_id in('.$user['corp_ids'].') order by p.project_id desc, a.contract_id asc {limit}';

        $fields = 'cg.project_id, cg.contract_id, cg.corporation_id, cg.is_main, cg.up_partner_id, cg.down_partner_id, p.project_code, p.type, p.create_time, b.buy_sell_type, 
                   a.type as contract_type, a.num, u.name, co.name as corp_name, su.name as create_name, up.name as up_partner_name, dp.name as down_partner_name, 
                   case when ifnull(a.status,0)=0 then 0 when a.status>=19 then 19 else a.status end as contract_status,a.split_type,a.original_id';
        $data=$this->queryTablesByPage($sql,$fields);
        $map = Map::$v;
        if (Utility::isNotEmpty($data['data']['rows'])) {
            foreach ($data['data']['rows'] as $key => $row) {
                $type_desc= $map['project_type'][$row['type']];
                if(!empty($row["buy_sell_type"])){
                    $type_desc .= '-'.$map['purchase_sale_order'][$row["buy_sell_type"]];
                }
                $data['data']['rows'][$key]['project_type_desc'] = $type_desc;
                if($row['is_main']==1){
                    $buy_sell_desc = $map['buy_sell_desc_type'][$row['is_main']];
                }else{
                    if($this->contractIsSplit($row['split_type'],$row['original_id'])){
                        $buy_sell_desc = '平移新合同';
                    }else{
                        $buy_sell_desc = $map['buy_sell_desc_type'][$row['is_main']][$row['contract_type']].$row['num'];
                    }
                }
                $data['data']['rows'][$key]['buy_sell_desc'] = $buy_sell_desc;
            }
        }

        if($status=="0" || $status=="-2" || $status=="19")
            $attr["a.status"]=$status;
        if (!empty($projectType))
            $attr['project_type'] = $projectType;

        $data["search"]=$search;

        $this->render("index", $data);
    }

    /**
     * 判断是否可以修改，子类需要修改该方法
     * @param $status
     * @return bool
     */
    public function checkIsCanEdit($status)
    {
        if($status < Contract::STATUS_SUBMIT)
        {
            return true;
        }
        else
            return false;
    }

    /**
     * @desc 是否是拆分的子合同
     * @param $contractInfo
     * @return bool
     */
    private function contractIsSplit($split_type,$original_id){
        return $split_type == Contract::SPLIT_TYPE_SPLIT && $original_id > 0;
    }


    public function actionEdit()
    {
        $id = Mod::app()->request->getParam("id");
        $project_id = Mod::app()->request->getParam("project_id");
        if(!Utility::checkQueryId($project_id))
        {
            $this->renderError("参数错误！", "/businessConfirm/");
        }

        if(!empty($id) && Utility::checkQueryId($id))
        {
            $contract = Contract::model()->findByPk($id);
            if(empty($contract))
            {
                $this->renderError("合同信息不存在");
            }
            if($contract->is_main!=1)
            {
                $this->redirect("/subContract/edit?id=".$id);
            }
        }
        else
        {
            $id=0;
            $contract = Contract::model()->find("project_id=".$project_id." and is_main=1");
            if(!empty($contract->contract_id)){
                if (!$this->checkIsCanEdit($contract->status)) {
                    $this->renderError(BusinessError::outputError(OilError::$CONTRACT_NOT_OPERATION));
                }
                $id = $contract->contract_id;
            }

            /*if(!empty($contract->contract_id) && empty($id))
            {
                $this->renderError("参数错误！", "/businessConfirm/");
            }*/
        }


        $project = Project::model()->with('base')->findByPk($project_id);
        if(empty($project->project_id))
        {
            $this->renderError("当前项目信息不存在！", "/businessConfirm/");
        }



        $temp = 0;
        $upContractConfig  = array();
        $downContractConfig= array();
        $contractConfig = array();
        $buyItems     = array();
        $sellItems    = array();
        $payments   = array();
        $proceeds   = array();

        $projectInfo = $project->getAttributes(true, array("start_date", "status_time", "storehouse_id", "project_name", "contract_id", "end_date", "old_status", "create_user_id", "create_time", "update_user_id", "update_time"));
        $projectBaseInfo = $project->base->getAttributes(true, array("status", "start_date", "end_date", "old_status", "create_user_id", "create_time", "update_user_id", "update_time"));
        $infoItems = array_merge($projectInfo, $projectBaseInfo);
        $infoItems['project_type']   = $infoItems['type'];
        $infoItems['project_status']   = $infoItems['status'];
        //如果是拆分后的合同  商品不能编辑
        $infoItems['goods_can_edit']=1;


        if(empty($id)){
            $infoItems['buy_price_type']     = $infoItems['price_type'];
            $infoItems['sell_price_type']    = $infoItems['price_type'];
            $infoItems['buy_manager_user_id']    = $infoItems['manager_user_id'];
            $infoItems['sell_manager_user_id']   = $infoItems['manager_user_id'];
            $upContractConfig = ContractExtraService::getContractConfig($infoItems['project_type'], ConstantMap::BUY_TYPE);
            $downContractConfig = ContractExtraService::getContractConfig($infoItems['project_type'], ConstantMap::SALE_TYPE);
            $contractConfig = ContractExtraService::getContractConfig($infoItems['project_type'], 0, $infoItems['buy_sell_type']);

            $buyItems  = ProjectBaseGoodsService::getProjectTransByType($project_id, ConstantMap::BUY_TYPE);
            $sellItems = ProjectBaseGoodsService::getProjectTransByType($project_id, ConstantMap::SALE_TYPE);
        }else{

            if(in_array($infoItems['type'], array_merge(ConstantMap::$channel_buy_project_type, ConstantMap::$warehouse_receive_project_type))){
                $buyContract    = Contract::model()->with('extra')->with('agent')->with('partner')->with('corporation')->with('goods')->with('agentDetail')->with('payments')->findByPk($id);
                $sellContract       = Contract::model()->with('extra')->with('partner')->with('corporation')->with('goods')->with('payments')->find("t.project_id=".$project_id." and t.is_main=1 and t.type=2");
                //$sellContract   = Contract::model()->with('extra')->with('partner')->with('corporation')->with('goods')->with('payments')->findByPk($contract->contract_id);
                if (empty($buyContract->contract_id) || empty($sellContract->contract_id)) {
                    $this->renderError(BusinessError::outputError(OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $id)));
                }
                if (!$this->checkIsCanEdit($buyContract->status) || !$this->checkIsCanEdit($sellContract->status) ) {
                    $this->renderError(BusinessError::outputError(OilError::$CONTRACT_NOT_OPERATION));
                }
            }else if($infoItems['type']==ConstantMap::PROJECT_TYPE_SELF_IMPORT || $infoItems['type']==ConstantMap::PROJECT_TYPE_SELF_INTERNAL_TRADE){
                if($infoItems['buy_sell_type']==ConstantMap::FIRST_BUY_LAST_SALE){
                    $buyContract    = Contract::model()->with('extra')->with('agent')->with('partner')->with('corporation')->with('goods')->with('agentDetail')->with('payments')->findByPk($id);
                    if (empty($buyContract->contract_id)) {
                        $this->renderError(BusinessError::outputError(OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $id)));
                    }
                    if (!$this->checkIsCanEdit($buyContract->status)) {
                        $this->renderError(BusinessError::outputError(OilError::$CONTRACT_NOT_OPERATION));
                    }
                    $temp = 1;
                }else{
                    $sellContract   = Contract::model()->with('extra')->with('partner')->with('corporation')->with('goods')->with('payments')->findByPk($id);
                    if (empty($sellContract->contract_id)) {
                        $this->renderError(BusinessError::outputError(OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $id)));
                    }
                    if (!$this->checkIsCanEdit($sellContract->status)) {
                        $this->renderError(BusinessError::outputError(OilError::$CONTRACT_NOT_OPERATION));
                    }
                    $temp = 2;
                }
            }

            if(!empty($buyContract)){
                $buyInfo = $buyContract->getAttributes(true, array("contract_status", "start_date", "end_date", "old_status", "create_user_id", "create_time","update_user_id", "update_time"));
                $infoItems['buy_contract_id'] = $buyInfo['contract_id'];
                $infoItems['buy_type'] = $buyInfo['type'];
                $infoItems['buy_category']    = $buyInfo['category'];
                $infoItems['up_partner_id']   = $buyInfo['partner_id'];
                $infoItems['corporation_id']  = $buyInfo['corporation_id'];
                $infoItems['buy_price_type']     = $buyInfo['price_type'];
                $infoItems['buy_manager_user_id']= $buyInfo['manager_user_id'];
                $infoItems['purchase_currency']  = $buyInfo['currency'];
                $infoItems['agent_id'] = $buyInfo['agent_id'];
                $infoItems['agent_type'] = $buyInfo['agent_type'];
                $infoItems['buy_exchange_rate'] = $buyInfo['exchange_rate'];
                $infoItems['buy_formula'] = $buyInfo['formula'];
                $infoItems['contract_status'] = $buyInfo['status'];

                $upContractConfig   = ContractExtraService::reverseExtraData($buyContract->extra->items, $buyInfo['type'], $buyInfo['category']);
                $buyItems = ContractGoodsService::reverseContractGoodsItems($buyContract->goods, $buyContract->agentDetail, $buyInfo['type'], $buyInfo['exchange_rate']);
                $payments = PaymentPlanService::reversePaymentPlans($buyContract->payments);
                //如果是拆分后的合同  不能驳回
                if($buyInfo['split_type']==Contract::SPLIT_TYPE_SPLIT&&$buyInfo['original_id']>0){
                    $infoItems['is_can_back']=0;
                    $infoItems['goods_can_edit']=0;
                }
            }

            if(!empty($sellContract)){
                $sellInfo = $sellContract->getAttributes(true, array("contract_status", "start_date", "end_date", "old_status", "create_user_id", "create_time", "update_user_id", "update_time"));
                $infoItems['sell_contract_id'] = $sellInfo['contract_id'];
                $infoItems['sell_type'] = $sellInfo['type'];
                $infoItems['sell_category']     = $sellInfo['category'];
                $infoItems['down_partner_id']   = $sellInfo['partner_id'];
                $infoItems['corporation_id']    = $sellInfo['corporation_id'];
                $infoItems['sell_price_type']       = $sellInfo['price_type'];
                $infoItems['sell_manager_user_id']  = $sellInfo['manager_user_id'];
                $infoItems['sell_currency']     = $sellInfo['currency'];
                $infoItems['sell_exchange_rate'] = $sellInfo['exchange_rate'];
                $infoItems['sell_formula'] = $sellInfo['formula'];
                $infoItems['contract_status'] = $sellInfo['status'];

                $downContractConfig = ContractExtraService::reverseExtraData($sellContract->extra->items, $sellInfo['type'], $sellInfo['category']);
                $sellItems = ContractGoodsService::reverseContractGoodsItems($sellContract->goods, 0, $sellInfo['type'], $sellInfo['exchange_rate']);
                $proceeds = PaymentPlanService::reversePaymentPlans($sellContract->payments);
                //如果是拆分后的合同  不能驳回
                if($sellInfo['split_type']==Contract::SPLIT_TYPE_SPLIT&&$sellInfo['original_id']>0){
                    $infoItems['is_can_back']=0;
                    $infoItems['goods_can_edit']=0;
                }
            }

            if($temp == 1)
                $contractConfig = $upContractConfig;
            else if($temp == 2)
                $contractConfig = $downContractConfig;
        }


        $map = Map::$v;

        $type_desc= $map['project_type'][$infoItems['project_type']];
        if(!empty($infoItems["buy_sell_type"])){
            $type_desc .= '-'.$map['purchase_sale_order'][$infoItems["buy_sell_type"]];
        }
        $infoItems['project_type_desc'] = $type_desc;
        $infoItems['buy_sell_desc']     = $map['buy_sell_desc_type'][1];

        // $goods = Goods::model()->findAllToArray('status = :status', array('status' => ConstantMap::STATUS_VALID));
        $goods = Goods::getActiveTreeTable();
        $attachments = Project::getAttachment($project_id);
        //交货日期
        $infoItems['up_delivery_term'] = empty($buyInfo['delivery_term'])&&$buyInfo['delivery_mode']==0?date("Y-m-d",strtotime("+".ConstantMap::CONTRACT_DEFAULT_DELIVERY_TERM." day")):$buyInfo['delivery_term'];

        $infoItems['up_days'] = is_null($buyInfo['days'])?ConstantMap::CONTRACT_DEFAULT_DAYS:$buyInfo['days'];
        $infoItems['up_delivery_mode'] = empty($buyInfo['delivery_mode'])?0:$buyInfo['delivery_mode'];
        $infoItems['down_delivery_term'] = empty($sellInfo['delivery_term'])&&$sellInfo['delivery_mode']==0?date("Y-m-d",strtotime("+".ConstantMap::CONTRACT_DEFAULT_DELIVERY_TERM." day")):$sellInfo['delivery_term'];
        $infoItems['down_days'] = is_null($sellInfo['days'])?ConstantMap::CONTRACT_DEFAULT_DAYS:$sellInfo['days'];
        $infoItems['down_delivery_mode'] = empty($sellInfo['delivery_mode'])?0:$sellInfo['delivery_mode'];
        $infoItems['contract_default_delivery_term'] = ConstantMap::CONTRACT_DEFAULT_DELIVERY_TERM;
        //单位换算比
        $infoItems['contractGoodsUnitConvert'] = ConstantMap::CONTRACT_GOODS_UNIT_CONVERT;
        $infoItems['contractGoodsUnitConvertValue'] = ConstantMap::CONTRACT_GOODS_UNIT_CONVERT_VALUE;

        $this->pageTitle="商务列表＞商务确认";
        $this->render("edit",array(
            'data' => $infoItems,
            'buyItems'=>$buyItems,
            'sellItems'=>$sellItems,
            'upContractConfig'=>$upContractConfig,
            'downContractConfig'=>$downContractConfig,
            'contractConfig'=>$contractConfig,
            'payments'=>$payments,
            'proceeds'=>$proceeds,
            'goods'=>$goods,
            'attachments'=>$attachments,
            )
        );
    }

    public function actionDetail()
    {
        $id=Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
        {
            $this->renderError("参数错误！", "/businessConfirm/");
        }

        $contract = ProjectService::getContractDetailModel($id);
        if(empty($contract) || empty($contract->project)) {
            $this->renderError(BusinessError::outputError(OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id'=>$contract->project_id)), "/riskManagement/");
        }

        $this->render('detail', array('contract'=>$contract));
    }

    public function actionSave() {
        $params = Mod::app()->request->getParam('data');
        // print_r($params);die;
        if ((empty($params['project_id']) && empty($params['project_type']))) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $contractArr = Contract::model()->findAllToArray("project_id=".$params['project_id']." and is_main=1");
        $flag = 0;
		$status = 0;
        if(!empty($contractArr)){
			foreach($contractArr as $contract){
				if($contract['type']==ConstantMap::BUY_TYPE){
					$params['buy_contract_id'] = $contract['contract_id'];
				}else{
					$params['sell_contract_id'] = $contract['contract_id'];
				}
				$status = $contract['satus'];
			}
		}
		if(!empty($status))
			$params['contract_status'] = $status;

        $buyItems   = array();
        $sellItems  = array();
        $paymentItems       = array();
        $proceedItems       = array();
        $upContractItems    = array();
        $downContractItems  = array();
        $contractItems = array();
        $bMark = 0;
        $sMark = 0;

        if(in_array($params['project_type'], array_merge(ConstantMap::$buy_select_contract_type, ConstantMap::$buy_static_contract_type))){ //双边项目
            $bMark = 1;
            $sMark = 1;
            $buyItems           = $params['buyItems'];
            $sellItems          = $params['sellItems'];
            $paymentItems       = $params['paymentItems'];
            $proceedItems       = $params['proceedItems'];
            $upContractItems    = $params['upContractItems'][$params['buy_type']];
            $downContractItems  = $params['downContractItems'][$params['sell_type']];
        }else if(in_array($params['project_type'], ConstantMap::$self_support_project_type) &&
            $params['buy_sell_type']== ConstantMap::FIRST_BUY_LAST_SALE){ //自营-先采后销
            $bMark = 1;
            $buyItems = $params['buyItems'];
            $paymentItems   = $params['paymentItems'];
            $contractItems  = $params['contractItems'][$params['contract_type']];
        }else if(in_array($params['project_type'], ConstantMap::$self_support_project_type) &&
            $params['buy_sell_type']== ConstantMap::FIRST_SALE_LAST_BUY){ //自营-先销后采
            $sMark = 1;
            $sellItems = $params['sellItems'];
            $proceedItems   = $params['proceedItems'];
            $contractItems  = $params['contractItems'][$params['contract_type']];
        }

        if($bMark==1){
            if(ContractService::isHaveSameGoods($buyItems))
                $this->returnError("采购交易明细品名不得重复！");
        }
        if($sMark==1){
            if(ContractService::isHaveSameGoods($sellItems))
                $this->returnError("销售交易明细品名不得重复！");
        }

        // print_r($params['sellItems']);die;
        // print_r($contractItems);die;

        unset($params['buyItems']);
        unset($params['sellItems']);
        unset($params['paymentItems']);
        unset($params['proceedItems']);
        unset($params['upContractItems']);
        unset($params['downContractItems']);
        unset($params['contractItems']);

        //项目是否存在
        /*if (!ProjectService::checkProjectExist($params['project_id'])) {
            $this->returnError(BusinessError::outputError(OilError::$PROJECT_NOT_EXIST, array('project_id' => $params['project_id'])));
        }*/
        $project = Project::model()->findByPk($params['project_id']);
        if(empty($project)) {
            $this->returnError(BusinessError::outputError(OilError::$PROJECT_NOT_EXIST, array('project_id' => $params['project_id'])));
        }

        //当前是否允许操作
        if (!ContractService::isCanOperateContract($params['project_status'], $params['contract_status'])) {
            $this->returnError(BusinessError::outputError(OilError::$CONTRACT_NOT_OPERATION));
        }
		
		

        //参数校验
        if (!$params['is_temp_save']) { //保存操作
            if($bMark==1 && count($buyItems)<1)
                $this->returnError(BusinessError::outputError(OilError::$PURCHASE_TRANSACTION_NOT_ALLOW_NULL));

            if($sMark==1 && count($sellItems)<1)
                $this->returnError(BusinessError::outputError(OilError::$SALE_TRANSACTION_NOT_ALLOW_NULL));
            //检查项目预算表是否上传
            /*if($params['fileUploadStatus']!=1){
                $this->returnError("请上传项目预算表！");
            }*/
            //上游项目合同参数及上游交易明细参数校验
            if(is_array($buyItems) && count($buyItems)>0){
                $paramsCheckRes = ContractService::checkBuyOrSellParamsValid($params, $params['buy_type']);
                if ($paramsCheckRes !== true) {
                    $this->returnError($paramsCheckRes);
                }

                //交易明细参数校验
                $paramsCheckRes = ContractGoodsService::checkParamsValid($params['buy_type'], $params['buy_price_type'], $buyItems, $params['buy_exchange_rate']);
                if ($paramsCheckRes !== true) {
                    $this->returnError($paramsCheckRes);
                }

                //代理手续费校验
                if($params['agent_id']>0 && $params['buy_category'] == ConstantMap::BUY_SALE_CONTRACT_TYPE_AGENT_IMPORT) {
                    $paramsCheckRes = ContractAgentDetailService::checkParamsValid($buyItems);
                    if ($paramsCheckRes !== true) {
                        $this->returnError($paramsCheckRes);
                    }
                }

                //付款计划参数校验
                if(is_array($paymentItems) && count(paymentItems)>0){
                    $paramsCheckRes = PaymentPlanService::checkParamsValid($paymentItems, $params['buy_amount'], $params['buy_type']);
                    if ($paramsCheckRes['code'] == ConstantMap::INVALID) {
                        $this->returnError($paramsCheckRes['error_msg']);
                    }
                }
            }

            //下游项目合同参数及下游交易明细参数校验
            if(is_array($sellItems) && count($sellItems)>0){
                $paramsCheckRes = ContractService::checkBuyOrSellParamsValid($params, $params['sell_type']);
                if ($paramsCheckRes !== true) {
                    $this->returnError($paramsCheckRes);
                }

                $paramsCheckRes = ContractGoodsService::checkParamsValid($params['sell_type'], $params['sell_price_type'], $sellItems, $params['sell_exchange_rate']);
                if ($paramsCheckRes !== true) {
                    $this->returnError($paramsCheckRes);
                }

                //收款计划参数校验
                if(is_array($proceedItems) && count(proceedItems)>0){
                    $paramsCheckRes = PaymentPlanService::checkParamsValid($proceedItems, $params['sell_amount'], $params['sell_type']);
                    if ($paramsCheckRes['code'] == ConstantMap::INVALID) {
                        $this->returnError($paramsCheckRes['error_msg']);
                    }
                }
            }

            if(!empty($params['up_partner_id']) && !empty($params['down_partner_id']) && $params['up_partner_id'] == $params['down_partner_id']) {
                $this->returnError(BusinessError::outputError(OilError::$PARTNER_NOT_ALLOW_REPEAT));
            }

            //合同条款参数校验
            if(is_array($upContractItems) && count($upContractItems)>0){
                $paramsCheckRes = ContractExtraService::checkParamsValid($upContractItems, $params['buy_type'], $params['buy_category']);
                if ($paramsCheckRes !== true) {
                    $this->returnError('上游条款中' . $paramsCheckRes);
                }
            }

            if(is_array($downContractItems) && count($downContractItems)>0){
                $paramsCheckRes = ContractExtraService::checkParamsValid($downContractItems, $params['sell_type'], $params['sell_category']);
                if ($paramsCheckRes !== true) {
                    $this->returnError('下游条款中' . $paramsCheckRes);
                }
            }

            if(is_array($contractItems) && count($contractItems)>0){
                $paramsCheckRes = ContractExtraService::checkParamsValid($contractItems, $params['contract_type'], $params['contract_category']);
                if ($paramsCheckRes !== true) {
                    $this->returnError('合同条款中' . $paramsCheckRes);
                }
            }

        }

        if (!empty($params['buy_contract_id'])) {
            $buyContract = Contract::model()->findByPk($params['buy_contract_id']);
        }

        if (empty($buyContract->contract_id)) {
            $buyContract = new Contract();
            $buyContract->status_time = Utility::getDateTime();
        } else {
            if (!$this->checkIsCanEdit($buyContract->status)) {
                $this->returnError(BusinessError::outputError(OilError::$CONTRACT_NOT_OPERATION));
            }
        }

        if (!empty($params['sell_contract_id'])) {
            $sellContract = Contract::model()->findByPk($params['sell_contract_id']);
        }
        if (empty($sellContract->contract_id)) {
            $sellContract = new Contract();
            $sellContract->status_time = Utility::getDateTime();
        } else {
            if (!$this->checkIsCanEdit($sellContract->status)) {
                $this->returnError(BusinessError::outputError(OilError::$CONTRACT_NOT_OPERATION));
            }
        }


        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {
            //项目合同信息保存
            unset($params["buy_contract_id"]);
            unset($params["sell_contract_id"]);
            if($bMark==1){
                $buyContract->project_id = $params['project_id'];
                if(!$params['is_temp_save'])
                    $buyContract->status = Contract::STATUS_SAVED;
                $buyContract->corporation_id = $params['corporation_id'];
                $buyContract->is_main = ConstantMap::CONTRACT_MAIN;
                $buyContract->type = $params['buy_type'];
                $buyContract->category = $params['buy_category'];
                $buyContract->partner_id = $params['up_partner_id'];
                $buyContract->price_type = $params['buy_price_type'];
                $buyContract->manager_user_id = $params['buy_manager_user_id'];
                $buyContract->currency = $params['purchase_currency'];
                $buyContract->exchange_rate = $params['buy_exchange_rate'];
                $buyContract->formula = $params['buy_formula'];
                $buyContract->agent_id = $params['agent_id'];
                $buyContract->agent_type = $params['agent_type'];
                $buyContract->amount = $params['buy_amount'];
                $buyContract->amount_cny = $params['buy_amount_cny'];
                $buyContract->delivery_term = empty($params['up_delivery_term'])?null:$params['up_delivery_term'];
                $buyContract->days = $params['up_days'];
                $buyContract->delivery_mode = $params['up_delivery_mode'];

                $buyLogRemark = ActionLog::getEditRemark($buyContract->isNewRecord, "商务确认信息");
                $buyContract->save();

                //上游合同补充信息保存
                $buyContractExtra = ContractExtra::model()->find('contract_id = :contractId and project_id = :projectId', array('contractId' => $buyContract->contract_id, 'projectId' => $params['project_id']));
                if (empty($buyContractExtra->contract_id)) {
                    $buyContractExtra = new ContractExtra();
                    $buyContractExtra->contract_id = $buyContract->contract_id;
                    $buyContractExtra->project_id = $params['project_id'];
                    $buyContractExtra->status = 0;
                }

                if(count($upContractItems)>0){
                    $buyContractExtra->content = json_encode($upContractItems);
                }else if(count($contractItems)>0){
                    $buyContractExtra->content = json_encode($contractItems);
                }
                $buyContractExtra->save();
                //商品交易明细&代理费保存
                ContractGoodsService::saveContractGoodsAndAgentFee($buyItems, $buyContract->contract_id, $params['is_temp_save']);

                //付款计划保存
                if (Utility::isNotEmpty($paymentItems)) {
                    PaymentPlanService::savePaymentPlanItems($paymentItems, $params['project_id'], $buyContract->contract_id);
                }
            }

            if($sMark==1){
                $sellContract->project_id = $params['project_id'];
                if(!$params['is_temp_save'])
                    $sellContract->status = Contract::STATUS_SAVED;
                $sellContract->corporation_id = $params['corporation_id'];
                $sellContract->is_main = ConstantMap::CONTRACT_MAIN;
                $sellContract->type = $params['sell_type'];
                $sellContract->category = $params['sell_category'];
                $sellContract->partner_id = $params['down_partner_id'];
                $sellContract->price_type = $params['sell_price_type'];
                $sellContract->manager_user_id = $params['sell_manager_user_id'];
                $sellContract->currency = $params['sell_currency'];
                $sellContract->exchange_rate = $params['sell_exchange_rate'];
                $sellContract->formula = $params['sell_formula'];
                $sellContract->agent_id = $params['agent_id'];
                $sellContract->agent_type = $params['agent_type'];
                $sellContract->amount = $params['sell_amount'];
                $sellContract->amount_cny = $params['sell_amount_cny'];

                $sellContract->delivery_term = empty($params['down_delivery_term'])?null:$params['down_delivery_term'];
                $sellContract->days = $params['down_days'];
                $sellContract->delivery_mode = $params['down_delivery_mode'];

                $sellLogRemark = ActionLog::getEditRemark($sellContract->isNewRecord, "商务确认信息");
                $sellContract->save();

                $buyContract->relative=$sellContract;

                //下游合同补充信息保存
                $sellContractExtra = ContractExtra::model()->find('contract_id = :contractId and project_id = :projectId', array('contractId' => $sellContract->contract_id, 'projectId' => $params['project_id']));
                if (empty($sellContractExtra->contract_id)) {
                    $sellContractExtra = new ContractExtra();
                    $sellContractExtra->contract_id = $sellContract->contract_id;
                    $sellContractExtra->project_id = $params['project_id'];
                    $sellContractExtra->status = 0;
                }

                if(count($downContractItems)>0){
                    $sellContractExtra->content = json_encode($downContractItems);
                }else if(count($contractItems)>0){
                    $sellContractExtra->content = json_encode($contractItems);
                }
                $sellContractExtra->save();

                //商品交易明细&代理费保存
                ContractGoodsService::saveContractGoodsAndAgentFee($sellItems, $sellContract->contract_id, $params['is_temp_save']);

                //收款计划保存
                if (Utility::isNotEmpty($proceedItems)) {
                    PaymentPlanService::savePaymentPlanItems($proceedItems, $params['project_id'], $sellContract->contract_id);
                }
            }

            if(!empty($buyContract->contract_id)) {
                $buy_contract_id = $buyContract->contract_id;
                ContractService::generateContractGroup($buyContract);
            }
            else {
                $sell_contract_id = $sellContract->contract_id;
                ContractService::generateContractGroup($sellContract);
            }

            if (in_array($params['project_type'], array_merge(ConstantMap::$buy_select_contract_type, ConstantMap::$buy_static_contract_type))){ //双边项目，更新合同表关联合同id
                if (!ContractService::updateRelationContractId($params['project_id'])) {
                    BusinessException::throw_exception(OilError::$UPDATE_RELATION_CONTRACT_ID_ERROR);
                }
            }

            /*if($flag==1){
                //TaskService::addTasks(Action::ACTION_10, $contract_id, ActionService::getActionRoleIds(Action::ACTION_10), 0, $obj->corporation_id, array('project_id'=>$params['project_id'],'contract_id'=>$contract_id));
                //TaskService::doneTask($params['project_id'], Action::ACTION_10);
            }*/

            $trans->commit();

            if ($sMark == 1) {
                Utility::addActionLog(json_encode($sellContract->oldAttributes), $sellLogRemark, "Contract", $sellContract->contract_id);
            }

            if ($bMark == 1) {
                Utility::addActionLog(json_encode($buyContract->oldAttributes), $buyLogRemark, "Contract", $buyContract->contract_id);
            }

            $this->returnSuccess(['buy_contract_id'=>$buy_contract_id,'sell_contract_id'=>$sell_contract_id], $params['is_temp_save']);
        } catch (Exception $e) {
            try {
                $trans->rollback();
            } catch (Exception $ee) {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $ee->getMessage(), CLogger::LEVEL_ERROR);
            }

            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);

            $this->returnError(BusinessError::outputError(OilError::$CONTRACT_SAVE_ADD_ERROR, array('reason' => $e->getMessage())));
        }
    }

    public function actionSubmit()
    {
        $id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($id) || $id <= 0)
        {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        try {
            $contractEntity = \ddd\infrastructure\DIService::getRepository(\ddd\domain\iRepository\contract\IContractRepository::class)->findByPk($id);
            if (empty($contractEntity->contract_id))
            {
                throw new \ddd\infrastructure\error\ZEntityNotExistsException($id, \ddd\domain\entity\contract\Contract::class);
            }

            $res = \ddd\application\contract\ContractService::service()->submitContract($id, $contractEntity);
            if ($res !== true) {
                throw new Exception($res);
            }

            $this->returnSuccess();
        } catch (Exception $e)
        {
            $this->returnError(BusinessError::outputError(OilError::$OPERATE_FAILED, array('reason' => $e->getMessage())));
        }
    }

    public function actionSaveBack()
    {
        $params = Mod::app()->request->getParam('data');
        if (!Utility::checkQueryId($params["id"]))
        {
            $this->returnError("参数错误！");
        }

        $project=Project::model()->findByPk($params["id"]);
        if(empty($project))
            $this->returnError("项目信息不存在！");
        if($project->is_can_back!=1)
            $this->returnError("当前项目不允许驳回！");

        $remark=Utility::filterInject($params["remark"]);

        $trans=Utility::beginTransaction();
        try{

            $project->status=Project::STATUS_BACK;
            $project->status_time=new CDbExpression("now()");
            $project->update_user_id=Utility::getNowUserId();
            $project->update_time=new CDbExpression("now()");
            $project->update(array("status","status_time","update_user_id","update_time"));

            $obj=new ProjectBackLog();
            $obj->project_id=$project->project_id;
            $obj->remark=$remark;
            $obj->create_user_id=Utility::getNowUserId();
            $obj->create_time=new CDbExpression("now()");
            $obj->save();

            TaskService::addTasks(Action::ACTION_PROJECT_BACK,$project->project_id,array(
                "corpId"=>$project->corporation_id,
                "userIds"=>$project->create_user_id,
                "projectCode"=>$project->project_code,
                "projectId"=>$project->project_id,
            ));
            TaskService::doneTask($project->project_id, Action::ACTION_10);
            $trans->commit();
            $this->returnSuccess();
        }
        catch (Exception $e)
        {
            try {
                $trans->rollback();
            } catch (Exception $ee) {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $ee->getMessage(), CLogger::LEVEL_ERROR);
            }

            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);
            $this->returnError("保存失败：".$e->getMessage());
        }




    }
}