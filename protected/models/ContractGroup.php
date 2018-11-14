<?php
/**
 * Created by youyi000.
 * DateTime: 2017/12/1 11:45
 * Describe：
 */

class ContractGroup extends BaseBusinessActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "t_contract_group";
    }

    /**
     * 根据合同id查找对应的信息
     * @param $contractId
     * @return CActiveRecord
     */
    public function findByContractId($contractId)
    {
        return parent::find("contract_id=".$contractId);
    }

    /**
     * 查询项目的主合同信息
     * @param $projectId
     * @return CActiveRecord
     */
    public function findMainByProjectId($projectId)
    {
        return parent::find("project_id=".$projectId." and is_main=1");
    }


    public function relations()
    {
        return array(
            "project" => array(self::BELONGS_TO, "Project", "project_id"),//项目信息
            "corporation" => array(self::BELONGS_TO, "Corporation", "corporation_id"),
            'contract' => array(self::BELONGS_TO, 'Contract', array('down_contract_id'=>'contract_id')),
            'downContract' => array(self::BELONGS_TO, 'Contract', array('down_contract_id'=>'partner_id')),
            'upPartner' => array(self::BELONGS_TO, 'Partner', array('up_partner_id'=>'partner_id')),
            'downPartner' => array(self::BELONGS_TO, 'Partner', array('down_partner_id'=>'partner_id')),
        );
    }


}