<?php

/**
 * Desc: 数据修复服务
 * User: susiehuang
 * Date: 2017/12/5 0009
 * Time: 15:03
 */
class RepairService
{

    /**
     * @desc 修复合同表t_contract中金额
     */
    public static function repairContractAmount()
    {
        $contracts = Contract::model()->findAll();
        if (Utility::isNotEmpty($contracts))
        {
            foreach ($contracts as $key => $row)
            {
                $contract = Contract::model()->findByPk($row['contract_id']);
                $sql = 'select sum(amount) as total_amount, sum(amount_cny) as total_amount_cny from t_contract_goods where contract_id = ' . $row['contract_id'];
                $res = Utility::query($sql);
                if (Utility::isNotEmpty($res))
                {
                    $contract->amount_cny = $res[0]['total_amount_cny'];
                    $contract->amount = $res[0]['total_amount'];
                    $contract->save();
                }
                unset($contract);
            }
        }
    }

    /**
     * @desc 修复合同组t_contract_group表数据
     */
    public static function repairContractGroup()
    {
        $projects = Project::model()->findAll('status >= :status', array('status' => Project::STATUS_SUBMIT));
        if (Utility::isNotEmpty($projects))
        {
            foreach ($projects as $project)
            {
                ContractService::initContractGroupByProject($project);
            }
        }
        $mainContracts = Contract::model()->findAll('is_main = 1 group by project_id');
        $subContracts = Contract::model()->findAll('is_main = 0');
        $contracts = array_merge_recursive($mainContracts, $subContracts);
        if (Utility::isNotEmpty($contracts))
        {
            foreach ($contracts as $contract)
            {
                if ($contract->is_main == 1)
                {
                    $contract->relative = ContractService::getRelatedContract($contract->contract_id);
                }
                ContractService::generateContractGroup($contract);
            }
        }
    }

