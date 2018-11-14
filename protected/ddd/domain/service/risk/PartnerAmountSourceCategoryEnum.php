<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/12 16:11
 * Describe：
 *  合作方额度变更类别枚举
 */

namespace ddd\domain\service\risk;


class PartnerAmountSourceCategoryEnum
{
    /**
     * 合同
     */
    const Contract_Submit=10;
    const Contract_Reject=11;
    const Contract_Done=19;
    /**
     * 收款
     */
    const Receipts=21;
    /**
     * 付款
     */
    const Payment=22;
    const Refund_In=23;
    const Refund_Out=24;
    const Pay_Claim=25;
    /**
     * 出入库
     */
    const Stock_In=31;
    const Stock_Out=32;
    const Stock_Refund_In=33;
    const Stock_Refund_Out=34;
    const Stock_In_Settle = 41;
    const Stock_Out_Settle = 42;

    /**
     * 合同结算
     */
    const Buy_Contract_Settle = 51;
    const Sell_Contract_Settle = 52;
}