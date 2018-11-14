<!DOCTYPE html>
<html>
    <?php include (ROOT_DIR.'/protected/views/layouts/header.php'); ?>
    <style>
    #print-container{
      width: 1000px;
      padding: 0;
      margin:10px auto;
    }
    td,th{ 
      border:1px solid black;
      /*border:1px solid #999999;*/
      text-align: left;
      font-size: 13px;
      padding: 0;
      margin: 0;
    }
    div.rotate_right
    {
        width:1000px;
        float:left;
        -ms-transform:rotate(-8deg); /* IE 9 */
        -moz-transform:rotate(-8deg); /* Firefox */
        -webkit-transform:rotate(-7deg); /* Safari and Chrome */
        -o-transform:rotate(-8deg); /* Opera */
        transform:rotate(-7deg);
    }
    p.caption
    {
        font-size : 3em;
        color  : gray;
        filter : alpha(opacity=20);
        -moz-opacity:0.2; 
        -khtml-opacity: 0.2; 
        opacity: 0.2; 
        position:absolute 
    }
    </style>
    <body>
        <div id="print-container">
            <div id="u3">
                <div id="u4">
                  <h1 align="center">收入-成本&nbsp;确认表<?php if($data[0]['income_status']<1) echo '（已作废）' ?></h1>
                  <div align="center">&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
                  &emsp;&emsp;&emsp;&emsp;&emsp;---------------<?php echo $data[0]['corporation_name'] ?></div>
                </div>
                <?php if($data[0]['income_status']<1){ ?>
                <div class="rotate_right">
                  <p id="water" class="caption"></p>
                </div>
                <?php } ?>
            </div>
            
            <p class="form-control-static">
                <b>所属期间：</b><?php echo $data[0]['account_period'] ?>
                <span class="pull-right">
                    <b>单&emsp;&emsp;号：</b><?php echo $data[0]['code'] ?>
                </span>
            </p>
            <table width="1000" border="1" cellspacing="0" cellpadding="0" align="center">
                <tbody>
                    <tr>
                        <th height="30px" rowspan="2" style="text-align:center;vertical-align:middle;">品名</th>
                        <th height="30px" rowspan="2" style="text-align:center;vertical-align:middle;">实际数量</th>
                        <th height="30px" colspan="3" style="text-align:center;vertical-align:middle;">销售</th>
                        <th height="30px" colspan="3" style="text-align:center;vertical-align:middle;">采购</th>    
                    </tr>
                    <tr>
                        <th height="30px" style="text-align:center;vertical-align:middle;">含税金额(元)</th>
                        <th height="30px" style="text-align:center;vertical-align:middle;">不含税金额(元)</th>
                        <th height="30px" style="text-align:center;vertical-align:middle;">税额(元)</th>
                        <th height="30px" style="text-align:center;vertical-align:middle;">含税金额(元)</th>
                        <th height="30px" style="text-align:center;vertical-align:middle;">不含税金额(元)</th>
                        <th height="30px" style="text-align:center;vertical-align:middle;">税额(元)</th>
                    </tr>
                    <?php foreach ($incomeArr as $key => $value) { ?>
                    <tr>
                        <td height="30px" style="text-align:left;"><?php echo $this->map['goods_type'][$key] ?></td>
                        <td height="30px" style="text-align:right;"><?php echo $value['quantity'] ?></td>
                        <td height="30px" style="text-align:right;"><?php echo number_format($value["tax_sales_amount"]/100,2) ?></td>
                        <td height="30px" style="text-align:right;"><?php echo number_format($value["sales_amount"]/100,2) ?></td>
                        <td height="30px" style="text-align:right;"><?php echo number_format($value["tax_sales"]/100,2) ?></td>
                        <td height="30px" style="text-align:right;"><?php echo number_format($value["tax_purchase_amount"]/100,2) ?></td>
                        <td height="30px" style="text-align:right;"><?php echo number_format($value["purchase_amount"]/100,2) ?></td>
                        <td height="30px" style="text-align:right;"><?php echo number_format($value["tax_purchase"]/100,2) ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <th height="30px" style="text-align:center;">小计</th>
                        <td height="30px" style="text-align:right;"><?php echo $totalArr["quantity_total"] ?></td>
                        <td height="30px" style="text-align:right;"><?php echo number_format($totalArr["tax_sales_amount_total"]/100,2) ?></td>
                        <td height="30px" style="text-align:right;"><?php echo number_format($totalArr["sales_amount_total"]/100,2) ?></td>
                        <td height="30px" style="text-align:right;"><?php echo number_format($totalArr["tax_sales_total"]/100,2) ?></td>
                        <td height="30px" style="text-align:right;"><?php echo number_format($totalArr["tax_purchase_amount_total"]/100,2) ?></td>
                        <td height="30px" style="text-align:right;"><?php echo number_format($totalArr["purchase_amount_total"]/100,2) ?></td>
                        <td height="30px" style="text-align:right;"><?php echo number_format($totalArr["tax_purchase_total"]/100,2) ?></td>
                    </tr>
                </tbody>
            </table>
            <br/>
            <h4>附：明细</h4>
        </div>
        <table width="1000" border="1" cellspacing="0" cellpadding="0" align="center">
            <tbody>
                <tr>
                    <th height="30px" rowspan="2" style="text-align:center;vertical-align:middle;">序号</th>
                    <th height="30px" rowspan="2" style="text-align:center;vertical-align:middle;">交易主体</th>
                    <th height="30px" rowspan="2" style="text-align:center;vertical-align:middle;">销售出库单号</th>
                    <th height="30px" rowspan="2" style="text-align:center;vertical-align:middle;">下游合作方</th>
                    <th height="30px" rowspan="2" style="text-align:center;vertical-align:middle;">采购合同单号</th>
                    <th height="30px" rowspan="2" style="text-align:center;vertical-align:middle;">上游合作方</th>
                    <th height="30px" rowspan="2" style="text-align:center;vertical-align:middle;">交易品种</th>
                    <th height="30px" colspan="4" style="text-align:center;vertical-align:middle;">销售</th>
                    <th height="30px" colspan="3" style="text-align:center;vertical-align:middle;">采购</th>
                </tr>
                <tr>
                    <th height="30px" style="text-align:center;vertical-align:middle;">出库数量</th>
                    <th height="30px" style="text-align:center;vertical-align:middle;">销售单价(元)</th>
                    <th height="30px" style="text-align:center;vertical-align:middle;">销售金额(元)</th>
                    <th height="30px" style="text-align:center;vertical-align:middle;">开票日期</th>
                    <th height="30px" style="text-align:center;vertical-align:middle;">采购数量</th>
                    <th height="30px" style="text-align:center;vertical-align:middle;">采购单价(元)</th>
                    <th height="30px" style="text-align:center;vertical-align:middle;">采购金额(元)</th>
                </tr>
                <?php foreach ($data as $key => $value) { ?>
                <tr>
                    <td height="30px" style="text-align:center;width:50px;"><?php echo $key+1 ?></td>
                    <td height="30px" style="text-align:left;width:150px;"><?php echo $value['corporation_name'] ?></td>
                    <td height="30px" style="text-align:center;"><?php echo $value['project_id'] ?></td>
                    <td height="30px" style="text-align:left;width:120px;"><?php echo $value['down_name'] ?></td>
                    <td height="30px" style="text-align:center;"><?php echo $value['project_id'] ?></td>
                    <td height="30px" style="text-align:left;width:120px;"><?php echo $value['up_name'] ?></td>
                    <td height="30px" style="text-align:left;width:100px;" ><?php echo $this->map['goods_type'][$value['goods_type']] ?></td>
                    <td height="30px" style="text-align:right;width:80px;"><?php echo $value['quantity'] ?></td>
                    <td height="30px" style="text-align:right;width:80px;"><?php echo number_format($value["sd_price"]/100,2) ?></td>
                    <td height="30px" style="text-align:right;width:100px;"><?php echo number_format($value["invoice_amount"]/100,2) ?></td>
                    <td height="30px" style="text-align:center;width:100px;"><?php echo $value['invoice_date_actual'] ?></td>
                    <td height="30px" style="text-align:right;width:80px;"><?php echo $value['quantity'] ?></td>
                    <td height="30px" style="text-align:right;width:80px;"><?php echo number_format($value["su_price"]/100,2) ?></td>
                    <td height="30px" style="text-align:right;width:100px;"><?php echo number_format($value["purchase_amount"]/100,2) ?></td>
                </tr>
                <?php } ?>
                <tr>
                    <th height="30px" style="text-align:center;width:50px;">小计</th>
                    <td height="30px" style="text-align:left;width:150px;"></td>
                    <td height="30px" style="text-align:center;"></td>
                    <td height="30px" style="text-align:left;width:120px;"></td>
                    <td height="30px" style="text-align:center;"></td>
                    <td height="30px" style="text-align:left;width:120px;"></td>
                    <td height="30px" style="text-align:left;width:100px;" ></td>
                    <td height="30px" style="text-align:right;width:80px;"><?php echo $dtArr['quantity_total'] ?></td>
                    <td height="30px" style="text-align:right;width:80px;"><?php echo number_format($dtArr["sell_price_total"]/100,2) ?></td>
                    <td height="30px" style="text-align:right;width:100px;"><?php echo number_format($dtArr["sell_amount_total"]/100,2) ?></td>
                    <td height="30px" style="text-align:center;width:100px;"></td>
                    <td height="30px" style="text-align:right;width:80px;"><?php echo $dtArr['quantity_total'] ?></td>
                    <td height="30px" style="text-align:right;width:80px;"><?php echo number_format($dtArr["purchase_price_total"]/100,2) ?></td>
                    <td height="30px" style="text-align:right;width:100px;"><?php echo number_format($dtArr["purchase_amount_total"]/100,2) ?></td>
                </tr>
            </tbody>
        </table>
        <br/>
        <div id="print-container" >
            <div class="pull-right">&emsp;制单人：<?php $prefix=Mod::app()->params['prefix']; $name=utf8_decode($_COOKIE[$prefix."system_user_name"]); echo $name;?>
                <?php 
                    if(strlen($name)==9){
                        echo '&emsp;&nbsp;&nbsp;&nbsp;';
                    }else{
                        echo '&emsp;&emsp;&nbsp;&nbsp;&nbsp;';
                    }
                ?>
            </div><br/>
            <div class="pull-right">打印日期：<?php echo date("Y.m.d")?></div>
        </div>   
        <br/>
    </body>
</html>
<script>
    $(function(){
        var conHeight = 100;
        var conWidth = 1000;
        var num = Math.ceil(conHeight*conWidth/1000/25);
        for(i=0;i<num;i++)
            $('#water').append('已作废&emsp;&emsp;&nbsp;&nbsp;');
        $('#water').append('<br/><br/><br/>');
        for(i=0;i<num-1;i++)
            $('#water').append('&emsp;&emsp;&emsp;&emsp;已作废');
        $('#water').append('<br/><br/><br/>');
        for(i=0;i<num;i++)
            $('#water').append('已作废&emsp;&emsp;&nbsp;&nbsp;');
    });
</script>