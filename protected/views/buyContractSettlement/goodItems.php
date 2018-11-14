    <?php
    $settle_currency_ico = empty($contractSettlement['settle_currency'])?'':$contractSettlement['settle_currency']['ico'];
    if(!empty($contractSettlement['settlementGoods'])):?> 
      <?php foreach ($contractSettlement['settlementGoods'] as $key=>$value):?>
    <div class="box">
        <div class="box-header link with-border">
            <h3 class="box-title"><span><?php echo $value['goods_name'];?></span> 应付结算</h3>
        </div>
        <div class="box-body form-horizontal">
            <div class="row">
                <form class="col-sm-12">
                    <fieldset>
                      
                        <?php if(!empty($value['lading_items'])):?>
                          <?php foreach ($value['lading_items'] as $bill=>$bill_value):?>
                        <fieldset style="border: 1px solid; padding: 0.35em 0.625em 0.75em;margin-bottom: 15px">
                            <legend class="h4 text-primary" style="border: 0;  width: auto;">入库通知单编号：<a href="/stockIn/detail?id=<?php echo $bill_value['batch_id'];?>&t=1" target="_blank"><?php echo $bill_value['batch_code'];?></a></legend>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">入库单数量</label>
                                    <p class="form-control-static col-sm-8">
                                    <?php 
                                    echo number_format($bill_value["in_quantity"]['quantity'],4); ?><?php echo Map::$v['goods_unit'][$bill_value['in_quantity']['unit']]['name'];
                                    if( !empty($bill_value['in_quantity_sub']['unit']) && $bill_value['in_quantity_sub']['unit']!=$bill_value['in_quantity']['unit']) {
                                        echo '/'. Utility::numberFormatToDecimal($bill_value['in_quantity_sub']['quantity'], 4). $this->map["goods_unit"][$bill_value["in_quantity_sub"]["unit"]]['name'];
                                    }
                                    ?>
                                    </p>
                                </div>
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">结算数量</label>
                                    <p class="form-control-static col-sm-8"><?php echo number_format($bill_value["quantity"]['quantity'],4); ?><?php echo Map::$v['goods_unit'][$bill_value['quantity']['unit']]['name'];?></p>
                                </div>
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">损耗量</label>
                                    <p class="form-control-static col-sm-8"><?php echo number_format($bill_value["quantity_loss"]['quantity'],4); ?><?php echo Map::$v['goods_unit'][$bill_value['quantity_loss']['unit']]['name'];?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">结算单价</label>
                                    <p class="form-control-static col-sm-8"><?php echo $settle_currency_ico.number_format($bill_value["price"]/100,2); ?></p>
                                </div>
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">结算金额</label>
                                    <p class="form-control-static col-sm-8"><?php echo  $settle_currency_ico.number_format($bill_value["amount"]/100,2); ?></p>
                                </div>
                                
                            </div>
                             <?php if(isset($contractSettlement['settle_currency']['id'])&&$contractSettlement['settle_currency']['id']==ConstantMap::CURRENCY_DOLLAR):?>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">人民币结算单价</label>
                                    <p class="form-control-static col-sm-8">￥<?php echo  number_format($bill_value["price_cny"]/100,2); ?></p>
                                </div>
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">人民币结算金额</label>
                                    <p class="form-control-static col-sm-8">￥<?php echo  number_format($bill_value["amount_cny"]/100,2); ?></p>
                                </div>

                            </div>
                            <?php endif;?>
                        </fieldset>
                        <?php endforeach;?>
                        <?php endif;?>
                      
                        <fieldset style="border: 1px solid; padding: 0.35em 0.625em 0.75em;margin-bottom: 15px">
                            <legend class="h4" style="border: 0;  width: auto;">合计</legend>
                            <div class="clearfix">
                                <div class="pull-right">
                                <?php if(!empty($value['hasDetail'])){ ?>
                                <button type="button" class="btn btn-link hideBtn"  onclick="hideDetail(<?php echo $value['goods_id'] ?>)"  id="<?php echo 'hideDetail_'.$value['goods_id'] ?>"> 收起明细</button>
                                <button type="button" class="btn btn-link showBtn" onclick="showDetail(<?php echo $value['goods_id'] ?>)" id="<?php echo 'showDetail_'.$value['goods_id'] ?>"> 展开明细</button>
                                <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">入库单数量</label>
                                    <p class="form-control-static col-sm-8">
                                    <?php 
                                    echo number_format($value["in_quantity"]['quantity'],4); ?><?php echo Map::$v['goods_unit'][$value['in_quantity']['unit']]['name'];
                                    if( !empty($value['in_quantity_sub']['unit']) && $value['in_quantity_sub']['unit']!=$value['in_quantity']['unit']) {
                                        echo '/'. Utility::numberFormatToDecimal($value['in_quantity_sub']['quantity'], 4). $this->map["goods_unit"][$value["in_quantity_sub"]["unit"]]['name'];
                                    }
                                    ?></p>
                                </div>
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">结算数量</label>
                                    <p class="form-control-static col-sm-8"><?php echo number_format($value["quantity"]['quantity'],4); ?><?php echo Map::$v['goods_unit'][$value['quantity']['unit']]['name'];?></p>
                                </div>
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">损耗量</label>
                                    <p class="form-control-static col-sm-8"><?php echo number_format($value["quantity_loss"]['quantity'],4); ?><?php echo Map::$v['goods_unit'][$value['quantity_loss']['unit']]['name'];?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">结算单价</label>
                                    <p class="form-control-static col-sm-8"><?php echo $settle_currency_ico.number_format($value["price"]/100,2); ?></p>
                                </div>
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">结算金额</label>
                                    <p class="form-control-static col-sm-8"><?php echo $settle_currency_ico.number_format($value["amount"]/100,2); ?></p>
                                </div>
                                <?php if(isset($contractSettlement['settle_currency']['id'])&&$contractSettlement['settle_currency']['id']==ConstantMap::CURRENCY_DOLLAR):?>
                                <div class="col-sm-4" data-bind="visible:cnyVisible">
                                    <label class="col-sm-4 control-label">结算汇率</label>
                                    <p class="form-control-static col-sm-8"><?php echo $value["unit_rate"]; ?></p>
                                </div>
                                <?php endif;?>
                            </div>
                            <?php if(isset($contractSettlement['settle_currency']['id'])&&$contractSettlement['settle_currency']['id']==ConstantMap::CURRENCY_DOLLAR):?>
                            <div class="form-group" data-bind="visible:cnyVisible">
                                 <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">人民币结算单价</label>
                                    <p class="form-control-static col-sm-8"><?php echo '￥'.number_format($value["price_cny"]/100,2); ?></p>
                                </div>
                                <div class="col-sm-4">
                                    <label class="col-sm-4 control-label">人民币结算金额</label>
                                    <p class="form-control-static col-sm-8"><?php echo '￥'.number_format($value["amount_cny"]/100,2); ?></p>
                                </div>
                               
                            </div>
                            <?php endif;?>
                            <?php if(!empty($value['hasDetail'])){ ?>
                            <fieldset id="<?php echo  'displayDetail_'.$value['goods_id'] ?>" >
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
                                    </table>
                                </div>
                                <div class="form-group col-sm-12">
                                    <div class="h5">相关税收
                                       
                                    </div>
                                    <table class="table table-striped table-bordered">
                                        <tr>
                                            <td>税收名目</td>
                                            <td>税率</td>
                                            <td>税收总金额</td>
                                            <td>税收单价</td>
                                            <td>备注</td>
                                        </tr>
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
                                    </table>
                                </div>
                                <div class="form-group col-sm-12">
                                    <div class="h5">其他费用
                                        
                                    </div>
                                    <table class="table table-striped table-bordered">
                                        <tr>
                                            <td>科目</td>
                                            <td>费用总额</td>
                                            <td>费用单价</td>
                                            <td>备注</td>
                                        </tr>
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
                                    </table>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        <label class="col-sm-4 control-label">调整金额</label>
                                        <p class="form-control-static col-sm-8">
                                          <?php echo $this->map['contract_settlement_adjust'][$value['settlementGoodsDetail']['adjust_type']]['name']; ?> &nbsp; <?php echo '￥'.number_format($value['settlementGoodsDetail']['amount_adjust']/100,2);?></p>
                                    </div>
                                    <div class="col-sm-8">
                                        <label class="col-sm-2 control-label" style="width: 16.1%">调整原因</label>
                                        <p class="form-control-static col-sm-10 row"><?php echo $value['settlementGoodsDetail']['reason_adjust'];?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        <label class="col-sm-4  control-label">总结算数量</label>
                                        <p class="form-control-static col-sm-8"><?php echo number_format($value['settlementGoodsDetail']['quantity']['quantity'],4);?><?php echo Map::$v['goods_unit'][$value['settlementGoodsDetail']['quantity']['unit']]['name'];?></p>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-4  control-label">人民币结算金额</label>
                                        <p class="form-control-static col-sm-8"><?php echo '￥'.number_format($value['settlementGoodsDetail']['amount']/100,2);?></p>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-4  control-label">人民币结算单价</label>
                                        <p class="form-control-static col-sm-8"><?php echo '￥'.number_format($value['settlementGoodsDetail']['price']/100,2);?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-4">
                                        <label class="col-sm-4  control-label">确定总结算数量 </label>
                                        <p class="form-control-static col-sm-8"><?php echo number_format($value['settlementGoodsDetail']['quantity_actual']['quantity'],4);?><?php echo Map::$v['goods_unit'][$value['settlementGoodsDetail']['quantity_actual']['unit']]['name'];?></p>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-4  control-label">确定人民币结算金额</label>
                                        <p class="form-control-static col-sm-8"><?php echo '￥'.number_format($value['settlementGoodsDetail']['amount_actual']/100,2);?></p>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="col-sm-4  control-label">确定人民币结算单价</label>
                                        <p class="form-control-static col-sm-8"><?php echo '￥'.number_format($value['settlementGoodsDetail']['price_actual']/100,2);?></p>
                                    </div>
                                </div>

                            </fieldset>
                            <?php } ?>
                            <div class="form-group">
                                
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">
                                            <span class='glyphicon ' ></span>&emsp;
                                         	   结算单据
                                        </label>
                                        
                                         <?php
                                                if(!empty($value['settleFiles'])){
                                                    foreach ($value['settleFiles'] as $sf){
                                                        echo '<p class="form-control-static col-sm-offset-4"><a href="/buyContractSettlement/getFile/?id='.$sf['id'].'&fileName='.$sf['name'].'" target="_blank" class="btn btn-primary btn-xs">'.$sf['name'].'</a></p>';
                                                    }
                                                }else{
                                                    echo '<p class="form-control-static col-sm-8">无</p>';
                                                }?>
                                       
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">其他附件</label>
                                         <?php
                                                if(!empty($value['goodsOtherFiles'])){
                                                    foreach ($value['goodsOtherFiles'] as $sf){
                                                        echo '<p class="form-control-static col-sm-offset-4"><a href="/buyContractSettlement/getFile/?id='.$sf['id'].'&fileName='.$sf['name'].'" target="_blank" class="btn btn-primary btn-xs">'.$sf['name'].'</a></p>';
                                                    }
                                                }else{
                                                    echo '<p class="form-control-static col-sm-8">无</p>';
                                                }?>
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <label>备注: </label>
                                    <p class="form-control-static"><?php echo $value['remark'];?></p>
                                </div>
                            </div>
                        </fieldset>
                    </fieldset>
                </form>
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