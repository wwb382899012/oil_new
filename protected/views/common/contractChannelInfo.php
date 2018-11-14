<?php
$buy_contract = ($contract->type==ConstantMap::BUY_TYPE)?$contract:$contract->relative;
$sell_contract = ($contract->type==ConstantMap::SALE_TYPE)?$contract:$contract->relative;

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
                上游合作方
                <span>
                    <span class="box-content__company-name box-content__color-f90">
                        [上游]
                    </span>
                    <span class="box-content__company-name">
                        <?php echo '<a href="/partner/detail/?id=' . $buy_contract->partner['partner_id'] . '&t=1" target="_blank">' . $buy_contract->partner['name'] . '</a>';?>
                    </span>
                    <span class="box-content__buy-currency">
                        采购币种:
                    </span>
                    <span class="box-content__currency-type">
                        <?php echo $this->map["currency"][$buy_contract['currency']]['name'];?>
                    </span>
                    <?php if($buy_contract['currency'] != 1):?>
                    <span class="box-content__buy-currency">
                        采购即期汇率:
                    </span>
                    <span class="box-content__currency-type">
                        <?php echo number_format($buy_contract['exchange_rate'], 2);?>
                    </span>
                    <?php endif;?>
                    <span class="box-content__buy-currency">
                        负责人:
                    </span>
                    <span class="box-content__currency-type">
                        <?php echo $buy_contract->manager['name'];?>
                    </span>
                    <span class="box-content__buy-currency">
                        合同签订日期:
                    </span>
                    <span class="box-content__currency-type">
                        <?php echo $buy_contract->contract_date;?>
                    </span>
                </span>
            </span>
        </div>
        <?php $this->renderPartial("/common/goodDetailList", array("goodArr"=>$buy_contract->goods, "title"=>"采购"));?>
        <div class="line-dot no-margin-top"></div>
        <div class="box-header  box-content-custom">
            <span class="box-content__company-style">
                下游合作方
                <span>
                    <span class="box-content__company-name box-content__color-f90">
                        [下游]
                    </span>
                    <span class="box-content__company-name">
                        <?php echo '<a href="/partner/detail/?id=' . $sell_contract->partner['partner_id'] . '&t=1" target="_blank">' . $sell_contract->partner['name'] . '</a>';?>
                    </span>
                    <span class="box-content__buy-currency">
                        销售币种:
                    </span>
                    <span class="box-content__currency-type">
                        <?php echo $this->map["currency"][$sell_contract['currency']]['name'];?>
                    </span>
                    <?php if($sell_contract['currency'] != 1):?>
                    <span class="box-content__buy-currency">
                        销售即期汇率:
                    </span>
                    <span class="box-content__currency-type">
                        <?php echo number_format($sell_contract['exchange_rate'], 2);?>
                    </span>
                    <?php endif;?>
                    <span class="box-content__buy-currency">
                        负责人:
                    </span>
                    <span class="box-content__currency-type">
                        <?php echo $sell_contract->manager['name'];?>
                    </span>
                    <span class="box-content__buy-currency">
                        合同签订日期:
                    </span>
                    <span class="box-content__currency-type">
                        <?php echo $sell_contract->contract_date;?>
                    </span>
                </span>
            </span>
        </div>

            
        <?php $this->renderPartial("/common/goodDetailList", array("goodArr"=>$sell_contract->goods, "title"=>"销售"));?>

        
        <?php if($buy_contract['formula'] || $sell_contract['formula']):?>
            <div class="line-dot no-margin-top"></div>
        <?php endif;?>
        <?php if($buy_contract['price_type'] == ConstantMap::PRICE_TYPE_TEMPORARY && $buy_contract['formula']):?>
        <div class="box-body form-horizontal form-horizontal-custom">
            <div class="form-group pd-bottom-0">
                <label for="type" class="col-sm-2 control-label custom-width-12">*采购计价公式:</label>
                <div class="col-sm-10">
                    <pre class="form-control-static form-control-static-custom"><?php echo $buy_contract['formula'];?></pre>
                </div>
            </div>
        </div>  
        <?php endif;?>
        <?php if($sell_contract['price_type'] == ConstantMap::PRICE_TYPE_TEMPORARY && $sell_contract['formula']):?>
        <div class="box-body form-horizontal form-horizontal-custom">
            <div class="form-group pd-bottom-0">
                <label for="type" class="col-sm-2 control-label custom-width-12">*销售计价公式:</label>
                <div class="col-sm-10">
                    <pre class="form-control-static form-control-static-custom"><?php echo $sell_contract['formula'];?></pre>
                </div>
            </div>
        </div>  
        <?php endif;?>
    </div>

    <?php if(!empty($buy_contract->agent) && !empty($buy_contract->agentDetail)):?>
    <!-- 收付款明细 -->
    <div class="box box-primary sub-container__box">
        <div class="box-header with-border box-content-title">
            <h3 class="box-title">代理手续费</h3>
            <span class="box-title__hiden">
                <i class="fa fa-angle-double-up"></i> 收起</span>
        </div>
        <div class="box-header  box-content-custom">
            <?php $this->renderPartial("/common/agentDetailsList", array("agentDetails"=>$buy_contract->agentDetail));?>
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
            <span class="box-content__company-style">上游付款计划</span>
        </div>
        <?php $this->renderPartial("/common/paymentList", array("payments"=>$buy_contract->payments, "showDates"=>$contractType, "title"=>"付款", "paymentType"=>"pay_type"));?>
        <div class="line-dot"></div>
        <div class="box-header  box-content-custom">
            <span class="box-content__company-style">下游收款计划</span>
        </div>
        <?php $this->renderPartial("/common/paymentList", array("payments"=>$sell_contract->payments, "showDates"=>$contractType, "title"=>"收款", "paymentType"=>"pay_type"));?>
        <div class="box-body form-horizontal form-horizontal-custom">
        </div>
    </div>

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
                        <div class="contract-type__circle">上游</div>
                    </div>
                    <div class="form-group form-group-custom form-group ">
                        <label for="type" class="col-lg-3 col-xl-2  control-label control-label-custom">
                            最终交货日期：
                        </label>
                        <div class="col-sm-8">
                             <span class="contract-desc">
                                 <?php echo $buy_contract['delivery_term'];?>&nbsp;&nbsp;
                                 <?php if(!empty($buy_contract['delivery_term'])) echo $this->map['contract_delivery_mode'][$buy_contract['delivery_mode']];?>
                             </span>

                        </div>

                    </div>
                    <div class="form-group form-group-custom form-group ">
                        <label for="type" class="col-lg-3 col-xl-2  control-label control-label-custom">
                            收票时间：
                        </label>
                        <div class="col-sm-8">
                            <span class="contract-desc"><?php echo empty($buy_contract['days'])&&$buy_contract['days']!=0?'':$buy_contract['days'].'天（根据入库单日期倒推）';?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="contract-type-container">
                        <div class="contract-type__circle contract-type__circle-right">下游</div>
                    </div>
                    <div class="form-group form-group-custom form-group ">
                        <label for="type" class="col-lg-3 col-xl-2  control-label control-label-custom">
                            最终发货日期：
                        </label>
                        <div class="col-sm-8">
                            <span class="contract-desc">
                                <?php echo $sell_contract['delivery_term'];?>&nbsp;&nbsp;
                                <?php if(!empty($sell_contract['delivery_term'])) echo $this->map['contract_delivery_mode'][$sell_contract['delivery_mode']];?>
                            </span>
                        </div>

                    </div>
                    <div class="form-group form-group-custom form-group ">
                        <label for="type" class="col-lg-3 col-xl-2  control-label control-label-custom">
                            开票时间：
                        </label>
                        <div class="col-sm-8">
                            <span class="contract-desc"><?php echo empty($sell_contract['days'])&&$sell_contract['days']!=0?'':$sell_contract['days'].'天（根据出库单日期倒推）';?></span>
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
                        <div class="contract-type__circle">上游</div>
                    </div>
                    <?php 
                    if(!empty($this->map['contract_config'][$buy_contract->type][$buy_contract['category']]['extra']))
                    $this->renderPartial("/common/contractExtra", array("mapValue"=>$this->map['contract_config'][$buy_contract->type][$buy_contract['category']]['extra'], "extraValue"=>$buy_contract->extra, "label_width"=>2));?>
                </div>
                <div class="col-md-6">
                    <div class="contract-type-container">
                        <div class="contract-type__circle contract-type__circle-right">下游</div>
                    </div>
                    <?php 
                    if(!empty($this->map['contract_config'][$sell_contract->type][$sell_contract['category']]['extra']))
                    $this->renderPartial("/common/contractExtra", array("mapValue"=>$this->map['contract_config'][$sell_contract->type][$sell_contract['category']]['extra'], "extraValue"=>$sell_contract->extra, "label_width"=>2));?>
                </div>
            </div>
        </div>
    </div>
