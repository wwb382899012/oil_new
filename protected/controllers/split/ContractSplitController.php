<?php

class ContractSplitController extends Controller{
    public function init(){
        $this->authorizedActions = array("index");
    }

    public function actionIndex(){
        $this->renderNewWeb();
    }
}