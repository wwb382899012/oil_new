<?php

/**
 * Created by vector.
 * DateTime: 2018/3/26 11:33
 * Describe：提单服务
 */
namespace ddd\domain\service\stock;

use ddd\Common\Domain\BaseService;
use ddd\domain\entity\stock\LadingBill;
use ddd\repository\stock\StockInRepository;

class LadingBillService extends BaseService
{
	/**
     * @desc 提单(入库通知单)是否可以结算
     * @param LadingBill $ladingBill
     * @return boolean
     */
    public function isCanSettle(LadingBill $ladingBill)
	{
        $isBool = $this->isStockInFinish($ladingBill);

        if($isBool === true){
            if($ladingBill->status>=\StockNotice::STATUS_SETTLE_SUBMIT || $ladingBill->status<\StockNotice::STATUS_SUBMIT)
                $isBool = "当前状态下的入库通知单（".$ladingBill->code."）不能发起结算！";
        }
		
        return $isBool;

	}

    /**
     * @desc 所有入库单是否已经完成
     * @param LadingBill $ladingBill
     * @return boolean
     */
    public function isStockInFinish(LadingBill $ladingBill)
    {
        if(empty($ladingBill))
            return "参数有误，不能发起结算";

        $stockIns = StockInRepository::repository()->findAllByBatchId($ladingBill->id);
        if (!empty($stockIns)) {
            $num = 0;
            foreach ($stockIns as $key => $row) {
                if($row->status!=\StockIn::STATUS_PASS) {
                    if($row->status==\StockIn::STATUS_INVALIDITY)
                        $num += 1;
                    else
                        return "入库通知单（".$ladingBill->code."）下入库单（".$row->code."）未审核通过，不能发起结算！";
                }
            }

            if(count($stockIns)==$num)
                return "入库通知单（".$ladingBill->code."）下所有入库单都是作废状态，不能发起结算！";

            return true;
        }

        return "入库通知单（".$ladingBill->code."）下没有入库单，不能发起结算！";
    }




}