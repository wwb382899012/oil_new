<?php

/**
 * Created by youyi000.
 * DateTime: 2016/11/7 15:51
 * Describe：
 */
class PartnerReview extends BaseActiveRecord
{
    const STATUS_REVIEW_NEW     = 0; //未提交
    // const STATUS_REVIEW_SUBMIT  = 10;//提交通过
    const STATUS_REVIEW_PASS    = 99;//通过
    const STATUS_REVIEW_REJECT  = -1;//否决
    const STATUS_NEED_REVIEW    = 40;//补资料需再评审
    const STATUS_NOT_REVIEW     = 45;//补资料无需再评
    const STATUS_INFO_BACK      = 50;//补充资料审核驳回
    const STATUS_INFO_ADDED     = 55;//资料已补待审核
    const STATUS_INFO_PASS      = 60;//补充资料审核通过


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_partner_review';
    }



    public function relations()
    {
        return array(
            "partner"=>array(self::BELONGS_TO, "PartnerApply",'partner_id'), // 创建人

        );
    }

    /**
     * 保存
     * @return mixed
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
            /*if($this->isNewRecord)
                $this->review_id=IDService::getPartnerReviewId();*/
            if (!empty($this->user_ids) && $this->user_ids!="0") {
                $ids = $this->user_ids;
                $idArray = explode(',', $ids);
                
                $ids = "0";
                foreach ($idArray as $v) {
                    if (isset($v) && $v != 0) {
                        if (strpos($ids . ",", "," . $v . ",") > 0)
                            continue;
                        $ids = $ids . "," . $v;
                    }
                }
                $this->user_ids = $ids;
            }
            
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
     * 获取评审会议最新一条记录的状态
     */
    public static function getReviewStatus($partnerId)
    {
        $data = PartnerReview::model()->findAllToArray(array("condition"=>"partner_id=".$partnerId,"order"=>"review_id desc limit 1"));
        if(Utility::isNotEmpty($data) && count($data)>0)
            return $data[0];
        return array();
    }
    


    /**
     * 获取附件信息
     * @param $id
     * @return array
     */
    public static function getReviewAttachments($id,$type='')
    {
        if(empty($id))
            return array();
        $query = "";
        if(!empty($type))
            $query = " and type=".$type;

        $sql    = "select * from t_partner_review_attachment where base_id=".$id." and status=1 ".$query." order by type asc";
        $data   = Utility::query($sql);
        if(Utility::isEmpty($data))
            return array();
        $attachments=array();
        foreach($data as $v)
        {
            $attachments[$v["type"]][]=$v;
        }
        return $attachments;
    }




}