<?php
if(!empty($buy_contract->quotas)||!empty($sell_contract->quotas)):?>
    <div class="box box-primary sub-container__box">
        <div class="box-header with-border box-content-title">
          <h3 class="box-title">&nbsp;&nbsp;&nbsp;合同占用额度</h3>
            <span class="box-title__hiden">
                <i class="fa fa-angle-double-up"></i> 收起</span>
        </div>
        <?php if(!empty($buy_contract->quotas)):?>
        <div class="box-header  box-content-custom">
            <span class="box-content__company-style">采购合同占用额度</span>
        </div>
        <?php $this->renderPartial("/common/quotasList", array("quotas"=>$buy_contract->quotas, "title"=>""));?>
        <?php endif;?>
        <?php if(!empty($buy_contract->quotas)&&!empty($sell_contract->quotas)):?>
        <div class="line-dot"></div>
        <?php endif;?>
        <?php if(!empty($sell_contract->quotas)):?>
        <div class="box-header  box-content-custom">
            <span class="box-content__company-style">销售合同占用额度</span>
        </div>
        <?php $this->renderPartial("/common/quotasList", array("quotas"=>$sell_contract->quotas, "title"=>""));?>
        <?php endif;?>
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
                <label for="type" class="col-lg-3 col-xl-2 control-label">采购合同创建人/时间：</label>
                <div class="col-lg-3 col-xl-4">
                    <p class="form-control-static form-control-static-custom">
                    <?php echo $buy_contract->creator['name'];?> /
                    <?php echo $buy_contract['create_time'];?></p>
                </div>
                <label for="type" class="col-lg-3 col-xl-2 control-label">采购合同修改人/时间：</label>
                <div class="col-lg-3 col-xl-4">
                <p class="form-control-static form-control-static-custom">
                    <?php $user = SystemUser::getUser($buy_contract["update_user_id"]); echo $user['name'] ?> /
                    <?php echo $buy_contract['update_time'];?></p>
                </div>
            </div>
            <div class="form-group pd-bottom-0">
                <label for="type" class="col-lg-3 col-xl-2 control-label">销售合同创建人/时间：</label>
                <div class="col-lg-3 col-xl-4">
                    <p class="form-control-static form-control-static-custom">
                    <?php echo $sell_contract->creator['name'];?> /
                    <?php echo $sell_contract['create_time'];?></p>
                </div>
                <label for="type" class="col-lg-3 col-xl-2 control-label">销售合同修改人/时间：</label>
                <div class="col-lg-3 col-xl-4">
                <p class="form-control-static form-control-static-custom">
                    <?php $user = SystemUser::getUser($sell_contract["update_user_id"]); echo $user['name'] ?> /
                    <?php echo $sell_contract['update_time'];?></p>
                </div>
            </div>
        </div>
    </div>