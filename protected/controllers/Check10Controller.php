<?php
/**
*   发货单结算审核
*/
class Check10Controller  extends BaseCheckController
{
    public $prefix="check10_";
    public function initRightCode()
    {
//        $attr = $_REQUEST["search"];
        $attr = $this->getSearch();
        $checkStatus = $attr["checkStatus"];
        $this->treeCode = $this->prefix.$checkStatus;
        $this->rightCode = $this->prefix;
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
        $this->newUIPrefix = 'new_';
    }
    public function actionIndex()
    {
        //$this->renderNewWeb();return ;
//        $attr = $_REQUEST['search'];
        $attr = $this->getSearch();

        $checkStatus=1;
        if(!empty($attr["checkStatus"]))
        {
            $checkStatus=$attr["checkStatus"];
            unset($attr["checkStatus"]);
        }
        
        $sql=$this->getMainSql($attr);
        
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
        $attr['checkStatus']=$checkStatus;
        $data["search"]=$attr;
        $data["b"]=$this->businessId;
        
        $this->render("index", $data);
    }

    public function getMainSql($search)
    {
        $sql = "select {col} from t_check_detail a
                left join t_delivery_order b on a.obj_id = b.order_id
                left join t_delivery_settlement s on s.order_id = b.order_id
                left join t_corporation c on c.corporation_id = b.corporation_id 
                left join t_partner p on p.partner_id = b.partner_id 
                left join t_stock_in d on d.stock_in_id = b.stock_in_id 
                left join t_check_item ci on ci.check_id = a.check_id and ci.node_id > 0 
                left join t_contract_file cf on cf.contract_id=b.contract_id and cf.is_main=1 and cf.type=1 
                left join t_contract ct on ct.contract_id = b.contract_id 
                " . $this->getWhereSql($search) . " and a.business_id = " . $this->businessId . "
                and ".AuthorizeService::getUserDataConditionString("b")."
                and (a.role_id = " . $this->nowUserRoleId . " or a.check_user_id=" . $this->nowUserId . ")";
        return $sql;
    }

    public function getFields()
    {
        $fields = "a.detail_id,a.obj_id,a.status,a.check_status,b.order_id,b.code,b.corporation_id,b.partner_id,b.type,ct.contract_code,ct.contract_id,
                   b.stock_in_id,c.name as corporation_name,p.name as partner_name,d.code as stock_in_code,s.settle_date,cf.code_out";
        return $fields;
    }


    public function getCheckObjectModel($objId)
    {
        return DeliveryOrder::model()->with('settlementDetails', 'settlementDetails.sub')->findByPk($objId);
        
        // return PayApplication::model()->with("details","contract","details.payment","extra","factor")->findByPk($objId);
    }
}