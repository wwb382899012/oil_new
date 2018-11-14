<?php

/**
 * Created by youyi000.
 * DateTime: 2016/11/7 15:51
 * Describe：
 */
class Partner extends BaseActiveRecord
{
    const RISK_TYPE_UP=1;//风控准入的风险类别之上游（代理商）
    const RISK_TYPE_DOWN=2;//风控准入的风险类别之下游

    const TYPE_UP=1;
    const TYPE_DOWN=2;
    const TYPE_AGENT=3;

    const STATUS_PASS   = 99;//评审通过

    /**
     * 附件上传路径
     */
    const FILE_PATH="/upload/partner/";

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_partner';
    }

    public function relations()
    {
        return array(
            'contractAmount' => array(self::HAS_ONE, 'PartnerAmount', 'partner_id', 'on'=>'contractAmount.type=1'),
            'usedAmount' => array(self::HAS_ONE, 'PartnerAmount', 'partner_id', 'on'=>'usedAmount.type=2'),
        );
    }

    /**
     * 保存
     * @return bool|string
     * @throws Exception
     */
    public function save($runValidation=true,$attributes=null)
    {
        $isInDbTrans=Utility::isInDbTrans();
        if(!$isInDbTrans)
        {
            $db = Mod::app()->db;
            $trans = $db->beginTransaction();
        }
        try {

            parent::save();

            if(!$isInDbTrans)
            {
                $trans->commit();
            }
            return true;
        } catch (Exception $e) {
            if(!$isInDbTrans)
            {
                try { $trans->rollback(); }catch(Exception $ee){}
                return $e->getMessage();
            }
            else
                throw $e;
        }
    }


    /**
     * 删除成功返回1，否则返回错误信息或0
     * @return int|string
     */
    public static function del($id)
    {
        if(empty($id))
            return "id不能为空！";
        if(!Utility::isIntString($id))
            return "非法Id";

        $sql="delete from t_partner where partner_id=".$id.";";
        $res=Utility::execute($sql);
        if($res==1)
        {
            return 1;
        }
        else
            return "操作失败！";
    }


    /**
     * 获取附件信息
     * @param $id
     * @return array
     */
    public static function getAttachment($id)
    {
        if(empty($id))
            return array();
        $sql="select * from t_partner_attachment where partner_id=".$id." and status=1 order by type asc";
        $data=Utility::query($sql);
        $attachments=array();
        foreach($data as $v)
        {
            $attachments[$v["type"]][]=$v;
        }
        return $attachments;
    }




}