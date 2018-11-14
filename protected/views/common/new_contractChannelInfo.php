<?php
$buy_contract = ($contract->type == ConstantMap::BUY_TYPE) ? $contract : $contract->relative;
$sell_contract = ($contract->type == ConstantMap::SALE_TYPE) ? $contract : $contract->relative;
$original = isset($_GET['original']) ? $_GET['original'] : 0;
if (1 == $original) {
    if (!empty($buy_contract->originalContractGoods)) {
        $buy_contract->goods = $buy_contract->originalContractGoods;
    }
    if (!empty($sell_contract->originalContractGoods)) {
        $sell_contract->goods = $sell_contract->originalContractGoods;
    }
}
?>
<!--  交易明细 -->
<div class="content-wrap">
    <div class="content-wrap-title">
        <div>
            <p>交易明细</p>
            <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
        </div>
    </div>
    <ul class="item-com item-com-for-table">
        <li>
            <label style="font-weight:500;">上游合作方：</label>
            <span>
                <?php echo '<a href="/partner/detail/?id=' . $buy_contract->partner['partner_id'] . '&t=1" target="_blank">' . $buy_contract->partner['name'] . '</a>'; ?>
            </span>
        </li>
        <li>
            <label>采购币种：</label>
            <span class="box-content__currency-type">
                <?php echo $this->map["currency"][$buy_contract['currency']]['name']; ?>
            </span>
        </li>
        <?php if ($buy_contract['currency'] != 1): ?>
            <li>
                <label>采购即期汇率：</label>
                <span class="box-content__currency-type">
                  <?php echo number_format($buy_contract['exchange_rate'], 2); ?>
              </span>
            </li>
        <?php endif; ?>
        <li>
            <label>负责人：</label>
            <span class="box-content__currency-type">
                <?php echo $buy_contract->manager['name']; ?>
            </span>
        </li>
        <li>
            <label>合同签订日期：</label>
            <span class="box-content__currency-type">
                <?php echo $buy_contract->contract_date; ?>
            </span>
        </li>
    </ul>
    <?php $this->renderPartial("/common/new_goodDetailList", array("goodArr" => $buy_contract->goods, "title" => "采购")); ?>
    <ul class="item-com item-com-for-table" style="margin-top:20px;">
        <li>
            <label style="font-weight:500;">下游合作方：</label>
            <span class="box-content__company-name">
                <?php echo '<a href="/partner/detail/?id=' . $sell_contract->partner['partner_id'] . '&t=1" target="_blank">' . $sell_contract->partner['name'] . '</a>'; ?>
            </span>
        </li>
        <li>
            <label>销售币种：</label>
            <span class="box-content__currency-type">
                <?php echo $this->map["currency"][$sell_contract['currency']]['name']; ?>
            </span>
        </li>
        <?php if ($sell_contract['currency'] != 1): ?>
            <li>
                <label>销售即期汇率：</label>
                <span class="box-content__currency-type">
                <?php echo number_format($sell_contract['exchange_rate'], 2); ?>
            </span>
            </li>
        <?php endif; ?>
        <li>
            <label>负责人：</label>
            <span class="box-content__currency-type">
                <?php echo $sell_contract->manager['name']; ?>
            </span>
        </li>
        <li>
            <label>合同签订日期：</label>
            <span class="box-content__currency-type">
                <?php echo $sell_contract->contract_date; ?>
            </span>
        </li>
    </ul>
    <?php $this->renderPartial("/common/new_goodDetailList", array("goodArr" => $sell_contract->goods, "title" => "销售")); ?>
    <?php if ($buy_contract['formula'] || $sell_contract['formula']): ?>
        <div class="line-dot no-margin-top"></div>
    <?php endif; ?>
    <?php if ($buy_contract['price_type'] == ConstantMap::PRICE_TYPE_TEMPORARY && $buy_contract['formula']): ?>
        <ul class="item-com">
            <li style="width:100%;height:unset;margin-top:10px;margin-right:0;">
                <label>采购计价公式：</label>
                <p class="form-control-static form-control-static-custom"><?php echo $buy_contract['formula']; ?></p>
            </li>
        </ul>
    <?php endif; ?>
    <?php if ($sell_contract['price_type'] == ConstantMap::PRICE_TYPE_TEMPORARY && $sell_contract['formula']): ?>
        <ul class="item-com">
            <li style="width:100%;height:unset;margin-top:10px;margin-right:0;">
                <label>销售计价公式：</label>
                <p class="form-control-static form-control-static-custom"><?php echo $sell_contract['formula']; ?></p>
            </li>
        </ul>
    <?php endif; ?>
