<?php

/**
 * Desc: 库存盘点审核
 * User: susiehuang
 * Date: 2017/11/13 0013
 * Time: 16:56
 */
class Check18Controller extends BaseCheckController {
    public $prefix = "check18_";

    public function initRightCode() {
        $attr = $_REQUEST["search"];
        $checkStatus = $attr["checkStatus"];
        $this->treeCode = $this->prefix . $checkStatus;
        $this->rightCode = $this->prefix;
    }

    public function pageInit() {
        parent::pageInit();
        $this->businessId = FlowService::BUSINESS_STOCK_INVENTORY;
        $this->checkButtonStatus["reject"] = 0;
        $this->detailPartialFile = "/stockInventory/detailBody";
        $this->detailPartialModelName = "stockInventory";
        $this->indexViewName = "/check18/index";
    }

    public function getMainSql($search) {
        $sql = "select {col} from t_check_detail a 
                left join t_stock_inventory b on a.obj_id = b.inventory_id
                left join t_stock_inventory_goods_detail c on c.inventory_id = b.inventory_id 
                left join t_corporation d on d.corporation_id = b.corporation_id 
                left join t_storehouse e on e.store_id = b.store_id
                left join t_goods f on f.goods_id = c.goods_id 
                left join t_system_user s on s.user_id = b.create_user_id 
                left join t_check_item ci on ci.check_id = a.check_id and ci.node_id > 0 
                left join t_flow_node n on n.node_id=ci.node_id
                " . $this->getWhereSql($search) . " and a.business_id = " . $this->businessId . "
                and (a.role_id = " . $this->nowUserRoleId . " or a.check_user_id=" . $this->nowUserId . ")";

        return $sql;
    }

    public function getFields() {
        $fields = "a.detail_id,a.obj_id,a.status,a.check_status,b.inventory_id,b.status as obj_status,b.corporation_id,b.store_id,b.inventory_date,c.goods_id,c.unit,c.quantity_active,
                   c.quantity_frozen,c.quantity_before,c.quantity_diff,c.quantity,d.name as corp_name,e.name as store_name,f.name as goods_name,s.name as create_name,c.goods_detail_id";

        return $fields;
    }


    public function getCheckObjectModel($objId) {
        return StockInventory::model()->findByPk($objId);
    }

}