<?php
/**
 * Created by youyi000.
 * DateTime: 2018/1/25 15:14
 * Describe：
 *      合同生命周期相关事件
 */

class ContractLifeService
{
    /**
     * 合同主文本审核通过后
     * @param $contractId
     * @param CActiveRecord|null $contract
     * @return bool
     */
    public static function afterFileChecked($contractId,CActiveRecord $contract=null)
    {
        if(empty($contract))
        {
            if(empty($contractId))
                return false;
            $contract=Contract::model()->findByPk($contractId);
        }
        if(empty($contract))
            return false;
        if($contract->status<Contract::STATUS_FILE_UPLOAD)
        {
            $contract->status=Contract::STATUS_FILE_UPLOAD;
            $contract->status_time=new CDbExpression("now()");
            return $contract->update(array("status","status_time"));
        }
        //$contract->update(array("status","status_time","update_time","update_time_id"));
        return true;
    }


}