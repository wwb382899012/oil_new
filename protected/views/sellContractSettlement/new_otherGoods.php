        <?php 
        $settle_currency_ico = empty($contractSettlement['settle_currency'])?'':$contractSettlement['settle_currency']['ico'];
        if(!empty($contractSettlement["other_expense"])):?>  
        <div class="z-card-body">
            <div class="busi-detail">
           <?php foreach ($contractSettlement["other_expense"] as $key=>$value):?>
           
           <?php      $currency_id = empty($value['currency'])?0:$value['currency']['id'];
                      $currency_ico = empty($value['currency'])?'':$value['currency']['ico'];
           ?>
                <div class="settlement-divide">
                    <div class="flex-grid form-group">
                        <label class="col field col-count-3 flex-grid">
                            <p class="form-cell-title w-fixed">科目:</p>
                            <p class="form-control-static"><?php echo empty($value['fee'])?'':$value['fee']['name']; ?></p>
                        </label>
                        <label class="col field col-count-3 flex-grid">
                            <p class="form-cell-title w-fixed">币种:</p>
                            <p class="form-control-static"><?php echo empty($value['currency'])?'':$value['currency']['name']; ?></p>
                        </label>
                        <label class="col field col-count-3 flex-grid">
                            <p class="form-cell-title w-fixed">金额:</p>
                            <p class="form-control-static"><?php echo $currency_ico.number_format($value['amount']/100,2); ?></p>
                        </label>
                    </div>
                    <?php if($currency_id==ConstantMap::CURRENCY_DOLLAR){?>
                    <div class="flex-grid form-group">
                        <label class="col field col-count-3 flex-grid">
                            <p class="form-cell-title w-fixed">汇率:</p>
                            <p class="form-control-static"><?php echo $value['exchange_rate']; ?></p>
                        </label>
                        <label class="col field col-count-3 flex-grid">
                            <p class="form-cell-title w-fixed">人民币金额:</p>
                            <p class="form-control-static">￥<?php echo number_format($value['amount_cny']/100,2); ?></p>
                        </label>
                    </div>
                    <?php } ?>
                    <div class="flex-grid form-group">
                        <?php
                        $attachments=AttachmentService::getAttachments(Attachment::C_CONTRACT_SETTLEMENT,$value['detail_id'], 101);
                        $this->renderPartial("/components/new_attachmentsDropdown", array(
                                'id' => $value['detail_id'],
                                'map_key'=>'contract_settlement_attachment',
                                'attach_type'=>101,
                                'attachment_type'=>Attachment::C_CONTRACT_SETTLEMENT,
                                'controller'=>'buyContractSettlement',
                            )
                        );
                        ?>
                        <!-- <label class="col field col-count-1 flex-grid">
                            <span class="w-fixed">
                                单据:
                            </span>
                            <?php
                            if(!empty($value['otherFiles'])){
                                foreach ($value['otherFiles'] as $of){
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
                            <p class="form-control-static"><?php echo $value["remark"] ?></p>
                        </label>
                    </div>
                    <!-- 提示：结算单据格式支持上传图片，Excel、word、pdf，压缩包格式文件，文件不能超过30M -->
                </div>
            <?php endforeach;?>
                <div class="total-amount">
                    合计人民币总额: <span ><?php echo number_format($contractSettlement['other_amount']/100,2);?></span>元
                </div>
            </div>
        </div>
        <?php endif;?>