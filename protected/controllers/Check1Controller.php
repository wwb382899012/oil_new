<?php
class Check1Controller extends CheckController
{
    public $businessId = 1;
    public function pageInit() {
        parent::pageInit();
        $this->businessId=1;
        $this->rightCode="check1";
        $this->mainUrl = "/check1/";
        $this->checkViewName = "/check1/check";
    }

    public function actionIndex() {
        // 获取仓库数据
        $search = $_GET['search'];
        $sql="select {col} from t_check_detail cd " . 
            "join t_check_item ci on ci.check_id = cd.check_id " . 
            "left join t_storehouse sh on sh.store_id=cd.obj_id and cd.business_id={$this->businessId} " 
            .$this->getWhereSql($search)
            ." and ci.node_id >=0 and sh.status in (10, -1) order by sh.store_id desc";
        $column_str = " sh.*, cd.detail_id, cd.check_id ";
        $data = $this->queryTablesByPage($sql,$column_str);
        $this->render('index', array('data'=>$data));
    }

    public function getCheckData($id)
    {
        $data=Utility::query("
              select a.*, c.detail_id as detail_id, b.store_id as store_id
              from t_check_item a
                left join t_storehouse b on a.obj_id=b.store_id
                left join t_check_detail c on c.check_id = a.check_id
                where c.check_status = 0 and a.business_id=".$this->businessId." and a.obj_id=".$id);
        return $data;
    }

}