<?php

/**
 * Created by youyi000.
 * DateTime: 2017/3/21 10:39
 * Describe：
 */
class GoodsController extends Controller
{
    public function pageInit()
    {
        $this->filterActions="ajaxSave,ajaxDel,ajaxChangeStatus";
        $this->authorizedActions=array("getSelect");
        $this->rightCode="goods";
    }

    public function actionGetSelect()
    {
        $sql = "select * from t_goods where status=1 order by parent_id asc,order_index asc,goods_id asc";
        $data = Utility::query($sql);
        echo json_encode($data);
    }

    public function actionIndex(){
        $attr=$_GET[search];
        $sql="select {col} from t_goods ".$this->getWhereSql($attr)." order by goods_id desc {limit}";
        $data=$this->queryTablesByPage($sql,'*');
        $data['tree'] = Goods::getActiveTree(0, true);
        $this->render("index",$data);
    }

    public function actionAdd(){
        $parent_id = Mod::app()->request->getParam('id');
        $parent = $this->getParent($parent_id);
        $this->pageTitle="添加商品信息";
        $this->render("edit",array(
            "data"=>$parent
        ));
    }

    public function actionSave(){
        $params=$_POST["data"];
        $user=$this->getUser();

        if(empty($params['name']))
            $this->returnError("品名不得为空！");

        $params['name']=trim($params['name']);
        if (!empty($params["goods_id"]))
        {
            if(!Utility::checkQueryId($params["goods_id"]))
                $this->returnError("id有误！");
            $obj=Goods::model()->findByPk($params["goods_id"]);

        }

        if (!empty($params['parent_id']) && Utility::checkQueryId($params["parent_id"])) {
            $parent = Goods::model()->findByPk($params["parent_id"]);
            if(!empty($parent)) {
                $params['parent_ids'] = $parent->parent_ids.';'.$parent->goods_id.';';
            } else {
                unset($params['parent_id']);
            }
        } else {
            unset($params['parent_id']);
        }

        if(empty($obj->goods_id)){
            $obj=new Goods();
            $obj->create_user_id=$user["user_id"];
            $obj->create_time=date('Y-m-d H:i:s');
        }

        $oldObj=Goods::model()->find("name='".$params["name"]."'");
        if(!empty($oldObj->goods_id) && $oldObj->goods_id!=$obj->goods_id)
            $this->returnError("当前名称的商品已经存在，请重新填写！");

        unset($params['goods_id']);
        $obj->setAttributes($params,false);

        $obj->update_user_id=$user["user_id"];
        $obj->update_time=date('Y-m-d H:i:s');
        $logRemark = ActionLog::getEditRemark($obj->isNewRecord, "商品");
        $res=$obj->save();
        if($res===true){
            Utility::addActionLog(json_encode($obj->oldAttributes), $logRemark, "Goods", $obj->goods_id);
            $this->returnSuccess($obj->goods_id);
        }else{
            $this->returnError("保存失败！".$res);
        }
    }

    public function actionAjaxSave() {
        $params=$_POST;
        $user=$this->getUser();

        if(empty($params['name']))
            $this->returnError("品名不得为空！");

        $params['name']=trim($params['name']);

        if (!empty($params["goods_id"]))
        {
            if(!Utility::checkQueryId($params["goods_id"]))
                $this->returnError("id有误！");
            $obj=Goods::model()->findByPk($params["goods_id"]);

        }

        if (!empty($params['parent_id']) && Utility::checkQueryId($params["parent_id"])) {
            $parent = Goods::model()->findByPk($params["parent_id"]);
            if(!empty($parent)) {
                $params['parent_ids'] = trim($parent->parent_ids,',').','.$parent->goods_id.',';
            } else {
                unset($params['parent_id']);
            }
        } else {
            unset($params['parent_id']);
        }

        if(empty($obj->goods_id)){
            $obj=new Goods();
            $obj->create_user_id=$user["user_id"];
            $obj->create_time=date('Y-m-d H:i:s');
        }
        $oldObj=Goods::model()->find("name='".$params["name"]."'");
        if(!empty($oldObj->goods_id) && $oldObj->goods_id!=$obj->goods_id)
            $this->returnError("当前名称的商品已经存在，请重新填写！");

        unset($params['goods_id']);
        $obj->setAttributes($params,false);
        $obj->update_user_id=$user["user_id"];
        $obj->update_time=date('Y-m-d H:i:s');
        $logRemark = ActionLog::getEditRemark($obj->isNewRecord, "商品");
        $res=$obj->save();
        if($res===true){
            Utility::addActionLog(json_encode($obj->oldAttributes), $logRemark, "Goods", $obj->goods_id);
            $this->returnSuccess($obj->goods_id);
        }else{
            $this->returnError("保存失败！".$res);
        }
    }

