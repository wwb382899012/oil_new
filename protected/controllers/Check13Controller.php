<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/27 10:26
 * Describe：
 */

class Check13Controller extends BaseCheckController
{

    public $prefix="check13_";
    public $checkedStatement = "当前信息已审核";

    public function initRightCode()
    {
        $attr = $this->getSearch();
        $checkStatus = $attr["checkStatus"];
        $this->treeCode = $this->prefix.$checkStatus;
        $this->rightCode = $this->prefix;
    }

    public function pageInit()
    {
        parent::pageInit();
        $this->businessId =FlowService::BUSINESS_PAY_APPLICATION;
        $this->checkButtonStatus["reject"] = 0;
        $this->detailPartialModelName="apply";
        $this->extraMapName="pay_application_check_extra";
        $this->indexViewName="/check13/index";
        $this->checkViewName="/check13/check";
        $this->detailViewName="/check13/detail";
        $this->newUIPrefix = 'new_';
        $this->detailPartialFile = $this->newUIPrefix ? "/pay/{$this->newUIPrefix}detailBody" : '/pay/detailBody';
    }

    public function getMainSql($search)
    {
        $sql = "select {col} from t_check_detail a 
                left join t_pay_application b on a.obj_id = b.apply_id 
                left join t_corporation d on d.corporation_id = b.corporation_id 
                left join t_contract e on e.contract_id = b.contract_id 
                left join t_partner p on p.partner_id = e.partner_id 
                left join t_system_user s on s.user_id = b.create_user_id 
                left join t_check_item ci on ci.check_id = a.check_id and ci.node_id > 0 
                left join t_check_log cl on cl.detail_id=a.detail_id 
                left join t_flow_node n on n.node_id=ci.node_id
                " . $this->getWhereSql($search) . " and a.business_id = " . $this->businessId . "
                and ".AuthorizeService::getUserDataConditionString("b")."
                and (a.role_id = " . $this->nowUserRoleId . " or a.check_user_id=" . $this->nowUserId . ")";
        return $sql;
    }

    public function getFields()
    {
        $fields = "a.detail_id,a.check_id,a.obj_id,a.status,a.check_status,b.apply_id,b.status as obj_status,b.contract_id,b.amount,e.partner_id,b.corporation_id,
                   b.currency,d.name as corp_name,e.contract_code,p.name as partner_name, s.name as create_name,n.node_name";
        return $fields;
    }


    public function getCheckObjectModel($objId)
    {
        $model = PayApplication::model()->with("details","contract","details.payment","extra","factor")->findByPk($objId);
        if (!empty($model) && $model->status == PayApplication::STATUS_WITHDRAW)
            $this->checkedStatement = '当前付款申请已撤回';

        if (!empty($model) && $model->status == PayApplication::STATUS_TRASHED)
            $this->checkedStatement = '当前付款申请已作废';

        return $model;
    }

    public function getExtraItems($checkDetail)
    {
        if($checkDetail["role_id"]){
            $key=$checkDetail["role_id"];
        }else{
            $flowNode=FlowNode::model()->findByPk($checkDetail['check_node_id']);
            $key=explode(',',$flowNode['role_ids'])[0];
        }
        $items=Map::$v[$this->extraMapName][$key];
        if(empty($items))
            $items=array();
        return $items;
    }

    public function checkPendingWithdraw($detailId) {
        $sql = 'select b.status from t_check_detail a left join t_pay_application b on a.obj_id = b.apply_id where a.detail_id = ' . $detailId . ' and b.status=' . PayApplication::STATUS_WITHDRAW;
        $res = Utility::query($sql);
        if (Utility::isEmpty($res))
            return false;

        if (!empty($res[0]))
            $this->returnError('该条付款单已被发起人撤回', -2);

        return false;
    }
}