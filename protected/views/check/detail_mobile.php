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


$apply['subject'] = null;
if ($model->subject)
    $apply['subject'] = $model->subject->getAttributesWithRelations();

$apply['project'] = null;
if(!empty($model->project_id))
    $apply['project'] = $model->project->getAttributesWithRelations();

$apply['contract'] = null;
if(!empty($model->contract_id)) {
    $apply['contract'] = $model->contract->getAttributesWithRelations();
    $apply['contract']['partner'] = $model->contract->partner->getAttributesWithRelations();
}

$apply['corporation'] = $model->corporation->getAttributesWithRelations();

$apply['extra'] = null;
if ($model->extra) {
    $apply['extra'] = $model->extra->getAttributesWithRelations();
    $apply['extra']['items'] = $model->extra->items;
}

$checkHistory = FlowService::getCheckLogModel($checkLog['obj_id'], $this->businessId);
foreach ($checkHistory as &$history) {
    $choices = array();
    if(!empty($history['extra']) && is_array($history['extra']['items']) && count($history['extra']['items'])>0)
    {
        foreach ($history['extra']['items'] as $key=>$item)
        {
            $choices[] = $item['name'] .'('. $item['displayValue'].')';
        }
    }
    $history = $history->getAttributesWithRelations();

    $history['checkChoices'] = join(";", $choices);
}

$apply['extra'] = null;
if ($model->extra) {
    $apply['extra'] = $model->extra->getAttributesWithRelations();
    $apply['extra']['items'] = $model->extra->items;
}

$attachments=AttachmentService::getAttachments(Attachment::C_PAY_APPLICATION,$model->apply_id);
$apply['attachments'] = $attachments;

$apply['contract_files'] = array();
if (!empty($model->project_id))
    $apply['contract_files'] = ContractService::getAllContractFile($model->project_id);

$ret['apply'] = $apply;

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
    'contract_file_attachment_type'
);
$ret['map'] = Map::getMaps($mapKeys);
$ret['checkHistory'] = $checkHistory;
$this->returnSuccess($ret);
