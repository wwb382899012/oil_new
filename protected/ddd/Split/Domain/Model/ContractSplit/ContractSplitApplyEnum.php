<?php
/**
 * Desc:
 * User: susiehuang
 * Date: 2018/5/29 0029
 * Time: 16:17
 */

namespace ddd\Split\Domain\Model\ContractSplit;


use ddd\Common\BaseEnum;

class ContractSplitApplyEnum extends BaseEnum{
    /**
     * @desc 合同拆分申请状态
     */
    const STATUS_TRASH = -5; //作废
    const STATUS_BACK = - 1; //驳回
    const STATUS_NEW = 0; //保存
    const STATUS_SUBMIT = 1; //提交待审核
    const STATUS_PASS = 10; //审核通过
    const STATUS_CAN_STOCK_SPLIT = 20; //可以进行出入库拆分

    /**
     * @desc 合同类型
     */
    const CONTRACT_TYPE_BUY = 1; //采购
    const CONTRACT_TYPE_SELL = 2; //销售

    /**
     * @desc 出入库类型
     */
    const STOCK_TYPE_IN = 1; //入库拆分
    const STOCK_TYPE_OUT = 2; //出库拆分

    /**
     * 未勾选平移
     */
    const STATUS_UN_SPLIT = 0;

    /**
     * 已勾选平移
     */
    const STATUS_SPLIT = 1;

}