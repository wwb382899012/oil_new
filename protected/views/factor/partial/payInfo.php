<div class="form-group">
    <label for="apply_id" class="col-sm-2 control-label">付款申请编号</label>
    <div class="col-sm-4">
        <p class="form-control-static">
            <a target="_blank" href="/pay/detail?id=<?php echo $factor->apply_id ?>"><?php echo $factor->apply_id ?></a>
        </p>
    </div>
    <label for="project_code" class="col-sm-2 control-label">项目编号</label>
    <div class="col-sm-4">
        <p class="form-control-static">
            <a target="_blank" href="/project/detail?id=<?php echo $factor->project_id ?>"><?php echo $factor->project->project_code ?></a>
        </p>
    </div>
</div>
<div class="form-group">
    <label for="contract_id" class="col-sm-2 control-label">采购合同编号</label>
    <div class="col-sm-4">
        <p class="form-control-static">
            <a target="_blank" href="/contract/detail?id=<?php echo $factor->contract_id ?>"><?php echo $factor->contract->contract_code ?></a>
        </p>
    </div>
</div>
<div class="form-group">
    <label for="contract_code" class="col-sm-2 control-label">保理对接编号</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $factor->contract_code ?></p>
    </div>
    <label for="contract_code_fund" class="col-sm-2 control-label">资金对接编号</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $factor->contract_code_fund ?></p>
    </div>
</div>
<div class="form-group">
    <label for="apply_id" class="col-sm-2 control-label">付款申请金额</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo Map::$v['currency'][$factor->payApply->currency]['ico'] . Utility::numberFormatFen2Yuan($factor->payApply->amount) ?></p>
    </div>
    <label for="project_code" class="col-sm-2 control-label">实际保理对接金额</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo '￥' . Utility::numberFormatFen2Yuan($factor->amount) ?></p>
    </div>
</div>

<?php
$checkingAmount = $factor->checkingAmount();
$buttedAmount = $factor->buttedAmount();
$balanceAmount = $factor->amount - $checkingAmount - $buttedAmount;
?>

<div class="form-group">
    <label for="apply_id" class="col-sm-2 control-label">保理对接审核中金额</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo '￥' . Utility::numberFormatFen2Yuan($factor->checkingAmount()) ?></p>
    </div>
    <label for="project_code" class="col-sm-2 control-label">已对接金额</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo '￥' . Utility::numberFormatFen2Yuan($factor->buttedAmount()) ?></p>
    </div>
</div>
<div class="form-group">
    <label for="apply_id" class="col-sm-2 control-label">剩余可对接金额</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo '￥' . Utility::numberFormatFen2Yuan($balanceAmount) ?></p>
    </div>
    <label for="contract_id" class="col-sm-2 control-label">年化利率</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $factor->rate * 100 . '%' ?></p>
    </div>
</div>