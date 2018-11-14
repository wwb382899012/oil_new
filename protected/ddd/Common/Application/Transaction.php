<?php
/**
 * Created by youyi000.
 * DateTime: 2018/5/28 11:48
 * Describe：
 */

namespace ddd\Common\Application;


trait Transaction
{
    /**
     * @var \CDbTransaction
     */
    protected $trans;

    /**
     * 是否在外部事务中
     * @var bool
     */
    protected $isInOutTrans=false;

    private $transKey="";


    /**
     * 是否在事务中
     * @param int $dbType
     * @return bool
     */
    function isInTransaction($dbType = 0)
    {
        $db = \Utility::getDb($dbType);
        $res = $db->getCurrentTransaction();

        if (!empty($res))
            $this->isInOutTrans=true;
        else
            $this->isInOutTrans=false;
        return $this->isInOutTrans;
    }

    /**
     * 开启事务
     * @param int $dbType
     * @return bool
     */
    function beginTransaction($dbType = 0)
    {
        if(!$this->isInTransaction($dbType))
        {
            $this->trans=\Utility::beginTransaction($dbType);
            return true;
        }
        else
            return false;
    }

    /**
     * 提交事务
     * @param bool $transStatus
     * @throws \Exception
     */
    function commitTransaction($transStatus=true)
    {
        if($transStatus && !empty($this->trans))
            $this->trans->commit();
    }

    /**
     * 回滚事务
     * @param bool $transStatus
     */
    function rollbackTransaction($transStatus=true)
    {
        if($transStatus && !empty($this->trans))
        {
            try { $this->trans->rollback(); }catch(\Exception $e){}
        }
    }
}