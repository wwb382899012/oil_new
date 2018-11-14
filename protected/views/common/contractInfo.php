<?php
$contractType = $contract->type == ConstantMap::BUY_TYPE || $contract->type == ConstantMap::CONTRACT_CATEGORY_SUB_BUY;
$titleStr = ($contractType)?'采购':'销售';
$partnerStr = ($contractType)?'上游':'下游';
$paymentStr = ($contractType)?'付款':'收款';
//$mapStrPre = ($contractType)?'buy':'sell';
$paymentType = ($contractType)?'pay_type':'proceed_type';
$is_main = $contract->is_main;
// debug($contract->agent);die;
?>
    <!-- 交易明细 -->
    <div class="box box-primary sub-container__box">
        <div class="box-header with-border box-content-title">
            <h3 class="box-title">
            &nbsp;&nbsp;&nbsp;交易明细
            </h3>
            <span class="box-title__hiden">
                <i class="fa fa-angle-double-up"></i> 收起</span>
        </div>
        <div class="box-header  box-content-custom">
            <span class="box-content__company-style">
                <?php echo $partnerStr?>合作方
                <span>
                    <span class="box-content__company-name box-content__color-f90">
                        [<?php echo $partnerStr?>]
                    </span>
                    <span class="box-content__company-name">
                        <?php echo '<a href="/partner/detail/?id=' . $contract->partner['partner_id'] . '&t=1" target="_blank">' . $contract->partner['name'] . '</a>';?>
                    </span>
                    <span class="box-content__buy-currency">
                        <?php echo $titleStr;?>币种:
                    </span>
                    <span class="box-content__currency-type">
                        <?php echo $this->map["currency"][$contract['currency']]['name'];?>
                    </span>
                    <?php if($contract['currency'] != 1):?>
                    <span class="box-content__buy-currency">
                        <?php echo $titleStr;?>即期汇率:
                    </span>
                    <span class="box-content__currency-type">
                        <?php echo number_format($contract['exchange_rate'], 2);?>
                    </span>
                    <?php endif;?>
                    <span class="box-content__buy-currency">
                        负责人:
                    </span>
                    <span class="box-content__currency-type">
                        <?php echo $contract->manager['name'];?>
                    </span>
                    <span class="box-content__buy-currency">
                        合同签订日期:
                    </span>
                    <span class="box-content__currency-type">
                        <?php echo $contract->contract_date;?>
                    </span>
                </span>
            </span>
        </div>

            
        <?php $this->renderPartial("/common/goodDetailList", array("goodArr"=>$contract->goods, "title"=>$titleStr));?>

        <div class="line-dot no-margin-top"></div>
        <?php if($contract['price_type'] == ConstantMap::PRICE_TYPE_TEMPORARY && $contract['formula']):?>
        <div class="box-body form-horizontal form-horizontal-custom">
            <div class="form-group pd-bottom-0">
                <label for="type" class="col-sm-2 control-label custom-width-12">*<?php echo $titleStr;?>计价公式:</label>
                <div class="col-sm-10">
                    <pre class="form-control-static form-control-static-custom"><?php echo $contract['formula'];?></pre>
                </div>
            </div>
        </div>
        <?php endif;?>      
    </div>


    <?php if(!empty($contract->agent)):?>
    <!-- 收付款明细 -->
    <div class="box box-primary sub-container__box">
        <div class="box-header with-border box-content-title">
            <h3 class="box-title">&nbsp;&nbsp;&nbsp;代理手续费</h3>
            <span class="box-title__hiden">
                <i class="fa fa-angle-double-up"></i> 收起</span>
        </div>
        <div class="box-header  box-content-custom">
            <?php $this->renderPartial("/common/agentDetailsList", array("agentDetails"=>$contract->agentDetail));?>
        </div>
    </div>
    <?php endif; ?>
    <!-- 收付款明细 -->
    <div class="box box-primary sub-container__box">
        <div class="box-header with-border box-content-title">
            <h3 class="box-title">&nbsp;&nbsp;&nbsp;收付款计划</h3>
            <span class="box-title__hiden">
                <i class="fa fa-angle-double-up"></i> 收起</span>
        </div>
        <div class="box-header  box-content-custom">
            <span class="box-content__company-style"><?php echo $partnerStr;?><?php echo $paymentStr;?>计划</span>
        </div>
        <?php $this->renderPartial("/common/paymentList", array("payments"=>$contract->payments, "showDates"=>$contractType, "title"=>$paymentStr, "paymentType"=>$paymentType));?>
        <div class="box-body form-horizontal form-horizontal-custom">
        </div>
    </div>
    <!-- 收付款明细 -->
    <!-- 额度占用情况 -->
    <div class="box box-primary sub-container__box">
        <div class="box-header with-border box-content-title">
            <h3 class="box-title">&nbsp;&nbsp;&nbsp;额度占用情况</h3>
            <span class="box-title__hiden">
                    <i class="fa fa-angle-double-up"></i> 收起</span>
        </div>
        <div class="box-header  box-content-custom">
            <?php $this->renderPartial("/common/partnerAmountList", array('contract'=>$contract));?>
        </div>
    </div>
    <!-- 额度占用情况 -->
    <!-- 截止日期与期限 -->
    <div class="box box-primary sub-container__box">
        <div class="box-header with-border box-content-title">
            <h3 class="box-title">&nbsp;&nbsp;&nbsp;最终交货/发货日期</h3>
                    <span class="box-title__hiden">
                                <i class="fa fa-angle-double-up"></i> 收起</span>
        </div>
        <div class="form-horizontal">
            <div class="box-body box-contract-extra">
                <div class="col-md-6 bd-right-2">
                    <div class="contract-type-container">
                        <div class="contract-type__circle"><?php if($contract->type == ConstantMap::CONTRACT_CATEGORY_SUB_BUY):?>上游<?php else:?>下游<?php endif;?></div>
                    </div>
                    <div class="form-group form-group-custom form-group ">
                        <label for="type" class="col-lg-3 col-xl-2  control-label control-label-custom">
                            最终<?php if($contract->type == ConstantMap::CONTRACT_CATEGORY_SUB_BUY):?>交货<?php else: ?>发货<?php endif;?>日期：
                        </label>
                        <div class="col-sm-8">
                            <span class="contract-desc">
                                <?php echo $contract->delivery_term;?>&nbsp;&nbsp;
                                <?php if(!empty($contract->delivery_term)) echo $this->map['contract_delivery_mode'][$contract->delivery_mode];?>
                            </span>

                        </div>

                    </div>
                    <div class="form-group form-group-custom form-group ">
                        <label for="type" class="col-lg-3 col-xl-2  control-label control-label-custom">
                            <?php if($contract->type == ConstantMap::CONTRACT_CATEGORY_SUB_BUY):?>收票<?php else: ?>开票<?php endif;?>时间：
                        </label>
                        <div class="col-sm-8">
                            <?php if($contract->type == ConstantMap::CONTRACT_CATEGORY_SUB_BUY):?>
                            <span class="contract-desc"><?php echo empty($contract->days)&&$contract->days!=0?'':$contract->days.'天（根据入库单日期倒推）';?></span>
                            <?php else: ?>
                            <span class="contract-desc"><?php echo empty($contract->days)&&$contract->days!=0?'':$contract->days.'天（根据出库单日期倒推）';?></span>
                            <?php endif;?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- 截止日期与期限 -->
    <!-- 条款合同 -->
    <div class="box box-primary sub-container__box">
        <div class="box-header with-border box-content-title">
          <h3 class="box-title">&nbsp;&nbsp;&nbsp;合同条款</h3>
            <span class="box-title__hiden">
                <i class="fa fa-angle-double-up"></i> 收起</span>
        </div>
        <div class="form-horizontal">
            <div class="box-body box-contract-extra">
                <div class="col-md-6 bd-right-2">
                    <div class="contract-type-container">
                        <div class="contract-type__circle"><?php echo $partnerStr;?></div>
                        
                    </div>
                    <?php 
                    if(!empty($this->map['contract_config'][$contract->type][$contract['category']]['extra']))
                    $this->renderPartial("/common/contractExtra", array("mapValue"=>$this->map['contract_config'][$contract->type][$contract['category']]['extra'], "extraValue"=>$contract->extra, "label_width"=>2));?>
                </div>
            </div>
        </div>
    </div>

    <!-- 条款合同end -->
