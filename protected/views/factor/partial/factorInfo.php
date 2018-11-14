<?php
$this->renderPartial("/factor/partial/payInfo", array('factor' => $factor));
?>
<div class="box-header with-border"></div>

<h4 class="box-title">保理信息</h4>
<div class="form-group">
    <label for="corporation_id" class="col-sm-2 control-label">保理对接流水号</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $detail->contract_code ?></p>
    </div>
    <label for="corporation_id" class="col-sm-2 control-label">资金对接流水号</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $detail->contract_code_fund ?></p>
    </div>
</div>
<div class="form-group">
    <label for="corporation_id" class="col-sm-2 control-label">交易主体</label>
    <div class="col-sm-4">
        <p class="form-control-static">
            <a href="/corporation/detail/?id=<?php echo $factor->corporation_id ?>&t=1" title="<?php echo $factor->corporation->name ?>" target="_blank"><?php echo $factor->corporation->name ?></a>
        </p>
    </div>
    <label for="status" class="col-sm-2 control-label">保理申请状态</label>
    <div class="col-sm-4">
        <p class="form-control-static">
            <span class="label label-info">
                <?php
                echo $this->map["factor_detail_status"][$detail->status];
                if ($detail->status == FactorDetail::STATUS_SUBMIT) {
                    $nodeName = FlowService::getNowCheckNode($detail->detail_id, FlowService::BUSINESS_FACTORING);
                    echo " - " . $nodeName;
                }
                ?>
            </span>
        </p>
    </div>
</div>
<div class="form-group">
    <label for="entry_date" class="col-sm-2 control-label">对接本金</label>
    <div class="col-sm-4">
        <p class="form-control-static">￥ <?php echo Utility::numberFormatFen2Yuan($detail->amount) ?></p>
    </div>
    <label for="interest" class="col-sm-2 control-label">利息</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo bccomp($detail->rate, 0, 6) == 1 ? '￥ ' . Utility::numberFormatFen2Yuan($detail->interest) : '' ?></p>
    </div>
</div>
<div class="form-group">
    <label for="pay_date" class="col-sm-2 control-label">合同放款时间</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $detail->pay_date ?></p>
    </div>
    <label for="return_date" class="col-sm-2 control-label">合同回款时间</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $detail->return_date ?></p>
    </div>
</div>
<?php
$attachTypes = $this->map["factor_attachment_type"];
if (Utility::isNotEmpty($attachTypes)) {
    foreach ($attachTypes as $key => $row) { ?>
        <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $row["name"] ?></label>
            <div class="col-sm-10">
                <?php if (Utility::isNotEmpty($attachments[$key])) {
                    foreach ($attachments[$key] as $val) {
                        if (!empty($val['file_url'])) { ?>
                            <p class="form-control-static">
                                <a href='/factor/getFile/?id=<?php echo $val['id'] ?>&fileName=<?php echo $val['name'] ?>' target='_blank' class='btn btn-primary btn-xs'>点击查看</a>
                            </p>
                            <?php
                        } else {
                            echo '无';
                        }
                    }
                } else {
                    echo '<p class="form-control-static">无</p>';
                }
                ?>
            </div>
        </div>
        <?php
    }
}
?>
<div class="form-group">
    <label for="remark" class="col-sm-2 control-label">备注</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $detail->remark ?></p>
    </div>
</div>
