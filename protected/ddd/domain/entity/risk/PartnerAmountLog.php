<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/12 9:49
 * Describe：
 */

namespace ddd\domain\entity\risk;


abstract class PartnerAmountLog extends RiskAmountLog
{
    /**
     * @var      int
     */
    public $partner_id;

    /**
     * 自定义的属性
     * attribute name => attribute value
     * @return array
     */
    public function customAttributes()
    {
        $fields = array('log_id', 'partner_id', 'type', 'method', 'category', 'relation_id', 'amount', 'amount_total', 'corporation_id', 'project_id', 'contract_id', 'remark', 'create_user_id', 'create_time');
        $attrs = array();
        foreach ($fields as $f)
        {
            $attrs[$f] = null;
        }

        return $attrs;
    }

    public function customAttributeNames()
    {
        return array('log_id', 'partner_id', 'type', 'method', 'category', 'relation_id', 'amount', 'amount_total', 'corporation_id', 'project_id', 'contract_id', 'remark', 'create_user_id', 'create_time');
    }
}