    /**
     * @desc 合同额外信息t_contract_extra中新增display_value字段
     */
    public static function repairContractExtra()
    {
        $resource = ContractExtra::model()->with('contract')->findAll();
        if (Utility::isNotEmpty($resource))
        {
            foreach ($resource as $row)
            {
                $itms = $row->items;
                if (Utility::isNotEmpty($itms))
                {
                    $extra = Map::$v['contract_config'][$row->contract->type][$row->contract->category]['extra'];
                    foreach ($itms as $k => $val)
                    {
                        if (!empty($val['display_value']))
                        {
                            continue;
                        }
                        if (Utility::isNotEmpty($extra))
                        {
                            foreach ($extra as $v)
                            {
                                $s = '';
                                if ($val['key'] == $v['key'])
                                {
                                    if (in_array($v['type'], array('koSelect', 'koMultipleSelect')))
                                    {
                                        $values = is_string($val['value']) ? explode(',', $val['value']) : (is_array($val['value']) ? $val['value'] : array());
                                        if (Utility::isNotEmpty($values))
                                        {
                                            foreach ($values as $p)
                                            {
                                                if (!empty($s))
                                                {
                                                    $s .= ',';
                                                }
                                                $s .= $v['items'][$p]['name'];
                                            }
                                        }
                                    } else
                                    {
                                        $s .= $val['value'];
                                    }
                                    $itms[$k]['display_value'] = $s;
                                }
                            }
                        }
                    }
                }
                $res = $row->updateByPk($row['contract_id'], array("content" => json_encode($itms)));
                if ($res != 1)
                {
                    Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' contract_id:' . $row['contract_id'] . ', content:' . json_encode($itms) . ', contractExtra update error:' . $res . '', CLogger::LEVEL_ERROR);
                }
            }
        }
    }

    public static function repairFactorInfo()
    {
        $resource = Factor::model()->findAll();
        if (Utility::isNotEmpty($resource))
        {
            foreach ($resource as $key => $row)
            {
                if ($row->payApply->is_factoring)
                {
                    if (empty($row->contract_code) || empty($row->contract_code_fund))
                    {
                        $codeInfo = CodeService::getFactoringCode($row->corporation_id, date('ym', strtotime($row->create_time)));
                        if ($codeInfo['code'] == ConstantMap::INVALID)
                        {
                            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' get factor code error:' . $codeInfo['msg'], CLogger::LEVEL_ERROR);
                            continue;
                        }
                        $row->contract_code = $codeInfo['data'];
                        $row->contract_code_fund = CodeService::getFactoringFundCode(date('ym', strtotime($row->create_time)));
                    }
                } else
                {
                    $row->remark = '不对接保理';
                }

                $trans = Utility::beginTransaction();
                try
                {
                    $row->save();

                    $factorFundCode = FactorFundCode::model()->findByCode($row->contract_code_fund);
                    if (empty($factorFundCode))
                    {
                        $factorFundCode = new FactorFundCode();
                        $factorFundCode->code = $row->contract_code_fund;
                        $factorFundCode->type = FactorFundCode::TYPE_INTERNAL;
                    }
                    $factorFundCode->save();

                    $trans->commit();
                } catch (Exception $e)
                {
                    try
                    {
                        $trans->rollback();
                    } catch (Exception $ee)
                    {
                    }
                    Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' save factoring and factoring fund code trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);
                    continue;
                }
            }
        }
    }

    public static function deleteContract($contractId)
    {
        if (Utility::checkQueryId($contractId) && $contractId > 0)
        {
            $contract = Contract::model()->findByPk($contractId);
            if (!empty($contract))
            {
                $contract->delete();
                CheckDetail::model()->deleteAll('business_id=2 and obj_id=' . $contractId);
                CheckDetail::model()->deleteAll('business_id=3 and obj_id=' . $contractId);
                CheckDetail::model()->deleteAll('business_id=4 and obj_id=' . $contractId);
            }
        }
    }

    /**
     * @desc 更新合同状态（合同上传相关状态）
     */
    public static function updateContractStatusByFile()
    {
        $contractFiles = ContractFile::model()->findAll('is_main = 1');
        if (Utility::isNotEmpty($contractFiles))
        {
            foreach ($contractFiles as $key => $row)
            {
                if ($row->contract->status >= Contract::STATUS_BUSINESS_CHECKED)
                {
                    ContractFileService::updateContractStatusByFileId($row->file_id);
                }
            }
        }
    }

    /**
     * @desc 修复付款计划中实付金额（将认领金额加上）
     */
    public static function repairPaymentPlanAmountPaid()
    {
        $sql = 'select a.plan_id, IFNULL(sum(a.amount),0) as claim_amount 
                from t_pay_claim_detail a 
                left join t_pay_claim b on b.claim_id=a.claim_id 
                where b.status>=1 group by a.plan_id';
        $data = Utility::query($sql);
        if (Utility::isNotEmpty($data))
        {
            foreach ($data as $row)
            {
                PaymentPlanService::updatePaidAmount($row['plan_id'], $row['claim_amount']);
            }
        }
    }

    /**
     * @desc 修复合作方额度数据
     * @param $partnerId
     */
    public static function repairPartnerAmount($partnerId = 0)
    {
        $condition = 'status=' . Partner::STATUS_PASS;
        if (!empty($partnerId))
        {
            $condition .= ' and partner_id=' . $partnerId;
        }
        $partners = Partner::model()->findAll($condition);
        if (Utility::isNotEmpty($partners))
        {
            foreach ($partners as $partner)
            {
                $contract_amount = 0;
                $used_amount = 0;

                //合同提交合同额度
                $sql = 'select ifnull(sum(amount_cny),0) as total_amount from t_contract where partner_id=' . $partner->partner_id . ' and status>=' . Contract::STATUS_SUBMIT . ' and status<>' . Contract::STATUS_BUSINESS_REJECT;
                $data = Utility::query($sql);
                if (Utility::isNotEmpty($data))
                {
                    $contract_amount += $data[0]['total_amount'];
                }

                $partnerAmountDetail = PartnerService::getPartnerAmountDetail($partner->partner_id);
                $contract_amount -= $partnerAmountDetail['received_amount'];
                $contract_amount -= $partnerAmountDetail['stock_in_amount'];
                $contract_amount += $partnerAmountDetail['in_settle_diff_amount'];
                $used_amount += $partnerAmountDetail['paid_amount'];
                $used_amount -= $partnerAmountDetail['received_amount'];
                $used_amount -= $partnerAmountDetail['stock_in_amount'];
                $used_amount += $partnerAmountDetail['stock_out_amount'];
                $used_amount += $partnerAmountDetail['in_settle_diff_amount'];
                $used_amount += $partnerAmountDetail['out_settle_diff_amount'];

                PartnerAmount::model()->updateAll(
                    array('used_amount' => $contract_amount),
                    'partner_id=:partnerId and type=:type',
                    array(
                        'partnerId' => $partner->partner_id,
                        'type' => PartnerAmount::TYPE_CONTRACT
                    )
                );
                PartnerAmount::model()->updateAll(
                    array('used_amount' => $used_amount),
                    'partner_id=:partnerId and type=:type',
                    array(
                        'partnerId' => $partner->partner_id,
                        'type' => PartnerAmount::TYPE_USED
                    )
                );
            }
        }
    }

    /**
     * @desc 修复合作方初始化额度，该方法主要用于修复合作方初始化额度，加上出入库差额、出入库结算差额，仅执行一次！！！
     * @param $partnerId
     */
    /*public static function repairInitPartnerAmoun($partnerId = 0)
    {
        $condition = 'status=' . Partner::STATUS_PASS;
        if (!empty($partnerId))
        {
            $condition .= ' and partner_id=' . $partnerId;
        }
        $partners = Partner::model()->findAll($condition);
        if (Utility::isNotEmpty($partners))
        {
            foreach ($partners as $partner)
            {
                $in_diff_amount = 0;
                $out_diff_amount = 0;
                $in_settle_diff_amount = 0;
                $out_settle_diff_amount = 0;
                //合作方下所有合同
                $contracts = Contract::model()->findAll('partner_id=:partnerId and status>=:status1 and status<>:status2', array('partnerId' => $partner->partner_id, 'status1' => Contract::STATUS_SUBMIT, 'status2' => Contract::STATUS_BUSINESS_REJECT));
                if (Utility::isNotEmpty($contracts))
                {
                    foreach ($contracts as $contract)
                    {
                        if ($contract->type == ConstantMap::BUY_TYPE)
                        { //采购合同
                            $in_diff_amount += ContractService::getContractStockInAmount($contract->contract_id) - ContractService::oldGetContractStockInAmount($contract->contract_id);
                            $in_settle_diff_amount += ContractService::getTradeGoodsInSettleDiffAmount($contract->contract_id, $contract);
                        } else
                        { //销售合同
                            $out_diff_amount += ContractService::getContractStockOutAmount($contract->contract_id) - ContractService::oldGetContractStockOutAmount($contract->contract_id);
                            $out_settle_diff_amount += ContractService::getTradeGoodsOutSettleDiffAmount($contract->contract_id, $contract);
                        }
                    }
                }
                $initContractAmountDiff = $in_settle_diff_amount - $in_diff_amount;
                $partnerContractAmount = PartnerAmount::model()->find('partner_id=:partnerId and type=:type', array('partnerId' => $partner->partner_id, 'type' => PartnerAmount::TYPE_CONTRACT));
                if (!empty($partnerContractAmount))
                {
                    if ($partnerContractAmount->remark !== null)
                    {
                        $partnerContractAmount->remark += $initContractAmountDiff;
                    }
                    $partnerContractAmount->save();
                }

                $initUsedAmountDiff = $in_settle_diff_amount + $out_settle_diff_amount - $in_diff_amount + $out_diff_amount;
                $partnerUsedAmount = PartnerAmount::model()->find('partner_id=:partnerId and type=:type', array('partnerId' => $partner->partner_id, 'type' => PartnerAmount::TYPE_USED));
                if (!empty($partnerUsedAmount))
                {
                    if ($partnerUsedAmount->remark !== null)
                    {
                        $partnerUsedAmount->remark += $initUsedAmountDiff;
                    }
                    $partnerUsedAmount->save();
                }
            }
        }
    }*/

    /**
     * @desc 修复合作方额度操作流水，增加直调发货单审批通过直接出库 额度变更
     */
    public static function repairPartnerAmountForDirectStockOut()
    {
        $directStockOuts = StockOutOrder::model()->findAll('type='.ConstantMap::STOCK_NOTICE_TYPE_DIRECT_TRANSFER.' and status in ('.StockOutOrder::STATUS_SUBMITED.','.StockOutOrder::STATUS_SETTLED.')');
        $partnerIds = array();
        if (Utility::isNotEmpty($directStockOuts)) {
            foreach ($directStockOuts as $k => $stockOut) {
                $partnerAmountLog = PartnerAmountLog::model()->find('category=32 and relation_id='.$stockOut->out_order_id);
                if(empty($partnerAmountLog)) {
                    $partnerAmountLog = new PartnerAmountLog();
                    $amount = 0;
                    $stockOutItems = StockOutDetail::model()->findAll('out_order_id='.$stockOut->out_order_id);
                    if (\Utility::isNotEmpty($stockOutItems))
                    {
                        foreach ($stockOutItems as $item)
                        {
                            $contractGoods = ContractGoods::model()->find('contract_id='.$item->contract_id.' and goods_id='.$item->goods_id);
                            $amount += $item->quantity * $contractGoods->price * $contractGoods->contract->exchange_rate;
                        }
                    }
                    if ($amount != 0) {
                        $partnerAmountLog->partner_id = $stockOut->partner_id;
                        $partnerAmountLog->type = 2;
                        $partnerAmountLog->method = $amount > 0 ? 1 : -1;
                        $partnerAmountLog->category = 32;
                        $partnerAmountLog->relation_id = $stockOut->out_order_id;
                        $partnerAmountLog->amount = $amount;
                        $partnerAmountLog->corporation_id = $stockOut->deliveryOrder->contract->corporation_id;
                        $partnerAmountLog->project_id = $stockOut->deliveryOrder->contract->project_id;
                        $partnerAmountLog->contract_id = $stockOut->deliveryOrder->contract->contract_id;
                        $partnerAmountLog->remark = '出库单审核通过';
                        $partnerAmountLog->create_time = $stockOut->update_time;
                        $partnerAmountLog->create_user_id = $stockOut->update_user_id;

                        $deliveryOrderCheckDetail = CheckDetail::model()->find('business_id='.FlowService::BUSIONESS_DELIVERY_ORDER_CHECK.' and obj_id='.$stockOut->order_id.' order by create_time desc');
                        if (!empty($deliveryOrderCheckDetail)) {
                            $partnerAmountLog->create_time = $deliveryOrderCheckDetail->update_time;
                            $partnerAmountLog->create_user_id = $deliveryOrderCheckDetail->update_user_id;
                        }

                        $partnerAmountLog->save();
                        array_push($partnerIds, $partnerAmountLog->partner_id);
                    }
                }
            }

            if (Utility::isNotEmpty($partnerIds)) {
                foreach ($partnerIds as $partnerId) {
                    self::repairPartnerInitUsedAmount($partnerId, 2);
                }
            }
        }
    }

    /**
     * @desc 修复合作方初始化额度
     * @param int $type          #额度类型 1:合同额度  2:实际额度
     * @param int $partnerId     #合作方
     */
    public static function repairPartnerInitUsedAmount($partnerId=0, $type=0)
    {
        $query = '';
        if($type > 0) {
            $query .= 'type='.$type;
        }
        if($partnerId > 0) {
            if(!empty($query)) {
                $query .= ' and ';
            }
            $query .= 'partner_id='.$partnerId;
        }
        $partnerAmounts = PartnerAmount::model()->findAll($query);
        if(Utility::isNotEmpty($partnerAmounts)) {
            foreach ($partnerAmounts as $partnerAmount) {
                $currentUsedAmount = !empty($partnerAmount->used_amount) ? $partnerAmount->used_amount : 0;
                $partnerAmountLogs = PartnerAmountLog::model()->findAll('type='.$partnerAmount->type.' and partner_id='.$partnerAmount->partner_id.' order by create_time desc');
                if(Utility::isNotEmpty($partnerAmountLogs)) {
                    foreach ($partnerAmountLogs as $k => $partnerAmountLog) {
                        if($k > 0) {
                            $currentUsedAmount -= $partnerAmountLogs[$k-1]->method * $partnerAmountLogs[$k-1]->amount;
                        }
                        $partnerAmountLog->amount_total = $currentUsedAmount;
                        $partnerAmountLog->save();
                    }
                }
                $partnerAmount->remark = $partnerAmountLogs[$k]->amount_total - $partnerAmountLogs[$k]->method * $partnerAmountLogs[$k]->amount;
                $partnerAmount->save();
            }
        }
    }

    /**
     * @desc 更新付款申请单付款中状态
     * @param int $applyId
     */
    public static function updatePayApplicationInPaymentStatus($applyId = 0)
    {
        $condition = 'status=' . PayApplication::STATUS_CHECKED;
        if(!empty($applyId)) {
            $condition .= ' and apply_id='.$applyId;
        }

        $payApplys = PayApplication::model()->findAll($condition);
        if(Utility::isNotEmpty($payApplys)) {
            foreach ($payApplys as $payApply) {
                if(bccomp($payApply->amount_paid, 0)==1 && bccomp($payApply->amount, $payApply->amount_paid)==1){ //已实付但未实付完成
                    $payApply->status = PayApplication::STATUS_IN_MANUAL_PAYMENT;
                    $payApply->save();
                }
            }
        }
    }
}