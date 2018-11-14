<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/2 16:03
 * Describe：
 */

namespace ddd\repository\contract;


use ddd\Common\Domain\BaseEntity;
use ddd\domain\entity\contract\ContractStat;
use ddd\domain\iRepository\contract\IContractStatRepository;
use ddd\infrastructure\error\ZException;
use ddd\Common\Repository\EntityRepository;

class ContractStatRepository extends EntityRepository implements IContractStatRepository
{
    public function getNewEntity()
    {
        // TODO: Implement getNewEntity() method.
        return new ContractStat();
    }

    public function getActiveRecordClassName()
    {
        // TODO: Implement getActiveRecordClassName() method.
        return "ContractStat";
    }



    /**
     * @param ContractStat $contractStat
     * @param $amount
     * @throws ZException
     */
    public function addAndSaveAmountOut(ContractStat $contractStat,$amount)
    {
        try
        {
            $this->addAndSaveAmount($contractStat->getId(),"amount_out",$amount);
        }
        catch (\Exception $e)
        {
            throw new ZException("增加付出金额失败");
        }
    }

    /**
     * @param ContractStat $contractStat
     * @param $amount
     * @throws ZException
     */
    public function addAndSaveAmountIn(ContractStat $contractStat,$amount)
    {
        try
        {
            $this->addAndSaveAmount($contractStat->getId(),"amount_in",$amount);
        }
        catch (\Exception $e)
        {
            throw new ZException("增加收到金额失败");
        }
    }

    /**
     * @param ContractStat $contractStat
     * @param $amount
     * @throws ZException
     */
    public function addAndSaveGoodsInAmount(ContractStat $contractStat,$amount)
    {
        try
        {
            $this->addAndSaveAmount($contractStat->getId(),"goods_in_amount",$amount);
        }
        catch (\Exception $e)
        {
            throw new ZException("增加入库商品金额失败");
        }
    }

    /**
     * @param ContractStat $contractStat
     * @param $amount
     * @throws ZException
     */
    public function addAndSaveGoodsOutAmount(ContractStat $contractStat,$amount)
    {
        try
        {
            $this->addAndSaveAmount($contractStat->getId(),"goods_out_amount",$amount);
        }
        catch (\Exception $e)
        {
            throw new ZException("增加出库商品金额失败");
        }
    }

    /**
     * @param ContractStat $contractStat
     * @param $amount
     * @throws ZException
     */
    public function addAndSaveInvoiceInAmount(ContractStat $contractStat,$amount)
    {
        try
        {
            $this->addAndSaveAmount($contractStat->getId(),"invoice_in_amount",$amount);
        }
        catch (\Exception $e)
        {
            throw new ZException("增加收票金额失败");
        }
    }

    /**
     * @param ContractStat $contractStat
     * @param $amount
     * @throws ZException
     */
    public function addAndSaveInvoiceOutAmount(ContractStat $contractStat,$amount)
    {
        try
        {
            $this->addAndSaveAmount($contractStat->getId(),"invoice_out_amount",$amount);
        }
        catch (\Exception $e)
        {
            throw new ZException("增加开票金额失败");
        }
    }


    /**
     * 更新指定金额
     * @param $id
     * @param $amountName
     * @param $amount
     * @throws ZException
     */
    protected function addAndSaveAmount($id,$amountName,$amount)
    {
        $rows=\ContractStat::model()->updateByPk($id
            ,array(
                  $amountName=>new \CDbExpression($amountName."+".$amount),
                 "update_time"=>new \CDbExpression("now()")
             )
        );
        if($rows!==1)
            throw new ZException("更新金额失败");
    }


}