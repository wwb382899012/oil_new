<?php

/**
 * Desc: 保理申请单审核
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
class Check14Controller extends CheckController {
    public function pageInit() {
        parent::pageInit();
        $attr = $_REQUEST["search"];
        $checkStatus = $attr["checkStatus"];
        $this->treeCode = "check14_" . $checkStatus;
        $this->filterActions = "";
        $this->businessId = 14;
        $this->rightCode = "check14_";
        $this->checkButtonStatus["back"] = 0;
        $this->mainUrl = "/check14/";
        $this->checkViewName = "/check14/check";
        $this->detailViewName = "/check14/detail";
    }

    public function actionIndex() {
        $attr = $_REQUEST[search];
        $checkStatus = 1;
        if (!empty($attr["checkStatus"])) {
            $checkStatus = $attr["checkStatus"];
            unset($attr["checkStatus"]);
        }

        $sql = "select {col} from t_check_detail a
                left join t_factoring_detail fd on fd.detail_id = a.obj_id 
                left join t_factoring b on b.factor_id = fd.factor_id 
                left join t_pay_application c on c.apply_id = fd.apply_id 
                left join t_corporation d on d.corporation_id = fd.corporation_id 
                left join t_contract e on e.contract_id = fd.contract_id 
                left join t_partner p on p.partner_id = e.partner_id 
                left join t_system_user s on s.user_id = fd.create_user_id 
                left join t_contract_file cf on cf.contract_id = e.contract_id and cf.is_main=1 and cf.type=1
                left join t_check_item ci on ci.check_id = a.check_id and ci.node_id > 0 " . $this->getWhereSql($attr) . " and a.business_id = " . $this->businessId . "
                and (a.role_id = " . $this->nowUserRoleId . " or a.check_user_id=" . $this->nowUserId . ") and b.status >= ".Factor::STATUS_CONFIRMED;

        $fields = "a.detail_id,a.obj_id,fd.contract_code as water_code,fd.status,fd.contract_id,fd.amount,fd.apply_id,e.partner_id,c.corporation_id,b.contract_code,cf.code_out,
                   b.contract_code_fund,c.amount as pay_apply_amount,c.currency,d.name as corp_name,e.contract_code as c_code,p.name as partner_name, s.name as create_name";
        switch ($checkStatus) {
            case 2:
                $sql .= " and a.status=1 and a.check_status=1 ";
                $fields .= ",0 isCanCheck, 2 as checkStatus ";
                break;
            case 3:
                $sql .= " and a.status=1 and a.check_status=0";
                $fields .= ",0 isCanCheck, 3 as checkStatus ";
                break;
            case 4:
                $sql .= " and a.status=1 and a.check_status=-1";
                $fields .= ",0 isCanCheck, 4 as checkStatus ";
                break;
            default:
                $sql .= " and a.status=0";
                $fields .= ",1 isCanCheck, 1 as checkStatus ";
                break;
        }

        $sql .= " and ".AuthorizeService::getUserDataConditionString('fd')." order by a.check_id desc {limit}";
        $user = Utility::getNowUser();
        if(!empty($user['corp_ids'])) {
            $data = $this->queryTablesByPage($sql, $fields);
        }else{
            $data = array();
        }

        $attr["checkStatus"] = $checkStatus;
        $data["search"] = $attr;
        $data["b"] = $this->businessId;
        $this->render('index', $data);
    }

    public function getCheckData($id) {
        $this->checkPageTitle = '保理对接信息审核';

        return Utility::query("select a.* from t_check_item a
                left join t_factoring_detail b on a.obj_id = b.detail_id
                left join t_check_detail c on c.check_id = a.check_id
                where c.check_status = 0 and a.business_id = " . $this->businessId . " and a.obj_id = " . $id);
    }
}