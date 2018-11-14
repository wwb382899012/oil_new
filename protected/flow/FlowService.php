<?php

/**
 * Created by youyi000.
 * DateTime: 2016/7/4 16:03
 * Describe：
 *      审核流相关的服务类
 */
class FlowService
{

    const BUSINESS_RISK_CHECK=2; //合同风控审核
    const BUSINESS_BUSINESS_CHECK=3; //合同业务审核
    const BUSINESS_PAY_APPLICATION=13;
    const BUSINESS_FACTORING=14;  //保理申请审核
    const BUSINESS_CONTRACT_FILD_UPLOAD_CHECK=4; //合同审核
    const BUSINESS_STOCK_INVENTORY = 18; //库存盘点审核
    const BUSINESS_STOCK_IN_CHECK = 7; //入库审核
    const BUSINESS_STOCK_NOTICE_SETTLEMENT_CHECK = 8; //入库通知单结算审核
    const BUSIONESS_DELIVERY_ORDER_CHECK  = 9; //发货单审核
    const BUSIONESS_DELIVERY_ORDER_SETTLEMENT_CHECK  = 10; //发货单结算审核
    const BUSINESS_STOCK_OUT_CHECK = 20; //出库审核
    const STOCK_CONTRACT_SETTLEMENT_CHECK=21; //采购合同结算审核
    const DELIVERY_CONTRACT_SETTLEMENT_CHECK=22; //采购合同结算审核
    const BUSINESS_CONTRACT_SPLIT_CHECK=23; //合同平移审核
    const BUSINESS_STOCK_SPLIT_CHECK=24; //出入库平移审核
    const BUSINESS_CONTRACT_TERMINATE_CHECK=25; //合同终止审核

    /**
     * 审核业务配置
     * @var array
     */
    public static $business=array(
        1=>array("id"=>1,"name"=>"仓库审核","model"=>"Storehouse","action_id"=>7),
        2=>array("id"=>2,"name"=>"风控审核","model"=>"Contract","action_id"=>11),
        3=>array("id"=>3,"name"=>"业务审核","model"=>"Contract","action_id"=>13),
        4=>array("id"=>4,"name"=>"合同审核","model"=>"ContractFile","action_id"=>15),

        //3开头的为风控准入相关的审核
        30=>array("id"=>30,"name"=>"合作方准入风控审核","model"=>"Partner","action_id"=>2),
        31=>array("id"=>31,"name"=>"会议评审补充资料审核","model"=>"Partner","action_id"=>6),


        7=>array("id"=>7,"name"=>"入库单审核","model"=>"StockIn","action_id"=>18),
        8=>array("id"=>8,"name"=>"入库通知结算审核","model"=>"LadingSettlement","action_id"=>45),
        9=>array("id"=>9,"name"=>"发货单审核","model"=>"DeliveryOrder","action_id"=>19),
        10=>array("id"=>10,"name"=>"发货单结算审核","model"=>"DeliveryOrder","action_id"=>47),
        11=>array("id"=>11,"name"=>"调货单审核","model"=>"CrossOrder","action_id"=>22),
        12=>array("id"=>12,"name"=>"调货处理审核","model"=>"CrossOrder","action_id"=>27),
        13=>array("id"=>13,"name"=>"付款申请审核","model"=>"PayApplication","action_id"=>21),
        14=>array("id"=>14,"name"=>"保理申请审核","model"=>"FactorDetail","action_id"=>20),
        15=>array("id"=>15,"name"=>"进项票申请审核","model"=>"InvoiceApplication","action_id"=>23),
        16=>array("id"=>16,"name"=>"销项票开票确认","model"=>"Invoice","action_id"=>26),
        17=>array("id"=>17,"name"=>"销项票申请审核","model"=>"InvoiceApplication","action_id"=>36),
        18=>array("id"=>18,"name"=>"库存盘点审核","model"=>"StockInventory","action_id"=>38),
        19=>array("id"=>19,"name"=>"付款止付审核","model"=>"PayApplication","action_id"=>52),
        20=>array("id"=>20,"name"=>"出库单审核","model"=>"StockOutOrder","action_id"=>53),
        21=>array("id"=>21,"name"=>"采购合同结算审核","model"=>"Contract","action_id"=>55),
        22=>array("id"=>22,"name"=>"销售合同结算审核","model"=>"Contract","action_id"=>57),
        23=>array("id"=>23,"name"=>"合同平移审核","model"=>"ContractSplit","action_id"=>59),
        24=>array("id"=>24,"name"=>"出入库平移审核","model"=>"StockSplit","action_id"=>61),
        25=>array("id"=>25,"name"=>"合同终止审核","model"=>"ContractTerminate","action_id"=>63),
    );

