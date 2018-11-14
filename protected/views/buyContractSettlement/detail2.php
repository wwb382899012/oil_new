<section class="content">
<?php 
$this->renderPartial("../sellContractSettlement/contractInfo", array('contract'=>$data['contract']));
?>
    <div class="box" data-bind="with:settlement">
        <div class="box-body form-horizontal">
            <label for="settle_date" class="col-sm-2 control-label">结算日期</label>
                <p class="form-control-static col-sm-3"><?php echo $data['contractSettlement']["settle_date"] ?></p>
        </div>
    </div>
     <?php $this->renderPartial("ladingBillList", array('lading_bills'=>$data['contractSettlement']['lading_bills'])); ?>

     
    <div class="box"  data-bind="with:settlement">
        <div class="box-header with-border">
            <h3 class="box-title">非货款类应付金额</h3>
        </div>
        <?php $this->renderPartial("otherGoods", array('contractSettlement'=>$data['contractSettlement'])); ?>
    </div>
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">审核意见</h3>
        </div>
        <div class="box-body">
            <p class="form-control-static"><?php echo $data["bill_quantity"] ?></p>
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

    var view;
    $(function () {
        view=new SettlementViewModel(<?php echo json_encode($data) ?>);
        ko.applyBindings(view);
    });



</script>