<?php

/*
 * Created By: yu.li
 * DateTime:2018-5-29 17:00:56.
 * Desc:合同终止
 */

class ContractTerminateAttachment extends BaseBusinessActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "t_contract_terminate_attachment";
    }


}
