<?php

/**
 * Created by youyi000.
 * DateTime: 2016/11/10 11:17
 * Describe：
 */
class AttachmentController extends Controller
{
    /**
     * 附件类型
     * @var string
     */
    public $attachmentType=Attachment::C_PROJECT;

    /**
     * word是否自动转PDF
     * @var int
     */
    public $isWordToPdf=0;

    /**
     * 判断是否可以保存文件，需要判断时子类可以重写该方法
     * @param $id
     * @param $type
     * @return bool
     */
    protected function checkIsCanSaveFile($id,$type)
    {
        return true;
    }

    /**
     * 判断是否可以删除文件，需要判断时子类可以重写该方法
     * @param $id
     * @return bool
     */
    protected function checkIsCanDelFile($id)
    {
        return true;
    }

    public function actionSaveFile()
    {
        $id=Mod::app()->request->getParam("id");
        if($id!=0 && !Utility::checkQueryId($id))
            $this->returnError("信息有误！");
        $type=Mod::app()->request->getParam("type");
        if(!Utility::checkQueryId($type))
            $this->returnError("信息有误！");

        if(!$this->checkIsCanSaveFile($id,$type))
        {
            $this->returnError("不允许上传文件！");
        }

        $user = $this->getUser();
        $obj=AttachmentFactory::getInstance($this->attachmentType);
        $extras=$this->getFileExtras();
        $res=$obj->saveFile($id,$type,$_FILES["files"],$user["user_id"],$extras,$this->isWordToPdf);

        if($res==1){
            $this->returnSuccess($obj->file["id"],array("name"=>$obj->file["name"],"status"=>$obj->file["status"],"file_url"=>$obj->file["fileUrl"]));
        }
        else
            $this->returnError($res);
    }

    /**
     * 获取文件上传的额外信息，子类重写该方法，格式：array("relation_id"=>122)，即字段名对应字段值的数组，字段名需要与表中一致
     * @return array
     */
    protected function getFileExtras()
    {
        return array();
    }

    public function actionGetFile()
    {
        $id=Mod::app()->request->getParam("id");
        $fileName = Mod::app()->request->getParam("fileName");
        if(empty($id))
            $this->returnError("信息有误！");

        $obj=AttachmentFactory::getInstance($this->attachmentType);
        $filePath=$obj->getFileReadPath($id);
        if(!empty($filePath))
            Utility::outputFile($filePath,$fileName);
        else
            echo "文件不存在";

    }

    public function actionDelFile()
    {
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
            $this->returnError("信息有误！");


        if(!$this->checkIsCanDelFile($id))
        {
            $this->returnError("不允许删除文件！");
        }

        $obj=AttachmentFactory::getInstance($this->attachmentType);
        $res=$obj->deleteFile($id);
        if($res)
            $this->returnSuccess();
        else
            $this->returnError("操作失败");
    }

    public function actionGetPdf()
    {
        $this->layout="empty";
        $id=Mod::app()->request->getParam("id");
        if(empty($id))
            $this->returnError("信息有误！");

        $obj=AttachmentFactory::getInstance($this->attachmentType);
        $filePath=$obj->getFileReadPath($id);

        $filePath=substr($filePath,0,strrpos($filePath,".")).".pdf";
        if(!empty($filePath))
            Utility::outputFile($filePath);
        else
            echo "文件不存在";
        /*$filePath="E:\\youyi000\\Projects\\JTJRSVN\\projects\\oss\\trunk\\Oil\\test.pdf";
        Utility::outputFile($filePath);*/
    }

    /**
     * 获取指定baseId的所有正常的附件
     * @param $baseId
     * @param null $fileType
     * @return array
     */
    public function getAttachments($baseId,$fileType=null)
    {
        $obj=AttachmentFactory::getInstance($this->attachmentType);
        return $obj->getAttachments($baseId,$fileType);
    }

}