<?php

/**
 * Desc: 合作方货票款信息服务
 * User: susiehuang
 * Date: 2018/3/30 0030
 * Time: 17:57
 */
class PartnerStatService
{
    public static function updatePartnerStat($partnerId = 0)
    {
        $query = '';
        if (!empty($partnerId))
        {
            $query .= ' and partner_id=' . $partnerId;
        }

        $resource = Partner::model()->findAll('status = ' . Partner::STATUS_PASS . $query . ' order by update_time desc');
        if (Utility::isNotEmpty($resource))
        {
            foreach ($resource as $partner)
            {
                $partnerStat = PartnerStat::model()->findByPk($partner->partner_id);
                if (empty($partnerStat))
                {
                    $partnerStat = new PartnerStat();
                    $partnerStat->partner_id = $partner->partner_id;
                    $partnerStat->create_user_id = Utility::getNowUserId();
                    $partnerStat->create_time = Utility::getDateTime();
                }
                $partnerAmountDetail = PartnerService::getPartnerAmountDetail($partner->partner_id);
                $partnerStat->amount_in = $partnerAmountDetail['received_amount'];
                $partnerStat->amount_out = $partnerAmountDetail['paid_amount'];
                $partnerStat->goods_in_amount = $partnerAmountDetail['stock_in_amount'];
                $partnerStat->goods_in_settle_amount = $partnerAmountDetail['stock_in_settle_amount'];
                $partnerStat->goods_in_unsettled_amount = $partnerAmountDetail['goods_in_unsettled_amount'];
                $partnerStat->goods_out_amount = $partnerAmountDetail['stock_out_amount'];
                $partnerStat->goods_out_settle_amount = $partnerAmountDetail['stock_out_settle_amount'];
                $partnerStat->goods_out_unsettled_amount = $partnerAmountDetail['goods_out_unsettled_amount'];
                $partnerStat->amount_invoice_out = $partnerAmountDetail['output_invoice_amount'];
                $partnerStat->amount_invoice_in = $partnerAmountDetail['input_invoice_amount'];
                $partnerStat->update_user_id = Utility::getNowUserId();
                $partnerStat->update_time = Utility::getDateTime();
                $partnerStat->save();
            }
        }
    }
}