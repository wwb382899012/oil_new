<?php

class RepairCommand extends CConsoleCommand {
    /**
     * @desc 修复合同表中交易金额
     */
    public function actionRepairContractAmount() {
        RepairService::repairContractAmount();
    }

    /**
     * @desc 修复合同组表数据
     */
    public function actionRepairContractGroup() {
        RepairService::repairContractGroup();
    }

    /**
     * @desc 合同条款内容中增加display_value字段
     */
    public function actionRepairContractExtra() {
        RepairService::repairContractExtra();
    }


    // 修正发货单的amount cny数据
    public function actionUpdateDeliveryAmountCny() {
        $deliveries = DeliverySettlementDetail::model()->with('contract')->findAll();

        foreach ($deliveries as $delivery) {
            $delivery->amount_cny = $delivery->amount * $delivery->contract->exchange_rate;
            $delivery->update(array('amount_cny'));
        }
    }

    /**
     * @desc 修复合作方数据
     */
    public function actionRepairPartnerApply() {
        $resource = PartnerApply::model()->findAll('partner_id > 200 and status = 99');
        if (Utility::isNotEmpty($resource)) {
            foreach ($resource as $row) {
                $partner = Partner::model()->findByPk($row['partner_id']);
                if (empty($partner)) {
                    $partner = new Partner();
                }
                $partner->setAttributes($row->attributes, false);
                $partner->save();
            }
        }
    }

    /**
     * @desc 修复保理单保理对接编号&资金对接编号
     */
    public function actionRepairFactorCode() {
        RepairService::repairFactorInfo();
    }

    /**
     * @desc 合同删除
     * @param int $contractId
     */
    public function actionDeleteContract($contractId) {
        if (Utility::checkQueryId($contractId) && $contractId > 0){
            RepairService::deleteContract($contractId);
        }
    }

    /**
     * @desc 更新合同状态（合同上传相关）
     */
    public function actionUpdateContractStatus() {
        RepairService::updateContractStatusByFile();
    }

    /**
     * @desc 修复付款计划中实付金额（将认领金额加上）
     */
    public function actionRepairPaymentPlanAmountPaid() {
        RepairService::repairPaymentPlanAmountPaid();
    }

    /**
     * @desc 修复合作方额度数据
     * @param int $partnerId
     */
    public function actionRepairPartnerAmount($partnerId=0)
    {
        RepairService::repairPartnerAmount($partnerId);
    }

    /**
     * @desc 更新合作方货票款信息数据
     * @param int $partnerId
     */
    public function actionUpdatePartnerStat($partnerId=0)
    {
        PartnerStatService::updatePartnerStat($partnerId);
    }

    /**
     * @desc 修复合作方初始化额度数据
     * @param int $partnerId
     * @param int $type          #额度类型 1:合同额度  2:实际额度
     */
    public function actionRepairInitPartnerAmount($partnerId=0, $type=0)
    {
        RepairService::repairPartnerInitUsedAmount($partnerId, $type);
    }

    /**
     * @desc 修复合作方额度变更数据，增加直调出库额度变更，仅执行一次！！！
     */
    public function actionAddPartnerAmountLogDirectOut()
    {
//        RepairService::repairPartnerAmountForDirectStockOut();
    }

    /**
     * @desc 更新付款申请单付款中状态
     * @param int $applyId
     */
    public function actionUpdatePayApplicationInPaymentStatus($applyId = 0)
    {
        RepairService::updatePayApplicationInPaymentStatus($applyId);
    }
}
