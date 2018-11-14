<?php
/**
 * Created by youyi000.
 * DateTime: 2017/12/22 9:49
 * Describe：
 */

class ShowController extends Controller
{
    public $showId="";

    public function pageInit()
    {
        parent::pageInit();
        $this->showId=Mod::app()->request->getParam("id");
        if(empty($this->showId))
        {
            $this->showId=$_GET["search"]["id"];
        }
        $this->rightCode ="show_".$this->showId;
        $this->newUIPrefix = 'new_';
    }

    /**
     * 获取Search查询条件
     * @return mixed
     */
    public function getSearch()
    {
        $search=$_GET["search"];
        $key=$this->id."_".$this->action->getId()."_".$this->showId."_search";
        if(empty($search))
        {
            $search=$_COOKIE[$key];
            if(!empty($search))
                $search=json_decode($search,true);
        }
        else
            setcookie($key, json_encode($search), time()+1500);
        return $search;
    }

    public function actionIndex()
    {
        $search=$this->getSearch();//$_GET["search"];
        if(empty($this->showId))
            $this->showId=$search["id"];
        unset($search["id"]);

        if(empty($this->showId))
            $this->renderError("参数错误");
        $sql=DataShowService::getSearchSql($this->showId);
        $config=DataShowService::getDataProviderConfig($this->showId);
        $tableOptions=DataShowService::getConfigValue($this->showId,"tableOptions");
        if(empty($sql))
            $this->renderError("脚本不存在");
        $condition=$this->getWhereSql($search);
        $sql= str_replace("{where}",$condition,$sql);

        //分页相关配置
        $pageSize = $this->getSearchPageSize();
        $config['pagination']=[
                'pageSize' => !empty($pageSize) ? $pageSize : 10,
            ];

        $dataProvider=new ZSqlDataProvider($sql,$config);
        $this->render('/show/index', array(
            'dataProvider' => $dataProvider,
            'columns'=>DataShowService::getColumns($this->showId),
            'isExport'=>DataShowService::checkIsCanExport($this->showId),
            'searchItems'=>DataShowService::getSearchItems($this->showId),
            "tableOptions"=>$tableOptions,
            ));
    }

    public function actionExport()
    {
        $search=$this->getSearch();
        $sql=DataShowService::getExportSql($this->showId);
        unset($search["id"]);
        $condition=$this->getWhereSql($search);
        $sql= str_replace("{where}",$condition,$sql);
        $data=Utility::query($sql);
        $mapKes = DataShowService::getMapKeysConfig($this->showId);
        if(Utility::isNotEmpty($mapKes)) {
            $data = DataShowService::getExportMapData($data, $mapKes);
        }
        $this->exportExcel($data);
    }

}