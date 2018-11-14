<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="/newUI/css/contract-file/index.css">
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<?php
if ($this->moduleType == ConstantMap::FINAL_CONTRACT_MODULE) {
    include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_contractFile.php";
} else {
    include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/signContractFile.php";
}
?>
<?php
$menus = [['text' => '合同管理', 'link' => '/contractUpload/'], ['text' => $this->pageTitle]];
$this->loadHeaderWithNewUI($menus, [], true);
?>
<section class="el-container is-vertical">
    <div class="card-wrapper">
        <div class="z-card">
            <h3 class="z-card-header">
                合同上传
            </h3>
            <div class="z-card-body">
                <div class="flex-grid form-group text-table-gap">
                    <label class="col col-count-3 field flex-grid">
                        <span class="line-h--text cell-title">项目编号:</span>
                        <span class="form-control-static line-h--text">
                            <a class="text-link" href="/project/detail/?id=<?php echo $project["project_id"] ?>&t=1"
                               target="_blank"><?php echo $project["project_code"] ?></a>
                        </span>
                    </label>
                    <label class="col col-count-3 field flex-grid">
                        <span class="line-h--text cell-title">业务类型:</span>
                        <span class="form-control-static line-h--text">
                            <?php echo $project['project_type_desc'] ?>
                        </span>
                    </label>
                    <label class="col col-count-3 field flex-grid">
                        <span class="line-h--text cell-title">交易主体:</span>
                        <span class="form-control-static line-h--text">
                            <a class="text-link"
                               href="/corporation/detail/?id=<?php echo $project["corporation_id"] ?>&t=1"
                               target="_blank"><?php echo Corporation::getCorporationName($project["corporation_id"]) ?></a>
                        </span>
                    </label>
                </div>
                <form role="form" id="mainForm">
                    <div class="flex-grid form-group align-between">
                        <label class="field flex-grid emphasis">
                            <span class="line-h--text cell-title colon">采购合同</span>
                            <span class="form-control-static line-h--text">
                                    <a href="javascript: void 0" class="text-link" title="合同详情" target="_blank"><span>YT234324JQ180619D01</span></a>
                                </span>
                        </label>
                        <label class="field flex-grid emphasis">
                            <span class="form-control-static line-h--text">
                                <a href="#" class="text-link" target="_blank"><span>东营市福润达商贸有限责任公司</span></a>
                            </span>
                        </label>
                        <label class="field flex-grid emphasis">
                            <span class="form-control-static line-h--text">$80,000.00</span>
                        </label>
                        <label class="field flex-grid emphasis">
                            <span class="form-control-static line-h--text">成品油</span>
                        </label>
                    </div>
                    <table class="table table-nowrap">
                        <thead>
                        <tr>
                            <th class="must-fill">合同名称</th>
                            <th class="must-fill">标准</th>
                            <th class="must-fill">我方合同编号</th>
                            <th>对方合同编号</th>
                            <?php if ($this->moduleType > ConstantMap::FINAL_CONTRACT_MODULE) { ?>
                                <th>最终合同</th>
                            <?php } ?>
                            <?php if ($this->moduleType == ConstantMap::PAPER_DOUBLE_SIGN_CONTRACT_MODULE) { ?>
                                <th>电子双签合同</th>
                            <?php } ?>
                            <th>状态</th>
                            <?php if ($this->moduleType == ConstantMap::ELECTRON_DOUBLE_SIGN_CONTRACT_MODULE) { ?>
                                <th>合同签订日期</th>
                            <?php } ?>
                            <th>附件</th>
                            <th>操作</th>

                        </tr>
                        </thead>

                        <?php
                        $attachType = $this->map["contract_file_attachment_type"][$this->moduleType];
                        ?>
                        <!-- ko foreach: contracts -->
                        <!-- ko if: $data.length > 0 -->
                        <!-- ko foreach: $data -->
                        <!-- ko component: {
                              name: "contract-files",
                              params: {
                                            project_id: project_id,
                                            contract_id: contract_id,
                                            contract_code:contract_code,
                                            partner_id:partner_id,
                                            partner_name:partner_name,
                                            amount:amount,
                                            goods:goods,
                                            categories: <?php echo json_encode($this->map["contract_file_categories"]) ?>,
                                            version_types:<?php echo json_encode($this->map["contract_standard_type"]) ?>,
                                            controller:"<?php echo $this->getId() ?>",
                                            fileConfig:<?php echo json_encode($attachType) ?>,
                                            file_status:<?php echo json_encode($this->moduleType == ConstantMap::FINAL_CONTRACT_MODULE ? $this->map['contract_upload_status'] : $this->map['sign_contract_status']) ?>,
                                            type:type,
                                            files:files,
                             }
                          } -->
                        <!-- /ko -->
                        <!-- /ko -->
                        <!-- /ko -->
                        <!-- /ko -->


                    </table>
                </form>
            </div>
        </div>
    </div>
</section>
<!--<table class="table table-hover">-->
<!--    <thead>-->
<!--    <tr>-->
<!---->
<!--    </tr>-->
<!--    </thead>-->
<!--</table>-->
<script>

    var view;
    $(function () {
        view = new ViewModel();
        view.formatContracts(<?php echo json_encode($contracts) ?>);
        ko.applyBindings(view);
    });

    function ViewModel() {
        var self = this;
        self.contracts = ko.observableArray();
        self.formatContracts = function (data) {
            var items = inc.objectToArray(data);
            self.contracts(items);
        };
        self.back = function () {
            history.back();
        }
    }
</script>