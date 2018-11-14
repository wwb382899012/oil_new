<?php

/**
 * Desc: 发货单审核
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
class Check9Controller extends CheckController {
    public function pageInit() {
        parent::pageInit();
//        $attr = $_REQUEST["search"];
        $attr = $this->getSearch();
        $checkStatus = $attr["checkStatus"];
        $this->treeCode = "check9_" . $checkStatus;
        $this->filterActions = "";
        $this->businessId = 9;
        $this->rightCode = "check9_";
        $this->checkButtonStatus["back"] = 0;
        $this->mainUrl = "/check9/";
        $this->checkViewName = "/check9/check";
        $this->detailViewName = "/check9/detail";
        $this->newUIPrefix="new_";
    }

    public function actionIndex() {
//        $attr = $_REQUEST[search];
        $attr = $this->getSearch();
        $checkStatus = 1;
        if (!empty($attr["checkStatus"])) {
            $checkStatus = $attr["checkStatus"];
            unset($attr["checkStatus"]);
        }

        $sql = "select {col} from t_check_detail a
                left join t_delivery_order b on a.obj_id = b.order_id
                left join t_corporation c on c.corporation_id = b.corporation_id 
                left join t_partner p on p.partner_id = b.partner_id 
                left join t_stock_in d on d.stock_in_id = b.stock_in_id 
                left join t_check_item ci on ci.check_id = a.check_id and ci.node_id > 0 " . $this->getWhereSql($attr) . " and a.business_id = " . $this->businessId . "
                and (a.role_id = " . $this->nowUserRoleId . " or a.check_user_id=" . $this->nowUserId . ")";

        $fields = "a.detail_id,a.obj_id,b.order_id,b.code,b.corporation_id,b.partner_id,b.type,b.status,
                   b.stock_in_id,c.name as corporation_name,p.name as partner_name,d.code as stock_in_code";
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

        $sql .= " and " . AuthorizeService::getUserDataConditionString("b") . " order by a.check_id desc {limit}";
        $user = Utility::getNowUser();
        if (!empty($user['corp_ids'])) {
            $data = $this->queryTablesByPage($sql, $fields);
        } else {
            $data = array();
        }

        $attr["checkStatus"] = $checkStatus;
        $data["search"] = $attr;
        $data["b"] = $this->businessId;
        $this->render('index', $data);
    }

    public function getCheckData($id) {
        $this->checkPageTitle = '发货单审核';

        return Utility::query("select a.* from t_check_item a
                left join t_delivery_order b on a.obj_id = b.order_id
                left join t_check_detail c on c.check_id = a.check_id
                where c.check_status = 0 and a.business_id = " . $this->businessId . " and a.obj_id = " . $id);
    }

    public function getDetailData($detailId) {
        return $data = Utility::query("
              select b.* from t_check_detail a
              left join t_check_log b on b.check_id = a.check_id
              where a.business_id=" . $this->businessId . " and a.detail_id=" . $detailId);
    }
}