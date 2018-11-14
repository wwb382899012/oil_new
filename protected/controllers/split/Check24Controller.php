<?php

class Check24Controller extends Controller{
    public function init(){
        $this->authorizedActions = array("index");
    }

    public function actionIndex(){
        $this->renderNewWeb();
    }
}