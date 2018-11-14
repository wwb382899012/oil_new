<?php
$ret = array();
$ret['data'] = array(
    'detail_id' => $checkLog->detail_id
);
$ret['checkLog'] = $checkLog->getAttributesWithRelations();
$apply = $model->getAttributesWithRelations();

if ($apply['details']) {
    foreach ($apply['details'] as $key => &$detail) {
        if ($model->details[$key]->contract)
            $detail['contract'] = $model->details[$key]->contract->getAttributesWithRelations();

        if ($model->details[$key]->project)
            $detail['project'] = $model->details[$key]->project->getAttributesWithRelations();
    }
} else {
    $apply['details'] = null;
}
$ret['payDetails'] = $apply['details'];


$apply['subject'] = null;
if ($model->subject)
    $apply['subject'] = $model->subject->getAttributesWithRelations();

$apply['project'] = null;
if(!empty($model->project_id))
    $apply['project'] = $model->project->getAttributesWithRelations();

$apply['contract'] = null;
if(!empty($model->contract_id)) {
    $apply['contract'] = $model->contract->getAttributesWithRelations();
    if($model->contract->partner)
        $apply['contract']['partner'] = $model->contract->partner->getAttributesWithRelations();
}

$apply['corporation'] = $model->corporation->getAttributesWithRelations();

$apply['extra'] = null;
if ($model->extra) {
    $apply['extra'] = $model->extra->getAttributesWithRelations();
    $apply['extra']['items'] = $model->extra->items;
}
$checkLogs = FlowService::getCheckLogModel($model->apply_id, $this->businessId);
$checkLogs = array_reverse($checkLogs);
$newCheckLogs = array();
foreach ($checkLogs as &$history) {
    $newHistory = array();
    $choices = array();
    if(!empty($history['extra']) && is_array($history['extra']['items']) && count($history['extra']['items'])>0)
    {
        $counter = 1;
        foreach ($history['extra']['items'] as $key=>$item)
        {
            $choices[] = $counter . '. ' . $item['name'] .'('. $item['displayValue'].')' . ($item['remark']?$item['remark']:'') ;
            $counter ++;
        }
    }
    $newHistory['name'] = $history->user->name;
    $newHistory['node_name'] = $history->checkNode->node_name;
    $newHistory['check_time'] = $history->check_time;
    $newHistory['check_status'] = $history->check_status;
    $newHistory['remark'] = $history->remark;
    $newHistory['checkChoices'] = join("；", $choices);
    $newCheckLogs[] = $newHistory;
}
$ret['checkLogs'] = array_reverse($newCheckLogs);
$apply['extra'] = null;
if ($model->extra) {
    $apply['extra'] = $model->extra->getAttributesWithRelations();
    $apply['extra']['items'] = $model->extra->items;
}

$attachments=AttachmentService::getAttachments(Attachment::C_PAY_APPLICATION,$model->apply_id);
foreach ($attachments as &$attachmentGroup) {
    foreach ($attachmentGroup as &$attachment) {
        $attachment['download_path'] = "/pay/getFile?id=" . $attachment['id'] . "&fileName=" . $attachment['name'];
    }
}
$apply['attachments'] = $attachments;

$apply['contract_files'] = MobileService::getESignContractFiles($model->contract['files']);

$ret['apply'] = $apply;
$ret['contractPaiedAmount'] = '￥' . Utility::numberFormatFen2Yuan(PayService::getContractActualPaidAmount($apply['contract_id']));

$flowNode = FlowService::getNodeByNodeId($checkLog->node_id);
if (empty($flowNode->node_id)) {
    $this->returnError("查询流程节点失败");
}
$ret['pageTitle'] = $flowNode->node_name;

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
    'project_launch_attachment_type',
    'buy_sell_type',
    'buy_sell_desc_type',
    'agent_fee_pay_type',
    'transaction_checkitems_config',
    'pay_application_extra',
    'pay_application_extra',
    'isNor',
    'contract_config',
    'contract_file_attachment_type',
    'contract_category'
);
$ret['map'] = Map::getMaps($mapKeys);
$ret['checkHistory'] = $checkHistory;
$this->returnSuccess($ret);
