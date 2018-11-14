<?php
/**
 * User: liyu
 * Date: 2018/8/1
 * Time: 20:30
 * Desc: ContractTerminateController.php
 */

class ContractTerminateController extends Controller
{
    public function init() {
        $this->authorizedActions = array("index");
    }

    public function actionIndex() {
        $this->renderNewWeb();
    }
}