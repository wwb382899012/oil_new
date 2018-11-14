<?php

/**
 * Created by PhpStorm.
 * User: vector
 * Date: 2016/11/7
 * Time: 17:39
 */
class PartnerController extends AttachmentController
{
    public function pageInit()
    {
        $this->attachmentType=Attachment::C_PARTNER;
        $this->filterActions="";
        $this->rightCode="partner";
//        $this->newUIPrefix = 'new_';
    }

    public function actionIndex(){
        $attr=$_GET[search];
        $type  = 0;
        $query = "";
        if(!empty($attr["a.type"])){
            $type = $attr["a.type"];
            unset($attr["a.type"]);
            $query .= " and find_in_set(".$type.",a.type)";
        }

        $sql="select {col} from t_partner a left join t_ownership b on a.ownership=b.id ".$this->getWhereSql($attr).$query." order by partner_id desc {limit}";
        $data=$this->queryTablesByPage($sql,"a.*,b.name as ownership_name");
        if (Utility::isNotEmpty($data['data']['rows'])) {
            foreach ($data['data']['rows'] as $key => $row) {
                $typeDesc = PartnerApplyService::getPartnerType($row['type']);
                $data['data']['rows'][$key]['type'] = str_replace('&nbsp;', ' ', $typeDesc);
            }
        }

        if(!empty($type))
            $attr["a.type"] = $type;
        $data["search"]=$attr;

        $this->render("index",$data);
    }

    public function actionDetail() {
        $partner_id = Mod::app()->request->getParam("id");
        if (!Utility::checkQueryId($partner_id)) {
            $this->renderError("非法参数！", "/partner/");
        }

        $obj = Partner::model()->findByPk($partner_id);
        if (empty($obj->partner_id)) {
            $this->renderError("当前信息不存在！", "/partner/");
        }
        $attachments= Partner::getAttachment($partner_id);

        $sql = "select {col} from t_partner_log where object_id =" . $partner_id . " and table_name ='t_partner_apply' order by create_time desc {limit}";
        $logData = PartnerApply::formatPartnerApplyLog($this->queryTablesByPage($sql, '*'));
        $checkLogs  = FlowService::getCheckLog($partner_id,"30,31");
        $this->pageTitle = "合作方详情";
        $this->render('detail', array(
            "data" => $obj->attributes, 
            "attachments" => $attachments, 
            "logData" => $logData['data'],
            "checkLogs"=>$checkLogs,
            )
        );
    }

    /**
     * 判断是否可以修改
     * @param $status[0] 合作方状态，$status[1] 会议评审状态
     * @return bool
     */
    public function checkIsCanEdit($status)
    {
        return false;
    }

    /*public function actionDetail(){
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
        {
            $this->renderError("信息异常！", "/partner/");
        }

        $sql = "select a.*,b.name as ownership_name from t_partner a 
                left join t_ownership b on a.ownership=b.id 
                where a.partner_id=".$id."";
        $data=Utility::query($sql);
        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！", "/partner/");
        }

        $attachments= Partner::getAttachment($id);
        $checkLogs  = FlowService::getCheckLog($id,"30,31");

        $this->pageTitle="查看合作方详情";
        $this->render('detail',array(
            "data"=>$data[0],
            "attachments"=>$attachments,
            "checkLogs"=>$checkLogs,
            )
        );
    }*/

    /**
     * 重写文件上传获取额外参数的方法
     * @return array
     */
    protected function getFileExtras()
    {
        return array();
    }

}