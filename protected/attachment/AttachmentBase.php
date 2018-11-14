<?php
/**
 * Created by youyi000.
 * DateTime: 2017/9/13 15:58
 * Describe：
 */

abstract class AttachmentBase extends Attachment
{
    public function init()
    {

    }

    /**
     * 获取附件对象，子类必须重写
     * @param int $id
     * @return CActiveRecord|
     */
    abstract function getModel($id=0);

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
        $model->type=$this->type;
        $model->name=$this->file["name"];
        $model->file_path=$this->file["filePath"];
        $model->file_url=$this->file["fileUrl"];
        $model->status=1;

        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {


            if($this->typeInfo["multi"]!=1)
            {
                $model->updateAll(array("status"=>0,"update_time"=>new CDbExpression("now()")),
                                  "type=".$this->type." and status>=1 and ".$this->config["baseFieldName"]."=".$baseId);
            }
            $model->save();

            $trans->commit();
            $this->file["id"]=$model->primaryKey;
            return 1;
        } catch (Exception $e) {
            try { $trans->rollback(); }catch(Exception $ee){}
            return $e->getMessage();
        }
    }

}