<section class="content">
<div class="box">
    <div class="box-header">
        <div class="pull-right box-tools">
            <button type="button" class="btn btn-primary btn-sm" onclick="print()">打印单据</button>
        </div>
        <div id="u3">
          <div id="u4">
            <h1 align="center">收入-成本&nbsp;确认表<?php  if($data[0]['income_status']<1) echo '（已作废）' ?></h1>
            <div align="center">&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
            &emsp;&emsp;&emsp;&emsp;&emsp;---------------<?php echo $data[0]['corporation_name'] ?></div>
          </div>
        </div>
        <p class="form-control-static"><b>所属期间：</b><?php echo $data[0]['account_period'] ?>
            <span class="pull-right box-tools ">
                <b>单&emsp;&emsp;号：</b><?php echo $data[0]['code'] ?>
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
                    <!-- <h4 class="box-title pull-right"> -->
                    <?php if($data[0]['income_status']==1){ ?>
                    <span class="pull-left">
                        <button type="button" class="btn btn-danger btn-sm" onclick="cancel()">作废</button>
                    </span>
                    <?php } ?>
                    <span class="pull-right">
                        <button type="button" class="btn btn-primary btn-sm" onclick="submit()">导出明细</button>&nbsp;
                        <button type="button" class="btn btn-default btn-sm" onclick="back()">返回</button>
                    </span>
                    <!-- </h4> -->

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
                                <th style="width:100px;text-align:center;vertical-align:middle;">出库数量</th>
                                <th style="width:100px;text-align:center;vertical-align:middle;">销售单价</th>
                                <th style="width:150px;text-align:center;vertical-align:middle;">销售金额</th>
                                <th style="width:100px;text-align:center;vertical-align:middle;">开票日期</th>
                                <th style="width:100px;text-align:center;vertical-align:middle;">实际采购数量</th>
                                <th style="width:100px;text-align:center;vertical-align:middle;">采购单价</th>
                                <th style="width:150px;text-align:center;vertical-align:middle;">实际采购金额</th>
                            </tr>
                            <?php foreach ($data as $key => $value) { ?>
                            <tr>
                                <td style="text-align:center;"><?php echo $key+1 ?></td>
                                <td style="text-align:left;"><a id="t_<?php echo corporation_id ?>" title="<?php echo $value['corporation_name'] ?>" target="_blank" href="/corporation/detail/?id=<?php echo corporation_id ?>&t=1" ><?php echo $value['corporation_name'] ?></a></td>
                                <td style="text-align:center;"><a id="t_<?php echo $value['project_id'] ?>" title="查看详情" target="_blank" href="/project/detail/?id=<?php echo $value['project_id'] ?>&t=1" ><?php echo $value['project_id'] ?></a></td>
                                <td style="text-align:left;"><a id="t_<?php echo $value['down_name'] ?>" title="<?php echo $value['down_name'] ?>" target="_blank" href="/partner/detail/?id=<?php echo $value['down_partner_id'] ?>&t=1" ><?php echo $value['down_name'] ?></a></td>
                                <td style="text-align:center;"><a id="t_<?php echo $value['project_id'] ?>" title="查看详情" target="_blank" href="/project/detail/?id=<?php echo $value['project_id'] ?>&t=1" ><?php echo $value['project_id'] ?></a></td>
                                <td style="text-align:left;"><a id="t_<?php echo $value['up_name'] ?>" title="<?php echo $value['up_name'] ?>" target="_blank" href="/partner/detail/?id=<?php echo $value['up_partner_id'] ?>&t=1" ><?php echo $value['up_name'] ?></a></td>
                                <td style="text-align:left;"><?php echo $this->map['goods_type'][$value['goods_type']] ?></td>
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
                                <td style="text-align:left;"></td>
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
    function back()
    {
        location.href="/monthIncome/";
    }

    function submit()
    {
        location.href="/monthIncome/export?id=<?php echo $data[0]['statement_id'] ?>&code=<?php echo $data[0]['code'] ?>";
    }

    function cancel()
    {
        location.href = "/monthIncome/edit?id=<?php echo $data[0]['statement_id'] ?>"
    }

    function print()
    {
        inc.openWindow({link:"/monthIncome/detail?id=<?php echo $data[0]['statement_id'] ?>&flag=1&t=1"});
        //location.href = "/monthIncome/detail?id=<?php echo $data[0]['statement_id'] ?>&t=1"
    }
</script>