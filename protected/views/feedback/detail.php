<div class="box">
    <div class="box-body form-horizontal">
        <div class="box-header with-border">
            <h3 class="box-title">下游收款计划</h3>
            <div class="pull-right box-tools">
                <button type="button"  class="btn btn-default history-back" onclick="back()">返回</button>
            </div>
        </div>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">销售单价</label>
            <div class="col-sm-4">
                <p class="form-control-static">￥ <?php echo number_format($data["down_price"]/100,2) ?></p>
            </div>
            <label for="type" class="col-sm-2 control-label">项目名称</label>
            <div class="col-sm-4">
                <p class="form-control-static">
                    <a href="/project/detail/?id=<?php echo $data["project_id"] ?>&t=1" target="_blank"><?php echo $data["project_name"] ?></a>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">销售数量</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $data["down_quantity"] ?></p>
            </div>
            <label for="type" class="col-sm-2 control-label">企业名称</label>
            <div class="col-sm-4">
                <p class="form-control-static">
                    <a href="/partner/detail/?id=<?php echo $data["partner_id"]?>&t=1" target="_blank"><?php echo $data["customer_name"]?></a>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">销售金额</label>
            <div class="col-sm-4">
                <p class="form-control-static">￥ <?php echo number_format($data["down_amount"]/100,2) ?></p>
            </div>
            <label for="type" class="col-sm-2 control-label">结算金额</label>
            <div class="col-sm-4">
                <p class="form-control-static">￥ <?php echo number_format($data["settle_amount"]/100,2) ?></p>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-1">
            </div>
            <div class="col-sm-8">
                <?php
                if(!empty($plans))
                {?>
                    <table class="table table-striped table-bordered table-condensed table-hover">
                        <thead>
                        <tr>
                            <th style="width:80px;text-align:center">期数</th>
                            <th style="width:140px;text-align:center">收款间隔</th>
                            <th style="width:180px;text-align:center">收款日期</th>
                            <th style="width:140px;text-align:center">收款形式</th>
                            <th style="width:140px;text-align:center">收款比例</th>
                            <th style="width:240px;text-align:center">金额(元)</th>
                            <th style="width:100px;text-align:center">状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($plans as $v){ ?>
                            <tr>
                                <td style="text-align:center"><?php echo empty($v["period"])?"-":$v["period"] ?></td>
                                <td style="text-align:center"><?php echo $v["receive_days"] ?></td>
                                <td style="text-align:center"><?php echo $v["return_date"] ?></td>
                                <td style="text-align:center">
                                    <?php echo $this->map["receive_time"][$v["receive_type"]] ?>
                                </td>
                                <td style="text-align:center"><?php echo $v["rate"]*100 ?>%</td>
                                <td style="text-align:right" <?php if($v["period"]==$data['period']) echo "class='text-red'" ?>>
                                    <?php echo number_format($v["amount"]/100,2) ?>
                                </td>
                                <td style="text-align:center">
                                    <?php echo $this->map["return_plan_status"][$v["status"]] ?>
                                </td>
                            </tr>
                        <?php  } ?>
                        </tbody>
                    </table>
                <?php  }
                ?>
            </div>
        </div>
        <div class="form-group"></div>
        <div class="box-header with-border">
        </div>
        <h4 class="box-title">开票计划</h4>
        <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">发票申请日期</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $data['invoice_date'] ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">纳税识别号</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $data['tax_code'] ?></p>
                    </div>
                </div>
                <!-- <div class="form-group">
                    <label class="col-sm-2 control-label">开票对象</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">下游</p>
                    </div>
                </div> -->
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">开票名称</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $data["invoice_name"] ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">开户银行</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $data["bank_name"] ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">银行账号</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $data["bank_account"] ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">开票内容</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $data['content'] ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">含税开票金额</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">￥ <?php echo number_format($data["amount"]/100,2) ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">地址</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $data['address'] ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">联系方式</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $data['phone'] ?></p>
                    </div>
                </div>
        <div class="box-header with-border">
        </div>
        <h4 class="box-title">反馈结果</h4>
        <div class="form-group">
            <label class="col-sm-2 control-label">反馈结果</label>
            <div class="col-sm-8">
                <p class="form-control-static"><?php echo $data["feedback"] ?></p>
            </div>
        </div>
        <div class="form-group ">
            <label for="prd_type" class="col-sm-2 control-label">反馈附件</label>
            <div class="col-sm-10">
                <p class="form-control-static">
                <?php if(!empty($data["file_url"]))
                          echo "<a href='/feedback/getFile/?id=".$data["attachment_id"]."&fileName=".$this->map["feedback_attachment_type"][121]['name']."'  target='_blank' class='btn btn-primary btn-xs'>点击查看</a>";
                      else
                        echo "无";
                ?>
              </p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">创建时间</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $data["create_time"] ?></p>
            </div>
            <label class="col-sm-2 control-label">更新时间</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $data["update_time"] ?></p>
            </div>
        </div>
    </div>
    <div class="box-footer">
        <?php if(!$this->isExternal){ ?>
            <button type="button"  class="btn btn-default" onclick="back()">返回</button>
        <?php } ?>
    </div>
</div>

<script>
    function back()
    {
        history.back();
    }

</script>
