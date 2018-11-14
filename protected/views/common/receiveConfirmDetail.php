
            <div class="form-group">
                <label class="col-sm-2 control-label">货款(外部)合同编号</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><a href="/businessConfirm/detail?id=<?php echo $receiveConfirm->contract->contract_id ?>"><?php echo $receiveConfirm->contract->contract_code;if(!empty($receiveConfirm->contract_out_code)) echo '('.$receiveConfirm->contract_out_code.')'; ?></a></p>
                </div>
                <label class="col-sm-2 control-label">货款合同类型</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $this->map['buy_sell_type'][$receiveConfirm->contract->type] ?></p>
                </div>
            </div>


            <div class="form-group">
                <label class="col-sm-2 control-label">项目编号</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><a href="/project/detail?id=<?php echo $receiveConfirm->project->project_id ?>"><?php echo $receiveConfirm->project->project_code ?></a></p>
                    </select>
                </div>
                <label class="col-sm-2 control-label">项目类型</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $this->map['project_type'][$receiveConfirm->project->type] ?></p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">收款合同类型</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $this->map['contract_category'][$receiveConfirm->sub_contract_type] ?></p>
                </div>
                <label class="col-sm-2 control-label">收款合同编号</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $receiveConfirm->sub_contract_code ?></p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">用途</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $receiveConfirm->finSubject->name ?></p>
                </div>
            </div>

            <?php if(!empty($receiveConfirm->receiveDetail)):?>
                <div class="form-group">
                    <label class="col-sm-2 control-label">收款计划</label>
                    <div class="col-sm-11 col-sm-push-1">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th style="width:120px;">期数 </th>
                                <th style="width:120px; text-align: left;">预计收款日期 </th>
                                <th style="width:120px; text-align: left;">收款类别 </th>
                                <th style="width:100px; text-align: left;">币种 </th>
                                <th style="width:200px; text-align: left;">金额 </th>
                                <th style="width:200px; text-align: left;">已收金额 </th>
                                <th style="width:200px; text-align: left;">未收金额 </th>
                                <th style="width:200px; text-align: left;">认领金额</th>
                            </tr>
                            </thead>
                            <tbody >
                            <?php foreach($receiveConfirm->receiveDetail as $detail):?>
                                <tr>
                                    <td>
                                        <p class="form-control-static"><?php echo $detail->paymentPlan->period?></p>
                                    </td>
                                    <td>
                                        <p class="form-control-static"><?php echo $detail->paymentPlan->pay_date?></p>
                                    </td>
                                    <td>
                                        <p class="form-control-static"><?php echo $this->map['proceed_type'][$detail->paymentPlan->expense_type]['name']?></p>
                                    </td>
                                    <td>
                                        <p class="form-control-static"><?php echo $this->map['currency'][$detail->paymentPlan->currency]['name']?></p>
                                    </td>
                                    <td>
                                        <p class="form-control-static"><?php echo number_format($detail->paymentPlan->amount/100, 2 )?></p>
                                    </td>
                                    <td>
                                        <p class="form-control-static"><?php echo number_format($detail->paymentPlan->amount_paid/100, 2 )?></p>
                                    </td>
                                    <td>
                                        <p class="form-control-static"><?php echo number_format(($detail->paymentPlan->amount - $detail->paymentPlan->amount_paid)/100, 2 )?></p>
                                    </td>
                                    <td>
                                        <p class="form-control-static"><?php echo number_format($detail->amount/100, 2 )?></p>
                                    </td>
                                </tr>
                            <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif;?>


            <div class="form-group">
                <label class="col-sm-2 control-label">认领金额</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo number_format($receiveConfirm->amount/100, 2 ) ?></p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">备注</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $receiveConfirm->remark ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">附件 </label>
                <div class="col-sm-10">
                    <p class="form-control-static">
                        <?php 
                        foreach ($attachments as $file) {
                            if (!empty($file[0]["file_url"])) {
                                $file = $file[0];
                                echo "<a href='/receiveConfirm/getFile/?id=" . $file["id"] . "&fileName=" . $file['name'] . "'  target='_blank' class='btn btn-primary btn-xs'>点击查看</a>";
                                } else {
                                echo "无";
                            }
                        }
                        ?>
                    </p>
                </div>
            </div>
