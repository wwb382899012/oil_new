<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/27 10:26
 * Describe：
 */

class Check19Controller extends BaseCheckController
{

    public $prefix="check19_";
    public $checkedStatement = "当前信息已审核";

    public function initRightCode()
    {
//        $attr = $_REQUEST["search"];
        $attr = $this->getSearch();
        $checkStatus = $attr["checkStatus"];
        $this->treeCode = $this->prefix.$checkStatus;
        $this->rightCode = $this->prefix;
    }

    public function pageInit()
    {
        parent::pageInit();
        $this->businessId =19;
        $this->checkButtonStatus["reject"] = 0;
        $this->detailPartialModelName="apply";
        $this->indexViewName="/check19/index";
        $this->checkViewName="/check19/check";
        $this->detailViewName="/check19/detail";
        $this->newUIPrefix = 'new_';
        $this->detailPartialFile=$this->newUIPrefix ? "/pay/{$this->newUIPrefix}detailBody" : '/pay/detailBody';
    }

    public function getMainSql($search)
    {
        $sql = "select {col} from t_check_detail a
                
                left join t_pay_application b on a.obj_id = b.apply_id 
                left join t_corporation c on c.corporation_id=b.corporation_id 
                left join t_finance_subject fs on fs.subject_id=b.subject_id 
                left join t_contract co on co.contract_id=b.contract_id 
                left join t_pay_application_extra e on b.apply_id=e.apply_id
                left join t_system_user su on su.user_id=e.create_user_id
                left join t_check_item ci on ci.check_id = a.check_id and ci.node_id > 0 
                left join t_flow_node n on n.node_id=ci.node_id
                " . $this->getWhereSql($search) . " and a.business_id = " . $this->businessId . "
                and ".AuthorizeService::getUserDataConditionString("b")."
                and (a.role_id = " . $this->nowUserRoleId . " or a.check_user_id=" . $this->nowUserId . ")";

        /*if(Utility::getNowUserId()==2)
            var_dump($sql);*/
        return $sql;
    }

    public function getFields()
    {
        $fields = "a.detail_id,a.check_id,a.obj_id,a.status,a.check_status,b.currency,e.stop_code,b.apply_id,b.status as obj_status,b.contract_id,b.amount,b.type,b.payee,
                   (b.amount - b.amount_paid) as amount_stop,e.stop_code,b.corporation_id,c.name as corp_name,n.node_name";
        return $fields;
    }


    public function getCheckObjectModel($objId)
    {
        return PayApplication::model()->with("details","contract","details.payment","extra","factor")->findByPk($objId);
    }

    public function getExtraItems($checkDetail)
    {
        $items=Map::$v[$this->extraMapName][$checkDetail["role_id"]];
        if(empty($items))
            $items=array();
        return $items;
    }


}