    public static function startFlowForCheck1($objId,$checkUserIds="0")
    {
        return self::startFlow(1,$objId,$checkUserIds,Utility::getNowUserId());
    }


    public static function startFlowForCheck2($objId,$checkUserIds="0")
    {
        return self::startFlow(2,$objId,$checkUserIds,Utility::getNowUserId());
    }

    public static function startFlowForCheck3($objId,$checkUserIds="0")
    {
        return self::startFlow(3,$objId,$checkUserIds,Utility::getNowUserId());
    }

    public static function startFlowForCheck4($objId,$checkUserIds="0")
    {
        return self::startFlow(4,$objId,$checkUserIds,Utility::getNowUserId());
    }

    public static function startFlowForCheck30($objId,$checkUserIds="0")
    {
        return self::startFlow(30,$objId,$checkUserIds,Utility::getNowUserId());
    }

    public static function startFlowForCheck31($objId,$checkUserIds="0")
    {
        return self::startFlow(31,$objId,$checkUserIds,Utility::getNowUserId());
    }

    public static function startFlowForCheck7($objId,$checkUserIds="0")
    {
        return self::startFlow(7,$objId,$checkUserIds,Utility::getNowUserId());
    }

    public static function startFlowForCheck8($objId,$checkUserIds="0")
    {
        return self::startFlow(8,$objId,$checkUserIds,Utility::getNowUserId());
    }

    public static function startFlowForCheck9($objId,$checkUserIds="0")
    {
        return self::startFlow(9,$objId,$checkUserIds,Utility::getNowUserId());
    }

    public static function startFlowForCheck10($objId,$checkUserIds="0")
    {
        return self::startFlow(10,$objId,$checkUserIds,Utility::getNowUserId());
    }

    public static function startFlowForCheck11($objId,$checkUserIds="0")
    {
        return self::startFlow(11,$objId,$checkUserIds,Utility::getNowUserId());
    }

    public static function startFlowForCheck12($objId,$checkUserIds="0")
    {
        return self::startFlow(12,$objId,$checkUserIds,Utility::getNowUserId());
    }

    public static function startFlowForCheck13($objId,$checkUserIds="0")
    {
        return self::startFlow(13,$objId,$checkUserIds,Utility::getNowUserId());
    }

    public static function startFlowForCheck14($objId,$checkUserIds="0")
    {
        return self::startFlow(14,$objId,$checkUserIds,Utility::getNowUserId());
    }

    public static function startFlowForCheck15($objId,$checkUserIds="0")
    {
        return self::startFlow(15,$objId,$checkUserIds,Utility::getNowUserId());
    }

    public static function startFlowForCheck16($objId,$checkUserIds="0")
    {
        return self::startFlow(16,$objId,$checkUserIds,Utility::getNowUserId());
    }

    public static function startFlowForCheck17($objId,$checkUserIds="0")
    {
        return self::startFlow(17,$objId,$checkUserIds,Utility::getNowUserId());
    }

    public static function startFlowForCheck18($objId,$checkUserIds="0")
    {
        return self::startFlow(18,$objId,$checkUserIds,Utility::getNowUserId());
    }

    public static function startFlowForCheck19($objId,$checkUserIds="0")
    {
        return self::startFlow(19,$objId,$checkUserIds,Utility::getNowUserId());
    }
    
    public static function startFlowForCheck21($objId,$checkUserIds="0")
    {
        return self::startFlow(21,$objId,$checkUserIds,Utility::getNowUserId());
    }
    
    public static function startFlowForCheck22($objId,$checkUserIds="0")
    {
        return self::startFlow(22,$objId,$checkUserIds,Utility::getNowUserId());
    }

    public static function startFlowForCheck23($objId,$checkUserIds="0")
    {
        return self::startFlow(23,$objId,$checkUserIds,Utility::getNowUserId());
    }

