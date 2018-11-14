<?php

$ret = array();
$ret['contract'] = $contract->getAttributesWithRelations();
$ret['checkHistory'] = $checkLogs;


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
    'transaction_checkitems_config'
);
$ret['map'] = Map::getMaps($mapKeys);
$this->returnSuccess($ret);