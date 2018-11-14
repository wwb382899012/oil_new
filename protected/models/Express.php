<?php

/**
 * Created by youyi000.
 * DateTime: 2016/6/21 15:14
 * Describe：
 */
class Express extends BaseActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_express_info';
    }

    /**
     * 保存
     * @return mixed
     */
    public function save($runValidation=true,$attributes=null)
    {
        
		$isInDbTrans=Utility::isInDbTrans();
        if(!$isInDbTrans)
        {
            $db = Mod::app()->db;
            $trans = $db->beginTransaction();
        }
        try {

            parent::save();

            if(!$isInDbTrans)
            {
                $trans->commit();
            }
            return true;
        } catch (Exception $e) {
            if(!$isInDbTrans)
            {
                try { $trans->rollback(); }catch(Exception $ee){}
                return $e->getMessage();
            }
            else
                throw $e;
        }
    }

}