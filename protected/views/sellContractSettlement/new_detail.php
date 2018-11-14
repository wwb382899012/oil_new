<section>
    <?php
    $menus = [['text' => '结算管理'],['text'=>'销售合同结算','link'=>'/sellContractSettlement/'], ['text' => $this->pageTitle]];

    if($data['isCanEdit'])
        $buttons[] = ['text' => '修改', 'attr' => ['onclick' => 'edit()', 'class_abbr' => 'action-default-base']];

    if($data['isCanSubmit'])
        $buttons[] = ['text' => '提交', 'attr' => ['onclick' => 'submit('.$data['settlement']['contract_id'].')', 'id' => 'saveButton']];

    $this->loadHeaderWithNewUI($menus, $buttons, '/sellContractSettlement/');

    $this->renderPartial("/sellContractSettlement/new_contractInfo", array('contract'=>$data['contract']));
    ?>
    <div class="z-card">
        <div class="z-card-body">
            <div class="flex-grid form-group" style="margin-bottom: 30px;">
                <label class="col col-count-3 field flex-grid">
                    <p class="form-cell-title w-fixed">结算日期:</p>
                    <p class="form-control-static"><?php echo $data['settlement']["settle_date"] ?></p>
                </label>
            </div>
        </div>
    </div>

    <?php

    if(empty($data['settlement']['settlementGoods'])){//按发货单结算
        if(!empty($data['settlement']['delivery_orders'])){
            foreach($data['settlement']['delivery_orders'] as $k=>$v){
                $this->renderPartial("/common/new_settlementDetail", array('settlement'=>$v,'type'=>3, 'isContractSettlement'=>1, 'isHiddenBtn'=>true));
            }
        }
    }else{//按销售合同结算
        $this->renderPartial("new_goodItems", array('contractSettlement'=>$data['settlement']));
    }
    ?>

    <div class="z-card">
        <div class="content-title-wrap">
            <h3 class="z-card-header">
                非货款类应收金额
                <p class="content-wrap-expand pull-right box-tools"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </h3>
        </div>
        <?php $this->renderPartial("new_otherGoods", array('contractSettlement'=>$data['settlement'])); ?>
    </div>
    <div class="z-card">
        <div class="z-card-body">
            <div class="flex-grid form-group">
                <label class="col field col-count-1 flex-grid">
                <span class="w-fixed">
                    备注:
                </span>
                    <p class="form-control-static"><?php echo $data['settlement']['remark'];?></p>
                </label>
            </div>
            <div>
                应收合计: <span><?php echo number_format($data['settlement']['amount_settle']/100,2);?></span>元
                <a href="javascript: void 0" class="o-btn o-btn-action primary" style="margin-left: 10px;" onclick="lookDetail()">查看明细</a>
            </div>
        </div>
    </div>

<?php 
if (Utility::isNotEmpty($data['checkLogs']))
    $this->renderPartial("/common/new_checkLogList", array('checkLogs' => $data['checkLogs']));
?>
    
    
<?php $this->renderPartial("amountDetail", array('contractSettlement'=>$data['settlement'],'contract'=>$data['contract'], 'contract_type'=>ConstantMap::SALE_TYPE));  ?>
    


</section>

<script>
    function edit() {
       location.href = '/<?php echo $this->getId() ?>/edit?id='+"<?php echo $data['contract']['contract_id']; ?>";
    }

    function submit(contract_id) {
        inc.vueConfirm({
            content: "您确定要提交销售合同结算单吗，该操作不可逆？",
            onConfirm: function () {
                var formData = "contract_id=" + contract_id;
                $.ajax({
                    type: "POST",
                    url: "/<?php echo $this->getId() ?>/submit",
                    data: formData,
                    dataType: "json",
                    success: function (json) {
                        if (json.state == 0) {
                            inc.vueMessage({
                                message: '操作成功',duration:500, onClose: function () {
                                    location.reload();
                                }
                            });
                        }
                        else {
                            inc.vueAlert(json.data);
                        }
                    },
                    error: function (data) {
                        inc.vueAlert("操作失败！" + data.responseText);
                    }
                });
            }
        })
    }



</script>