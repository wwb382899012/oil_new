        <?php
    $settle_currency_ico = empty($contractSettlement['settle_currency'])?'':$contractSettlement['settle_currency']['ico'];
    if(!empty($contractSettlement['settlementGoods'])):?> 
      <?php foreach ($contractSettlement['settlementGoods'] as $key=>$value):?>
    <div class="z-card ">
        <div class="content-title-wrap">
            <h3 class="z-card-header"><span><?php echo $value['goods_name'];?></span>应收结算
                <p class="content-wrap-expand pull-right box-tools"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </h3>
        </div>
        <div class="z-card-body">
            <div class="busi-detail">
                <?php if(!empty($value['order_items'])):?>
                    <?php foreach ($value['order_items'] as $bill=>$bill_value):?>
                <div class="settlement-divide">
                    <div class="clearfix  content-title">
                        <h3 class="pull-left">发货单编号：<a class="text-link" href="/deliveryOrder/detail?id=<?php echo $bill_value['bill_id'];?>&t=1" target="_blank"><?php echo $bill_value['delivery_code'];?></a></h3>
                    </div>
                    <div class="flex-grid form-group">
                        <label class="col field col-count-3 flex-grid">
                            <p class="form-cell-title w-fixed">出库单数量:</p>
                            <p class="form-control-static">
                                <?php echo number_format($bill_value["out_quantity"]['quantity'],4); ?><?php echo Map::$v['goods_unit'][$bill_value['out_quantity']['unit']]['name'];?>
                            </p>
                        </label>
                        <label class="col field col-count-3 flex-grid">
                            <p class="form-cell-title w-fixed">结算数量:</p>
                            <p class="form-control-static"><?php echo number_format($bill_value["quantity"]['quantity'],4); ?><?php echo Map::$v['goods_unit'][$bill_value['quantity']['unit']]['name'];?></p>
                        </label>
                        <label class="col field col-count-3 flex-grid">
                            <p class="form-cell-title w-fixed">损耗量:</p>
                            <p class="form-control-static"><?php echo number_format($bill_value["quantity_loss"]['quantity'],4); ?><?php echo Map::$v['goods_unit'][$bill_value['quantity_loss']['unit']]['name'];?></p>
                        </label>
                    </div>
                    <div class="flex-grid form-group">
                        <label class="col field col-count-3 flex-grid">
                            <p class="form-cell-title w-fixed">结算单价:</p>
                            <p class="form-control-static">
                                <?php echo $settle_currency_ico.number_format($bill_value["price"]/100,2); ?>
                            </p>
                        </label>
                        <label class="col field col-count-3 flex-grid">
                            <p class="form-cell-title w-fixed">结算金额:</p>
                            <p class="form-control-static"><?php echo  $settle_currency_ico.number_format($bill_value["amount"]/100,2); ?></p>
                        </label>
                    </div>
                    <?php if(isset($contractSettlement['settle_currency']['id'])&&$contractSettlement['settle_currency']['id']==ConstantMap::CURRENCY_DOLLAR):?>
                    <div class="flex-grid form-group">
                        <label class="col field col-count-3 flex-grid">
                            <p class="form-cell-title w-fixed">人民币结算单价:</p>
                            <p class="form-control-static">
                                ￥<?php echo  number_format($bill_value["price_cny"]/100,2); ?>
                            </p>
                        </label>
                        <label class="col field col-count-3 flex-grid">
                            <p class="form-cell-title w-fixed">人民币结算金额:</p>
                            <p class="form-control-static">￥<?php echo  number_format($bill_value["amount_cny"]/100,2); ?></p>
                        </label>
                    </div>
                    <?php endif; ?>
                </div>
                    <?php endforeach;?>
                <?php endif;?>
                <div class="settlement-divide">
                    <div class="clearfix">
                        <h3 class="pull-left content-title">合计</h3>
                        <div class="pull-right">
                            <?php if(!empty($value['hasDetail'])){ ?>
                                <a href="javascript: void 0" role="button" class="o-btn o-btn-action hideBtn"  onclick="hideDetail(<?php echo $value['goods_id'] ?>)"  id="<?php echo 'hideDetail_'.$value['goods_id'] ?>"> 收起明细</a>
                                <a href="javascript: void 0" role="button" class="o-btn o-btn-action showBtn" onclick="showDetail(<?php echo $value['goods_id'] ?>)" id="<?php echo 'showDetail_'.$value['goods_id']?>"> 展开明细</a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="flex-grid form-group">
                        <label class="col col-count-3 field flex-grid">
                            <p class="form-cell-title w-fixed">
                                <span>出库单数量:
                            </p>
                            <p class="form-control-static">
                                <?php echo number_format($value["out_quantity"]['quantity'],4); ?><?php echo Map::$v['goods_unit'][$value['out_quantity']['unit']]['name'];?>
                            </p>
                        </label>
                        <label class="col field col-count-3 flex-grid">
                            <p class="form-cell-title w-fixed">结算数量:</p>
                            <p class="form-control-static"><?php echo number_format($value["quantity"]['quantity'],4); ?><?php echo Map::$v['goods_unit'][$value['quantity']['unit']]['name'];?></p>
                        </label>
                        <label class="col field col-count-3 flex-grid">
                            <p class="form-cell-title w-fixed">损耗量:</p>
                            <p class="form-control-static"><?php echo number_format($value["quantity_loss"]['quantity'],4); ?><?php echo Map::$v['goods_unit'][$value['quantity_loss']['unit']]['name'];?></p>
                        </label>
                    </div>
                    <div class="flex-grid form-group">
                        <label class="col field col-count-3 flex-grid">
                            <p class="form-cell-title w-fixed">结算单价:</p>
                            <p class="form-control-static"><?php echo $settle_currency_ico.number_format($value["price"]/100,2); ?></p>
                        </label>
                        <label class="col field col-count-3 flex-grid">
                            <p class="form-cell-title w-fixed">结算金额:</p>
                            <p class="form-control-static"><?php echo $settle_currency_ico.number_format($value["amount"]/100,2); ?></p>
                        </label>
                        <?php if(isset($contractSettlement['settle_currency']['id'])&&$contractSettlement['settle_currency']['id']==ConstantMap::CURRENCY_DOLLAR){?>
                            <label class="col field col-count-3 flex-grid">
                                <p class="form-cell-title w-fixed">结算汇率:</p>
                                <p class="form-control-static"><?php echo $value["unit_rate"]; ?></p>
                            </label>
                        <?php } ?>
                    </div>
                    <?php if(isset($contractSettlement['settle_currency']['id'])&&$contractSettlement['settle_currency']['id']==ConstantMap::CURRENCY_DOLLAR){?>
                        <div class="flex-grid form-group">
                            <label class="col field col-count-3 flex-grid">
                                <p class="form-cell-title w-fixed">人民币结算单价:</p>
                                <p class="form-control-static"><?php echo '￥'.number_format($value["price_cny"]/100,2); ?></p>
                            </label>
                            <label class="col field col-count-3 flex-grid">
                                <p class="form-cell-title w-fixed">人民币结算金额:</p>
                                <p class="form-control-static"><?php echo '￥'.number_format($value["amount_cny"]/100,2); ?></p>
                            </label>
                        </div>
                    <?php } ?>
                    <?php if(!empty($value['hasDetail'])){ ?>
                    <fieldset class="input-detail form-group" id="<?php echo 'displayDetail_'.$value['goods_id'] ?>" >
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
                                            <p class="form-control-static"><?php echo isset($value['settlementGoodsDetail']['currency']['name'])?$value['settlementGoodsDetail']['currency']['name']:'';?></p>
                                        </td>
                                        <td>
                                            <p class="form-control-static"><?php echo $settle_currency_ico.number_format($value['settlementGoodsDetail']['amount_currency']/100,2);?></p>
                                        </td>
                                        <td>
                                            <p class="form-control-static"><?php echo $value['settlementGoodsDetail']['exchange_rate'];?></p>
                                        </td>
                                        <td>
                                            <p class="form-control-static">￥<?php echo number_format($value['settlementGoodsDetail']['amount_goods']/100,2);?></p>
                                        </td>
                                        <td>
                                            <p class="form-control-static">￥<?php echo number_format($value['settlementGoodsDetail']['price_goods']/100,2);?></p>
                                        </td>
                                        <td><p class="form-control-static"><?php echo $value['settlementGoodsDetail']['exchange_rate_tax'];?></p></td>
                                        <td>
                                            <p class="form-control-static">￥<?php echo number_format($value['settlementGoodsDetail']['amount_goods_tax']/100,2);?></p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group">
                            <p class="form-cell-title">相关税收</p>
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
                                <?php if(!empty($value['settlementGoodsDetail']['tax_detail_item'])):?>
                                 <?php foreach ($value['settlementGoodsDetail']['tax_detail_item'] as $tax_key=>$tax_value):?>
                                    <tr>
                                        <td>
                                            <p class="form-control-static"><?php echo isset($tax_value['subject_list']['name'])?$tax_value['subject_list']['name']:''; ?></p>
                                        </td>
                                        <td><p class="form-control-static"><?php echo ($tax_value['rate']*100).'%'; ?></p></td>
                                        <td><p class="form-control-static">￥<?php echo number_format($tax_value['amount']/100,2); ?></p></td>
                                        <td><p class="form-control-static">￥<?php echo number_format($tax_value['price']/100,2); ?></p></td>
                                        <td><p class="form-control-static"><?php echo $tax_value['remark']; ?></p></td>
                                    </tr>
                                <?php endforeach;?>
                                <?php endif;?>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group">
                            <p class="form-cell-title">其他费用</p>
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
                               <?php if(!empty($value['settlementGoodsDetail']['other_detail_item'])):?>
                                 <?php foreach ($value['settlementGoodsDetail']['other_detail_item'] as $other_key=>$other_value):?>
                                    <tr>
                                        <td>
                                            <p class="form-control-static"><?php echo isset($other_value['subject_list']['name'])?$other_value['subject_list']['name']:''; ?></p>
                                        </td>
                                        <td><p class="form-control-static">￥<?php echo number_format($other_value['amount']/100,2); ?></p></td>
                                        <td><p class="form-control-static">￥<?php echo number_format($other_value['price']/100,2); ?></p></td>
                                        <td><p class="form-control-static"><?php echo $other_value['remark']; ?></p></td>
                                    </tr>
                                 <?php endforeach;?>
                                <?php endif;?>
                                </tbody>
                            </table>
                        </div>
                        <div class="o-row form-group">
                            <label class="o-col-sm-4 flex-grid">
                                <p class="form-cell-title w-fixed">调整金额:</p>
                                <p class="form-control-static"><?php echo $this->map['contract_settlement_adjust'][$value['settlementGoodsDetail']['adjust_type']]['name']; ?> &nbsp; <?php echo '￥'.number_format($value['settlementGoodsDetail']['amount_adjust']/100,2);?></p>
                            </label>
                            <label class="o-col-sm-8 flex-grid">
                                <p class="form-cell-title w-fixed">调整原因:</p>
                                <p class="form-control-static"><?php echo $value['settlementGoodsDetail']['reason_adjust'];?></p>
                            </label>
                        </div>
                        <div class="flex-grid form-group">
                            <label class="col field col-count-3 flex-grid align-start">
                                <p class="form-cell-title w-fixed">总结算数量:</p>
                                <p class="form-control-static"><?php echo number_format($value['settlementGoodsDetail']['quantity']['quantity'],4);?><?php echo Map::$v['goods_unit'][$value['settlementGoodsDetail']['quantity']['unit']]['name'];?></p>
                            </label>
                            <label class="col field col-count-3 flex-grid align-start">
                                <p class="form-cell-title w-fixed">人民币结算金额:</p>
                                <p class="form-control-static"><?php echo '￥'.number_format($value['settlementGoodsDetail']['amount']/100,2);?></p>
                            </label>
                            <label class="col field col-count-3 flex-grid align-start">
                                <p class="form-cell-title w-fixed">人民币结算单价:</p>
                                <p class="form-control-static"><?php echo '￥' . number_format($value['settlementGoodsDetail']['price']/100, 2); ?></p>
                            </label>
                        </div>
                        <div class="flex-grid form-group">
                            <label class="col field col-count-3 flex-grid align-start">
                                <p class="form-cell-title w-fixed">确定总结算数量:</p>
                                <p class="form-control-static"><?php echo number_format($value['settlementGoodsDetail']['quantity_actual']['quantity'],4);?><?php echo Map::$v['goods_unit'][$value['settlementGoodsDetail']['quantity_actual']['unit']]['name'];?></p>
                            </label>
                            <label class="col field col-count-3 flex-grid align-start">
                                <p class="form-cell-title cell-title">确定人民币结算金额:</p>
                                <p class="form-control-static"><?php echo '￥'.number_format($value['settlementGoodsDetail']['amount_actual']/100,2);?></p>
                            </label>
                            <label class="col field col-count-3 flex-grid align-start">
                                <p class="form-cell-title cell-title">确定人民币结算单价:</p>
                                <p class="form-control-static"><?php echo '￥'.number_format($value['settlementGoodsDetail']['price_actual']/100,2);?></p>
                            </label>
                        </div>
                    </fieldset>
                    <?php } ?>
                    <div class="flex-grid form-group">
                        <?php
                        $attachments=AttachmentService::getAttachments(Attachment::C_CONTRACT_SETTLEMENT,$value['item_id'], 1);
                        $this->renderPartial("/components/new_attachmentsDropdown", array(
                                'id' => $value['item_id'],
                                'map_key'=>'contract_settlement_attachment',
                                'attach_type'=>1,
                                'attachment_type'=>Attachment::C_CONTRACT_SETTLEMENT,
                                'controller'=>'buyContractSettlement',
                            )
                        );
                        ?>
                        <!-- <label class="col field col-count-1 flex-grid">
                            <span class="w-fixed">
                                结算单据:
                            </span>
                            <?php
                            if(!empty($value['settleFiles'])){
                                foreach ($value['settleFiles'] as $sf){
                                    echo '<p class="form-control-static"><a href="/buyContractSettlement/getFile/?id='.$sf['id'].'&fileName='.$sf['name'].'" target="_blank" class="text-link">'.$sf['name'].'</a></p>';
                                }
                            }else{
                                echo '<p class="form-control-static">无</p>';
                            }?>
                        </label> -->
                    </div>
                    <div class="flex-grid form-group">
                        <?php
                        $attachments=AttachmentService::getAttachments(Attachment::C_CONTRACT_SETTLEMENT,$value['item_id'], 2);
                        $this->renderPartial("/components/new_attachmentsDropdown", array(
                                'id' => $value['item_id'],
                                'map_key'=>'contract_settlement_attachment',
                                'attach_type'=>2,
                                'attachment_type'=>Attachment::C_CONTRACT_SETTLEMENT,
                                'controller'=>'buyContractSettlement',
                            )
                        );
                        ?>
                        <!-- <label class="col field col-count-1 flex-grid">
                            <span class="w-fixed">
                                其他附件:
                            </span>
                            <?php
                            if(!empty($value['goodsOtherFiles'])){
                                foreach ($value['goodsOtherFiles'] as $of){
                                    echo '<p class="form-control-static"><a href="/buyContractSettlement/getFile/?id='.$of['id'].'&fileName='.$of['name'].'" target="_blank" class="text-link">'.$of['name'].'</a></p>';
                                }
                            }else{
                                echo '<p class="form-control-static">无</p>';
                            }?>
                        </label> -->
                    </div>
                    <div class="flex-grid form-group">
                        <label class="col field col-count-1 flex-grid">
                            <span class="w-fixed">
                                备注:
                            </span>
                            <p class="form-control-static"><?php echo $value['remark']; ?></p>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach;?>
   <?php endif;?>
   
    
    
   
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


    </script>