</div>

<!--  代理手续费 -->
<?php if (!empty($buy_contract->agent) && !empty($buy_contract->agentDetail)): ?>
    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>代理手续费</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <div class="box-header  box-content-custom">
            <?php $this->renderPartial("/common/new_agentDetailsList", array("agentDetails" => $buy_contract->agentDetail)); ?>
        </div>
    </div>
<?php endif; ?>
<!--  收付款计划 -->
<div class="content-wrap">
    <div class="content-wrap-title">
        <div>
            <p>收付款计划</p>
            <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
        </div>
    </div>
    <ul class="item-com">
        <li>
            <label>上游付款计划</label>
        </li>
    </ul>
    <?php $this->renderPartial("/common/new_paymentList", array("payments" => $buy_contract->payments, "showDates" => $contractType, "title" => "付款", "paymentType" => "pay_type")); ?>
    <ul class="item-com" style="margin-top:20px;">
        <li>
            <label>下游收款计划</label>
        </li>
    </ul>
    <?php $this->renderPartial("/common/new_paymentList", array("payments" => $sell_contract->payments, "showDates" => $contractType, "title" => "收款", "paymentType" => "pay_type")); ?>
</div>

<!--  额度占用情况 -->
<div class="content-wrap">
    <div class="content-wrap-title">
        <div>
            <p>额度占用情况</p>
            <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
        </div>
    </div>
    <?php $this->renderPartial("/common/new_partnerAmountList", array('contract' => $contract)); ?>
</div>

<!-- 截止日期与期限 -->
<div class="content-wrap contract-item">
    <div class="content-wrap-title">
        <div>
            <p>最终交货/发货日期</p>
            <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
        </div>
    </div>
    <div style="display:flex;">
        <div style="flex:1;">
            <div class="contract-type-container" style="margin-bottom:14px;">
                <div style="font-size:16px;font-weight:500;margin-bottom:14px;">上游最终交货日期</div>
            </div>
            <ul class="item-com item-com-1 ul-contract-item">
                <li>
                    <label>最终交货日期：</label>
                    <span>
                                 <?php echo $buy_contract['delivery_term']; ?>&nbsp;&nbsp;
                        <?php if (!empty($buy_contract['delivery_term'])) echo $this->map['contract_delivery_mode'][$buy_contract['delivery_mode']]; ?>
                             </span>
                </li>
                <li>
                    <label>收票时间：</label>
                    <span><?php echo empty($buy_contract['days']) && $buy_contract['days'] != 0 ? '' : $buy_contract['days'] . '天（根据入库单日期倒推）'; ?></span>
                </li>
            </ul>
        </div>
        <div style="flex:1;">
            <div class="contract-type-container" style="margin-bottom:14px;">
                <div style="font-size:16px;font-weight:500;margin-bottom:14px;">下游最终发货日期</div>
            </div>
            <ul class="item-com item-com-1 ul-contract-item">
                <li>
                    <label>最终发货日期：</label>
                    <span>
                                <?php echo $sell_contract['delivery_term']; ?>&nbsp;&nbsp;
                        <?php if (!empty($sell_contract['delivery_term'])) echo $this->map['contract_delivery_mode'][$sell_contract['delivery_mode']]; ?>
                            </span>
                </li>
                <li>
                    <label>开票时间：</label>
                    <span><?php echo empty($sell_contract['days']) && $sell_contract['days'] != 0 ? '' : $sell_contract['days'] . '天（根据出库单日期倒推）'; ?></span>
                </li>
            </ul>
        </div>

    </div>

