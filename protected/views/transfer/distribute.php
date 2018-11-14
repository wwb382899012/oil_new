
<div class="form-group">
    <label class="col-sm-2 control-label">调货处理方式</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $this->map['cross_method_type'][$returnDetail['type']] ?></p>
    </div>
    <?php if($returnDetail['type']==ConstantMap::ORDER_BUY_TYPE){ ?>
    <label class="col-sm-2 control-label">采购合同数量</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo number_format($returnDetail['quantity'],4).$this->map['goods_unit'][$returnDetail['unit']]['name'] ?></p>
    </div>
    <?php } ?>
</div>
<?php if($returnDetail['type']==ConstantMap::ORDER_BACK_TYPE){ ?>
<div class="form-group">
    <label class="col-sm-2 control-label">还货明细</label>
    <div class="col-sm-10">
    <?php if (Utility::isNotEmpty($returnDetail['details'])) { ?>
    <table class="table table-striped table-bordered table-condensed table-hover">
        <thead>
        <tr>
            <th style="width:100px;text-align:center">还货采购合同编号</th>
            <th style="width:120px;text-align:center">入库单编号</th>
            <th style="width:120px;text-align:center">品名</th>
            <th style="width:80px;text-align:center">可用库存数量</th>
            <th style="width:80px;text-align:center">还货数量</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($returnDetail['details'] as $val) { ?>
            <tr>
                <td style="text-align:center"><?php echo $val['contract_code'] ?></td>
                <td style="text-align:center"><?php echo $val['stock_code'] ?></td>
                <td style="text-align:center"><?php echo $returnDetail['goods_name'] ?></td>
                <td style="text-align:right"><?php echo number_format($val['quantity_balance'], 4).$this->map['goods_unit'][$val['unit']]['name'] ?></td>
                <td style="text-align:right"><?php echo number_format($val['quantity'],4).$this->map['goods_unit'][$val['unit']]['name'] ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php } ?>
    </div>
</div>
<?php } ?>
<div class="form-group">
    <label class="col-sm-2 control-label">备注</label>
    <div class="col-sm-10">
        <p class="form-control-static"><?php echo $returnDetail['remark'] ?></p>
    </div>
</div>