<?php

/**
 * Desc: 交易主体利润分配
 * User: susiehuang
 * Date: 2017/11/27 0024
 * Time: 10:01
 */
class CorporationProfitController extends ProfitController {
    public $prefix = "projectprofit_";

    public function pageInit() {
        parent::pageInit();
        $this->filterActions = "";
        $this->rightCode = "corporationprofit_";
        $this->category = ProjectProfit::CATEGORY_CORPORATION;
        $attr = $_REQUEST["search"];
        $type = $attr["type"];
        $this->treeCode = $this->prefix . $type;
    }
}