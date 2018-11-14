<div class="content-wrap">
    <div class="content-wrap-title">
        <div>
            <p>付款申请信息</p>
            <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
        </div>
    </div>

    <?php
    if ($apply->status == PayApplication::STATUS_STOP) {?>
    <ul class="item-com">
            <li>
                <label>止付原因：</label>
                <p class="form-control-static"  style="color:#FF6E34;"><?php echo $apply->extra->stop_remark; ?></p>
            </li>
    </ul>
    <?php }
    ?>
    <ul class="item-com">
        <li>
            <label>付款申请编号：</label>
            <p><a href="/pay/detail/?id=<?php echo $apply->apply_id ?>&t=1"
                  target="_blank"><?php echo $apply->apply_id ?></a></p>
        </li>
        <li>
            <label>付款申请状态：</label>
            <p>
                <span style="color:#FF6E34;"><?php
                echo $this->map["pay_application_status"][$apply->status];
                if ($apply->status == PayApplication::STATUS_SUBMIT) {
                    $nodeName = FlowService::getNowCheckNode($apply->apply_id, FlowService::BUSINESS_PAY_APPLICATION);
                    echo " - " . $nodeName;
                }
                ?>
                    </span>
            </p>
        </li>
        <li>
            <label>交易主体：</label>
            <p>
                <a href="/corporation/detail/?id=<?php echo $apply->corporation_id ?>&t=1"
                   target="_blank"><?php echo $apply->corporation->name ?></a>
            </p>
        </li>
        <?php if (!empty($apply->contract_id)) { ?>
            <li>
                <label>合作方：</label>
                <p>
                    <a href="/partner/detail/?id=<?php echo $apply->contract->partner_id ?>&t=1"
                       target="_blank"><?php echo $apply->contract->partner->name ?></a>
                </p>
            </li>
        <?php } ?>

        <?php if (!empty($apply->project_id)) { ?>
            <li>
                <label>项目编号：</label>
                <p>
                    <a href="/project/detail/?id=<?php echo $apply->project_id ?>&t=1"
                       target="_blank"><?php echo $apply->project->project_code ?></a>
                </p>
            </li>
            <li>
                <label>项目类型：</label>
                <p>
                    <?php echo $this->map["project_type"][$apply->project->type] ?>
                </p>
            </li>
        <?php } ?>

        <?php if (!empty($apply->contract_id)) { ?>
            <li>
                <label>合同编号：</label>
                <p>
                    <a href="/contract/detail/?id=<?php echo $apply->contract_id ?>&t=1"
                       target="_blank"><?php echo $apply->contract->contract_code ?></a>
                </p>
            </li>
            <li>
                <label>合同类型：</label>
                <p>
                    <?php echo $this->map["contract_config"][$apply->contract["type"]][$apply->contract['category']]["name"]; ?>
                </p>
            </li>
        <?php } ?>
        <?php if (Utility::checkQueryId($apply->contract_id)) { ?>
            <li>
                <label>合同已实付金额：</label>
                <p>
                    ￥<?php echo Utility::numberFormatFen2Yuan(PayService::getContractActualPaidAmount($apply->contract_id)) ?>
                </p>
            </li>
        <?php } ?>
        <li>
            <label style="width: unset">付款单已实付金额：</label>
            <p>
                <?php echo $this->map["currency"][$apply->currency]['ico'] ?><?php echo Utility::numberFormatFen2Yuan($apply->amount_paid) ?>
            </p>
        </li>
        <li>
            <label style="width: unset">付款单未实付金额：</label>
            <p class="text-red">
                <?php echo $this->map["currency"][$apply->currency]['ico'] ?><?php echo Utility::numberFormatFen2Yuan($apply->amount - $apply->amount_paid) ?>
            </p>
        </li>
    </ul>


</div>


