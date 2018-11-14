<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>

<?php
$menus = [['text' => '结算管理'],['text'=>'采购合同结算','link'=>'/buyContractSettlement/'], ['text' => $this->pageTitle]];
$buttons = [];
$buttons[] = ['text' => '暂存', 'attr' => ['data-bind' => 'click:tempSave, html:tempSaveBtnText',  'id' => 'tempSaveButton', 'class_abbr'=>'action-default-base']];
$buttons[] = ['text' => '保存', 'attr' => ['data-bind' => 'click:save, html:saveBtnText', 'id' => 'saveButton']];
$this->loadHeaderWithNewUI($menus, $buttons, true);
?>
<?php 
$this->renderPartial("/sellContractSettlement/new_contractInfo", array('contract'=>$data['contract']));
?>
    <div class="z-card">
        <div class="z-card-body">
            <form data-bind="with:settlement">
                <div class="flex-grid form-group" style="margin-bottom: 30px;">
                    <label class="col col-count-3 field">
                        <p class="form-cell-title must-fill">结算日期:</p>
                        <input type="text" id="settle_date" class="form-control input-sm date" placeholder="请选择时间" data-bind="date:settle_date">
                    </label>
                </div>
            </form>
        </div>
    </div>
   
    <?php 

    if(!empty($data['settlement']['lading_bills'])){
        foreach($data['settlement']['lading_bills'] as $k=>$v){
            $this->renderPartial("/common/new_settlementDetail", array('settlement'=>$v,'type'=>1, 'isContractSettlement'=>1, 'isHiddenBtn'=>true));
        }
    }

    ?>

    <?php
    include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_uploadNew.php";
    include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_otherSettlementDetail.php";
    ?>
    <div class="z-card">
        <div class="z-card-body">
            <div class="form-group flex-grid align-start first-line">
                <p class="form-cell-title w-fixed">备注</p>
                <textarea class="form-control" id="remark" name= "obj[remark]" rows="3" placeholder="备注" data-bind="value:settlement.remark"></textarea>
            </div>
            <div>
                应付合计：<span data-bind="moneyText:settlement.amount"></span>元
                <!--                <button class="btn btn-link" style="margin-left: 10px;">查看明细</button>-->
            </div>
        </div>
    </div>





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