</div>

<!-- 截止日期与期限 -->


<!--  合同条款 -->
<div class="content-wrap contract-item">
    <div class="content-wrap-title">
        <div>
            <p>合同条款</p>
            <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
        </div>
    </div>
    <div style="display:flex;">
        <div style="flex:1;">
            <div class="contract-type-container" style="margin-bottom:14px;">
                <div style="font-size:16px;font-weight:500;margin-bottom:14px;">上游合同条款</div>
            </div>
            <?php
            if (!empty($this->map['contract_config'][$buy_contract->type][$buy_contract['category']]['extra']))
                $this->renderPartial("/common/new_contractExtra", array("mapValue" => $this->map['contract_config'][$buy_contract->type][$buy_contract['category']]['extra'], "extraValue" => $buy_contract->extra, "label_width" => 2)); ?>
        </div>
        <div style="flex:1;">
            <div class="contract-type-container" style="margin-bottom:14px;">
                <div style="font-size:16px;font-weight:500;margin-bottom:14px;">下游合同条款</div>
            </div>
            <?php
            if (!empty($this->map['contract_config'][$sell_contract->type][$sell_contract['category']]['extra']))
                $this->renderPartial("/common/new_contractExtra", array("mapValue" => $this->map['contract_config'][$sell_contract->type][$sell_contract['category']]['extra'], "extraValue" => $sell_contract->extra, "label_width" => 2)); ?>
        </div>
    </div>
</div>

<?php
if (!empty($buy_contract->quotas) || !empty($sell_contract->quotas)):?>
    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>合同占用额度</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <?php if (!empty($buy_contract->quotas)): ?>
            <ul class="item-com">
                <li>
                    <label>采购合同占用额度</label>
                </li>
            </ul>
            <?php $this->renderPartial("/common/new_quotasList", array("quotas" => $buy_contract->quotas, "title" => "")); ?>
        <?php endif; ?>
        <?php if (!empty($buy_contract->quotas) && !empty($sell_contract->quotas)): ?>
        <?php endif; ?>
        <?php if (!empty($sell_contract->quotas)): ?>
            <ul class="item-com" style="margin-top:20px;">
                <li>
                    <label>销售合同占用额度</label>
                </li>
            </ul>
            <?php $this->renderPartial("/common/new_quotasList", array("quotas" => $sell_contract->quotas, "title" => "")); ?>
        <?php endif; ?>
        <div class="box-body form-horizontal form-horizontal-custom">
        </div>
    </div>
<?php endif; ?>

<!--  创建人信息 -->
<div class="content-wrap creater">
    <div class="content-wrap-title">
        <div>
            <p>创建人信息</p>
            <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
        </div>
    </div>
    <div style="display:flex;">
        <ul class="item-com item-com-2">
            <li>
                <label style="width:unset;">采购合同创建人/时间：</label>
                <p class="form-control-static form-control-static-custom">
                    <?php echo $buy_contract->creator['name']; ?> /
                    <?php echo $buy_contract['create_time']; ?>
                </p>
            </li>
            <li>
                <label style="width:unset;">采购合同修改人/时间：</label>
                <p class="form-control-static form-control-static-custom">
                    <?php $user = SystemUser::getUser($buy_contract["update_user_id"]);
                    echo $user['name'] ?> / <?php echo $buy_contract['update_time']; ?>
                </p>
            </li>
            <li>
                <label style="width:unset;">销售合同创建人/时间：</label>
                <p class="form-control-static form-control-static-custom">
                    <?php echo $sell_contract->creator['name']; ?> / <?php echo $sell_contract['create_time']; ?>
                </p>
            </li>
            <li>
                <label style="width:unset;">销售合同修改人/时间：</label>
                <p class="form-control-static form-control-static-custom">
                    <?php $user = SystemUser::getUser($sell_contract["update_user_id"]);
                    echo $user['name'] ?> / <?php echo $sell_contract['update_time']; ?>
                </p>
            </li>
        </ul>
    </div>
</div>

<!--  ok -->