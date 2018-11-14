<?php
$menus = [['text' => '收付款管理'], ['text' => '付款实付', 'link' => '/payConfirm/'],['text'=>'批量打印']];
$buttons[] = ['text' => '打印', 'attr' => ['onclick' => "preview(1)"]];
$this->loadHeaderWithNewUI($menus, $buttons, '/payConfirm/');

?>



<div class="">

    <!--startprint1-->

    <style type="text/css">

        .box{padding:2px;}
        .table{border:1px solid #777272;width: 770px;margin:0 auto !important;}

        .table tbody tr td,.table thead tr th{border: 1px solid #777272;width:12.5%;}
        .table thead tr th{border: 1px solid #777272 !important;width:12.5%;}
        .bold { font-weight: bold;text-align: center;}
        .table thead tr .payNo { font-weight: bold !important;text-align: center !important;font-size: 16px;}
        .table th.center{text-align: center;width: 15%;}
        .table td{text-align: center;}
        .table td.pay_type{width: 16%;}
        .table td.corporation{width: 30%;}
        .table td.left{text-align: left;width: 16%;}
        .table td.subject_name{width:8%;}

        .table td.center{text-align: center;}
        .form-horizontal{margin: 20px 0 40px 0;}

        @page {
            size: auto;
            margin: 0mm;
        }
    </style>


    <?php if(!empty($rows)):?>
         <?php foreach($rows as $key=>$value): ?>
            <?php
            $checkDetail = FlowService::getCheckDetail($value['apply_id'],$businessId);
            $goods_name = PayService::getGoodsName($value['apply_id'],$value['category'],$value['type']);
            ?>
        <div class="box-body form-horizontal">

            <table class="table">
                <thead>
                <tr style="height:40px !important;line-height: 40px !important;">
                    <th colspan="8" class="payNo">付款申请详单</th>
                </tr>
                </thead>
                <tbody>
                <tr style="height:40px !important;line-height: 40px !important;">
                    <td colspan="4" class="left bold"><?php  echo empty($value['project_type'])?'':(Map::$v['project_type'][$value['project_type']].' - '); ?><?php echo $checkDetail[0]['create_time']; ?></td>
                    <td class="left">付款申请编号</td>
                    <td colspan="3"><?php echo $value['apply_id']; ?><?php if($value['status'] == PayApplication::STATUS_STOP) echo '（已止付）'; ?></td>

                </tr>
                <tr style="height:40px !important;line-height: 40px !important;">
                    <td class="left">交易主体</td>
                    <td colspan="3" class="corporation"><?php echo $value['corporation_name'] ?></td>
                    <td class="left">项目编号</td>
                    <td colspan="3"><?php echo $value['project_code'] ?></td>
                </tr>
                <tr style="height:40px !important;line-height: 40px !important;">
                    <td class="left">申请人</td>
                    <td colspan="3"><?php echo $value['user_name'] ?></td>
                    <!--<td class="left">申请角色</td>
                    <td></td>-->
                    <td class="left">品名</td>
                    <td colspan="3"><?php  echo $goods_name; ?></td>
                </tr>
                <tr style="height:40px !important;line-height: 40px !important;">
                    <td class="left">付款合同编号</td>
                    <td colspan="3"><?php echo $value['sub_contract_code']; ?></td>
                    <td class="left">付款合同类型</td>
                    <td class="pay_type"><?php echo Map::$v['contract_category'][$value['sub_contract_type']]; ?></td>
                    <td class="left subject_name">用途</td>
                    <td><?php echo $value['subject_name']; ?></td>
                </tr>
                <tr style="height:40px !important;line-height: 40px !important;">
                    <td class="left">收款单位</td>
                    <td colspan="3"><?php echo $value['payee']; ?></td>
                    <td class="left">收款账户名</td>
                    <td colspan="3"><?php echo $value['account_name']; ?></td>
                </tr>
                <tr style="height:40px !important;line-height: 40px !important;">
                    <td class="left">开户银行</td>
                    <td colspan="3"><?php echo $value['bank']; ?></td>
                    <td class="left">银行账号</td>
                    <td colspan="3"><?php echo $value['account']; ?></td>
                </tr>
                <tr style="height:40px !important;line-height: 40px !important;">
                    <td class="bold" colspan="8">付款信息</td>
                </tr>
                <tr style="height:40px !important;line-height: 40px !important;">
                    <td class="center">付款申请金额</td>
                    <td colspan="3"><?php echo Map::$v["currency"][$value["currency"]]["ico"].number_format($value['amount']/100,2);?></td>
                    <td class="center">实付金额</td>
                    <td colspan="3"><?php echo Map::$v["currency"][$value["currency"]]["ico"].number_format($value['amount_paid']/100,2);?></td>
                </tr>
                <tr style="height:40px !important;line-height: 40px !important;">
                    <td class="bold" colspan="8">审核记录</td>
                </tr>
                <tr style="height:40px !important;line-height: 40px !important;">
                    <td class="center" colspan="2">审核节点</td>
                    <td class="center" colspan="2">审核人</td>
                    <td class="center" colspan="2">审核意见</td>
                    <td class="center" colspan="2">审核时间</td>
                </tr>
                <?php
                       $checkLogs = FlowService::getCheckLogLast($value['apply_id'],$businessId);
                       if(!empty($checkLogs)){
                        foreach($checkLogs as $log_key=>$log_value){
                ?>
                <tr style="height:40px !important;line-height: 40px !important;">
                    <td class="center" colspan="2"><?php echo $log_value['node_name']; ?></td>
                    <td class="center" colspan="2"><?php echo $log_value['name']; ?></td>
                    <td class="center" colspan="2">同意</td>
                    <td class="center" colspan="2"><?php echo $log_value['check_time']; ?></td>
                </tr>
                <?php }}?>

                </tbody>
            </table>



            <?php if($key<count($rows)-1): //最后一条记录不需要分页符 ?>
            <p style="page-break-after:always"></p>
            <?php endif;?>
        </div>
    <?php endforeach;?>

    <?php endif;?>
    <!--endprint1-->

</div>

<script type="text/javascript">
    function back() {
        location.href="/<?php echo $this->getId() ?>/";
    }
    function preview(oper)
    {
        if (oper < 10){
            bdhtml=window.document.body.innerHTML;//获取当前页的html代码
            sprnstr="<!--startprint1-->";//设置打印开始区域
            eprnstr="<!--endprint1-->";//设置打印结束区域
            prnhtml=bdhtml.substring(bdhtml.indexOf(sprnstr)+18); //从开始代码向后取html

            prnhtml=prnhtml.substring(0,prnhtml.indexOf(eprnstr));//从结束代码向前取html
            document.body.style.minHeight= '';
            window.document.body.innerHTML=prnhtml;
            window.print();
            window.document.body.innerHTML=bdhtml;


        } else {
            window.print();
        }

    }
    $(function() {
        var mediaQueryList = window.matchMedia('print');
        mediaQueryList.addListener(function(mql) {
            if (!mql.matches) {
                $('.treeview a').click(function(e){
                    $(this).next().toggle();

                })
            }
        });



    })


</script>