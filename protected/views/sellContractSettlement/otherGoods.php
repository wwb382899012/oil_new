        <?php
        $settle_currency_ico = empty($contractSettlement['settle_currency'])?'':$contractSettlement['settle_currency']['ico'];
        if(!empty($contractSettlement["other_expense"])):
        ?>  
        <div class="box-body form-horizontal">
            <div class="row">
                <?php foreach ($contractSettlement["other_expense"] as $key=>$value):?>
                <?php $currency_id = empty($value['currency'])?0:$value['currency']['id'];
                      $currency_ico = empty($value['currency'])?'':$value['currency']['ico'];
                ?>
                <div class="col-sm-12">
                    <fieldset style="border: 1px solid #aaa; padding: 10px 8px; border-radius: 5px;">
                        <div class="form-group">
                            <div class="col-sm-4">
                                <label class="col-sm-4 control-label">科目 </label>
                                <p class="form-control-static col-sm-8 row"><?php echo empty($value['fee'])?'':$value['fee']['name']; ?></p>
                               
                            </div>
                            <div class="col-sm-4">
                                <label class="col-sm-4 control-label">币种</label>
                                <p class="form-control-static col-sm-8 row"><?php echo empty($value['currency'])?'':$value['currency']['name']; ?></p>
                               
                            </div>
                            <div class="col-sm-4">
                                <label class="col-sm-4 control-label">金额</label>
                                <p class="form-control-static col-sm-6"><?php echo $currency_ico.number_format($value['amount']/100,2); ?></p>
                               
                            </div>
                        </div>
                         <?php if($currency_id==ConstantMap::CURRENCY_DOLLAR):?>
                        <div class="form-group" data-bind="visible:cnyIsVisible">
                            <div class="col-sm-4">
                                <label class="col-sm-4 control-label">汇率</label>
                                <p class="form-control-static col-sm-8 row"><?php echo $value['exchange_rate']; ?></p>
                               
                            </div>
                            <div class="col-sm-4">
                                <label class="col-sm-4 control-label">人民币金额</label>
                                <p class="form-control-static col-sm-8">￥<?php echo number_format($value['amount_cny']/100,2); ?></p>
                              
                            </div>
                        </div>
                        <?php endif;?>
                        <div class="form-group">
                            <div class="col-sm-4">
                                <label class="col-sm-4 control-label">单据</label>
                                
                               <?php
                               if(!empty($value['otherFiles'])){
                                        foreach ($value['otherFiles'] as $sf){
                                            echo '<p class="form-control-static col-sm-offset-4"><a href="/sellContractSettlement/getFile/?id='.$sf['id'].'&fileName='.$sf['name'].'" target="_blank" class="btn btn-primary btn-xs">'.$sf['name'].'</a></p>';
                                        }
                                    }else{
                                        echo '<p class="form-control-static col-sm-8">无</p>';
                                    }?>
                            </div>
                            <div class="col-sm-8" style="margin-left: -4px;">
                                <label class="col-sm-2 control-label">备注</label>
                                <p class="form-control-static col-sm-10"><?php echo $value["remark"] ?></p>
                                
                            </div>
                        </div>
                    </fieldset>
                </div>
                <?php endforeach;?>
               
                <div class="total-amount col-sm-12" style="margin-top: 10px">
                                                        合计人民币总额：<span class="form-control-static"><?php echo number_format($contractSettlement["other_amount"]/100,2) ?> </span>元
                </div>
        </div>
     </div>
        <?php endif;?>