    public static function startFlowForCheck24($objId,$checkUserIds="0")
    {
        return self::startFlow(24,$objId,$checkUserIds,Utility::getNowUserId());
    }

    public static function startFlowForCheck25($objId,$checkUserIds="0")
    {
        return self::startFlow(25,$objId,$checkUserIds,Utility::getNowUserId());
    }

    /**
     * 开始审批流
     * @param $businessId
     * @param $objId
     * @param string $checkUserIds "1,2,3"
     * @param int $userId
     * @return int|string
     */
    public static function startFlow($businessId, $objId,$checkUserIds="0",$userId=0)
    {
        $obj=CheckFactory::getInstance($businessId);
        if(empty($userId))
            $userId=Utility::getNowUserId();
        $res=$obj->startCheck($objId,$checkUserIds,$userId);

        return $res;
    }

    /**
     * 撤回审批流
     * @param $businessId
     * @param $objId
     * @param string $checkUserIds
     * @param int $userId
     * @return int|string
     * @throws Exception
     */
    public static function revocationFlow($businessId, $objId,$checkUserIds="0",$userId=0){
        if(empty($userId))
            $userId=Utility::getNowUserId();

        $checkItem = CheckItem::model()->find("business_id=:businessId and obj_id=:objId and node_id>0",
            array("businessId" => $businessId, "objId" => $objId));
        if (empty($checkItem->check_id)) {
            Mod::log("业务为" . $businessId . "的Id为" . $objId . "的对象不在流程中，无法撤销", "error");
            return "当前对象不在流程中，无法撤销";
        }

        $roleId = UserService::getUserMainRoleId($userId);
        $remark = '自动驳回审核';

        $obj = CheckFactory::getInstance($checkItem->business_id);
        return $obj->revocationCheck($checkItem,$roleId, $remark, $userId, $checkUserIds);
    }

    /**
     * @param $checkItem
     * @param $isPass
     * @param $roleId
     * @param $remark
     * @param $userId
     * @param string $checkUserIds "1,2,3"
     * @param null $extras
     * @param null $extraCheckItems
     * @return int|string
     */
    public static function check($checkItem,$isPass,$roleId,$remark,$userId,$checkUserIds="0",$extras=null,$extraCheckItems=null)
    {
        if($checkItem->node_id==0)
        {
            return "当前审核信息已经审核完成，无需再审！";
        }
        $isInDbTrans=Utility::isInDbTrans();
        if(!$isInDbTrans)
        {
            $db = Mod::app()->db;
            $trans = $db->beginTransaction();
        }
        //$trans=Utility::beginTransaction();
        try
        {
            
            $obj = CheckFactory::getInstance($checkItem->business_id);
            $res = $obj->doCheck($checkItem, $isPass, $roleId, $remark, $userId, $checkUserIds);
            if ($res != 1) {
                throw new Exception($res);
            }

            /*if (is_array($extras))
            {
                foreach ($extras as $v)
                {
                    $note = new CheckNote();
                    $note->detail_id = $obj->checkDetailId;
                    $note->business_id = $checkItem->business_id;
                    $note->check_id = $checkItem->check_id;
                    $note->user_id = $userId;
                    $note->obj_id = $checkItem->obj_id;
                    $note->flow_id = $checkItem->flow_id;
                    $note->node_id = $checkItem->node_id;
                    $note->type = $v["type"];
                    $note->display_name = $v["display_name"];
                    $note->key_name = $v["key"];
                    $note->status = 1;
                    $note->remark = $v["value"];
                    $note->create_time = date("Y-m-d H:i:s");
                    $note->create_user_id = $userId;
                    $note->update_time = date("Y-m-d H:i:s");
                    $note->update_user_id = $userId;
                    $note->save();
                }
            }*/

            if (is_array($extraCheckItems))
            {
                // 改成json数组输入就可以了
                $itemLog = new CheckExtraLog();
                $itemLog->detail_id = $obj->checkDetailId;
                $itemLog->check_id = $checkItem->check_id;
                $itemLog->log_id = $obj->checkLogId;//$checkItem->obj_id;
                $itemLog->status = 1;
                $itemLog->content = json_encode($extraCheckItems);
                $itemLog->create_time = date("Y-m-d H:i:s");
                $itemLog->create_user_id = $userId;
                $itemLog->update_time = date("Y-m-d H:i:s");
                $itemLog->update_user_id = $userId;
                $itemLog->save();
            }
            if (!$isInDbTrans)
            {
                $trans->commit();
            }
            return 1;
        }
        catch (Exception $e)
        {
            if(!$isInDbTrans)
            {
                try { $trans->rollback(); }catch(Exception $ee){}
            }
            return $e->getMessage();
        }
    }

