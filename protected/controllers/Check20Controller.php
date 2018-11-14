<?php

/**
 * Desc: 出库单审核
 * User: phpdragon
 * Date: 2018/03/13 16:44
 * Time: 16:44
 */
class Check20Controller extends BaseCheckController {

    public $prefix="check20_";
    public $checkPageTitle = "出库单审核";
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
        $this->businessId = FlowService::BUSINESS_STOCK_OUT_CHECK;
        $this->checkButtonStatus["reject"] = 0;
        $this->detailPartialFile="/stockOut/partial/stockOutOrderInfo";
        $this->detailPartialModelName="stockOutOrder";
        $this->indexViewName="/check20/index";
        $this->checkViewName="/check20/check";
        $this->detailViewName="/check20/detail";
    }

    public function getMainSql($search){
        $sql = 'select {col} from t_check_detail a
                LEFT JOIN t_stock_out_order soo ON a.obj_id = soo.out_order_id 
                left join t_delivery_order do on do.order_id = soo.order_id
                left join t_partner p on p.partner_id = do.partner_id 
                left join t_storehouse s on s.store_id = soo.store_id 
                LEFT JOIN t_contract c ON c.contract_id = do.contract_id 
                left join t_check_item ci on ci.check_id = a.check_id and ci.node_id > 0'
            . $this->getWhereSql($search)
            . " and a.business_id = " . $this->businessId
            . ' and '.AuthorizeService::getUserDataConditionString('soo')
            . " and (a.role_id = " . $this->nowUserRoleId . " or a.check_user_id=" . $this->nowUserId . ")";
        return $sql;
    }

    public function getFields(){
        $fields = array(
            'a.detail_id,a.check_id,a.obj_id,a.status,a.check_status, soo.out_order_id, soo.order_id, soo.type, soo.code, soo.out_date',
            'do.partner_id, do.order_id AS delivery_order_id,do.code AS delivery_code',
            'p.name as partner_name,s.name as store_name,c.contract_id,c.contract_code'
        );

        return implode(",", $fields);
    }

    /**
     * 获取被审核对象模型,视图数据
     * @param $objId
     */
    function getCheckObjectModel($objId){
        return StockOutOrder::model()->with('details','deliveryOrder')->findByPk($objId);
    }

    /**
     * 检查是否已经撤回、或其他逻辑，子类覆盖实现即可
     * @param $objId
     * @param string $backUrl
     */
    protected function checkObjectStatus($objId, $backUrl = '') {
        $model = StockOutOrder::model()->findByPk($objId);

        if (StockOutOrder::STATUS_REVOCATION == $model['status']){
            $this->renderError('该条出库单已被发起人撤回', $backUrl);
        }

        if (StockOutOrder::STATUS_BACK == $model['status']){
            $this->renderError('该条出库单已被驳回',$backUrl);
        }

        if (StockOutOrder::STATUS_SUBMITED == $model['status']){
            $this->renderError('该条出库单已被审核',$backUrl);
        }
    }
}