    public function actionAjaxChangeStatus() {
        $id=Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
            $this->renderError("非法参数！");

        $obj=Goods::model()->findByPk($id);
        if(empty($obj->goods_id))
            $this->renderError("当前信息不存在！");

        $objData = $obj->getAttributes(true,array("create_user_id","create_time","update_user_id","update_time",));
        $data = array_merge($objData, $parent);
        $this->render("edit",array(
            "data"=>$data
        ));
    }

    public function actionEdit(){
        $id=Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
            $this->renderError("非法参数！","/Goods/");

        $obj=Goods::model()->findByPk($id);
        if(empty($obj->goods_id))
            $this->renderError("当前信息不存在！","/Goods/");

        $this->pageTitle="修改商品信息";
        $objData = $obj->getAttributes(true,array("create_user_id","create_time","update_user_id","update_time",));
        $parent = $this->getParent($obj->parent_id);
        $data = array_merge($objData, $parent);
        $this->render("edit",array(
            "data"=>$data
        ));
    }

    public function actionDel(){
        $id=Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
            $this->returnError("非法参数！");

        $projectArr=Project::model()->findAllToArray('goods_type='.$id);
        if(count($projectArr)>0){
            $this->returnError("当前商品已有项目在使用，不能删除！");
        }
        $res=Goods::model()->deleteByPk($id);
        if($res==1){
            Utility::addActionLog(null, "删除商品", "Goods", $id);
            $this->returnSuccess();
        }else{
            $this->returnError("删除失败！");
        }
    }

    public function actionAjaxDel(){
        $id=Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
            $this->returnError("非法参数！");

        // 判断是否叶子节点
        $count = Goods::model()->count("parent_id=:parent_id", array('parent_id'=>$id));
        if($count > 0) {
            $this->returnError("当前商品已有子节点，不能删除！");
        }

        $contractGoodsCount=ContractGoods::model()->count("goods_id=:goods_id", array('goods_id'=>$id));
        if($contractGoodsCount>0){
            $this->returnError("当前商品已有项目在使用，不能删除！");
        }
        
        // 检查合作方及合作方申请
        $partnerCondition = "(goods_ids=:goods_id) or (goods_ids like '%,:goods_id') or (goods_ids like '%,:goods_id,%') or (goods_ids like ':goods_id,%')";
        $companyGoodsCount=Partner::model()->count($partnerCondition, array('goods_id'=>$id));
        if($companyGoodsCount>0){
            $this->returnError("当前商品已关联合作方信息，不能删除！");
        }
        $applyGoodsCount=PartnerApply::model()->count($partnerCondition, array('goods_id'=>$id));
        if($applyGoodsCount>0){
            $this->returnError("当前商品已关联合作方申请信息，不能删除！");
        }

        $res=Goods::model()->deleteByPk($id);
        if($res==1){
            Utility::addActionLog(null, "删除商品", "Goods", $id);
            $this->returnSuccess();
        }else{
            $this->returnError("删除失败！");
        }
    }

    public function actionDetail(){
        $id=Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
            $this->renderError("非法参数！","/Goods/");

        $obj=Goods::model()->findByPk($id);
        if(empty($obj->goods_id))
            $this->renderError("当前信息不存在！","/Goods/");

        $this->pageTitle="查看商品详情";
        $this->render('detail',array("data"=>$obj->attributes));
    }

    private function getParent($parent_id) {
        if(!empty($parent_id) && Utility::checkQueryId($parent_id)) {
            $parent = Goods::model()->findByPk($parent_id);
            $parent = array('parent_id'=>$parent->goods_id, 'parent_name'=>$parent->name);
        } else {
            $parent = array('parent_id'=>'0', 'parent_name'=>'根节点');
        }
        return $parent;
    }
}