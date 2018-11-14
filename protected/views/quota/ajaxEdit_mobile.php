<?php
$ret = array();
if (!empty($contract->relative))
    $ret['relative'] = $contract->relative->getAttributesWithRelations();
else 
	$ret['relative'] = null;
$ret['contract'] = $contract->getAttributesWithRelations();
$ret['upManagers'] = $upManagers;
$ret['downManagers'] = $downManagers;
$upPartnerOnly = false;
if(empty($contract->relative)) {
    // 单边合同
    $upPartnerOnly=($contract->type==ConstantMap::BUY_TYPE)||($contract->type==ConstantMap::CONTRACT_CATEGORY_SUB_BUY);
}
$ret['upPartnerOnly'] = $upPartnerOnly;

$this->returnSuccess($ret);