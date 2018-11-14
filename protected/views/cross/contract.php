<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            <b><a href="/businessConfirm/detail/?id=<?php echo $contract['contract_id'] ?>&t=1" target="_blank">销售合同</a> &nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 16px;">NO.<span class="text-red"><?php echo $contract['contract_code'] ?></span></span></b>
        </h3>
        <div class="pull-right box-tools">
            <?php if(!$this->isExternal){ ?>
                <button type="button"  class="btn btn-default btn-sm" onclick="back()">返回</button>
            <?php } ?>
        </div>
    </div>
    <div class="box-body form-horizontal">

        <?php
        if (Utility::isNotEmpty($transactions)) { ?>
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                <tr>
                    <th style="width:120px;text-align:center">品名</th>
                    <th style="width:80px;text-align:center">数量</th>
                    <th style="width:80px;text-align:center">数量单位</th>
                    <th style="width:80px;text-align:center">销售溢短装比例</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($transactions as $v) { ?>
                    <tr <?php if($v['detail_id']==$contract['detail_id']) echo 'class="bg-yellow color-palette"'; ?>>
                        <td style="text-align:center"><?php echo !empty($v['goods_name']) ? $v['goods_name'] : '-' ?></td>
                        <td style="text-align:center"><?php echo number_format($v["quantity"], 2) ?></td>
                        <td style="text-align:center"><?php echo $this->map["goods_unit"][$v["unit"]]['name'] ?></td>
                        <td style="text-align:center"><?php echo $v["more_or_less_rate"] * 100 . '%' ?></td>

                    </tr>
                <?php } ?>
                </tbody>
            </table>
        <?php } ?>
    </div>
</div>