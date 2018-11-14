<?php if(Utility::isNotEmpty($nowDetail)){ ?>
<div class="form-group">
    <label class="col-sm-2 control-label">本次调货明细</label>
</div>
<div class="form-group" id="view-div">
    <div class="col-sm-offset-1 col-sm-11">
        <table class="table table-condensed table-hover table-bordered table-layout">
            <thead>
                <tr>
                    <th style="width: 180px; text-align: center;">调货单编码</th>
                    <th style="width: 160px; text-align: center;">采购合同编号</th>
                    <th style="width: 120px; text-align: center;">上游合作方</th>
                    <th style="width: 170px; text-align: center;">入库单编号</th>
                    <th style="width: 120px; text-align: center;">仓库</th>
                    <?php if(empty($data['check_id'])){ ?>
                    <th style="width: 100px; text-align: center;">可用库存数量</th>
                    <?php } ?>
                    <th style="width: 100px; text-align: center;">预计调货数量</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php foreach ($nowDetail['detail'] as $key => $value) {?>
                <tr>
                    <td style="text-align: left;"><?php echo $value['cross_code'] ?></td>
                    <td style="text-align: left;"><?php echo $value['contract_code'] ?></td>
                    <td style="text-align: left;" title="<?php echo $value['partner_name'] ?>"><?php echo $value['partner_name'] ?></td>
                    <td style="text-align: left;"><?php echo $value['stock_code'] ?></td>
                    <td style="text-align: left;" title="<?php echo $value['store_name'] ?>"><?php echo $value['store_name'] ?></td>
                    <?php if(empty($data['check_id'])){ ?>
                    <td style="text-align: right;"><?php echo number_format($value['quantity_balance'], 2).$this->map['goods_unit'][$value['unit']]['name'] ?></td>
                    <?php } ?>
                    <td style="text-align: right;"><?php echo number_format($value['quantity'], 2).$this->map['goods_unit'][$value['unit']]['name'] ?></td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td style="text-align: center;">合计</td>
                    <td >&nbsp;</td>
                    <td >&nbsp;</td>
                    <td >&nbsp;</td>
                    <td >&nbsp;</td>
                    <?php if(empty($data['check_id'])){ ?>
                    <td style="text-align: right;"><?php echo number_format($nowDetail['total_balance_quantity'], 2).$this->map['goods_unit'][$nowDetail['unit']]['name'] ?></td>
                    <?php } ?>
                    <td style="text-align: right;"><?php echo number_format($nowDetail['total_quantity'], 2).$this->map['goods_unit'][$nowDetail['unit']]['name'] ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php } ?>