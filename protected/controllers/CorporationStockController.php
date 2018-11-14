<?php

/**
 * Desc: 库存报表
 * User: wwb
 * Date: 2018/6/14 0022
 * Time: 16:42
 */


class CorporationStockController extends Controller
{
    const BUSINESS_ID_CHECK3 = 3; //业务审核
    const STORE_ID_ONE ="0,1"; //虚拟库
    public function pageInit()
    {
        $this->rightCode = 'CorporationStock';
        $this->filterActions = "index,add,export";
        $this->newUIPrefix = 'new_';
    }

    public function actionIndex()
    {
        $params = Mod::app()->request->getParam('search');

        $sql = 'select {col} from t_corporation_goods_stock a
                left join t_corporation b on a.corporation_id = b.corporation_id
                left join t_goods c on a.goods_id=c.goods_id' . $this->getWhereSql($params) .' and '. AuthorizeService::getUserDataConditionString('b') . ' order by a.orderby_time desc,a.goods_update_time desc {limit}';

        $fields = 'a.*,b.name as corporation_name,c.name goods_name';
        $data = $this->queryTablesByPage($sql, $fields);

        $this->render("index", $data);
    }

    public function actionExport()
    {
        $params = Mod::app()->request->getParam('search');

        $fields = "b.name 交易主体,
                   c.name 品名,
                   concat(a.on_way_quantity,'') 在途货物（吨）,
                   concat(a.stock_quantity,'') 在库库存（吨）,
                   concat(a.not_lading_quantity,'') 已付未提数量（吨）,
                   concat(a.unexecuted_quantity,'') 采购未执行数量（吨）,
                   '-' 代储货物数量（吨）

                  ";

        $sql = 'select ' . $fields . ' from t_corporation_goods_stock a
                left join t_corporation b on a.corporation_id = b.corporation_id
                left join t_goods c on a.goods_id=c.goods_id ' . $this->getWhereSql($params).' and '. AuthorizeService::getUserDataConditionString('b') . ' order by a.orderby_time desc,a.goods_update_time desc';

        $data = Utility::query($sql);
        $this->exportExcel($data);
    }

    /**
     * 生成数据
     * */
    public function actionAdd(){
        $riskAmount = new ReportCommand(null,null);
        $riskAmount->actionCorporationStock();
    }
}