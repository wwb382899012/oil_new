<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">合作方额度占用详情</h3>
            <div class="pull-right box-tools">
                <button type="button" class="btn btn-default btn-sm" onclick="back()">返回</button>
            </div>
        </div>
        <div class="box-body form-horizontal">
            <div class="form-group">
                <label for="type" class="col-sm-2 control-label">企业编号</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $data["partner_id"] ?></p>
                </div>
                <label for="type" class="col-sm-2 control-label">确认额度</label>
                <div class="col-sm-4">
                    <p class="form-control-static">￥ <?php echo number_format($credit["credit_amount"]/100,2) ?></p>
                </div>
            </div>
            <div class="form-group">
                <label for="type" class="col-sm-2 control-label">企业名称</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><a target="_blank" href="/partner/detail/?id=<?php echo $data['partner_id'] ?>&t=1" ><?php echo $data["name"] ?></a></p>
                </div>
                <label for="type" class="col-sm-2 control-label">正占用额度</label>
                <div class="col-sm-4">
                    <p class="form-control-static">￥ <?php echo number_format($credit["use_amount"]/100,2) ?></p>
                </div>
                
            </div>
            <div class="form-group">
                <label for="type" class="col-sm-2 control-label"></label>
                <div class="col-sm-4">
                    <p class="form-control-static"></p>
                </div>
                <label for="type" class="col-sm-2 control-label">剩余额度</label>
                <div class="col-sm-4">
                    <p class="form-control-static">￥ <?php echo number_format(($credit["credit_amount"]-$credit["use_amount"])/100,2) ?></p>
                </div>
            </div>
        </div>
        <div class="box box-primary" style="margin-bottom: 15px;">
            <?php 
                function showCell($row,$self){
                    $s = '';
                    $num = 0;
                    $userAmount = UserCreditUseDetail::model()->findAllToArray('project_id='.$row['project_id']);
                    if(count($userAmount)>0){
                        $s .= '<table width="180" border="0" align="center">';
                        foreach ($userAmount as $key => $value) {
                            $username = UserService::getNameById($value['user_id']);
                            if($value['amount']-$value['amount_free']>0){
                                $s .= '<tr>';
                                $s .= '<td style="text-align:left;">'.$username.'：</td>';
                                $s .= '<td style="text-align:right;">￥ '.number_format(($value['amount']-$value['amount_free'])/100,2).'</td>';
                                $s .= '</tr>';
                                $num++;
                            }
                        }
                        $s .= '</table>';
                    }
                    if(empty($num))
                        $s = '￥ 0.00';
                    return $s;
                }
    
                $table_array = array(
                    array('key' => 'rowno', 'type' => '', 'style' => 'width:50px;text-align:center;vertical-align:middle;', 'text' => '序号'),
                    array('key' => 'project_id', 'type' => '', 'style' => 'width:100px;text-align:center;vertical-align:middle;', 'text' => '项目编号'),
                    array('key' => 'project_id,project_name', 'type' => 'href', 'style' => 'text-align:left;vertical-align:middle;', 'text' => '项目名称','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/project/detail/?id={1}&t=1" >{2}</a>'),
                    array('key' => 'trade_type', 'type' => 'map_val', 'map_name'=>'trade_type', 'style' => 'width:80px;text-align:center;vertical-align:middle;', 'text' => '业务类型'),
                    array('key' => 'plan_amount', 'type' => 'amount', 'style' => 'width:120px;text-align:right;vertical-align:middle;', 'text' => '预计下游应收'),
                    array('key' => 'actual_amount', 'type' => 'amount', 'style' => 'width:120px;text-align:right;vertical-align:middle;', 'text' => '实际下游应收'),
                    array('key' => 'received_amount', 'type' => 'amount', 'style' => 'width:120px;text-align:right;vertical-align:middle;', 'text' => '下游已收'),
                    array('key' => 'unreceive_amount', 'type' => 'amount', 'style' => 'width:120px;text-align:right;vertical-align:middle;', 'text' => '下游未收'),
                    array('key' => 'balance_amount', 'type' => 'amount', 'style' => 'width:120px;text-align:right;vertical-align:middle;', 'text' => '下游占用额度'),
                    array('key' => 'project_id', 'type' => 'href', 'style' => 'width:200px;text-align:center;', 'text' => '个人额度','href_text'=>'showCell'),
                );
                $style = empty($_data_['amountInfo']) ? "min-width:900px;" : "min-width:1150px;";
                $this->show_table($table_array, $_data_['amountInfo'], "", $style,"table-bordered table-layout");
            ?>
        </div>
    </div>
</section>

<script>

    function back() {
        location.href="/partnerReport/";
    }
</script>