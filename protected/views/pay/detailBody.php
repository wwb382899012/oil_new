<div class="form-group">
    <label for="type" class="col-sm-2 control-label">付款申请编号</label>
    <div class="col-sm-4">
        <p class="form-control-static"><a href="/pay/detail/?id=<?php echo $apply->apply_id ?>&t=1" target="_blank"><?php echo $apply->apply_id ?></a></p>
    </div>
    <label for="type" class="col-sm-2 control-label">付款申请状态</label>
    <div class="col-sm-4">
        <p class="form-control-static"><span class="label label-info"><?php
            echo $this->map["pay_application_status"][$apply->status];
            if($apply->status==PayApplication::STATUS_SUBMIT)
            {
                $nodeName=FlowService::getNowCheckNode($apply->apply_id,FlowService::BUSINESS_PAY_APPLICATION);
                echo " - ".$nodeName;
            }
                ?></span></p>
    </div>

</div>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">交易主体</label>
    <div class="col-sm-4">
        <p class="form-control-static"><a href="/corporation/detail/?id=<?php echo $apply->corporation_id ?>&t=1" target="_blank"><?php echo $apply->corporation->name ?></a></p>
    </div>
    <?php if(!empty($apply->contract_id)){
        ?>
        <label for="type" class="col-sm-2 control-label">合作方</label>
        <div class="col-sm-4">
            <p class="form-control-static"><a href="/partner/detail/?id=<?php echo $apply->contract->partner_id ?>&t=1" target="_blank"><?php echo $apply->contract->partner->name ?></a></p>
        </div>
        <?php
    } ?>

</div>
<?php if(!empty($apply->project_id)){
    ?>
    <div class="form-group">
        <label for="type" class="col-sm-2 control-label">项目编号</label>
        <div class="col-sm-4">
            <p class="form-control-static"><a href="/project/detail/?id=<?php echo $apply->project_id ?>&t=1" target="_blank"><?php echo $apply->project->project_code ?></a></p>
        </div>
        <label for="type" class="col-sm-2 control-label">项目类型</label>
        <div class="col-sm-4">
            <p class="form-control-static"><?php echo $this->map["project_type"][$apply->project->type] ?></p>
        </div>

    </div>
    <?php
} ?>
<?php if(!empty($apply->contract_id)){
    ?>
    <div class="form-group">
        <label for="type" class="col-sm-2 control-label">合同编号</label>
        <div class="col-sm-4">
            <p class="form-control-static"><a href="/contract/detail/?id=<?php echo $apply->contract_id ?>&t=1" target="_blank"><?php echo $apply->contract->contract_code ?></a></p>
        </div>
        <label for="type" class="col-sm-2 control-label">合同类型</label>
        <div class="col-sm-4">
            <p class="form-control-static"><?php  echo $this->map["contract_config"][$apply->contract["type"]][$apply->contract['category']]["name"]; ?></p>
        </div>
    </div>
    <?php
} ?>
<?php if(Utility::checkQueryId($apply->contract_id)){ ?>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">合同已实付金额</label>
    <div class="col-sm-4">
        <p class="form-control-static">￥<?php echo Utility::numberFormatFen2Yuan(PayService::getContractActualPaidAmount($apply->contract_id)) ?></p>
    </div>
</div>
<?php } ?>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">付款单已实付金额</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $this->map["currency"][$apply->currency]['ico'] ?> <?php echo Utility::numberFormatFen2Yuan($apply->amount_paid) ?></p>
    </div>
    <label for="type" class="col-sm-2 control-label">付款单未实付金额</label>
    <div class="col-sm-4">
        <p class="form-control-static"><span class="text-red"><?php echo $this->map["currency"][$apply->currency]['ico'] ?> <?php echo Utility::numberFormatFen2Yuan($apply->amount - $apply->amount_paid) ?></span></p>
    </div>