    /**
     * 获取流程ID
     * @param $businessId
     * @return int
     */
    public static function getFlowId($businessId)
    {
        $model=FlowBusiness::model()->find("business_id=".$businessId." and status=1");
        if(empty($model))
        {
            return 0;
        }
        return $model->flow_id;
    }

    public static function getNextCheckNode($flowId,$nodeId)
    {
        $sql="select * from t_flow_node where flow_id=".$flowId." and previous_id=".$nodeId."";
        $data=Utility::query($sql);
        if(Utility::isNotEmpty($data))
            return $data[0];
        else
            return null;
    }

    /**
     * 获取审核记录
     * @param $id
     * @param string $businessIds 为0时返回所有审核记录，可以是多个businessId组合
     * @return array|null
     */
    public static function getCheckLog($id,$businessIds="0")
    {
        if(empty($id))
            return null;

        $sql="select a.*,u.name ,b.node_name,b.role_ids from t_check_log a
                left join t_system_user  u on u.user_id=a.user_id
                left join t_flow_node  b on b.node_id=a.node_id
              where obj_id=".$id." AND b.node_id > 0";

        if(!empty($businessIds))
            $sql.=" and a.business_id in(".$businessIds.")";

        $sql.=" order by a.id desc";

        return Utility::query($sql);
    }
    /**
     * 获取审核记录，只需要最后一次审核通过的全部流程,排序： 按流程近到远
     * @param $id
     * @param string $businessIds 为0时返回所有审核记录，可以是多个businessId组合
     * @return array|null
     */
    public static function getCheckLogLast($id,$businessIds="0")
    {
        if(empty($id))
            return null;

        $sql="select a.*,u.name ,b.node_name from t_check_log a
                left join t_system_user  u on u.user_id=a.user_id
                left join t_flow_node  b on b.node_id=a.node_id
              where obj_id=".$id." AND b.node_id > 0";

        if(!empty($businessIds))
            $sql.=" and a.business_id in(".$businessIds.")";

        $sql.=" order by a.id desc";
        $list = Utility::query($sql);
        $lastBackId = '';
        $flag=true;
        if(!empty($list)){
            foreach ($list as $k=>$v) {
                if($v['check_status']=='-1'&&$flag){
                    $lastBackId=$v['id'];
                    $flag=false;
                }

            }
        }
        $return=array();
        if(empty($lastBackId))
            $return=$list;
        else{
            foreach($list as $key=>$value){
                if($value['id']>$lastBackId)
                    array_push($return,$value);
            }
        }
        return $return;
    }
    /**
     * 获取审核项
     * @param $id
     * @param string $businessIds 为0时返回所有审核记录，可以是多个businessId组合
     * @return array|null
     */
    public static function getCheckDetail($id,$businessIds="0")
    {
        if(empty($id))
            return null;

        $sql="select a.*,u.name from t_check_detail a
                left join t_system_user  u on u.user_id=a.create_user_id
              where a.obj_id=".$id;

        if(!empty($businessIds))
            $sql.=" and a.business_id in(".$businessIds.")";
        $sql.=" order by a.detail_id asc";
        return Utility::query($sql);
    }
    /**
     * 获取审核记录对象
     * @param $id
     * @param string $businessIds
     * @return array|null
     */
    public static function getCheckLogModel($id,$businessIds="0")
    {
        if(empty($id))
            return null;

        $condition="t.obj_id=".$id;
        if(!empty($businessIds))
            $condition.=" and t.business_id in(".$businessIds.")";

        $logs=CheckLog::model()->with("user","extra","checkNode")->findAll(array(
            "condition"=>$condition,
            "order"=>"t.id desc",
         ));
        return $logs;
    }

