<?php
/**
 * User: liyu
 * Date: 2018/8/1
 * Time: 20:31
 * Desc: Check25Controller.php
 */

class Check25Controller extends Controller{
    public function init() {
        $this->authorizedActions = array("index");
    }

    public function actionIndex() {
        $this->renderNewWeb();
    }
}