<?php
/**
 * Created by youyi000.
 * DateTime: 2017/9/5 14:46
 * Describe：
 */

class ContractAgentDetail extends BaseActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_contract_agent_detail';
    }

    public function relations() {
        return array(
            "project" => array(self::BELONGS_TO, "Project", "project_id"),//项目信息
            "contract" => array(self::BELONGS_TO, "Contract", "contract_id"),
            "goods" => array(self::BELONGS_TO, "Goods", "goods_id"),
            "agentGoods" => array(self::BELONGS_TO, "Goods", "goods_id"),
            "contractGoods" => array(self::BELONGS_TO, "ContractGoods", "goods_detail_id"),
        );
    }
}