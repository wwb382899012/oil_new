<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="/newUI/css/contract-file/index.css">
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<?php
if ($this->moduleType == ConstantMap::FINAL_CONTRACT_MODULE) {
    include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_contractFile.php";
} else {
    include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_signContractFile.php";
}
?>
<?php
$menus = [['text' => '合同管理', 'link' => '/contractUpload/'], ['text' => $this->pageTitle]];
$this->loadHeaderWithNewUI($menus, [], true);
?>
<section class="el-container is-vertical" id="contract-upload-edit">
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

                    <div class="children-gap--top">
                        <!-- ko foreach: contracts -->
                        <!-- ko if: $data.length > 0 -->
                        <div style="position: relative;">
                            <div>
                                <table class="table table-nowrap table-in-table table-fixed" autoscroll>
                                    <thead>
                                    <tr>
                                        <?php
                                        $styleWidth = '152px';
                                        $colspan = 7;
                                        if($this->moduleType == ConstantMap::FINAL_CONTRACT_MODULE) {
                                            $styleWidth = '152px';
                                        }
                                        ?>
                                        <th class="must-fill" style="width:<?php echo $styleWidth ?>;">合同名称</th>
                                        <th class="must-fill" style="width:115px;">标准</th>
                                        <th class="must-fill" style="width:210px;">我方合同编号</th>
                                        <th style="width:210px;">对方合同编号</th>
                                        <?php if ($this->moduleType > ConstantMap::FINAL_CONTRACT_MODULE) { ?>
                                            <th style="width:80px;">最终合同</th>
                                            <?php $colspan = $colspan + 1 ?>
                                        <?php } ?>
                                        <?php if ($this->moduleType == ConstantMap::PAPER_DOUBLE_SIGN_CONTRACT_MODULE) { ?>
                                            <th style="width:120px;">电子双签合同</th>
                                            <?php $colspan = $colspan + 1 ?>
                                        <?php } ?>
                                        <th style="width:73px;">状态</th>
                                        <?php if ($this->moduleType == ConstantMap::ELECTRON_DOUBLE_SIGN_CONTRACT_MODULE) { ?>
                                            <th class="must-fill" style="width:140px;">合同签订日期</th>
                                            <?php $colspan = $colspan + 1 ?>
                                        <?php } ?>
                                        <th style="width:130px;">附件</th>
                                        <th style="width: 192px;">操作</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td colspan="<?php echo $colspan ?>">
                                            <?php
                                            $attachType = $this->map["contract_file_attachment_type"][$this->moduleType];
                                            ?>


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

                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /ko -->
                        <!-- /ko -->
                    </div>
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
        page.positionInTable()
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