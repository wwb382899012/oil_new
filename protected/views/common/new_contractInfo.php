<?php
$contractType = $contract->type == ConstantMap::BUY_TYPE || $contract->type == ConstantMap::CONTRACT_CATEGORY_SUB_BUY;
$titleStr = ($contractType) ? '采购' : '销售';
$partnerStr = ($contractType) ? '上游' : '下游';
$paymentStr = ($contractType) ? '付款' : '收款';
//$mapStrPre = ($contractType)?'buy':'sell';
$paymentType = ($contractType) ? 'pay_type' : 'proceed_type';
$is_main = $contract->is_main;
// debug($contract->agent);die;
$original = isset($_GET['original']) ? $_GET['original'] : 0;
if (1 == $original) {
    if (!empty($contract->originalContractGoods)) {
        $contract->goods = $contract->originalContractGoods;
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
            <label style="font-weight:500;"><?php echo $partnerStr ?>合作方：</label>
            <?php echo '<a href="/partner/detail/?id=' . $contract->partner['partner_id'] . '&t=1" target="_blank">' . $contract->partner['name'] . '</a>'; ?>
        </li>
        <li>
            <label> <?php echo $titleStr; ?>币种： </label>
            <span><?php echo $this->map["currency"][$contract['currency']]['name']; ?></span>
        </li>
        <?php if ($contract['currency'] != 1): ?>
            <li>
                <label><?php echo $titleStr; ?>即期汇率：</label>
                <span><?php echo number_format($contract['exchange_rate'], 2); ?></span>
            </li>
        <?php endif; ?>

        <li>
            <label>负责人：</label>
            <span> <?php echo $contract->manager['name']; ?></span>
        </li>
        <li>
            <label>合同签订日期：</label>
            <span> <?php echo $contract->contract_date; ?></span>
        </li>
    </ul>
    <?php $this->renderPartial("/common/new_goodDetailList", array("goodArr" => $contract->goods, "title" => $titleStr)); ?>
    <?php if ($contract['price_type'] == ConstantMap::PRICE_TYPE_TEMPORARY && $contract['formula']): ?>
        <ul class="item-com">
            <li style="width:100%;height:unset;margin-top:10px;margin-right:0;">
                <label><?php echo $titleStr; ?>计价公式：</label>
                <p><?php echo $contract['formula']; ?></p>
            </li>
        </ul>
    <?php endif; ?>

</div>

<!--  代理手续费 -->
<?php if(!empty($contract->agent) && !empty($contract->agentDetail)): ?>
    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>代理手续费</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <?php $this->renderPartial("/common/new_agentDetailsList", array("agentDetails" => $contract->agentDetail)); ?>
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
    <ul class="form-com">
        <li>
            <label><?php echo $partnerStr; ?><?php echo $paymentStr; ?>计划</label>
        </li>
    </ul>

    <?php $this->renderPartial("/common/new_paymentList", array("payments" => $contract->payments, "showDates" => $contractType, "title" => $paymentStr, "paymentType" => $paymentType)); ?>
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
    <div style="flex:1;">
        <div class="contract-type-container">
            <div style="font-size:16px;font-weight:500;margin-bottom:14px;">
                <?php if($contract->type == ConstantMap::CONTRACT_CATEGORY_SUB_BUY):?>上游<?php else:?>下游<?php endif;?>最终<?php if($contract->type == ConstantMap::CONTRACT_CATEGORY_SUB_BUY):?>交货<?php else: ?>发货<?php endif;?>日期
            </div>
        </div>
        <ul class="item-com item-com-1 ul-contract-item">
            <li>
                <label>
                    最终<?php if($contract->type == ConstantMap::CONTRACT_CATEGORY_SUB_BUY):?>交货<?php else: ?>发货<?php endif;?>日期：
                </label>
                <span>
                                <?php echo $contract->delivery_term;?>&nbsp;&nbsp;
                    <?php if(!empty($contract->delivery_term)) echo $this->map['contract_delivery_mode'][$contract->delivery_mode];?>
                            </span>
            </li>
            <li>
                <label>
                    <?php if($contract->type == ConstantMap::CONTRACT_CATEGORY_SUB_BUY):?>收票<?php else: ?>开票<?php endif;?>时间：
                </label>
                <?php if($contract->type == ConstantMap::CONTRACT_CATEGORY_SUB_BUY):?>
                    <span ><?php echo empty($contract->days)&&$contract->days!=0?'':$contract->days.'天（根据入库单日期倒推）';?></span>
                <?php else: ?>
                    <span ><?php echo empty($contract->days)&&$contract->days!=0?'':$contract->days.'天（根据出库单日期倒推）';?></span>
                <?php endif;?>
            </li>
        </ul>
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
    <div style="flex:1;">
        <div class="contract-type-container">
            <div style="font-size:16px;font-weight:500;margin-bottom:14px;">
                <?php
                if ($contract['type'] == ConstantMap::CONTRACT_CATEGORY_SUB_BUY) {
                    echo '上游合同条款';
                } else {
                    echo '下游合同条款';
                }
                ?>
            </div>
        </div>

        <?php
        if (!empty($this->map['contract_config'][$contract->type][$contract['category']]['extra']))
            $this->renderPartial("/common/new_contractExtra", array("mapValue" => $this->map['contract_config'][$contract->type][$contract['category']]['extra'], "extraValue" => $contract->extra, "label_width" => 2)); ?>

    </div>
</div>

<?php
if (!empty($contract->quotas)):?>
    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>合同占用额度</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <ul class="item-com">
            <li>
                <label><?php echo $titleStr;?>合同占用额度</label>
            </li>
        </ul>
        <?php $this->renderPartial("/common/new_quotasList", array("quotas" => $contract->quotas, "title" => "")); ?>
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
                <label style="width:unset;"><?php echo $titleStr; ?>合同创建人/时间：</label>
                <p class="form-control-static-custom">
                    <?php echo $contract->creator['name'];?> / <?php echo $contract['create_time'];?>
                </p>
            </li>
            <li>
                <label style="width:unset;"><?php echo $titleStr; ?>合同修改人/时间：</label>
                <p class="form-control-static-custom">
                    <?php $user = SystemUser::getUser($contract["update_user_id"]); echo $user['name'] ?> / <?php echo $contract['update_time'];?>
                </p>
            </li>
        </ul>
    </div>
</div>