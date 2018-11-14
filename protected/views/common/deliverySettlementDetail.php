<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">发货单结算操作</h3>
    </div>
    <div class="box-body form-horizontal">


        <div class="row">
            <form class="col-sm-12">
                <div class="form-group">
                    <div class="col-sm-4">
                        <label class="col-sm-4 control-label">结算日期</label>
                        <p class="form-control-static col-sm-8"><?php echo $deliverySettlement["settle_date"]; ?></p>
                    </div>
                </div>
                <?php
                if(!empty($deliverySettlement['settlementGoods'])) {
                    foreach ($deliverySettlement['settlementGoods'] as $k => $v) { ?>
                        <fieldset
                            style="border: 1px solid; padding: 0.35em 0.625em 0.75em;margin-bottom: 15px">
                            <legend class="h4 text-primary"
                                    style="border: 0;  width: auto;"><?php echo $v['goods_name']; ?></legend>
                            <div class="clearfix">
                                <div class="pull-right">
                                    <?php if(!empty($v['hasDetail'])){ ?>
                                        <button type="button" class="btn btn-link hideBtn"  onclick="hideDetail(<?php echo $v['goods_id'] ?>)"  id="<?php echo 'hideDetail_'.$v['goods_id'] ?>"> 收起明细</button>
                                        <button type="button" class="btn btn-link showBtn" onclick="showDetail(<?php echo $v['goods_id'] ?>)" id="<?php echo 'showDetail_'.$v['goods_id'] ?>"> 展开明细</button>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">出库单数量 </label>
                                    <p class="form-control-static col-sm-8">
                                        <?php echo number_format($v['out_quantity']['quantity'], 4); ?><?php echo Map::$v['goods_unit'][$v['out_quantity']['unit']]['name']; ?>
                                    </p>
                                </div>
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">结算数量</label>
                                    <p class="form-control-static col-sm-8"><?php echo number_format($v['quantity']['quantity'], 4); ?><?php echo Map::$v['goods_unit'][$v['quantity']['unit']]['name']; ?></p>
                                </div>
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">损耗量</label>
                                    <p class="form-control-static col-sm-8"><?php echo number_format($v['quantity_loss']['quantity'], 4); ?><?php echo Map::$v['goods_unit'][$v['quantity_loss']['unit']]['name']; ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">结算单价 </label>
                                    <p class="form-control-static col-sm-8"><?php echo $deliverySettlement['settle_currency']['ico'] . number_format($v['price']/100, 2); ?></p>
                                </div>
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">结算金额</label>
                                    <p class="form-control-static col-sm-8"><?php echo $deliverySettlement['settle_currency']['ico'] . number_format($v['amount']/100,2); ?></p>
                                </div>
                                <?php if($deliverySettlement['settle_currency']['id']!=1){ ?>
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">结算汇率</label>
                                    <p class="form-control-static col-sm-8"><?php echo $v['unit_rate']; ?></p>
                                </div>
                                <?php } ?>
                            </div>
                            <?php if($deliverySettlement['settle_currency']['id']!=1){ ?>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">人民币结算单价</label>
                                    <p class="form-control-static col-sm-8"><?php echo '￥' . number_format($v['price_cny']/100, 2); ?></p>
                                </div>
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">人民币结算金额 </label>
                                    <p class="form-control-static col-sm-8"><?php echo '￥' . number_format($v['amount_cny']/100, 2); ?></p>
                                </div>
                            </div>
                            <?php } ?>
                            <?php if(!empty($v['hasDetail'])){ ?>
                            <fieldset id="<?php echo  'displayDetail_'.$v['goods_id'] ?>">
                                <legend class="h5 text-info">结算明细</legend>
                                <div class="form-group col-sm-12">
                                    <div class="h5">贷款金额</div>
                                    <table class="table table-striped table-bordered">
                                        <tr>
                                            <td>计价币种</td>
                                            <td>计价币种货款金额</td>
                                            <td>汇率</td>
                                            <td>人民币货款总额</td>
                                            <td>货款单价</td>
                                            <td>计税汇率</td>
                                            <td>计税人民币货款总额</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <p class="form-control-static"><?php echo $v['settlementGoodsDetail']['currency']['name'] ?></p>
                                            </td>
                                            <td>
                                                <p class="form-control-static"><?php echo $v['settle_currency']['ico'] . number_format($v['settlementGoodsDetail']['amount_currency']/100, 2); ?></p>
                                            </td>
                                            <td>
                                                <p class="form-control-static"><?php echo $v['settlementGoodsDetail']['exchange_rate']; ?></p>
                                            </td>
                                            <td>
                                                <p class="form-control-static"><?php echo $v['settle_currency']['ico'] . number_format($v['settlementGoodsDetail']['amount_goods']/100 ,2 ); ?></p>
                                            </td>
                                            <td>
                                                <p class="form-control-static"><?php echo $v['settle_currency']['ico'] . number_format($v['settlementGoodsDetail']['price_goods']/100, 2); ?></p>
                                            </td>
                                            <td>
                                                <p class="form-control-static"><?php echo $v['settlementGoodsDetail']['exchange_rate_tax']; ?></p>
                                            </td>
                                            <td>
                                                <p class="form-control-static"><?php echo $v['settle_currency']['ico'] . number_format($v['settlementGoodsDetail']['amount_goods_tax']/100 ,2); ?></p>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="form-group col-sm-12">
                                    <div class="h5">相关税收</div>
                                    <table class="table table-striped table-bordered">
                                        <tr>
                                            <td>税收名目</td>
                                            <td>税率</td>
                                            <td>税收总金额</td>
                                            <td>税收单价</td>
                                            <td>备注</td>
                                        </tr>
                                        <?php if (!empty($v['settlementGoodsDetail']['tax_detail_item'])): ?>
                                            <?php foreach ($v['settlementGoodsDetail']['tax_detail_item'] as $tax_key => $tax_value): ?>
                                                <tr>
                                                    <td>
                                                        <p class="form-control-static"><?php echo $tax_value['subject_list']['name']; ?></p>
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
                                    </table>
                                </div>
                                <div class="form-group col-sm-12">
                                    <div class="h5">其他费用</div>
                                    <table class="table table-striped table-bordered">
                                        <tr>
                                            <td>科目</td>
                                            <td>费用总额</td>
                                            <td>费用单价</td>
                                            <td>备注</td>
                                        </tr>
                                        <?php if (!empty($v['settlementGoodsDetail']['other_detail_item'])): ?>
                                            <?php foreach ($v['settlementGoodsDetail']['other_detail_item'] as $other_key => $other_value): ?>
                                                <tr>
                                                    <td>
                                                        <p class="form-control-static"><?php echo $other_value['subject_list']['name']; ?></p>
                                                    </td>
                                                    <td>
                                                        <p class="form-control-static"><?php echo number_format($other_value['amount']/100, 2); ?></p>
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
                                    </table>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        <label class="col-sm-4 control-label">调整金额 </label>
                                        <p class="form-control-static col-sm-8"><?php echo $this->map['contract_settlement_adjust'][$v['settlementGoodsDetail']['adjust_type']]['name'] ?>&nbsp;<?php echo '￥' . number_format($v['settlementGoodsDetail']['amount_adjust']/100, 2); ?></p>
                                    </div>
                                    <div class="col-sm-8" style="margin-left: -4px;">
                                        <label class="col-sm-2 control-label">调整原因 </label>
                                        <p class="form-control-static col-sm-10"><?php echo $v['settlementGoodsDetail']['reason_adjust']; ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        <label class="col-sm-4 control-label">总结算数量 </label>
                                        <p class="form-control-static col-sm-8"><?php echo $v['settlementGoodsDetail']['quantity']['quantity']; ?><?php echo Map::$v['goods_unit'][$v['settlementGoodsDetail']['quantity']['unit']]['name']; ?></p>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-4 control-label">人民币结算金额 </label>
                                        <p class="form-control-static col-sm-8"><?php echo '￥' . number_format($v['settlementGoodsDetail']['amount']/100, 2); ?></p>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-4 control-label">人民币结算单价</label>
                                        <p class="form-control-static col-sm-8"><?php echo '￥' . number_format($v['settlementGoodsDetail']['price']/100, 2); ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        <label class="col-sm-4 control-label">确定总结算数量 </label>
                                        <p class="form-control-static col-sm-8"><?php echo $v['settlementGoodsDetail']['quantity_actual']['quantity']; ?><?php echo Map::$v['goods_unit'][$v['settlementGoodsDetail']['quantity_actual']['unit']]['name']; ?></p>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-4 control-label">确定人民币结算金额 </label>
                                        <p class="form-control-static col-sm-8"><?php echo '￥' . number_format($v['settlementGoodsDetail']['amount_actual']/100, 2); ?></p>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-4 control-label">确定人民币结算单价 </label>
                                        <p class="form-control-static col-sm-8"><?php echo '￥' . number_format($v['settlementGoodsDetail']['price_actual']/100, 2); ?></p>
                                    </div>
                                </div>
                            </fieldset>
                            <?php } ?>
                            <fieldset>
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        <label class="col-sm-4 control-label">结算单据 </label>
                                        <?php
                                        if(!empty($v['settleFiles'])){
                                            foreach ($v['settleFiles'] as $sf){
                                                echo '<p class="form-control-static col-sm-offset-4"><a href="/deliverySettlement/getFile/?id='.$sf['id'].'&fileName='.$sf['name'].'" target="_blank" class="btn btn-primary btn-xs">'.$sf['name'].'</a></p>';
                                            }
                                        }else{
                                            echo '<p class="form-control-static col-sm-8">无</p>';
                                        }?>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        <label class="col-sm-4 control-label">其他附件 </label>
                                        <?php
                                        if(!empty($v['goodsOtherFiles'])){
                                            foreach ($v['goodsOtherFiles'] as $of){
                                                echo '<p class="form-control-static col-sm-offset-4"><a href="/deliverySettlement/getFile/?id='.$of['id'].'&fileName='.$of['name'].'" target="_blank" class="btn btn-primary btn-xs">'.$of['name'].'</a></p>';
                                            }
                                        }else{
                                            echo '<p class="form-control-static col-sm-8">无</p>';
                                        }?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-8" style="margin-left: -4px;">
                                        <label class="col-sm-2 control-label">备注说明 </label>
                                        <p class="form-control-static col-sm-10"><?php echo $v['remark']; ?></p>
                                    </div>
                                </div>
                            </fieldset>
                        </fieldset>
                    <?php }
                }?>
            </form>


        </div>


    </div>
    <div class="box-footer">
        <div class="form-group">
            <div class="pull-right">
                <?php if(!empty($isCanEdit)){ ?>
                    <button type="button" class="btn btn-success" onclick="edit(<?php echo $deliverySettlement['order_id']?>)">修改</button>
                    <?php if(!empty($isCanSubmit)){?>
                        <button type="button" class="btn btn-danger" onclick="submit(<?php echo $deliverySettlement['order_id']?>)">提交</button>
                    <?php } ?>
                <?php } ?>
                <button type="button"  class="btn btn-default history-back" onclick="back()">返回</button>
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

</script>