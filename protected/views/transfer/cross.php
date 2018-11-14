<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            <b>调货单 &nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 16px;">NO.<span class="text-red"><?php echo $cross['cross_code'] ?></span></span></b>
        </h3>
        <div class="pull-right box-tools">
            <?php if(!$this->isExternal){ ?>
                <button type="button"  class="btn btn-default btn-sm" onclick="back()">返回</button>
            <?php } ?>
        </div>
    </div>
    <div class="box-body form-horizontal">
        <div class="form-group">
            <label class="col-sm-2 control-label">销售合同编号</label>
            <div class="col-sm-2">
                <p class="form-control-static"><?php echo $cross['contract_code'] ?></p>
            </div>
            <label class="col-sm-2 control-label">品名</label>
            <div class="col-sm-2">
                <p class="form-control-static"><?php echo $cross['goods_name'] ?></p>
            </div>
            <label class="col-sm-2 control-label">调货日期</label>
            <div class="col-sm-2">
                <p class="form-control-static"><?php echo $cross['cross_date'] ?></p>
            </div>
        </div>
        <?php
        if (Utility::isNotEmpty($data)) { ?>
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                <tr>
                    <th style="width:120px;text-align:center">采购合同编号</th>
                    <th style="width:80px;text-align:center">入库单编号</th>
                    <th style="width:80px;text-align: center;">仓库</th>
                    <th style="width:80px;text-align:center">预计调货数量</th>
                    <th style="width:80px;text-align:center">实际出库数量</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data as $v) { ?>
                    <tr>
                        <td style="text-align:center"><?php echo $v['buy_contract_code'] ?></td>
                        <td style="text-align:center"><?php echo $v['stock_code'] ?></td>
                        <td style="text-align: left;" title="<?php echo $v['store_name'] ?>"><?php echo $v['store_name'] ?></td>
                        <td style="text-align:right"><?php echo number_format($v['quantity'], 4).$this->map["goods_unit"][$v["unit"]]['name'] ?></td>
                        <td style="text-align:right"><?php echo number_format($v['quantity_out'], 4).$this->map["goods_unit"][$v["unit"]]['name'] ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <div class="form-group"></div>
        <?php } ?>
        <div class="form-group">
            <label class="col-sm-2 control-label">调货原因</label>
            <div class="col-sm-10">
                <p class="form-control-static"><?php echo $cross['remark'] ?></p>
            </div>
        </div>
    </div>
</div>