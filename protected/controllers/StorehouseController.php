<?php
class StorehouseController extends AttachmentController
{
    public function pageInit() {
        $this->rightCode="storehouse";
        $this->filterActions = "ajaxCheckName";
    }

    public function actionIndex() {
		$search = $_GET['search'];
        $sql="select {col} from t_storehouse sh " 
            .$this->getWhereSql($search)
            ." order by sh.store_id desc";
        $column_str = "sh.*";
        $data = $this->queryTablesByPage($sql,$column_str);

		$this->render('index', array('data'=>$data));
    }

    public function actionAdd() {
    	$this->render('edit', array('storehouse'=>array()));
    }

    public function actionEdit() {
    	$store_id = $_GET['store_id'];
        if(!Utility::checkQueryId($store_id))
            $this->renderError(BusinessError::outputError(OilError::$STOREHOUSE_PARAMS_ERROR),"/storehouse/");
    	$store_id = $_GET['store_id'];
    	$storehouse = Storehouse::model()->findByPk($store_id);
        if(empty($storehouse))
            $this->renderError(BusinessError::outputError(OilError::$STOREHOUSE_PARAMS_ERROR),"/storehouse/");
    	if(StorehouseService::editable($storehouse->getAttributes(), $this->getUser())) {
    		$this->render('edit', array('storehouse'=>$storehouse->getAttributes()));
    	} else {
    		$this->renderError(BusinessError::outputError(OilError::$STOREHOUSE_EDITABLE_ERROR),"/storehouse/");
    	}
    }

    public function actionSave() {
    	$storehouse = $_POST['obj'];
    	$store_id = $storehouse['store_id'];
    	$store_name = $storehouse['name'];
		unset($storehouse['store_id']);
    	$checkName = $this->checkName($store_name, $store_id);
    	// 判断是否重名
    	if($checkName) {
    		$this->returnError(BusinessError::outputError(OilError::$STOREHOUSE_NAME_ERROR));
    	} 
		if(!empty($store_id) && Utility::checkQueryId($store_id)) {
			$storehouseModel = Storehouse::model()->findByPk($store_id);
		}
		$storehouseModel = empty($storehouseModel)?new Storehouse():$storehouseModel;
		// 判断是否可以编辑
    	if(!empty($storehouseModel->store_id) && !StorehouseService::editable($storehouseModel->getAttributes(), $this->getUser())) {
    		$this->returnError(BusinessError::outputError(OilError::$STOREHOUSE_EDITABLE_ERROR));
    	} 

        $user=$this->getUser();
    	$storehouseModel->setAttributes($storehouse);
        $isNew = 0;
    	if(empty($storehouseModel->store_id)) {
            $isNew = 1;
	        $storehouseModel->create_user_id=$user["user_id"];
	        $storehouseModel->create_time=date('Y-m-d H:i:s');
    	}
        $storehouseModel->update_user_id=$user["user_id"];
        $storehouseModel->update_time=date('Y-m-d H:i:s');
        $logRemark = ActionLog::getEditRemark($storehouseModel->isNewRecord, "仓库");
        $res=$storehouseModel->save();
        if($res===true){
            if($isNew==1)
                TaskService::addPartnerTasks(Action::ACTION_8, $storehouseModel->store_id, 0, $storehouseModel->create_user_id, array('name'=>$storehouseModel->name));
            Utility::addActionLog(json_encode($storehouseModel->oldAttributes), $logRemark, "StoreHouse", $storehouseModel->store_id);
            $this->returnSuccess($storehouseModel->store_id);
        } else {
            $this->returnError(BusinessError::outputError(OilError::$STOREHOUSE_SAVE_ERROR).$res);
        }
    }

    public function actionDetail() {
    	$store_id = $_GET['store_id'];
        if(!Utility::checkQueryId($store_id))
            $this->renderError(BusinessError::outputError(OilError::$STOREHOUSE_PARAMS_ERROR),"/storehouse/");
    	$storehouse = Storehouse::model()->findByPk($store_id);
        if(empty($storehouse))
            $this->renderError(BusinessError::outputError(OilError::$STOREHOUSE_PARAMS_ERROR),"/storehouse/");
        $user=$this->getUser();
    	$editable = StorehouseService::editable($storehouse->getAttributes(), $this->getUser());
    	$this->render('detail', array('storehouse'=>$storehouse->getAttributes(), 'editable'=>$editable));
    }

    public function actionSubmit() {
    	$store_id = $_POST['store_id'];
        if(!Utility::checkQueryId($store_id))
            $this->renderError(BusinessError::outputError(OilError::$STOREHOUSE_PARAMS_ERROR),"/storehouse/");
    	$storehouse = Storehouse::model()->findByPk($store_id);
    	if(!StorehouseService::editable($storehouse->getAttributes(), $this->getUser())) {
    		$this->returnError(BusinessError::outputError(OilError::$STOREHOUSE_EDITABLE_ERROR));
    	}
    	$oldStatus = $storehouse->status;
        $trans = Utility::beginTransaction();
        try{
            // 仅靠用户角色来判断是否能够审核
            FlowService::startFlowForCheck1($storehouse->store_id);
            
            //TaskService::addPartnerTasks(Action::ACTION_7, $storehouse->store_id, ActionService::getActionRoleIds(Action::ACTION_7));
            TaskService::doneTask($storehouse->store_id, Action::ACTION_8);
            // 仓库驳回donetask28
            TaskService::doneTask($storehouse->store_id, Action::ACTION_28);
            $trans->commit();

            Utility::addActionLog(json_encode(array('oldStatus'=>$oldStatus)), "提交仓库信息", "StoreHouse", $storehouse->store_id);
            $this->returnSuccess('提交成功');
        }catch(Exception $e){
            try{ $trans->rollback(); }catch(Exception $ee){}

            $this->returnError("操作失败！".$e->getMessage());
        }
    	
    }

    public function actionAjaxCheckName() {
    	$store_name = $_POST['name'];
    	$store_id = $_POST['store_id'];
    	$checkName = $this->checkName($store_name, $store_id);
    	if($checkName) {
    		$this->returnError(BusinessError::outputError(OilError::$STOREHOUSE_NAME_ERROR));
    	} else {
    		$this->returnSuccess();
    	}
    }

    private function checkName($store_name, $store_id = null) {
    	$store = Storehouse::model()->find(
    		array(
    			'condition'=>'name=:name', 
    			'params'=>array(
    					'name'=>trim($store_name)
    				), 
    			'select'=>'store_id'
    			)
    		);
    	$checkName = (empty($store_id) && !empty($store)) || (!empty($store_id) && !empty($store) && $store['store_id'] != $store_id);
    	return $checkName;
    }
}