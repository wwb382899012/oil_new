<?php
$controller = "";
$bill_id = 0;
$bill_code = '';
$bill_quantity = "";
if($type==1){
    $controller = 'stockBatchSettlement';
    $bill_id = $settlement['batch_id'];
    $bill_code = $settlement['batch_code'];
    $bill_quantity = 'in_quantity';
    $bill_quantity_sub = 'in_quantity_sub';
    $attachment_type = Attachment::C_STOCK_BATCH_SETTLEMENT;
    $map_key = 'stock_batch_settlement_type';
    $attach_type_settle = 1;
    $attach_type_other =  11;
}else if($type==3){
    $controller = 'deliverySettlement';
    $bill_id = $settlement['order_id'];
    $bill_code = $settlement['order_code'];
    $bill_quantity = 'out_quantity';
    $attachment_type = Attachment::C_DELIVERY_ORDER_SETTLEMENT;
    $map_key = 'delivery_settlement_attachment';
    $attach_type_settle = 3;
    $attach_type_other =  4;
}
?>

<?php
if(!$isHiddenBtn){
    $buttons = [];
    if(!empty($isCanEdit)) {
        $buttons[] = ['text' => '修改', 'attr' => ['onclick' => 'edit('.$bill_id.')', 'class_abbr' => 'action-default-base']];
        if(!empty($isCanSubmit)){
            $buttons[] = ['text' => '提交', 'attr' => ['onclick' => 'submit('.$bill_id.')', 'id' => 'saveButton']];
        }
    }
    $this->loadHeaderWithNewUI($menus, $buttons, '/'.$controller.'/');
}
?>
<div class="z-card">
    <div class="content-title-wrap">
        <h3 class="z-card-header"><?php if($type==1) echo '入库通知单'; else echo '发货单'; ?>结算操作
            <?php if($type==1): ?>
                <a class="title-code text-link" href="/stockIn/detail?id=<?php echo $bill_id;?>&t=1" target="blank"><span><?php echo $bill_code;?></span></a>
            <?php else:?>
                <a class="title-code text-link" href="/deliveryOrder/detail?id=<?php echo $bill_id;?>&t=1" target="blank"><span><?php echo $bill_code;?></span></a>
            <?php endif;?>
            <p class="content-wrap-expand pull-right box-tools"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
        </h3>
    </div>
    <div class="z-card-body">
        <form>
            <?php if(empty($isContractSettlement)):?>
                <div class="flex-grid form-group" style="margin-bottom: 30px;">
                    <label class="col col-count-3 field flex-grid">
                        <p class="form-cell-title w-fixed">结算日期:</p>
                        <p class="form-control-static"><?php echo $settlement["settle_date"]; ?></p>
                    </label>
                </div>
            <?php endif;?>
            <div>
                <?php
                if(!empty($settlement['settlementGoods'])) {
                    foreach ($settlement['settlementGoods'] as $k => $v) { ?>
                        <div class="settlement-divide">
                            <div class="clearfix">
                                <h3 class="pull-left content-title"><span><?php echo $v['goods_name']; ?></span></h3>
                                <div class="pull-right">
                                    <?php if(!empty($v['hasDetail'])){ ?>
                                        <a href="javascript: void 0" role="button" class="o-btn o-btn-action hideBtn"  onclick="hideDetail(<?php echo $v['goods_id'].$bill_id ?>)"  id="<?php echo 'hideDetail_'.$v['goods_id'].$bill_id ?>">收起明细</a>
                                        <a href="javascript: void 0" role="button" class="o-btn o-btn-action showBtn" onclick="showDetail(<?php echo $v['goods_id'].$bill_id ?>)" id="<?php echo 'showDetail_'.$v['goods_id'].$bill_id ?>">展开明细</a>
                                    <?php } ?>
                                    <?php if($type==1){ ?>
                                        <a href="javascript: void 0" role="button" class="o-btn o-btn-action" onclick="displayLockPriceDetail(<?php echo $v['batch_id'] ?>, <?php echo $v['goods_id'] ?>)">查看锁价</a>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="flex-grid form-group">
                                <label class="col col-count-3 field flex-grid">
                                    <p class="form-cell-title w-fixed">
                                        <span><?php if($type==1) echo '入'; else echo '出'; ?>库单数量:
                                    </p>
                                    <p class="form-control-static">
                                        <?php echo number_format($v[$bill_quantity]['quantity'], 4); ?><?php echo Map::$v['goods_unit'][$v[$bill_quantity]['unit']]['name']; ?>
                                        <?php if(!empty($v[$bill_quantity_sub]['unit']) && $v[$bill_quantity]['unit']!=$v[$bill_quantity_sub]['unit']){ ?>
                                            /<?php echo number_format($v[$bill_quantity_sub]['quantity'], 4); ?><?php echo Map::$v['goods_unit'][$v[$bill_quantity_sub]['unit']]['name']; ?>
                                        <?php } ?>
                                    </p>
                                </label>
                                <label class="col field col-count-3 flex-grid">
                                    <p class="form-cell-title w-fixed">结算数量:</p>
                                    <p class="form-control-static"><?php echo number_format($v['quantity']['quantity'], 4); ?><?php echo Map::$v['goods_unit'][$v['quantity']['unit']]['name']; ?></p>
                                </label>
                                <label class="col field col-count-3 flex-grid">
                                    <p class="form-cell-title w-fixed">损耗量:</p>
                                    <p class="form-control-static"><?php echo number_format($v['quantity_loss']['quantity'], 4); ?><?php echo Map::$v['goods_unit'][$v['quantity_loss']['unit']]['name']; ?></p>
                                </label>
                            </div>
                            <div class="flex-grid form-group">
                                <label class="col field col-count-3 flex-grid">
                                    <p class="form-cell-title w-fixed">结算单价:</p>
                                    <p class="form-control-static"><?php echo $settlement['settle_currency']['ico'] . number_format($v['price']/100, 2); ?></p>
                                </label>
                                <label class="col field col-count-3 flex-grid">
                                    <p class="form-cell-title w-fixed">结算金额:</p>
                                    <p class="form-control-static"><?php echo $settlement['settle_currency']['ico'] . number_format($v['amount']/100,2); ?></p>
                                </label>
                                <?php if($settlement['settle_currency']['id']!=1){ ?>
                                    <label class="col field col-count-3 flex-grid">
                                        <p class="form-cell-title w-fixed">结算汇率:</p>
                                        <p class="form-control-static"><?php echo $v['unit_rate']; ?></p>
                                    </label>
                                <?php } ?>
                            </div>
                            <?php if($settlement['settle_currency']['id']!=1){ ?>
                                <div class="flex-grid form-group">
                                    <label class="col field col-count-3 flex-grid">
                                        <p class="form-cell-title w-fixed">人民币结算单价:</p>
                                        <p class="form-control-static"><?php echo '￥' . number_format($v['price_cny']/100, 2); ?></p>
                                    </label>
                                    <label class="col field col-count-3 flex-grid">
                                        <p class="form-cell-title w-fixed">人民币结算金额:</p>
                                        <p class="form-control-static"><?php echo '￥' . number_format($v['amount_cny']/100, 2); ?></p>
                                    </label>
                                </div>
                            <?php } ?>
                            <?php if(!empty($v['hasDetail'])){ ?>
                                <fieldset  class="input-detail form-group" id="<?php echo  'displayDetail_'.$v['goods_id'].$bill_id ?>">
                                    <legend>结算明细</legend>
                                    <div class="form-group">
                                        <p class="form-cell-title">贷款金额</p>
                                        <table class="table table-fixed table-nowrap--head">
                                            <thead>
                                                <tr>
                                                    <th style="width: 140px;">计价币种</th>
                                                    <th>计价币种货款金额</th>
                                                    <th>汇率</th>
                                                    <th>人民币货款总额</th>
                                                    <th>货款单价</th>
                                                    <th>计税汇率</th>
                                                    <th>计税人民币货款总额</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <p class="form-control-static"><?php echo isset($v['settlementGoodsDetail']['currency']['name'])?$v['settlementGoodsDetail']['currency']['name']:''; ?></p>
                                                    </td>
                                                    <td>
                                                        <p class="form-control-static"><?php echo (isset($v['settlementGoodsDetail']['currency']['ico'])?$v['settlementGoodsDetail']['currency']['ico']:'') . number_format($v['settlementGoodsDetail']['amount_currency']/100, 2); ?></p>
                                                    </td>
                                                    <td>
                                                        <p class="form-control-static"><?php echo $v['settlementGoodsDetail']['exchange_rate']; ?></p>
                                                    </td>
                                                    <td>
                                                        <p class="form-control-static"><?php echo '￥' . number_format($v['settlementGoodsDetail']['amount_goods']/100 ,2 ); ?></p>
                                                    </td>
                                                    <td>
                                                        <p class="form-control-static"><?php echo '￥' . number_format($v['settlementGoodsDetail']['price_goods']/100, 2); ?></p>
                                                    </td>
                                                    <td>
                                                        <p class="form-control-static"><?php echo $v['settlementGoodsDetail']['exchange_rate_tax']; ?></p>
                                                    </td>
                                                    <td>
                                                        <p class="form-control-static"><?php echo '￥' . number_format($v['settlementGoodsDetail']['amount_goods_tax']/100 ,2); ?></p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="form-group">
                                        <p class="form-cell-title">相关税收
                                        </p>
                                        <table class="table table-fixed table-nowrap--head">
                                            <thead>
                                                <tr>
                                                    <th style="width: 140px;">税收名目</th>
                                                    <th>税率</th>
                                                    <th>税收总金额</th>
                                                    <th>税收单价</th>
                                                    <th>备注</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php if (!empty($v['settlementGoodsDetail']['tax_detail_item'])): ?>
                                                <?php foreach ($v['settlementGoodsDetail']['tax_detail_item'] as $tax_key => $tax_value): ?>
                                                    <tr>
                                                        <td>
                                                            <p class="form-control-static"><?php echo isset($tax_value['subject_list']['name'])?$tax_value['subject_list']['name']:''; ?></p>
                                                        </td>
                                                        <td>
                                                            <p class="form-control-static"><?php echo ($tax_value['rate']*100).'%'; ?></p>
                                                        </td>
                                                        <td><p class="form-control-static">
                                                                ￥<?php echo number_format($tax_value['amount']/100, 2); ?></p>
                                                        </td>
                                                        <td><p class="form-control-static">
                                                                ￥<?php echo number_format($tax_value['price']/100 ,2); ?></p>
                                                        </td>
                                                        <td>
                                                            <p class="form-control-static"><?php echo $tax_value['remark']; ?></p>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="form-group">
                                        <p class="form-cell-title">其他费用
                                        </p>
                                        <table class="table table-fixed table-nowrap--head">
                                            <thead>
                                                <tr >
                                                    <th style="width: 140px;">科目</th>
                                                    <th>费用总额</th>
                                                    <th>费用单价</th>
                                                    <th>备注</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php if (!empty($v['settlementGoodsDetail']['other_detail_item'])): ?>
                                                <?php foreach ($v['settlementGoodsDetail']['other_detail_item'] as $other_key => $other_value): ?>
                                                    <tr>
                                                        <td>
                                                            <p class="form-control-static"><?php echo isset($other_value['subject_list']['name'])?$other_value['subject_list']['name']:''; ?></p>
                                                        </td>
                                                        <td>
                                                            <p class="form-control-static">￥<?php echo number_format($other_value['amount']/100, 2); ?></p>
                                                        </td>
                                                        <td><p class="form-control-static">
                                                                ￥<?php echo number_format($other_value['price']/100, 2); ?></p>
                                                        </td>
                                                        <td><p class="form-control-static">
                                                                <?php echo $other_value['remark']; ?></p>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="o-row form-group">
                                        <label class="o-col-sm-4 flex-grid">
                                            <p class="form-cell-title w-fixed">调整金额:</p>
                                            <p class="form-control-static"><?php echo $this->map['contract_settlement_adjust'][$v['settlementGoodsDetail']['adjust_type']]['name']; ?> &nbsp; <?php echo '￥'.number_format($v['settlementGoodsDetail']['amount_adjust']/100,2);?></p>
                                        </label>
                                        <label class="o-col-sm-8 flex-grid">
                                            <p class="form-cell-title w-fixed">调整原因:</p>
                                            <p class="form-control-static"><?php echo $v['settlementGoodsDetail']['reason_adjust'];?></p>
                                        </label>
                                    </div>
                                    <div class="flex-grid form-group">
                                        <label class="col field col-count-3 flex-grid align-start">
                                            <p class="form-cell-title w-fixed">总结算数量:</p>
                                            <p class="form-control-static"><?php echo number_format($v['settlementGoodsDetail']['quantity']['quantity'],4);?><?php echo Map::$v['goods_unit'][$v['settlementGoodsDetail']['quantity']['unit']]['name'];?></p>
                                        </label>
                                        <label class="col field col-count-3 flex-grid align-start">
                                            <p class="form-cell-title w-fixed">人民币结算金额:</p>
                                            <p class="form-control-static"><?php echo '￥'.number_format($v['settlementGoodsDetail']['amount']/100,2);?></p>
                                        </label>
                                        <label class="col field col-count-3 flex-grid align-start">
                                            <p class="form-cell-title w-fixed">人民币结算单价:</p>
                                            <p class="form-control-static"><?php echo '￥' . number_format($v['settlementGoodsDetail']['price']/100, 2); ?></p>
                                        </label>
                                    </div>
                                    <div class="flex-grid form-group">
                                        <label class="col field col-count-3 flex-grid align-start">
                                            <p class="form-cell-title w-fixed">确定总结算数量:</p>
                                            <p class="form-control-static"><?php echo number_format($v['settlementGoodsDetail']['quantity_actual']['quantity'],4);?><?php echo Map::$v['goods_unit'][$v['settlementGoodsDetail']['quantity_actual']['unit']]['name'];?></p>
                                        </label>
                                        <label class="col field col-count-3 flex-grid align-start">
                                            <p class="form-cell-title cell-title">确定人民币结算金额:</p>
                                            <p class="form-control-static"><?php echo '￥'.number_format($v['settlementGoodsDetail']['amount_actual']/100,2);?></p>
                                        </label>
                                        <label class="col field col-count-3 flex-grid align-start">
                                            <p class="form-cell-title cell-title">确定人民币结算单价:</p>
                                            <p class="form-control-static"><?php echo '￥'.number_format($v['settlementGoodsDetail']['price_actual']/100,2);?></p>
                                        </label>
                                    </div>

                                </fieldset>
                            <?php } ?>
                            <div class="flex-grid form-group">
                                <?php
                                $attachments=AttachmentService::getAttachments($attachment_type,$v['item_id'], $attach_type_settle);
                                $this->renderPartial("/components/new_attachmentsDropdown", array(
                                        'id' => $v['item_id'],
                                        'map_key'=>$map_key,
                                        'attach_type'=>$attach_type_settle,
                                        'attachment_type'=>$attachment_type,
                                        'controller'=>$controller,
                                    )
                                );
                                ?>
                                <!-- <label class="col field col-count-1 flex-grid">
                                    <span class="w-fixed">
                                        结算单据:
                                    </span>
                                    <?php
                                    if(!empty($v['settleFiles'])){
                                        foreach ($v['settleFiles'] as $sf){
                                            echo '<p class="form-control-static"><a href="/'.$controller.'/getFile/?id='.$sf['id'].'&fileName='.$sf['name'].'" target="_blank" class="text-link">'.$sf['name'].'</a></p>';
                                        }
                                    }else{
                                        echo '<p class="form-control-static">无</p>';
                                    }?>
                                </label> -->
                            </div>
                            <div class="flex-grid form-group">
                                <?php
                                $attachments=AttachmentService::getAttachments($attachment_type,$v['item_id'], $attach_type_other);
                                $this->renderPartial("/components/new_attachmentsDropdown", array(
                                        'id' => $v['item_id'],
                                        'map_key'=>$map_key,
                                        'attach_type'=>$attach_type_other,
                                        'attachment_type'=>$attachment_type,
                                        'controller'=>$controller,
                                    )
                                );
                                ?>
                                <!-- <label class="col field col-count-1 flex-grid">
                                    <span class="w-fixed">
                                        其他附件:
                                    </span>
                                    <?php
                                    if(!empty($v['goodsOtherFiles'])){
                                        foreach ($v['goodsOtherFiles'] as $of){
                                            echo '<p class="form-control-static"><a href="/'.$controller.'/getFile/?id='.$of['id'].'&fileName='.$of['name'].'" target="_blank" class="text-link">'.$of['name'].'</a></p>';
                                        }
                                    }else{
                                        echo '<p class="form-control-static">无</p>';
                                    }?>
                                </label> -->
                            </div>
                            <div class="flex-grid form-group">
                                <label class="col field col-count-1 flex-grid">
                                    <span class="w-fixed">
                                        备注说明:
                                    </span>
                                    <p class="form-control-static"><?php echo $v['remark']; ?></p>
                                </label>
                            </div>
                        </div>
                    <?php }
                }?>
            </div>
        </form>
        <div class="modal fade draggable-modal"  id="buy_lock_dialog" tabindex="-1" role="dialog" aria-labelledby="modal">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header--flex">
                        <h4 class="modal-title">锁价/转月记录</h4>
                        <a type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">×</span></a>
                    </div>
                    <div class="modal-body" id="buy_lock_dialog_body">
                    </div>
                    <div class="modal-footer flex-center">
                        <a href="javascript: void 0" role="button" class="o-btn o-btn-primary" data-dismiss="modal">确定</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(".hideBtn").show();
    $(".showBtn").hide();
    function showDetail(id){
        $('#displayDetail_'+id).show();
        $("#hideDetail_" + id).show();
        $("#showDetail_"+id).hide();
    }

    function hideDetail(id){
        $('#displayDetail_'+id).hide();
        $("#hideDetail_" + id).hide();
        $("#showDetail_"+id).show();
    }

    $("div.link").unbind('click').click(function () {
        $(this).next().toggle();
    });

    function displayLockPriceDetail(batch_id, goods_id) {
        $.ajax({
            data: {
                batch_id:batch_id,
                goods_id:goods_id
            },
            url:"/stockBatchSettlement/ajaxGetBuyLockList",
            method:'post',
            success:function(res) {
                $("#buy_lock_dialog_body").html(res);
                $("#buy_lock_dialog").modal("show");
            },
            error:function(res) {
                inc.vueAlert("操作失败！" + res.responseText);
            }
        });
    }


</script>