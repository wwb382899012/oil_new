<?php
$contract = ProjectService::getContractDetailModel($data['obj_id']);
$ret = array();
$ret['check'] = $data;
$ret['data'] = $data;
$ralative = null;
if ($contract->relative) {
   $relative = $contract->relative->getAttributesWithRelations();
}

$ret['map'] = getMaps();
$ret['creator'] = $contract->creator->name;
$updater = SystemUser::getUser($contract->update_user_id); 
$ret['updater'] = $updater['name'];
$ret['create_time'] = $contract->create_time;
$ret['update_time'] = $contract->update_time;
$ret['goods_unit_convert'] = ConstantMap::CONTRACT_GOODS_UNIT_CONVERT;

$ret['contract'] = $contract->getAttributesWithRelations();
if(!empty($ret['contract'])) {
    $ret['contract']['delivery_term'] = empty($ret['contract']['delivery_term']) ?'无':$ret['contract']['delivery_term'].'&nbsp;&nbsp;'.$this->map["contract_delivery_mode"][$ret['contract']['delivery_mode']];
    $ret['contract']['days'] = $ret['contract']['days'].'天';
}
$ret['contract']['buy_sell_desc'] = $contract->getContractType();
$ret['relative'] = ($relative) ? $relative : array();
if(!empty($ret['relative'])) {
    $ret['relative']['delivery_term'] = empty($ret['relative']['delivery_term']) ?'无':$ret['relative']['delivery_term'].'&nbsp;&nbsp;'.$this->map["contract_delivery_mode"][$ret['relative']['delivery_mode']];
    $ret['relative']['days'] = $ret['relative']['days'].'天';
}
$ret['user'] = SystemUser::getUser($contract["update_user_id"]);

$projectInfo = Project::model()->with("base", "attachments")->findByPk($ret['contract']['project_id']);
$ret['contract']['project'] = $projectInfo->getAttributesWithRelations();
if($ret['contract']['project']['attachments']) {
    foreach ($ret['contract']['project']['attachments'] as &$attachment) {
        $attachment['download_path'] = "/project/getFile/?id=" . $attachment['id'] . "&fileName=" . $attachment['name'];
    }
}

$ret['relative']['project'] = $ret['contract']['project'];
if($ret['relative']['project']['attachments']) {
    foreach ($ret['relative']['project']['attachments'] as &$attachment) {
        $attachment['download_path'] = "/project/getFile/?id=" . $attachment['id'] . "&fileName=" . $attachment['name'];
    }
}

$typeDesc = $this->map["project_type"][$contract->project['type']];
if (!empty($contract->project['base']['buy_sell_type'])) {
    $typeDesc .= '-' . $this->map["purchase_sale_order"][$contract->project['base']["buy_sell_type"]];
}

$ret['typeDesc'] = $typeDesc;

$ret['agentDetails'] = null;
if (!empty($contract->relative)) { // 双边合同
    $buy_contract = ($contract->type==ConstantMap::BUY_TYPE)?$contract:$contract->relative;
    if(!empty($buy_contract->agent)) {
        $ret['agentDetails'] = (array)$buy_contract->agentDetail;
    }
}

$flowNode = FlowService::getNodeByNodeId($data['flow_id']);
if (empty($flowNode->node_id)) {
    $this->returnError("查询流程节点失败");
}
$ret['pageTitle'] = $flowNode->node_name;
$checkLogs = FlowService::getCheckLog($contract->contract_id,"2, 3");
$checkLogs = array_reverse($checkLogs);
$ret['checkLogs'] = array_reverse($checkLogs);
$this->returnSuccess($ret);

function getMaps() {
    $mapKeys = array(
        'riskmanagement_checkitems_config',
        'project_type',
        'purchase_sale_order',
        'contract_config',
        'contract_status',
        'buy_agent_type',
        'currency',
        'price_type',
        'goods_unit',
        'pay_type',
        'proceed_type',
        'project_launch_attachment_type',
        'buy_sell_type',
        'buy_sell_desc_type',
        'agent_fee_pay_type'
    );
    return Map::getMaps($mapKeys);
}
