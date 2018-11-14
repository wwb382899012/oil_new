<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<section class="content">
<?php 
$this->renderPartial("../sellContractSettlement/contractInfo", array('contract'=>$data['contract']));
?>
    <div class="box" data-bind="with:settlement">
        <div class="box-body form-horizontal">
            <label for="settle_date" class="col-sm-2 control-label">结算日期
                <span class="text-red fa fa-asterisk"></span></label>
            <div class="col-sm-3">
                <input type="text" id="settle_date" class="form-control input-sm date" placeholder="请选择时间" data-bind="date:settle_date">
            </div>
        </div>
    </div>
   
    <?php //$this->renderPartial("ladingBillList", array('lading_bills'=>$data['settlement']['lading_bills'],'contract_id'=>$data['settlement']['contract_id']));

    if(!empty($data['settlement']['lading_bills'])){
        foreach($data['settlement']['lading_bills'] as $k=>$v){

            $this->renderPartial("/common/settlementDetail", array('settlement'=>$v,'type'=>1, 'isContractSettlement'=>1));
        }
    }

    ?>

    <?php
    include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/upload.php";
    include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/otherSettlementDetail.php";
    ?>
     <div class="box box-solid">
        <div class="box-body form-horizontal">
            <div class="form-group">
                <label class="col-sm-2 control-label">备注</label>
                <div class="col-sm-8">
                    <textarea class="form-control" id="remark" name= "obj[remark]" rows="3" placeholder="备注" data-bind="value:settlement.remark"></textarea>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <div class="pull-left">
                应付合计：<span data-bind="moneyText:settlement.amount"></span>元
<!--                <button class="btn btn-link" style="margin-left: 10px;">查看明细</button>-->
            </div>
            <div class="pull-right">
                <div class="btn btn-warning" data-bind="click:save, html:saveBtnText">保存</div>
                <div class="btn btn-primary" data-bind="click:tempSave, html:tempSaveBtnText" >暂存</div>
                <?php if(!$this->isExternal){ ?>
                <div class="btn btn-default" data-bind="click:back">返回</div>
                <?php } ?>
            </div>

        </div>
    </div>

</section>




<script type="text/javascript" src="/js/pages/settlement.js?key=20180423001"></script>
<script>
    settlementConfigs.currencies=<?php echo json_encode($this->map["currency"]); ?>;
    settlementConfigs.expenseNames=<?php echo json_encode($this->map["pay_type"]); ?>;
    settlementConfigs.units=<?php echo json_encode($this->map["goods_unit"]) ?>;
    settlementConfigs.taxSubjects=<?php echo json_encode(\ddd\domain\entity\value\Tax::getConfigs()) ?>;
    settlementConfigs.goodsOtherSubjects=<?php echo json_encode(\ddd\domain\entity\value\Expense::getConfigs()) ?>;
    settlementConfigs.otherExpenseSubjects=<?php echo json_encode(\ddd\domain\entity\value\OtherFee::getConfigs()) ?>;
    settlementConfigs.controllerName = "<?php echo $this->getId() ?>";

    var view;
    $(function () {
        view=new SettlementViewModel(<?php echo json_encode($data['settlement']) ?>);
        ko.applyBindings(view);
    });



</script>