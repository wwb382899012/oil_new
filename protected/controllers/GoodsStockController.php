<?php

/**
 * Desc: 商品库存查询
 * User: susiehuang
 * Date: 2017/11/13 0013
 * Time: 16:56
 */
class GoodsStockController extends Controller {
    public function pageInit() {
        $this->filterActions = '';
        $this->rightCode = 'goodsStock';
    }

    public function actionIndex() {
        $attr = Mod::app()->request->getParam('search');
        $fields = 'p.corporation_id,a.goods_id,a.unit,sum(a.quantity_balance) AS total_quantity_balance,sum(a.quantity_frozen) AS total_quantity_frozen,sum(a.quantity_balance)+sum(a.quantity_frozen) as total_stock_quantity,c.name as corp_name,g.name as goods_name';
        $user = SystemUser::getUser(Utility::getNowUserId());
        /*$sql = 'select {col} from t_stock a
                left join t_contract p on p.contract_id = a.contract_id
                left join t_corporation c on c.corporation_id = p.corporation_id
                left join t_goods g on g.goods_id = a.goods_id ' . $this->getWhereSql($attr) . '
                and c.corporation_id in (' . $user['corp_ids'] . ') group by p.corporation_id, a.goods_id, a.unit having sum(a.quantity_balance) > 0 or sum(a.quantity_frozen) > 0 {limit}';*/
        $sql1 = 'select ' . $fields . ' from t_stock a 
                 left join t_contract p on p.contract_id = a.contract_id
                 left join t_corporation c on c.corporation_id = p.corporation_id 
                 left join t_goods g on g.goods_id = a.goods_id ' . $this->getWhereSql($attr) . ' 
                 and c.corporation_id in (' . $user['corp_ids'] . ') group by p.corporation_id, a.goods_id, a.unit having sum(a.quantity_balance) > 0 or sum(a.quantity_frozen) > 0';
        $sql = 'select {col} from (' . $sql1 . ') as gs where 1=1 {limit}';

        $data = $this->queryTablesByPage($sql, '*');
        if (Utility::isNotEmpty($data['rows'])) {
            $data['search'] = $attr;
        }
        $this->pageTitle = '商品库存查询';
        $this->render('index', $data);
    }

    public function actionDetail() {
        $corp_id = Mod::app()->request->getParam('corp_id');
        $goods_id = Mod::app()->request->getParam('goods_id');
        $unit = Mod::app()->request->getParam('unit');
        if (!Utility::checkQueryId($corp_id) || !Utility::checkQueryId($goods_id) || !Utility::checkQueryId($unit)) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $data['corporation_id'] = $corp_id;
        $data['goods_id'] = $goods_id;
        $data['unit'] = $unit;
        $params = array('corporationId' => $corp_id, 'goodsId' => $goods_id, 'unit' => $unit);
        $data['details'] = StockService::getStockDetail($params);
        $this->render('detail', array('data' => $data));
    }
}