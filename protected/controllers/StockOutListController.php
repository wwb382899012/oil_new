<?php
class StockOutListController extends AttachmentController {
    public function pageInit() {
        $this->filterActions = "index,view";
        $this->rightCode = "stockOutList";
        $this->newUIPrefix="new_";
    }

    public function actionIndex() {
        $this->pageTitle = '出库单列表';

        $user = Utility::getNowUser();
        if(empty($user['corp_ids'])) {
            return [];
        }

        $attr = $this->getSearch();
        $where_sql = $this->getWhereSql($attr);
        $sub_where_sql = AuthorizeService::getUserDataConditionString('soo');
        $sql = <<<SQL
SELECT DISTINCT {col} FROM t_stock_out_order soo 
LEFT JOIN t_stock_out_detail sod ON sod.out_order_id = soo.out_order_id 
LEFT JOIN t_delivery_order do ON do.order_id = soo.order_id 
LEFT JOIN t_partner p ON p.partner_id = do.partner_id 
LEFT JOIN t_storehouse s ON s.store_id = soo.store_id 
LEFT JOIN t_contract c ON c.contract_id = do.contract_id 
$where_sql AND sod.quantity > 0 AND $sub_where_sql
ORDER BY soo.out_order_id desc {limit}
SQL;

        $fields = array(
            'soo.out_order_id, soo.order_id, soo.type, soo.code, soo.out_date, soo.status,do.partner_id, soo.is_virtual, soo.is_virtual',
            'p.name as partner_name,s.name as store_name,c.contract_id,c.contract_code'
        );
        $data = $this->queryTablesByPage($sql, implode(',',$fields));

        $this->render('/stockOut/list', $data);
    }

    public function actionView() {
        $out_order_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($out_order_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $stockOutOrder = StockOutOrder::model()->with('store', 'details', 'originalOrder', 'details.stock', 'details.contract', 'details.stockDeliveryDetail', 'details.contract.partner', 'details.goods')->findByPk($out_order_id);
        if (empty($stockOutOrder->out_order_id)) {
            $this->renderError(BusinessError::outputError(OilError::$STOCK_IN_NOT_EXIST, array('out_order_id' => $out_order_id)));
        }

        $this->pageTitle = '出库单明细';
        $this->render('/stockOut/view', array('stockOutOrder' => $stockOutOrder));
    }

    /**
     * 撤销
     */
    public function actionRevocation(){
        $id = Mod::app()->request->getParam("out_order_id");

        if(!Utility::checkQueryId($id)){
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $stockOutOrderModel = StockOutService::getInstance()->getCanRevocationStockOutOrder($id);
        if(empty($stockOutOrderModel) || !StockOutService::isCanRevocation($stockOutOrderModel['status'])){
            $this->returnError(BusinessError::outputError(OilError::$STOCK_OUT_NOT_ALLOW_REVOCATION));
        }

        if(StockOutService::revocationStockOutBill($stockOutOrderModel)){
            $this->returnSuccess();
        }else{
            $this->returnError("操作失败");
        }
    }

    /**
     * 作废
     */
    public function actionInvalid(){
        $id = Mod::app()->request->getParam("out_order_id");
        $remark = Mod::app()->request->getParam('remark');

        if(!Utility::checkQueryId($id)){
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $stockOutOrderModel = StockOutService::getInstance()->getCanInvalidStockOutOrder($id);
        if(empty($stockOutOrderModel) || !StockOutService::isCanInvalid($stockOutOrderModel['status'])){
            $this->returnError(BusinessError::outputError(OilError::$STOCK_OUT_NOT_ALLOW_INVALID));
        }

        if(StockOutService::invalidStockOutBill($stockOutOrderModel,$remark)){
            $this->returnSuccess();
        }else{
            $this->returnError("操作失败");
        }
    }
}