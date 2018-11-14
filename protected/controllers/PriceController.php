<?php
/**
 * Created by youyi000.
 * DateTime: 2017/11/23 16:19
 * Describe：
 */

class PriceController extends Controller
{
    public function pageInit()
    {
        //$this->authorizedActions=array("index","add","");
        $this->rightCode="goods_price";
    }

    public function actionIndex()
    {
        $search=$_GET["search"];
        $dataProvider=GoodsPriceSearch::search($search);

        $this->render('grid', array(
            'dataProvider' => $dataProvider,
        ));

//        $attr=$_GET[search];
//        $sql="select {col} from t_account a left join t_corporation b on a.corporation_id=b.corporation_id ".$this->getWhereSql($attr)." order by account_id desc {limit}";
//        $data=$this->queryTablesByPage($sql,'a.*,b.name as corporation_name');
//        $this->render("index",$data);
    }

    public function actionAdd(){
        $this->pageTitle="添加信息";
        $this->render("edit");
    }

    public function actionSave()
    {
        $params=$_POST["data"];
        if(empty($params['goods_id']) || empty($params['price'])){
            $this->returnError("信息异常！");
        }
        if(Utility::checkQueryId($params["price_id"]))
            $obj=GoodsPrice::model()->findByPk($params["price_id"]);
        if(empty($obj))
        {
            $obj=new GoodsPrice();
        }

        unset($params['price_id']);
        $obj->setAttributes($params,false);

        $logRemark = ActionLog::getEditRemark($obj->isNewRecord, "商品价格");
        $res=$obj->save();
        if($res===true){
            Utility::addActionLog(json_encode($obj->oldAttributes), $logRemark, "GoodsPrice", $obj->price_id);
            $this->returnSuccess($obj->price_id);
        }else{
            $this->returnError("保存失败：".$res);
        }
    }

    public function actionEdit(){
        $id=Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
        {
            $this->renderError("信息异常！");
        }
        $obj=GoodsPrice::model()->findByPk($id);
        if(empty($obj))
        {
            $this->renderError("价格信息不存在！");
        }

        $this->pageTitle="修改商品价格信息";
        $this->render("edit",array(
            "data"=>$obj->getAttributes(true,Utility::getIgnoreFields())
        ));
    }

    public function actionDel()
    {
        $id=Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
        {
            $this->returnError("信息异常！");
        }

        $res=GoodsPrice::model()->deleteByPk($id);
        if($res)
        {
            Utility::addActionLog(null, "删除商品价格", "GoodsPrice", $id);
            $this->returnSuccess();
        }
        else
        {
            $this->returnError($res);
        }
    }

    public function actionDetail()
    {
        $id=Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
        {
            $this->renderError("信息异常！");
        }
        $obj=GoodsPrice::model()->with("goods")->findByPk($id);
        if(empty($obj))
        {
            $this->renderError("价格信息不存在！");
        }


        $this->pageTitle="查看商品价格详情";
        $this->render('detail',array("model"=>$obj));
    }
}