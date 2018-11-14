<?php

/**
 * Desc: 采购合同库存报表
 * User: wwb
 * Date: 2018/6/14 0022
 * Time: 16:42
 */

class ContractStockController extends Controller
{
    const BUSINESS_ID_CHECK3 = 3; //业务审核
    const STORE_ID_ONE =1; //虚拟库
    public function pageInit()
    {
        $this->rightCode = 'ContractStock';
        $this->filterActions = "index,add,export";
        $this->newUIPrefix = 'new_';
    }

    public function actionIndex()
    {
        $params = Mod::app()->request->getParam('search');
        $_GET['search']['b.name*']=$params['b.name*']= empty($_GET['name'])?$params['b.name*']:$_GET['name'];
        $params['c.goods_id'] = $_GET['goods_id'];
        if(!empty($_GET['goods_id'])){
            $goods=Goods::model()->findByPk($_GET['goods_id']);
            $_GET['search']['c.name*']=$goods['name'];
        }

        $sql = 'select {col} from t_contract_goods_stock a
                left join t_corporation b on a.corporation_id = b.corporation_id
                left join t_goods c on c.goods_id=a.goods_id
                left join t_contract d on d.contract_id=a.contract_id
                left join t_project e on e.project_id= d.project_id' . $this->getWhereSql($params) .' and '. AuthorizeService::getUserDataConditionString('b') . ' order by a.orderby_time desc,a.goods_update_time desc {limit}';

        $fields = 'a.*,b.name as corporation_name,c.name goods_name,d.contract_code,d.project_id,e.project_code';
        $data = $this->queryTablesByPage($sql, $fields);

        $this->render("index", $data);
    }

    public function actionExport()
    {
        $params = Mod::app()->request->getParam('search');

        $fields = "b.name 交易主体,
                   e.project_code 项目编号,
                   d.contract_code 采购合同编号,
                   c.name 品名,
                   concat(a.on_way_quantity,'') 在途货物（吨）,
                   concat(a.stock_quantity,'') 在库库存（吨）,
                   concat(a.not_lading_quantity,'') 已付未提数量（吨）,
                   concat(a.unexecuted_quantity,'') 采购未执行数量（吨）,
                   '-' 代储货物数量（吨）
                  ";

        $sql = 'select ' . $fields . ' from t_contract_goods_stock a
                left join t_corporation b on a.corporation_id = b.corporation_id
                left join t_goods c on c.goods_id=a.goods_id
                left join t_contract d on d.contract_id=a.contract_id
                left join t_project e on e.project_id= d.project_id ' . $this->getWhereSql($params) .' and '. AuthorizeService::getUserDataConditionString('b') . ' order by a.orderby_time desc,a.goods_update_time desc';

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