    /**
     * 获取审核记录对象
     * @param $id
     * @param string $businessIds
     * @return array|null
     */
    public static function getCheckLogWithExtra($id, $businessIds = "0") {
        $checkItem = self::getCheckItem($id, $businessIds);
        if (Utility::isEmpty($checkItem)) {
            return [];
        }
        $checkLogs = [];
        $checkLogModel = self::getCheckLogModel($id, $businessIds);
        if (Utility::isNotEmpty($checkLogModel)) {
            foreach ($checkLogModel as $v) {
                $item = [];
                if (Utility::isNotEmpty($v->extra->items)) {
                    foreach ($v->extra->items as $key => $value) {
                        $item[] = [
                            'name' => $value['name'],
                            'value' => $value['value'],
                            'remark' => $value['remark']
                        ];
                    }
                }
                $checkLogs[] = [
                    'node_name' => $v->checkNode["node_name"],
                    'name' => $v->user["name"],
                    'check_time' => $v['check_time'],
                    'check_status' => $v["check_status"],
                    'remark' => $v['remark'],
                    'item' => $item
                ];
            }
        }
        $checkLogs[] = [
            'node_name' => '发起流程',
            'name' => $checkItem['name'],
            'check_time' => $checkItem['create_time'],
            'check_status' => -100,
            'remark' => '',
            'item' => []
        ];
        return $checkLogs;
    }

    /**
     * 获取审核流程发起人
     * @param $id
     * @param string $businessIds
     * @return Array
     */
    public static function getCheckItem($id,$businessIds="0"){
        if(empty($id))
            return null;

        $sql="select a.*,u.name from t_check_item a
                left join t_system_user  u on u.user_id=a.create_user_id
              where obj_id=".$id;

        if(!empty($businessIds))
            $sql.=" and a.business_id in(".$businessIds.")";

        $data=Utility::query($sql);
        return $data[0];
    }

    /**
     * 合并获取审核记录
     * @param array $id 所有的需要获取审核记录的id
     * @param string $businessIds 为0时返回所有审核记录，可以是多个businessId组合
     * @return array|null
     */
    public static function getMoreCheckLog(array $ids,$businessIds="0")
    {
        if(empty($ids))
            return null;

        $inIds = implode(',',$ids);
        $inIds = trim($inIds,',');
        $sql="select a.*,u.name ,b.node_name ,c.flow_name,d.type from t_check_log a
                left join t_system_user  u on u.user_id=a.user_id
                left join t_flow_node  b on b.node_id=a.node_id
                left join t_flow c on a.flow_id = c.flow_id
                left join t_contract d on d.contract_id=a.obj_id and a.business_id in(13,14)
              where obj_id in($inIds)";

        if(!empty($businessIds))
            $sql.=" and a.business_id in(".$businessIds.")";

        $sql.=" order by a.id desc";

        return Utility::query($sql);
    }

    /**
     * 获取额外审核信息
     * @param $detailId
     * @return array
     */
    public static function getExtraItems($detailId)
    {
        $sql="select * from t_check_note where detail_id=".$detailId."";
        $data=Utility::query($sql);
        return $data;
    }

    /**
     * 获取单独审核项信息
     * @param $detailId
     * @return array
     */
    public static function getExtraCheckItems($detailId)
    {
        $sql="select * from t_item_check_log where detail_id=".$detailId."";
        $data=Utility::query($sql);
        return $data;
    }

    /**
     * 根据审核业务获取对应的公司信息
     * @param $businessId
     * @param $keyId
     * @return int
     */
    public static function getCorpId($businessId,$keyId)
    {
        $a=array("30", "31");
        if(in_array($businessId,$a))
        {
            return 0;
        }
        else
        {
            $projectId=0;
            switch ($businessId)
            {
                case 3:
                case 4:
                case 13:
                case 14:

                    $model=Contract::model()->findByPk($keyId);
                    $projectId= $model->project_id;
                    break;

                case 5:

                    $model=PayRequest::model()->findByPk($keyId);
                    $projectId= $model->project_id;
                    break;

                case 11:
                case 12:

                    $model=Invoice::model()->findByPk($keyId);
                    $projectId= $model->project_id;
                    break;

                default:
                    $projectId=$keyId;
                    break;
            }

            $p=Project::model()->findByPk($projectId);
            return $p->corporation_id;
        }
    }

