<?php
$contractGoods = array();
foreach ($deliveryOrder->details as $detail) {
    $contractGoods[$detail['contract_id'].'-'.$detail['goods_id']][] = $detail;
}
?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title" >
            <b><?php echo Map::$v['stock_notice_delivery_type'][$deliveryOrder['type']] . '发货单' ?>&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-size: 16px;">NO.<span class="text-red"><?php echo $deliveryOrder['code'] ?></span></span></b>
        </h3>
        <div class="pull-right box-tools">
            <button type="button" class="btn btn-default btn-sm" onclick="back()">返回</button>
        </div>
    </div>
    <div class="box-body form-horizontal">
        <div class="form-group">
            <label class="col-sm-2 control-label">交易主体</label>
            <div class="col-sm-4">
                <p class="form-control-static">
                    <a target="_blank" href="/corporation/detail?t=1&id=<?php echo $deliveryOrder->corporation->corporation_id?>">
                        <?php echo $deliveryOrder->corporation->name?>
                    </a>
                </p>
            </div>
            <label class="col-sm-2 control-label">销售合同编号</label>
            <div class="col-sm-4">
                <p class="form-control-static">
                    <a title="合同详情" target="_blank" href="/contract/detail/?id=<?php echo $deliveryOrder->contract->contract_id; ?>&t=1">
                        <?php echo $deliveryOrder->contract->contract_code; ?>
                    </a>
                </p>
            </div>
            <label class="col-sm-2 control-label">下游合作方</label>
            <div class="col-sm-4">
                <p class="form-control-static">
                    <a target="_blank" href="/partner/detail?t=1&id=<?php echo $deliveryOrder->partner->partner_id?>">
                        <?php echo $deliveryOrder->partner->name?>
                    </a>
                </p>
            </div>
            <label class="col-sm-2 control-label">预计发货日期</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $deliveryOrder->delivery_date?></p>
            </div>
            <?php
            $attachments=AttachmentService::getAttachments(Attachment::C_STOCK_DELIVERY,$deliveryOrder['order_id'], 1);
            $this->renderPartial("/components/attachmentsDropdown", array(
                    'id' => $deliveryOrder['order_id'],
                    'map_key'=>'stock_delivery_attachment',
                    'attach_type'=>ConstantMap::STOCK_DELIVERY_ATTACH_TYPE,
                    'attachment_type'=>Attachment::C_STOCK_DELIVERY,
                    'controller'=>'deliveryOrder',
                )
            );
            ?>
        </div>
        <div class="form-group">
            <!-- <label for="type" class="col-sm-2 control-label">发货明细</label> -->
            <div class="col-sm-12">
            <!-- <div class="col-sm-12"> -->
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                    <tr>
                        <th style="width:120px;text-align:center">品名</th>
                        <th style="width:140px;text-align:center">合同数量</th>
                        <th style="width:90px;text-align:center">发货数量</th>
                        <th style="width:180px;text-align:center">配货入库单编号</th>
                        <th style="width:100px;text-align:center">配货数量</th>
                        
                        <th style="width:100px;text-align:center">出库</th>
                        <th style="width:100px;text-align:center">总出库数量</th>
                        <th style="width:100px;text-align:center">未出库数量</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php 
                    $lastGoods = '';
                    foreach ($contractGoods as $goodsKey => $goodsDetail) :
                        $countGoods = count($goodsDetail);
                        foreach ($goodsDetail as $detail) :
                            foreach ($detail->stockDeliveryDetail as $stockDetail) :
                            ?>
                            <tr>
                                <?php if($lastGoods != $goodsKey):?>
                                <td style="text-align:center;vertical-align: middle;" rowspan="<?php echo $countGoods?>">
                                    <?php echo $detail->goods->name;?>
                                </td>
                                <td style="text-align:right;vertical-align: middle;" rowspan="<?php echo $countGoods?>">
                                    <?php echo $detail->contractGoods->quantity?>
                                    <?php echo $this->map['goods_unit'][$detail->contractGoods['unit']]['name']?>
                                    ±10%
                                </td>
                                <td style="text-align:right;vertical-align: middle;" rowspan="<?php echo $countGoods?>">
                                    <?php echo $detail->quantity?>
                                    <?php echo $this->map['goods_unit'][$detail->contractGoods['unit']]['name']?>
                                </td>
                                <?php endif;?>
                                <td style="text-align:center;vertical-align: middle;">
                                    <?php echo "<a target='_blank' href='/stockInList/view/?id=".$stockDetail->stock->stockIn->stock_in_id."&t=1'>".$stockDetail->stock->stockIn->code."</a>";?> 
                                </td>
                                <td style="text-align:right;vertical-align: middle;">
                                    <?php echo $stockDetail->quantity?>
                                    <?php echo $this->map['goods_unit'][$detail->contractGoods['unit']]['name']?>
                                </td>
                              
                                <td style="text-align:center;vertical-align: middle;">
                                    <?php echo empty($stockDetail->store->name) ? "虚拟库" : $stockDetail->store->name;?>
                                </td>
                                 <td style="width:100px;text-align:right;vertical-align: middle!important;">
                            	<?php 

                            	$total_out=0;
                            	if(!empty($deliveryOrder->stockOuts)){
                            	    foreach ($deliveryOrder->stockOuts as $k=>$v){
                            	        if($v['status']==1 || $v['status']==30){//已出库
                            	            if(!empty($v->details)){
                            	                foreach ($v->details as $m){
                            	                    if($m['stock_detail_id']==$stockDetail->stock_detail_id) //t_stock_delivery_detail 的stock_detail_id 和  t_stock_out_detail 的stock_detail_id对应
                            	                    $total_out+=$m['quantity'];
                            	                }
                            	            }
                            	        }
                            	        
                            	    }
                            	    
                            	}
                            	echo sprintf("%.4f",$total_out). Map::$v['goods_unit'][$detail->contractGoods['unit']]['name']; 
                            	?>
                            	</td>
                            	<td style="width:100px;text-align:right;vertical-align: middle!important;">
                            	<?php 
                            	
                            	echo sprintf("%.4f",round($stockDetail->quantity - $total_out,4)) . Map::$v['goods_unit'][$detail->contractGoods['unit']]['name']
                            	?>
                            	</td>
                                
                            </tr>
                            <?php endforeach;?>
                        <?php endforeach;?>
                    <?php 
                    $lastGoods = $goodsKey;
                    endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">备注</label>
            <div class="col-sm-10">
                <p class="form-control-static"><?php echo $deliveryOrder['remark']; ?></p>
            </div>
        </div>
        <!-- <div class="box-header with-border"></div> -->
        <hr/>
        <div class="form-group">
            <label class="col-sm-2 control-label">审核状态</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $deliveryOrder['status']<DeliveryOrder::STATUS_PASS ? Map::$v['delivery_order_status'][$deliveryOrder['status']] : Map::$v['delivery_order_status'][DeliveryOrder::STATUS_PASS]; ?></p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">审核意见</label>
            <div class="col-sm-4">
                <p class="form-control-static">
                    <?php
                    $checkLogs=FlowService::getCheckLog($deliveryOrder['order_id'],9);
                    if(Utility::isNotEmpty($checkLogs))
                        echo $checkLogs[0]['remark'];
                    ?>
                </p>
            </div>
        </div>
    </div>
    
    
</div>
<script>
    function back()
    {
        // history.back();
        location.href = /<?php echo $this->getId() ?>/;
    }

</script>