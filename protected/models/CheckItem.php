<?php

/**
 * Created by youyi000.
 * DateTime: 2016/7/4 16:05
 * Describe：
 */
class CheckItem extends BaseActiveRecord
{


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_check_item';
    }

    public function relations()
    {
        return array(
            "checkDetails" => array(self::HAS_MANY, "CheckDetail", "check_id"),
            "node" => array(self::BELONGS_TO, "FlowNode", array('node_id'=>'node_id')),
            "nextNode" => array(self::BELONGS_TO, "FlowNode", array('next_node_id'=>'node_id')),
            "user" => array(self::BELONGS_TO, "SystemUser", "create_user_id"),
        );
    }

    /**
     * 保存审核信息，自动根据节点的角色生成待审核明细
     *      当参数$checkUserIds不为空或0时，则以该参数为对象进行审核明显的处理，否则以角色$roleIds为对象进行审核明细的处理。
     * @param string $roleIds   "1,2,3,4"
     * @param string $checkUserIds   "1,2,3,4"
     * @return int|string
     * @throws Exception
     */
    public function saveCheck($roleIds="0",$checkUserIds="0")
    {
        $isInTrans=Utility::isInDbTrans();
        if(!$isInTrans)
        {
            $db=Mod::app()->db;
            $trans = $db->beginTransaction();
        }

        try{
            $this->save();
            if(!empty($checkUserIds))
            {
                $userIdArr=explode(",",$checkUserIds);
                foreach($userIdArr as $v)
                {
                    if(!empty($v))
                    {
                        $detail=new CheckDetail();
                        $detail->business_id = $this->business_id;
                        $detail->flow_id = $this->flow_id;
                        $detail->obj_id=$this->obj_id;
                        $detail->check_id=$this->check_id;
                        //$detail->role_id=$v;
                        $detail->check_user_id=$v;
                        $detail->check_node_id=$this->node_id;
                        $detail->status=0;
                        $detail->create_time = date("Y-m-d H:i:s");
                        $detail->create_user_id =$this->create_user_id;
                        $detail->update_time = date("Y-m-d H:i:s");
                        $detail->update_user_id =$this->update_user_id;
                        $detail->save();
                    }
                }
            }
            else
            {
                if(!empty($roleIds))
                {
                    $roleIdArr=explode(",",$roleIds);
                    foreach($roleIdArr as $v)
                    {
                        if(!empty($v))
                        {
                            $detail=new CheckDetail();
                            $detail->business_id = $this->business_id;
                            $detail->flow_id = $this->flow_id;
                            $detail->obj_id=$this->obj_id;
                            $detail->check_id=$this->check_id;
                            $detail->role_id=$v;
                            $detail->check_node_id=$this->node_id;
                            $detail->status=0;
                            $detail->create_time = date("Y-m-d H:i:s");
                            $detail->create_user_id =$this->create_user_id;
                            $detail->update_time = date("Y-m-d H:i:s");
                            $detail->update_user_id =$this->update_user_id;
                            $detail->save();
                        }
                    }
                }
            }
            if(!$isInTrans)
            {
                $trans->commit();
            }
            return 1;
        }
        catch(Exception $e)
        {
            Mod::log("业务为" . $this->business_id . "的Id为" . $this->obj_id . "的对象保存节点ID为".$this->node_id." 的审核流程出错：".$e->getMessage(),"error");
            if(!$isInTrans)
            {
                try { $trans->rollback();  } catch (Exception $ee) { }
                return "业务为" . $this->business_id . "的Id为" . $this->obj_id . "的对象保存节点ID为".$this->node_id." 的审核流程出错：".$e->getMessage();
            }
            else
                throw $e;
        }
    }


}