    /**
     * 根据审核业务获取对应的项目信息
     * @param $businessId
     * @param $keyId
     * @return int
     */
    public static function getProjectInfo($businessId,$keyId)
    {
        $a=array("30", "31");
        if(in_array($businessId,$a))
        {
            return 0;
        }
        else
        {
            $projectId=0;
            switch ($businessId)
            {
                case 3:
                case 4:
                case 13:
                case 14:

                    $model=Contract::model()->findByPk($keyId);
                    $projectId= $model->project_id;
                    break;

                case 5:

                    $model=PayRequest::model()->findByPk($keyId);
                    $projectId= $model->project_id;
                    break;

                case 11:
                case 12:

                    $model=Invoice::model()->findByPk($keyId);
                    $projectId= $model->project_id;
                    break;

                default:
                    $projectId=$keyId;
                    break;
            }

            $p=Project::model()->findByPk($projectId);
            return $p;
        }
    }

    /**
     * 获取指定审核明细的相关附件
     * @param $detailId
     * @return array
     */
    public static function getAttachments($detailId)
    {
        if(!Utility::checkQueryId($detailId))
            return array();
        $sql="select * from t_check_attachment where detail_id=".$detailId." and status=1  order by type asc";
        $data=Utility::query($sql);
        $attachments=array();

        foreach($data as $v)
        {
            $attachments[$v["type"]][]=$v;
        }
        return $attachments;
    }

    /**
     * 获取不同业务的审核对象实例
     * @param $businessId
     * @param $objId
     * @return mixed
     */
    public static function getCheckObjectModel($businessId,$objId)
    {
        $check=CheckFactory::getInstance($businessId);
        return $check->getCheckObjectModel($objId);
    }

    /**
     * 获取审核状态，根据CheckDetail中的status和check_status判断
     * @param $row
     * @param $self
     * @return string
     */
    public static function showCheckStatus($row,$self)
    {
        if($row["status"]==0)
            return "待审核";
        else
        {
            return Map::$v['check_status'][$row["check_status"]];
        }
    }

    /**
     * 获取审核流节点模型
     * @param $flowId
     * @return array
     */
    public static function getFlowNodeModels($flowId)
    {
        $nodes=FlowNode::model()->findAll("flow_id=".$flowId."");
        $nodeList=array();
        $node=static ::getNextNodeModel($nodes,0);
        if(!empty($node))
            $nodeList[]=$node;
        while (!empty($node))
        {
            $node=static ::getNextNodeModel($nodes,$node->node_id);
            if(!empty($node))
                $nodeList[]=$node;
        }

        return $nodeList;
    }

    /**
     * 获取下级节点模型
     * @param $nodes
     * @param $nodeId
     * @return null
     */
    protected static function getNextNodeModel($nodes,$nodeId)
    {
        foreach ($nodes as $node)
        {
            if($node->previous_id==$nodeId)
                return $node;
        }
        return null;
    }

    /**
     * 获取审核对象当前待审核节点
     * @param $objId
     * @param $businessId
     * @return string
     */
    public static function getNowCheckNode($objId,$businessId)
    {
        $check=CheckItem::model()->with("node")->find("t.obj_id=".$objId." and t.business_id=".$businessId." and t.node_id>0");
        if(!empty($check))
        {
            return $check->node->node_name;
        }
        return "";
    }

    public static function getNowCheckNodeModel($objId,$businessId)
    {
        $check=CheckItem::model()->with("node")->find("t.obj_id=".$objId." and t.business_id=".$businessId." and t.node_id>0");
        if(!empty($check))
        {
            return $check->node;
        }
        return null;
    }

    public static function getNodeByNodeId($nodeId) {
        $flowNode = FlowNode::model()->find("node_id=".$nodeId);
        return $flowNode;
    }

    /**
     * @desc 获取审核对象当前审核驳回节点
     * @param $objId
     * @param $businessId
     * @return string
     */
    public static function getNowCheckBackNode($objId,$businessId)
    {
        $log = CheckLog::model()->find("obj_id=".$objId." and business_id in(".$businessId.") and node_id>0 and check_status=-1 and status=1 order by id desc");
        if(!empty($log)) {
            return $log->checkNode->node_name;
        }
        
        return "";
    }
}
