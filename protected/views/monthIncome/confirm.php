<section class="content">
<div class="box">
    <div class="box-header">
        <p class="form-control-static"><b>单&emsp;&emsp;号：</b><?php echo $codeId ?>
            <span class="pull-right box-tools ">
                <button type="button" class=" btn btn-danger btn-sm" onclick="submit()">收入确认</button>&emsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <button type="button" class=" btn btn-default btn-sm" onclick="back()">取消</button>
            </span>
            <!-- <span class="pull-right" style="vertical-align:middle;">
                <button class="btn btn-sm btn-default" onclick="showData()"><i class="fa fa-search"></i></button>
            </span> -->
        </p>
        <p class="form-control-static"><b>交易主体：</b><?php echo $args["corp_name"] ?>
            <span class="pull-right" style="vertical-align:middle;">所属期间：&emsp;
                <input type="text" id="start_date" name="start_date" class="form-control input-sm pull-right" value="<?php echo date_format(new DateTime($args['start_date']),'Y-m') ?>" style="width: 150px;" placeholder="所属期间">
            </span>
        </p>
    </div>
    <div class="box-body">
        <div class="box-group">
            <div class="box box-primary" style="margin-bottom: 15px;">
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover table-bordered table-layout">
                        <tbody>
                            <tr>
                                <th rowspan="2" style="text-align:center;vertical-align:middle;">品名</th>
                                <th rowspan="2" style="text-align:center;vertical-align:middle;">实际数量</th>
                                <th colspan="3" style="text-align:center;vertical-align:middle;">销售</th>
                                <th colspan="3" style="text-align:center;vertical-align:middle;">采购</th>
    
                            </tr>
                            <tr>
                                <th style="text-align:center;vertical-align:middle;">含税金额</th>
                                <th style="text-align:center;vertical-align:middle;">不含税金额</th>
                                <th style="text-align:center;vertical-align:middle;">税额</th>
                                <th style="text-align:center;vertical-align:middle;">含税金额</th>
                                <th style="text-align:center;vertical-align:middle;">不含税金额</th>
                                <th style="text-align:center;vertical-align:middle;">税额</th>
                            </tr>
                            <?php foreach ($incomeArr as $key => $value) { ?>
                            <tr>
                                <td style="text-align:left;"><?php echo $this->map['goods_type'][$key] ?></td>
                                <td style="text-align:right;"><?php echo $value['quantity'] ?></td>
                                <td style="text-align:right;">￥<?php echo number_format($value["tax_sales_amount"]/100,2) ?></td>
                                <td style="text-align:right;">￥<?php echo number_format($value["sales_amount"]/100,2) ?></td>
                                <td style="text-align:right;">￥<?php echo number_format($value["tax_sales"]/100,2) ?></td>
                                <td style="text-align:right;">￥<?php echo number_format($value["tax_purchase_amount"]/100,2) ?></td>
                                <td style="text-align:right;">￥<?php echo number_format($value["purchase_amount"]/100,2) ?></td>
                                <td style="text-align:right;">￥<?php echo number_format($value["tax_purchase"]/100,2) ?></td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <th style="text-align:center;">小计</th>
                                <td class="bg-info" style="text-align:right;"><?php echo $totalArr["quantity_total"] ?></td>
                                <td class="bg-info" style="text-align:right;">￥<?php echo number_format($totalArr["tax_sales_amount_total"]/100,2) ?></td>
                                <td class="bg-info" style="text-align:right;">￥<?php echo number_format($totalArr["sales_amount_total"]/100,2) ?></td>
                                <td class="bg-info" style="text-align:right;">￥<?php echo number_format($totalArr["tax_sales_total"]/100,2) ?></td>
                                <td class="bg-info" style="text-align:right;">￥<?php echo number_format($totalArr["tax_purchase_amount_total"]/100,2) ?></td>
                                <td class="bg-info" style="text-align:right;">￥<?php echo number_format($totalArr["purchase_amount_total"]/100,2) ?></td>
                                <td class="bg-info" style="text-align:right;">￥<?php echo number_format($totalArr["tax_purchase_total"]/100,2) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="box box-success" style="margin-bottom: 15px;">
                <div class="box-header">
                    <h4 class="box-title">
                        附：明细
                    </h4>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-hover table-bordered table-layout" style="min-width:1650px;">
                        <tbody>
                            <tr>
                                <th rowspan="2" style="width:50px;text-align:center;vertical-align:middle;">序号</th>
                                <th rowspan="2" style="text-align:center;vertical-align:middle;">交易主体</th>
                                <th rowspan="2" style="text-align:center;vertical-align:middle;">销售出库单号</th>
                                <th rowspan="2" style="text-align:center;vertical-align:middle;">下游合作方</th>
                                <th rowspan="2" style="text-align:center;vertical-align:middle;">采购合同单号</th>
                                <th rowspan="2" style="text-align:center;vertical-align:middle;">上游合作方</th>
                                <th rowspan="2" style="text-align:center;vertical-align:middle;">交易品种</th>
                                <th colspan="4" style="text-align:center;vertical-align:middle;">销售</th>
                                <th colspan="3" style="text-align:center;vertical-align:middle;">采购</th>
                            </tr>
                            <tr>
                                <th style="text-align:center;vertical-align:middle;">出库数量</th>
                                <th style="text-align:center;vertical-align:middle;">销售单价</th>
                                <th style="text-align:center;vertical-align:middle;">销售金额</th>
                                <th style="text-align:center;vertical-align:middle;">开票日期</th>
                                <th style="text-align:center;vertical-align:middle;">实际采购数量</th>
                                <th style="text-align:center;vertical-align:middle;">采购单价</th>
                                <th style="text-align:center;vertical-align:middle;">实际采购金额</th>
                            </tr>
                            <?php foreach ($data as $key => $value) { ?>
                            <tr>
                                <td style="text-align:center;"><?php echo $key+1 ?></td>
                                <td style="text-align:left;width:120px;"><a id="t_<?php echo corporation_id ?>" title="<?php echo $value['corporation_name'] ?>" target="_blank" href="/corporation/detail/?id=<?php echo corporation_id ?>&t=1" ><?php echo $value['corporation_name'] ?></a></td>
                                <td style="text-align:center;"><a id="t_<?php echo $value['project_id'] ?>" title="查看详情" target="_blank" href="/project/detail/?id=<?php echo $value['project_id'] ?>&t=1" ><?php echo $value['project_id'] ?></a></td>
                                <td style="text-align:left;width:120px;"><a id="t_<?php echo $value['down_name'] ?>" title="<?php echo $value['down_name'] ?>" target="_blank" href="/partner/detail/?id=<?php echo $value['down_partner_id'] ?>&t=1" ><?php echo $value['down_name'] ?></a></td>
                                <td style="text-align:center;"><a id="t_<?php echo $value['project_id'] ?>" title="查看详情" target="_blank" href="/project/detail/?id=<?php echo $value['project_id'] ?>&t=1" ><?php echo $value['project_id'] ?></a></td>
                                <td style="text-align:left;width:120px;"><a id="t_<?php echo $value['up_name'] ?>" title="<?php echo $value['up_name'] ?>" target="_blank" href="/partner/detail/?id=<?php echo $value['up_partner_id'] ?>&t=1" ><?php echo $value['up_name'] ?></a></td>
                                <td><?php echo $this->map['goods_type'][$value['goods_type']] ?></td>
                                <td style="text-align:right;"><?php echo $value['quantity'] ?></td>
                                <td style="text-align:right;">￥<?php echo number_format($value["sd_price"]/100,2) ?></td>
                                <td style="text-align:right;">￥<?php echo number_format($value["invoice_amount"]/100,2) ?></td>
                                <td style="text-align:center;"><?php echo $value['invoice_date_actual'] ?></td>
                                <td style="text-align:right;"><?php echo $value['quantity'] ?></td>
                                <td style="text-align:right;">￥<?php echo number_format($value["su_price"]/100,2) ?></td>
                                <td style="text-align:right;">￥<?php echo number_format($value["purchase_amount"]/100,2) ?></td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <th style="text-align:center;width:50px;">小计</th>
                                <td style="text-align:center;"></td>
                                <td style="text-align:center;"></td>
                                <td style="text-align:left;"></td>
                                <td style="text-align:center;"></td>
                                <td style="text-align:left;"></td>
                                <td style="text-align:left;" ></td>
                                <td class="bg-info" style="text-align:right;"><?php echo $dtArr['quantity_total'] ?></td>
                                <td class="bg-info" style="text-align:right;">￥<?php echo number_format($dtArr["sell_price_total"]/100,2) ?></td>
                                <td class="bg-info" style="text-align:right;">￥<?php echo number_format($dtArr["sell_amount_total"]/100,2) ?></td>
                                <td style="text-align:center;"></td>
                                <td class="bg-info" style="text-align:right;"><?php echo $dtArr['quantity_total'] ?></td>
                                <td class="bg-info" style="text-align:right;">￥<?php echo number_format($dtArr["purchase_price_total"]/100,2) ?></td>
                                <td class="bg-info" style="text-align:right;">￥<?php echo number_format($dtArr["purchase_amount_total"]/100,2) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</section>
<script>
    $(function(){
        $("#start_date").datetimepicker({format: 'yyyy-mm',startView: 'year',minView: 'year',forceParse: false});
    });

    function back()
    {
        history.back();
    }

    function submit()
    {
        if(confirm("您确定要提交当前收入确认单据吗，该操作不可逆？")) {
            var startDate = $('#start_date').val().replace("-","");
            /*var formData = "id='<?php echo $args['id'] ?>'&corp_id=<?php echo $args['corp_id'] ?>"+
                           "&code_id=<?php echo $codeId ?>&total_amount="+
                           JSON.stringify(<?php echo json_encode($totalArr) ?>)+
                           "&start_date="+startDate+
                           "&income_amount="+JSON.stringify(<?php echo json_encode($incomeArr) ?>);*/
            var formData = {
                id:"<?php echo $args['id'] ?>",
                corp_id:<?php echo $args['corp_id'] ?>,
                corp_name:"<?php echo $args['corp_name'] ?>",
                code_id:<?php echo $codeId ?>,
                start_date:startDate,
                total_amount:JSON.stringify(<?php echo json_encode($totalArr) ?>),
                income_items:JSON.stringify(<?php echo json_encode($incomeArr) ?>),
                income_detail:JSON.stringify(<?php echo json_encode($data) ?>)
            };
            $.ajax({
                type: 'POST',
                url: '/monthIncome/save',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        inc.showNotice("操作成功");
                        location.href="/monthIncome/detail/?id="+json.data;
                    }
                    else {
                        alertModel(json.data);
                    }
                },
                error: function (data) {
                    alertModel("操作失败！" + data.responseText);
                }
            });
        }

    }
</script>