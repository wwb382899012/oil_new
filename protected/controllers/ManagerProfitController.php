<?php

/**
 * Desc: 项目负责人利润分配
 * User: susiehuang
 * Date: 2017/11/27 0024
 * Time: 10:01
 */
class ManagerProfitController extends ProfitController {
    public $prefix = "projectprofit_";

    public function pageInit() {
        parent::pageInit();
        $this->filterActions = "";
        $this->rightCode = "managerprofit_";
        $this->category = ProjectProfit::CATEGORY_PROJECT_LEADER;
        $attr = $_REQUEST["search"];
        $type = $attr["type"];
        $this->treeCode = $this->prefix . $type;
    }
}