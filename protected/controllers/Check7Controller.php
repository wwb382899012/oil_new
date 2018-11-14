<?php

/**
 * Desc: 入库单审核
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
class Check7Controller extends BaseCheckController {

    public $prefix="check7_";
    public $checkPageTitle = "入库单审核";
    public $checkedStatement = "当前信息已审核";

    public function initRightCode(){
//        $attr = $_REQUEST["search"];
        $attr = $this->getSearch();
        $checkStatus = $attr["checkStatus"];
        $this->treeCode = $this->prefix.$checkStatus;
        $this->rightCode = $this->prefix;
        $this->newUIPrefix="new_";
    }

    public function pageInit(){
        parent::pageInit();
        $this->businessId = FlowService::BUSINESS_STOCK_IN_CHECK;
        $this->checkButtonStatus["reject"] = 0;
        $this->detailPartialFile="/stockIn/partial/stockInInfo";
        $this->detailPartialModelName="stockIn";
        $this->indexViewName="/check7/index";
        $this->checkViewName="/check7/check";
        $this->detailViewName="/check7/detail";
    }

    public function getMainSql($search){
        $sql = "select {col} from t_check_detail a
                left join t_stock_in t on a.obj_id = t.stock_in_id
                left join t_contract c on c.contract_id = t.contract_id 
                left join t_partner p on p.partner_id = c.partner_id 
                left join t_storehouse s on s.store_id = t.store_id 
                left join t_check_item ci on ci.check_id = a.check_id and ci.node_id > 0 
                LEFT JOIN t_contract_file f ON f.contract_id = c.contract_id AND f.is_main = 1 AND f.type = 1"
            . $this->getWhereSql($search)
            . " and a.business_id = ". $this->businessId
            . ' and '.AuthorizeService::getUserDataConditionString('c')
            . " and (a.role_id = " . $this->nowUserRoleId . " or a.check_user_id=" . $this->nowUserId . ")";

        return $sql;
    }

    public function getFields(){
        $fields = array(
            "a.detail_id,a.obj_id,a.status,a.check_status",
            "t.batch_id,t.code,t.store_id,t.entry_date,t.contract_id",
            "f.code_out,c.contract_code,c.partner_id,p.name as partner_name",
            "s.name as store_name"
        );

        return implode(",", $fields);
    }

    /**
     * 获取被审核对象模型,视图数据
     * @param $objId
     */
    function getCheckObjectModel($objId){
        return StockIn::model()->with('details', 'details.sub')->findByPk($objId);
    }

    /**
     * 检查是否已经撤回、或其他逻辑
     * @param $objId
     * @param string $backUrl
     */
    protected function checkObjectStatus($objId, $backUrl = '') {
        $model = StockIn::model()->findByPk($objId);

        if (StockIn::STATUS_REVOCATION == $model['status']){
            $this->renderError('该条入库单已被发起人撤回', $backUrl);
        }

        if (StockIn::STATUS_BACK == $model['status']){
            $this->renderError('该条入库单已被驳回',$backUrl);
        }

        if (StockIn::STATUS_PASS == $model['status']){
            $this->renderError('该条入库单已被审核',$backUrl);
        }
    }
}