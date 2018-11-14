<div class="box">
    <div class="box-body form-horizontal">
        <?php if($this->type==1){ ?>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">采购单价</label>
            <div class="col-sm-4">
                <p class="form-control-static">￥ <?php echo number_format($data[0]["up_price"]/100,2) ?></p>
            </div>
            <label for="type" class="col-sm-2 control-label">项目名称</label>
            <div class="col-sm-4">
                <p class="form-control-static">
                    <a href="/project/detail/?id=<?php echo $data[0]["project_id"] ?>&t=1" target="_blank"><?php echo $data[0]["project_name"] ?></a>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">采购数量</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $data[0]["up_quantity"] ?></p>
            </div>
            <label for="type" class="col-sm-2 control-label">企业名称</label>
            <div class="col-sm-4">
                <p class="form-control-static">
                    <a href="/partner/detail/?id=<?php echo $data[0]["partner_id"]?>&t=1" target="_blank"><?php echo $data[0]["customer_name"]?></a>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">采购金额</label>
            <div class="col-sm-4">
                <p class="form-control-static">￥ <?php echo number_format($data[0]["up_amount"]/100,2) ?></p>
            </div>
            <label for="type" class="col-sm-2 control-label">付款期数</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo '第'.$data[0]["period"].'期' ?></p>
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
                            <th style="width:140px;text-align:center">付款间隔</th>
                            <th style="width:180px;text-align:center">付款日期</th>
                            <th style="width:140px;text-align:center">付款形式</th>
                            <th style="width:140px;text-align:center">付款比例</th>
                            <th style="width:240px;text-align:center">金额(元)</th>
                            <th style="width:100px;text-align:center">状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach($plans as $v){ ?>
                            <tr>
                                <td style="text-align:center"><?php echo empty($v["period"]) ? '-' : $v["period"] ?></td>
                                <td style="text-align:center"><?php echo $v["pay_days"] ?></td>
                                <td style="text-align:center"><?php echo $v["pay_date"] ?></td>
                                <td style="text-align:center">
                                    <?php echo $this->map["pay_time"][$v["pay_type"]] ?>
                                </td>
                                <td style="text-align:center"><?php echo $v["rate"]*100 ?>%</td>
                                <td style="text-align:right" <?php if($v["period"]==$data[0]['period']) echo "class='text-red'" ?>>
                                    <?php echo number_format($v["amount"]/100,2) ?>
                                </td>
                                <td style="text-align:center">
                                    <?php 
                                        if($v['type']==1)
                                            echo $this->map["return_plan_status"][$v["status"]];
                                        else
                                            echo $this->map["pay_plan_status"][$v["status"]]; 
                                    ?>
                                </td>
                            </tr>
                        <?php  } ?>
                        </tbody>
                    </table>
                <?php  }
                ?>
            </div>
        </div>
        <?php }else{ ?>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">销售单价</label>
            <div class="col-sm-4">
                <p class="form-control-static">￥ <?php echo number_format($data[0]["down_price"]/100,2) ?></p>
            </div>
            <label for="type" class="col-sm-2 control-label">项目名称</label>
            <div class="col-sm-4">
                <p class="form-control-static">
                    <a href="/project/detail/?id=<?php echo $data[0]["project_id"] ?>&t=1" target="_blank"><?php echo $data[0]["project_name"] ?></a>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">销售数量</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $data[0]["down_quantity"] ?></p>
            </div>
            <label for="type" class="col-sm-2 control-label">企业名称</label>
            <div class="col-sm-4">
                <p class="form-control-static">
                    <a href="/partner/detail/?id=<?php echo $data[0]["partner_id"]?>&t=1" target="_blank"><?php echo $data[0]["customer_name"]?></a>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">销售金额</label>
            <div class="col-sm-4">
                <p class="form-control-static">￥ <?php echo number_format($data[0]["down_amount"]/100,2) ?></p>
            </div>
            <label for="type" class="col-sm-2 control-label">付款期数</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo '第'.$data[0]["period"].'期' ?></p>
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
                                <td style="text-align:right" <?php if($v["period"]==$data[0]['period']) echo "class='text-red'" ?>>
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
        <?php } ?>
        <div class="form-group"></div>
        <table class="table table-bordered">
            <tbody>
            <tr>
              <th style="width: 80px;text-align:center">催收次数</th>
              <th style="width: 190px;text-align:center">通话时间</th>
              <th style="width: 100px;text-align:center">通话凭证</th>
              <th style="width: 80px;text-align:center">还款期数</th>
              <th style="text-align:left">备注</th>
            </tr>
            <?php foreach ($data as $value) {
            ?>
            <tr>
              <td style="text-align:center"><?php echo $value['times'] ?></td>
              <td style="text-align:center"><?php echo $value['call_time'] ?></td>
              <td style="text-align:center"><a href="/<?php echo $this->getId() ?>/getFile/?id=<?php echo $value['id']?>&fileName=<?php echo $this->map['remind_attachment_type'][101]['name'] ?>" target='_blank'>点击查看</a></td>
              <td style="text-align:center"><?php echo $value['period'] ?></td>
              <td><?php echo $value['remark'] ?></td>
            </tr>
            <?php 
            }
            ?>
          </tbody>
		</table>
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
