<div class="modal fade draggable-modal" id="detail_dialog" tabindex="-1" role="dialog" aria-labelledby="modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header--flex">
                <h4 class="modal-title">查看明细</h4>
                <a type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">×</span></a>
            </div>
            <div class="modal-body">
                <div>
                    <h3 class="form-group">货款<?php echo $contract_type==ConstantMap::SALE_TYPE ? '应收' : '应付'; ?>金额：</h3>
                    <div class="form-group">
                        <table class="table table-fixed">
                            <thead>
                            <tr>
                                <th>品名</th>
                                <th>人民币结算金额</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(empty($contractSettlement['settlementGoods'])): //按发货单或入库通知单结算 ?>

                                <?php 
                                foreach ($contract['items'] as $key=>$value):?>
                                    <?php
                                    $goods_amount=0;
                                    $bill_items=empty($contractSettlement['lading_bills'])?$contractSettlement['delivery_orders']:$contractSettlement['lading_bills'];
                                    if(!empty($bill_items)){
                                        foreach ($bill_items as $m=>$n){
                                            if(!empty($n['settlementGoods'])){
                                                foreach ($n['settlementGoods'] as $mm=>$nn){
                                                    if($nn['goods_id']==$value['goods_id'])
                                                        $goods_amount+=$nn['amount_cny'];
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <p class="form-control-static"><?php echo $value['goods_name'];?></p>
                                        </td>
                                        <td>
                                            <p class="form-control-static"><?php echo number_format($goods_amount/100,2);?>元</p>
                                        </td>
                                    </tr>
                                <?php endforeach;?>

                            <?php else://按合同结算?>
                                <?php foreach ($contractSettlement['settlementGoods'] as $k=>$v):?>
                                <tr>
                                    <td>
                                        <p class="form-control-static"><?php echo $v['goods_name'];?></p>
                                    </td>
                                    <td>
                                        <p class="form-control-static"><?php echo number_format($v['amount_cny']/100,2);?>元</p>
                                    </td>
                                </tr>
                                <?php endforeach;?>
                            <?php endif;?>
                            <tr>
                                <td></td>
                                <td>
                                    <span class="form-control-static">合计 </span>
                                    <span class="form-control-static text-theme"><?php echo number_format($contractSettlement['goods_amount']/100,2); ?>元</span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <h3 class="form-group">非货款<?php echo $contract_type==ConstantMap::SALE_TYPE ? '应收' : '应付'; ?>金额：</h3>
                    <p class="form-control-static col-sm-6"">合计</p><p class="form-control-static col-sm-6 text-theme"><?php echo number_format($contractSettlement['other_amount']/100,2);?>元</p>
                </div>
            </div>
            <div class="modal-footer flex-center">
                <a href="javascript: void 0" role="button" class="o-btn o-btn-primary" data-dismiss="modal">确定</a>
            </div>
        </div>
    </div>
</div>

<script>

function lookDetail(){
	$("#detail_dialog").modal("show");
}

</script>