<?php
/**
 * User: liyu
 * Date: 2018/8/1
 * Time: 17:12
 * Desc: api模块下附件上传的基类Controller
 */

class ApiAttachmentController extends Controller
{
    /**
     * 附件类型
     * @var string
     */
    public $attachmentType = Attachment::C_PROJECT;

    /**
     * word是否自动转PDF
     * @var int
     */
    public $isWordToPdf = 0;

    /**
     * 获取查询页数
     * @return int
     */
    public function getSearchPage(){
        $page = $_GET['page'];
        $page = empty($page) ? 1 : $page;
        return $page;
    }

    /**
     * 判断是否可以保存文件，需要判断时子类可以重写该方法
     * @param $id
     * @param $type
     * @return bool
     */
    protected function checkIsCanSaveFile($id, $type) {
        return true;
    }

    /**
     * 判断是否可以删除文件，需要判断时子类可以重写该方法
     * @param $id
     * @return bool
     */
    protected function checkIsCanDelFile($id) {
        return true;
    }

    /**
     * @api {GET} /xxxx/saveFile [saveFile] 附件上传
     * @apiName saveFile
     * @apiGroup ApiAttachment
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} id 标志id
     * @apiParam (输入字段) {int} type 类型，1是附件
     * @apiParam (输入字段) {arr} files 文件信息
     * @apiExample {FormData} 输入示例:
     * {
     *      "id":779,
     *      "type"=>1,
     *      "files"=>[]
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     * "state": 0,
     * "data": {
     * "id": 1,
     * "name": "test",
     * "status": 1,
     * "file_url": "/xxx/xx/test.pdf"
     * }
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data": ""
     * }
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 成功时返回附件id
     */
    public function actionSaveFile() {
        $id = Mod::app()->request->getParam("id");
        if ($id != 0 && !Utility::checkQueryId($id))
            $this->returnJsonError("信息有误！");
        $type = Mod::app()->request->getParam("type");
        if (!Utility::checkQueryId($type))
            $this->returnJsonError("信息有误！");

        if (!$this->checkIsCanSaveFile($id, $type)) {
            $this->returnJsonError("不允许上传文件！");
        }

        $user = $this->getUser();
        $obj = AttachmentFactory::getInstance($this->attachmentType);
        $extras = $this->getFileExtras();
        $res = $obj->saveFile($id, $type, $_FILES["files"], $user["user_id"], $extras, $this->isWordToPdf);

        if ($res == 1) {
            $this->returnJson(array("id"=>$obj->file["id"],"name" => $obj->file["name"], "status" => $obj->file["status"], "file_url" => $obj->file["fileUrl"]));
        } else
            $this->returnJsonError($res);
    }

    /**
     * 获取文件上传的额外信息，子类重写该方法，格式：array("relation_id"=>122)，即字段名对应字段值的数组，字段名需要与表中一致
     * @return array
     */
    protected function getFileExtras() {
        return array();
    }

    public function actionGetFile() {
        $id = Mod::app()->request->getParam("id");
        $fileName = Mod::app()->request->getParam("fileName");
        if (empty($id))
            $this->returnJsonError("信息有误！");

        $obj = AttachmentFactory::getInstance($this->attachmentType);
        $filePath = $obj->getFileReadPath($id);
        if (!empty($filePath))
            Utility::outputFile($filePath, $fileName);
        else
            echo "文件不存在";

    }

    /**
     * @api {GET} /xxxxx/delFile [delFile] 附件删除
     * @apiName delFile
     * @apiGroup ApiAttachment
     * @apiVersion 1.0.0
     * @apiParam (输入字段) {int} id 文件id
     * @apiExample {FormData} 输入示例:
     * {
     *      "id":779,
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     *      "state":0,
     *      "data":1
     * }
     * 失败返回：
     * {
     *      "state":1,
     *      "data":""
     * }
     * @apiParam (输出字段) {string} state 状态码
     * @apiParam (输出字段) {array} data 成功时返回附件id
     */
    public function actionDelFile() {
        $id = Mod::app()->request->getParam("id");
        if (empty($id))
            $this->returnJsonError("信息有误！");


        if (!$this->checkIsCanDelFile($id)) {
            $this->returnJsonError("不允许删除文件！");
        }

        $obj = AttachmentFactory::getInstance($this->attachmentType);
        $res = $obj->deleteFile($id);
        if ($res)
            $this->returnJson();
        else
            $this->returnJsonError("操作失败");
    }

    public function actionGetPdf() {
        $this->layout = "empty";
        $id = Mod::app()->request->getParam("id");
        if (empty($id))
            $this->returnJsonError("信息有误！");

        $obj = AttachmentFactory::getInstance($this->attachmentType);
        $filePath = $obj->getFileReadPath($id);

        $filePath = substr($filePath, 0, strrpos($filePath, ".")) . ".pdf";
        if (!empty($filePath))
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
    public function getAttachments($baseId, $fileType = null) {
        $obj = AttachmentFactory::getInstance($this->attachmentType);
        return $obj->getAttachments($baseId, $fileType);
    }

}