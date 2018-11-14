<?php
/**
 *   销售合同结算审核
 */
class Check22Controller  extends BaseCheckController
{
    public $prefix="check22_";
    public function initRightCode()
    {
//        $attr = $_REQUEST["search"];
        $attr = $this->getSearch();
        $checkStatus = $attr["checkStatus"];
        $this->treeCode = $this->prefix.$checkStatus;
        $this->rightCode = $this->prefix;
        $this->filterActions = "index,doCheck,save,detail,check";
    }
    
    public function pageInit()
    {
        parent::pageInit();
        $this->businessId =22;
        $this->checkButtonStatus["reject"] = 0;
        $this->detailPartialFile="/common/deliverySettlementList";
        $this->detailPartialModelName="deliveryOrder";
        $this->indexViewName="/check22/index";
        $this->detailViewName="/check22/detail";
        $this->checkViewName="/check22/check";
        $this->newUIPrefix = 'new_';
    }
    
    
    public function actionIndex(){
        //$this->renderNewWeb();return ;
//        $attr = $_REQUEST['search'];
        $attr = $this->getSearch();
        $checkStatus = 1;
        if (!empty($attr["checkStatus"])) {
            $checkStatus = $attr["checkStatus"];
            unset($attr['checkStatus']);
        }
        $query = "";
        $userId = Utility::getNowUserId();
        $roleId = UserService::getNowUserMainRoleId();
        $sql = "select {col}
            from t_check_detail a
            left join t_check_log d on d.detail_id=a.detail_id
            left join t_contract e on a.obj_id = e.contract_id
            left join t_contract_settlement s on s.contract_id = e.contract_id
            left join t_system_user as b on b.user_id = e.manager_user_id
            left join t_project f on e.project_id = f.project_id
            left join t_partner c on c.partner_id = e.partner_id
            left join t_corporation g on g.corporation_id = e.corporation_id
            left join t_contract_file cf on cf.contract_id=e.contract_id and cf.is_main=1 and cf.type=1
            " . $this->getWhereSql($attr);
        $fields = "a.check_id,e.contract_id,a.detail_id,a.obj_id, b.name as manager_user_name,e.contract_code,e.category, f.project_id, f.project_code,
        e.partner_id, c.name as partner_name, f.corporation_id, g.name as corporation_name , cf.code_out,b.name as manager_user_name,
        s.settle_date";
        switch ($checkStatus) {
            case 2:
                // 审核通过
                $sql .= " and a.status=1 and a.check_status=1";
                $fields .= ",0 isCanCheck, " . $checkStatus . " as checkStatus ";
                break;
            case 3:
                // 审核驳回
                $sql .= " and a.status=1 and a.check_status=-1";
                $fields .= ",0 isCanCheck, " . $checkStatus . " as checkStatus ";
                break;
            case 1:
            default:
                // 待审核
                $sql .=  " and a.status=0 and a.check_status=0";
                $fields .= ",1 isCanCheck, " . $checkStatus . " as checkStatus ";
                $checkStatus = 1;
                break;
        }
        
        $sql .= $query . " and " . AuthorizeService::getUserDataConditionString('e') . " and (a.role_id= {$roleId} or a.check_user_id={$userId}) and a.business_id={$this->businessId} order by a.check_id desc {limit}";
        $user = Utility::getNowUser();
        if (!empty($user['corp_ids'])) {
            $data = $this->queryTablesByPage($sql, $fields);
        } else {
            $data = array();
        }
      
        $this->render("index", $data);
    }
   
    
    public function getFields()
    {
        $fields = "a.detail_id,a.obj_id,a.status,a.check_status,b.order_id,b.code,b.corporation_id,b.partner_id,b.type,
                   b.stock_in_id,c.name as corporation_name,p.name as partner_name,d.code as stock_in_code";
        return $fields;
    }
    
    
    public function getCheckObjectModel($objId)
    {
        return Contract::model()->findByPk($objId);
        
        // return PayApplication::model()->with("details","contract","details.payment","extra","factor")->findByPk($objId);
    }
}