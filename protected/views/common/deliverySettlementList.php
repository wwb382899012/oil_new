<?php
$attachments=AttachmentService::getAttachments(Attachment::C_STOCK_DELIVERY,$deliveryOrder['order_id'], 3);
?>
<div class="form-group">
    <label class="col-sm-2 control-label">发货结算单</label>
    <div class="col-sm-4">
        <p class='form-control-static'>
            <?php 
            if(!empty($attachments[3]))
                echo "<a href='/deliverySettlement/getFile/?id=" . $attachments[3][0]["id"] . "&fileName=" . $attachments[3][0]['name'] . "'  target='_blank' class='btn btn-primary btn-xs'>点击查看</a>"; 
            else
                echo '无';
            ?>
        </p>
    </div>
    <label class="col-sm-2 control-label">结算日期</label>
    <div class="col-sm-4">
        <p class='form-control-static'><?php echo $deliveryOrder->settle_date;?></p>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-offset-1 col-sm-11">
        <table class="table table-condensed table-hover table-bordered ">
            <thead>
            <tr>
                <th style="width:200px;">销售合同编号 </th>
                <th style="width:110px; text-align: left;">品名 </th>
                <th style="width:200px; text-align: left;">实际出库 </th>
                <th style="width:130px; text-align: left;">结算单位 </th>
                <th style="width:100px; text-align: left;">换算比例 </th>
                <th style="width:180px; text-align: left;">结算单价 </th>
                <th style="width:200px; text-align: left;">结算数量 </th>
                <th style="width:200px; text-align: left;">结算金额 </th>
                <th style="width:180px; text-align: left;">发货单损耗</th>
            </tr>
            </thead>
            <tbody>

            <?php 
            foreach ($deliveryOrder['settlementDetails'] as $settlementDetail) :
                ?>
                <tr>
                    <td>
                        <?php echo $settlementDetail->contract->contract_code?>
                    </td>
                    <td>
                        <?php echo $settlementDetail->goods->name?>
                    </td>
                    <td>
                        <?php echo $settlementDetail->quantity.$this->map['goods_unit'][$settlementDetail->unit]['name']?>
                        <?php if($settlementDetail->sub->unit != $settlementDetail->unit):?>
                        /
                        <?php echo $settlementDetail->sub->quantity.$this->map['goods_unit'][$settlementDetail->sub->unit]['name']?>
                        <?php endif?>
                    </td>
                    <td>
                        <?php echo $this->map['goods_unit'][$settlementDetail->unit]['name']?>
                    </td>
                    <td>
                        <?php echo empty($settlementDetail->unit_rate)?1:$settlementDetail->unit_rate?>
                    </td>
                    <td>
                        <?php echo number_format($settlementDetail->price/100, 2).$this->map['currency'][$settlementDetail->currency]['ico'].'/'.$this->map['goods_unit'][$settlementDetail->unit]['name']?>
                        <?php if($settlementDetail->sub->unit != $settlementDetail->unit):?>
                        /
                        <?php echo number_format($settlementDetail->sub->price/100, 2).$this->map['currency'][$settlementDetail->currency]['ico'].'/'.$this->map['goods_unit'][$settlementDetail->sub->unit]['name']?>
                        <?php endif?>
                    </td>
                    <td>
                        <?php echo $settlementDetail->quantity_settle.$this->map['goods_unit'][$settlementDetail->unit]['name']?>
                        <?php if($settlementDetail->sub->unit != $settlementDetail->unit):?>
                        /
                        <?php echo $settlementDetail->sub->quantity_settle.$this->map['goods_unit'][$settlementDetail->sub->unit]['name']?>
                        <?php endif?>
                    </td>
                    <td>
                        <?php echo $this->map['currency'][$settlementDetail->currency]['ico'].number_format($settlementDetail->amount/100, 2)?>
                    </td>
                    <td>
                        <?php echo $settlementDetail->quantity_loss.$this->map['goods_unit'][$settlementDetail->unit]['name']?>
                        <?php if($settlementDetail->sub->unit != $settlementDetail->unit):?>
                        /
                        <?php echo $settlementDetail->sub->quantity_loss.$this->map['goods_unit'][$settlementDetail->sub->unit]['name']?>
                        <?php endif?>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-2 control-label">备注</label>
    <div class="col-sm-10">
        <p class='form-control-static'><?php echo $deliveryOrder->settle_remark;?></p>
    </div>
</div>