<?php
if(!empty($contract->quotas)):?>
    <div class="box box-primary sub-container__box">
        <div class="box-header with-border box-content-title">
          <h3 class="box-title">&nbsp;&nbsp;&nbsp;额度占用</h3>
            <span class="box-title__hiden">
                <i class="fa fa-angle-double-up"></i> 收起</span>
        </div>
        <div class="box-header  box-content-custom">
            <span class="box-content__company-style">
                额度占用
            </span>
        </div>
        <?php $this->renderPartial("/common/quotasList", array("quotas"=>$contract->quotas, "title"=>""));?>
        <div class="box-body form-horizontal form-horizontal-custom">
        </div>
    </div>
<?php endif;?>



    <div class="box box-primary sub-container__box">
        <div class="box-header with-border box-content-title">
            <h3 class="box-title">&nbsp;&nbsp;&nbsp;创建人信息</h3>
            <span class="box-title__hiden">
                <i class="fa fa-angle-double-up"></i> 收起</span>
        </div>
        <div class="box-body form-horizontal form-horizontal-custom">
            <div class="form-group pd-bottom-0">
                <label for="type" class="col-lg-3 col-xl-2 control-label">合同创建人/时间：</label>
                <div class="col-lg-3 col-xl-4">
                    <p class="form-control-static form-control-static-custom">
                    <?php echo $contract->creator['name'];?> /
                    <?php echo $contract['create_time'];?></p>
                </div>
                <label for="type" class="col-lg-3 col-xl-2 control-label">合同修改人/时间：</label>
                <div class="col-lg-3 col-xl-4">
                <p class="form-control-static form-control-static-custom">
                    <?php $user = SystemUser::getUser($contract["update_user_id"]); echo $user['name'] ?> /
                    <?php echo $contract['update_time'];?></p>
                </div>
            </div>
        </div>
    </div>