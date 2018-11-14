<?php
/**
 * Created by youyi000.
 * DateTime: 2017/9/13 14:56
 * Describe：
 *      合同上传基础类
 */

class AttachmentContract extends Attachment
{

    function __construct($key="")
    {
        if(empty($key))
            $key=Attachment::C_CONTRACTFILE;
        parent::__construct($key);
    }

    public function init()
    {

    }

    /**
     * 获取附件对象
     * @param int $id
     * @return CActiveRecord|ContractFile
     */
    protected function getModel($id=0)
    {
        if(Utility::isIntString($id))
            $model=ContractFile::model()->findByPk($id);
        if(empty($model))
            $model=new ContractFile();
        return $model;
    }

    /**
     * 保存附件信息
     * @param $baseId
     * @param int $userId
     * @param null $extras
     * @return int|string
     */
    protected function saveAttachmentLog($baseId,$userId=0,$extras=null)
    {
        $idName=$this->getIdFiledName();
        $id=0;
        if(!empty($extras[$idName]))
        {
            $id=$extras[$idName];
            unset($extras[$idName]);
        }
        $model=$this->getModel($id);
        $model->setAttributes($extras);
        if($model->isNewRecord)
        {
            $model->$idName = $id;
        }
        $model->type=$this->type;
        $model->name=$this->file["name"];
        $model->file_path=$this->file["filePath"];
        $model->file_url=$this->file["fileUrl"];
        $model->status=1;

        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try
        {
            $model->save();

            $trans->commit();
            $this->file["id"]=$model->primaryKey;
            $this->file["status"] = 1;
            return 1;
        } catch (Exception $e) {
            try { $trans->rollback(); }catch(Exception $ee){}
            return $e->getMessage();
        }
    }
}