<?php

/**
 * Desc: 入库单列表
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
class StockInListController extends AttachmentController {
    public function pageInit() {
        $this->filterActions = "";
        $this->rightCode = "stockInList";
        $this->newUIPrefix="new_";
    }

    public function actionIndex() {
        $this->pageTitle = '入库单列表';

        $user = Utility::getNowUser();
        if(empty($user['corp_ids'])) {
            return [];
        }

        $attr = $this->getSearch();
        $where_sql = $this->getWhereSql($attr);
        $sub_where_sql = AuthorizeService::getUserDataConditionString('b');
        $sql = <<<SQL
select DISTINCT {col} from t_stock_in a 
left join t_stock_in_detail sid on sid.stock_in_id = a.stock_in_id 
left join t_contract b on b.contract_id = a.contract_id 
left join t_partner c on c.partner_id = b.partner_id 
left join t_storehouse d on d.store_id = a.store_id 
left join t_stock_in_batch e on e.batch_id = a.batch_id 
left join t_contract_file f on f.contract_id = b.contract_id and f.is_main=1 and f.type=1 
$where_sql AND sid.quantity > 0  AND $sub_where_sql
order by a.stock_in_id desc {limit}
SQL;
        $fields = 'a.stock_in_id, a.contract_id, a.batch_id, a.type, a.code, a.entry_date, a.status, a.is_virtual, a.is_virtual, b.contract_code, b.partner_id, c.name as partner_name, 
                   d.name as store_name, a.batch_id, e.code as stock_batch_code, a.store_id, f.code_out';

        $data = $this->queryTablesByPage($sql, $fields);

        $this->render('/stockIn/list', $data);
    }

    public function actionView() {
        $stock_in_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($stock_in_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $stockInModel = StockIn::model()->with('details', 'originalOrder', 'details.sub')->findByPk($stock_in_id);
        if (empty($stockInModel->stock_in_id)) {
            $this->renderError(BusinessError::outputError(OilError::$STOCK_IN_NOT_EXIST, array('stock_in_id' => $stock_in_id)));
        }

        //入库单库存变化详情
        $stockDetail = StockInService::getStockChangeForStockIn($stockInModel->stock_in_id);

        $this->pageTitle = '入库单明细';
        $this->render('/stockIn/view', array('stockIn' => $stockInModel, 'stockDetail' => $stockDetail));
    }

    /**
     * 撤销
     */
    public function actionRevocation() {
        $stock_in_id = Mod::app()->request->getParam('stock_in_id');

        if (!Utility::checkQueryId($stock_in_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        //查询入库单信息
        $stockInModel = StockInService::getInstance()->getCanRevocationStockInBill($stock_in_id);
        //是否可撤销
        if (empty($stockInModel->stock_in_id) || !StockInService::isCanRevocation($stockInModel['status'])) {
            $this->renderError(BusinessError::outputError(OilError::$STOCK_IN_NOT_ALLOW_REVOCATION));
        }

        if(StockInService::revocationStockInBill($stockInModel)){
            $this->returnSuccess();
        }else{
            $this->returnError("操作失败");
        }
    }

    /**
     * 作废
     */
    public function actionInvalid() {
        $stock_in_id = Mod::app()->request->getParam('stock_in_id');
        $remark = Mod::app()->request->getParam('remark');

        if (!Utility::checkQueryId($stock_in_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        //查询入库单信息
        $stockInModel = StockInService::getInstance()->getCanInvalidStockInBill($stock_in_id);
        //是否可作废
        if (empty($stockInModel->stock_in_id) || !StockInService::isCanInvalid($stockInModel['status'])) {
            $this->renderError(BusinessError::outputError(OilError::$STOCK_IN_NOT_ALLOW_INVALID));
        }

        if(StockInService::invalidStockInBill($stockInModel,$remark)){
            $this->returnSuccess();
        }else{
            $this->returnError("操作失败");
        }
    }
}