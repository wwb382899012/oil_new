<?php

/**
 * Desc: 项目发起信息
 * User: susiehuang
 * Date: 2017/8/29 0031
 * Time: 10:05
 */
class  ProjectBase extends BaseActiveRecord {
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return "t_project_base";
    }

    public function relations()
    {
        return array(
            "project" => array(self::BELONGS_TO, "Project", "project_id"),//项目信息
            "corporation" => array(self::BELONGS_TO, "Corporation", "corporation_id"),
            'up_partner' => array(self::BELONGS_TO, 'Partner', array('up_partner_id'=>'partner_id')),
            'down_partner' => array(self::BELONGS_TO, 'Partner', array('down_partner_id'=>'partner_id')),
            'agent' => array(self::BELONGS_TO, 'Partner',array('agent_id'=>'partner_id')),//代理商
            "goods" => array(self::HAS_MANY, "ProjectBaseGoods", "base_id"),//商品交易信息
        );
    }

    protected function beforeDelete()
    {
        $res= parent::beforeDelete(); // TODO: Change the auto generated stub

        if(!$res)
            return false;

        foreach ($this->goods as $model)
        {
            $res=$model->delete();
            if(!$res)
                return false;
        }

        return true;
    }
}