</div>
<?php if(!empty($apply->contract_id) && is_array($apply->details) && count($apply->details)>0){
    ?>
    <h4 class="section-title">相关付款计划</h4>
    <div class="form-group">
        <div class="col-sm-offset-1 col-sm-11">
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
                if(is_array($apply->details))
                    foreach($apply->details as $detail)
                    {?>
                        <tr>
                            <td>
                                <?php echo $detail->payment['period'];?>
                            </td>
                            <td>
                                <?php
                                echo $this->map["pay_type"][$detail->payment['expense_type']]['name'];
                                if($detail->payment['expense_type'] == 5)
                                    echo '--' . $detail->payment['expense_name'];
                                ?>
                            </td>
                            <td>
                                <?php echo $this->map['currency'][$detail->payment['currency']]["ico"];?> <?php echo number_format($detail->payment['amount']/100, 2);?>
                            </td>

                            <td>
                                <?php echo $this->map['currency'][$detail->payment['currency']]["ico"];?> 
                                <?php
                                    $amount = $detail['amount'];
                                    if($apply->status==PayApplication::STATUS_STOP) 
                                        $amount = $detail['amount_paid'];
                                    echo $apply->status>=PayApplication::STATUS_SUBMIT ? number_format(($detail->payment['amount_paid'] - $amount)/100, 2) : number_format($detail->payment['amount_paid']/100, 2);
                                ?>
                            </td>
                            <td>
                                <?php echo $this->map['currency'][$detail->payment['currency']]["ico"];?> 
                                    <?php 
                                        /*if($detail->payment['amount'] < $detail->payment['amount_paid'])
                                            echo number_format(($detail->payment['amount']-($detail->payment['amount_paid']-$detail->amount))/100, 2);
                                        else*/
                                            echo number_format(($detail->payment['amount']-$detail->payment['amount_paid'])/100, 2);
                                    ?>
                            </td>
                            <td>
                                <?php echo $this->map['currency'][$apply['currency']]["ico"];?> <?php echo number_format($detail['amount']/100, 2);?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
} ?>

<?php if(empty($apply->contract_id) && is_array($apply->details) && count($apply->details)>0){
    ?>
    <h4 class="section-title">相关合同</h4>
    <div class="form-group">
        <div class="col-sm-offset-1 col-sm-11">
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
                if(is_array($apply->details))
                    foreach($apply->details as $detail)
                    {?>
                        <tr>
                            <td>
                                <?php echo Map::$v["buy_sell_type"][$detail->contract->type];?>
                            </td>
                            <td>
                                <?php echo $detail->contract->contract_code  ?>
                            </td>
                            <td>
                                <?php echo $detail->project->project_code  ?>
                            </td>
                            <td>
                                <?php echo $this->map['currency'][$apply['currency']]["ico"];?> <?php echo number_format($detail['amount']/100, 2);?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
} ?>

<h4 class="section-title">付款信息</h4>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">付款合同类别</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php
            echo $this->map["contract_category"][$apply->sub_contract_type];
            /*if($apply->type==PayApplication::TYPE_CONTRACT)
                $t=1;
            else
                $t=2;
            echo $this->map["contract_file_categories"][$t][$apply->sub_contract_type]["name"]*/ ?></p>
    </div>
    <label for="type" class="col-sm-2 control-label">付款合同编号</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $apply->sub_contract_code ?></p>
    </div>
</div>

<div class="form-group">
    <label for="type" class="col-sm-2 control-label">收款单位</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $apply->payee ?></p>
    </div>
    <label for="type" class="col-sm-2 control-label">收款账户名</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $apply->account_name ?></p>
    </div>
</div>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">开户银行</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $apply->bank ?></p>
    </div>
    <label for="type" class="col-sm-2 control-label">银行帐号</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $apply->account ?></p>
    </div>
</div>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">用途</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $apply->subject->name ?></p>
    </div>
    <label for="type" class="col-sm-2 control-label">付款币种</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $this->map["currency_type"][$apply->currency] ?></p>
    </div>
</div>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">付款金额</label>
    <div class="col-sm-4">
        <div class="input-group">
            <p class="form-control-static"><?php echo $this->map["currency"][$apply->currency]["ico"].number_format($apply->amount/100,2) ?></p>
        </div>
    </div>

</div>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">止付金额</label>
    <div class="col-sm-4">
        <div class="input-group">
            <p class="form-control-static"><?php echo $this->map["currency"][$apply->currency]["ico"].number_format((PayService::getStopPayAmount($apply->apply_id))/100,2) ?></p>
        </div>
    </div>

</div>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">是否对接保理</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php if($apply->is_factoring) echo "对接保理"; else echo "不对接保理"; ?></p>
    </div>
    <label for="type" class="col-sm-2 control-label">保理金额</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $apply->is_factoring ? number_format($apply->amount_factoring/100,2) : '-' ?></p>
    </div>
</div>
<?php
if(!empty($apply->factor)) { ?>
    <div class="form-group">
        <label for="type" class="col-sm-2 control-label">保理对接编号</label>
        <div class="col-sm-4">
            <p class="form-control-static"><?php echo $apply->factor->contract_code ?></p>
        </div>
        <label for="type" class="col-sm-2 control-label">资金对接编号</label>
        <div class="col-sm-4">
            <p class="form-control-static"><?php echo $apply->factor->contract_code_fund ?></p>
        </div>
    </div>
<?php } ?>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">附件</label>
    <div class="col-sm-10">
        <p class="form-control-static">
            <?php

            $attachments=AttachmentService::getAttachments(Attachment::C_PAY_APPLICATION,$apply->apply_id,1);
            if(is_array($attachments) && count($attachments)>0)
            {

                foreach ($attachments as $v)
                {
                    foreach ($v as $file)
                    {
                        echo "<a href='/pay/getFile/?id=" . $file["id"] . "&fileName=" . $file['name'] . "' title='点击查看' target='_blank' class='btn btn-primary btn-xs'>" . $file['name'] . "</a> <br /><br />";
                    }
                }
            }
            else
            {
                echo "无";
            }
            ?>
        </p>
    </div>
</div>
<div class="form-group">
    <label for="remark" class="col-sm-2 control-label">付款原因</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo  $apply->remark.$apply->extra->remark; ?></p>
    </div>
</div>
<?php if(!empty($apply->extra))
{
    ?>
    <h4 class="section-title">风险提示信息</h4>
    <?php
    foreach (Map::$v["pay_application_extra"] as $k=>$item)
    {
        $class="";
        if($apply->extra->items[$k])
            $class="text-red";
        ?>
        <div class="form-group <?php echo $class ?>">
            <label for="remark" class="col-sm-4 control-label"><?php echo $item["name"] ?></label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $this->map["isNor"][$apply->extra->items[$k]]; ?></p>
            </div>
        </div>
        <?php
    }

}
?>