<?php if (!empty($apply->contract_id) && is_array($apply->details) && count($apply->details) > 0) {
    ?>
    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>相关付款计划</p>

            </div>
        </div>
        <table class="table table-hover">
            <thead>
            <tr>
                <th style="width:80px; text-align: left;">期数</th>
                <th style="width:120px; text-align: left;">类别</th>
                <th style="width:180px; text-align: left;">计划付款金额</th>
                <th style="width:180px; text-align: left;">已申请金额</th>
                <th style="width:180px; text-align: left;">未申请金额</th>
                <th style="text-align: left;">本次付款金额</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (is_array($apply->details))
                foreach ($apply->details as $detail) {
                    ?>
                    <tr>
                        <td>
                            <?php echo $detail->payment['period']; ?>
                        </td>
                        <td>
                            <?php
                            echo $this->map["pay_type"][$detail->payment['expense_type']]['name'];
                            if ($detail->payment['expense_type'] == 5)
                                echo '--' . $detail->payment['expense_name'];
                            ?>
                        </td>
                        <td>
                            <?php echo $this->map['currency'][$detail->payment['currency']]["ico"]; ?><?php echo number_format($detail->payment['amount'] / 100, 2); ?>
                        </td>

                        <td>
                            <?php echo $this->map['currency'][$detail->payment['currency']]["ico"]; ?>
                            <?php
                            $amount = $detail['amount'];
                            if ($apply->status == PayApplication::STATUS_STOP)
                                $amount = $detail['amount_paid'];
                            echo $apply->status >= PayApplication::STATUS_SUBMIT ? number_format(($detail->payment['amount_paid'] - $amount) / 100, 2) : number_format($detail->payment['amount_paid'] / 100, 2);
                            ?>
                        </td>
                        <td>
                            <?php echo $this->map['currency'][$detail->payment['currency']]["ico"]; ?>
                            <?php
                            /*if($detail->payment['amount'] < $detail->payment['amount_paid'])
                                echo number_format(($detail->payment['amount']-($detail->payment['amount_paid']-$detail->amount))/100, 2);
                            else*/
                            echo number_format(($detail->payment['amount'] - $detail->payment['amount_paid']) / 100, 2);
                            ?>
                        </td>
                        <td>
                            <?php echo $this->map['currency'][$apply['currency']]["ico"]; ?><?php echo number_format($detail['amount'] / 100, 2); ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>
<?php } ?>
<?php if (empty($apply->contract_id) && is_array($apply->details) && count($apply->details) > 0) { ?>
    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>相关合同</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <table class="table table-hover">
            <thead>
            <tr>
                <th style="width:120px;">合同类型</th>
                <th style="width:200px; text-align: left;">合同编号</th>
                <th style="width:160px; text-align: left;">项目编号</th>
                <th style="text-align: left;">本次付款金额</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (is_array($apply->details))
                foreach ($apply->details as $detail) {
                    ?>
                    <tr>
                        <td>
                            <?php echo Map::$v["buy_sell_type"][$detail->contract->type]; ?>
                        </td>
                        <td>
                            <?php echo $detail->contract->contract_code ?>
                        </td>
                        <td>
                            <?php echo $detail->project->project_code ?>
                        </td>
                        <td>
                            <?php echo $this->map['currency'][$apply['currency']]["ico"]; ?><?php echo number_format($detail['amount'] / 100, 2); ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>
<?php } ?>
<div class="content-wrap">
    <div class="content-wrap-title">
        <div>
            <p>付款信息</p>
            <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
        </div>
    </div>
    <div class="o-row" style="word-break: break-all;">
        <div class="o-col-sm-8" style="display: flex;">
            <div class="pay-info border-theme" style="flex: 1; width: 100%;">
                <div class="flex-grid form-group align-start">
                    <div class="col field col-count-2 flex-grid align-start">
                        <span  class="form-cell-title w-fixed">付款合同编号：</span>
                        <span  class="form-control-static"><?php echo $apply->sub_contract_code ?></span>
                    </div>
                    <div class="col field col-count-2 flex-grid align-start">
                        <span  class="form-cell-title w-fixed">付款合同类别：</span>
                        <span  class="form-control-static"><?php echo $this->map["contract_category"][$apply->sub_contract_type]; ?></span>
                    </div>
                </div>
                <div class="flex-grid align-start form-group">
                    <div class="col field col-count-2 flex-grid align-start">
                        <span  class="form-cell-title w-fixed">付款币种：</span>
                        <span  class="form-control-static"><?php echo $this->map["currency_type"][$apply->currency] ?></span>
                    </div>
                    <div class="col field col-count-2 flex-grid align-start">
                        <span  class="form-cell-title w-fixed">付款金额：</span>
                        <span  class="form-control-static"><?php echo $this->map["currency"][$apply->currency]["ico"] . number_format($apply->amount / 100, 2) ?></span>
                    </div>
                </div>
                <div class="flex-grid align-start form-group">
                    <div class="col field col-count-2 flex-grid align-start">
                        <span  class="form-cell-title w-fixed">止付金额：</span>
                        <span  class="form-control-static"><?php echo $this->map["currency"][$apply->currency]["ico"] . number_format((PayService::getStopPayAmount($apply->apply_id)) / 100, 2) ?></span>
                    </div>
                    <div class="col field col-count-2 flex-grid align-start">
                        <span  class="form-cell-title w-fixed">用途：</span>
                        <span  class="form-control-static"><?php echo $apply->subject->name ?></span>
                    </div>
                </div>
                <?php if (!empty($apply->factor)) { ?>
                    <div class="flex-grid align-start form-group">
                        <div class="col field col-count-2 flex-grid align-start">
                            <span  class="form-cell-title w-fixed">是否对接保理：</span>
                            <span  class="form-control-static"><?php if ($apply->is_factoring) echo "对接保理"; else echo "不对接保理"; ?></span>
                        </div>
                        <div class="col field col-count-2 flex-grid align-start">
                            <span  class="form-cell-title w-fixed">保理金额：</span>
                            <span  class="form-control-static"><?php echo $apply->is_factoring ? number_format($apply->amount_factoring / 100, 2) : '-' ?></span>
                        </div>
                    </div>

                    <div class="flex-grid align-start form-group">
                        <div class="col field col-count-2 flex-grid align-start">
                            <span  class="form-cell-title w-fixed">保理对接编号：</span>
                            <span  class="form-control-static"><?php echo $apply->factor->contract_code ?></span>
                        </div>
                        <div class="col field col-count-2 flex-grid align-start">
                            <span  class="form-cell-title w-fixed">资金对接编号：</span>
                            <span  class="form-control-static"><?php echo $apply->factor->contract_code_fund ?></span>
                        </div>
                    </div>
                <?php } ?>
                <div class="flex-grid align-start form-group">
                    <span  class="form-cell-title w-fixed">付款原因：</span>
                    <span  class="form-control-static color-emphasis"><?php echo $apply->remark . $apply->extra->remark; ?></span>
                </div>
                <div class="flex-grid align-start form-group">
                    <span  class="form-cell-title w-fixed">附件：</span>
                    <ul  class="form-control-static ellipsis">
                        <?php

                        $attachments = AttachmentService::getAttachments(Attachment::C_PAY_APPLICATION, $apply->apply_id, 1);
                        if (is_array($attachments) && count($attachments) > 0) {

                            foreach ($attachments as $v) {
                                foreach ($v as $file) {
                                    echo "<li><a href='/pay/getFile/?id=" . $file["id"] . "&fileName=" . $file['name'] . "' title='" . $file['name'] . "' target='_blank' class='text-link'>" . $file['name'] . "</a></li>";
                                }
                            }
                        } else {
                            echo "无";
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="o-col-sm-4" style="display: flex; padding-left: 15px;">
            <div class="receive-money border-blue" style="flex: 1; width: 100%;">
                <div class="flex-grid align-start form-group">
                    <span  class="form-cell-title w-fixed">收款单位：</span>
                    <span  class="form-control-static"><?php echo $apply->payee ?></span>
                </div>
                <div class="flex-grid form-group align-start">
                    <span  class="form-cell-title w-fixed">收款账户名：</span>
                    <span  class="form-control-static"><?php echo $apply->account_name ?></span>
                </div>
                <div class="flex-grid form-group align-start">
                    <span  class="form-cell-title w-fixed">开户银行：</span>
                    <span  class="form-control-static"><?php echo $apply->bank ?></span>
                </div>
                <div class="flex-grid form-group align-start">
                    <span  class="form-cell-title w-fixed">银行帐号：</span>
                    <span  class="form-control-static"><?php echo $apply->account ?></span>
                </div>
            </div>
        </div>
    </div>

</div>
<?php if (!empty($apply->extra)) { ?>
    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>风险提示信息</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <ul style="display: flex; flex-wrap: wrap" class="children-left-margin">
            <?php
            foreach (Map::$v["pay_application_extra"] as $k => $item) {
                $class = "label-info";
                if ($apply->extra->items[$k]) {
                    $class = "label-emphasis";
                } else {
                    $class = "label-info";
                }
                ?>
                <li style="flex: 0 0 auto" class="child">
                    <label class="<?php echo $class ?>"><?php echo $item["name"] ?></label>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>