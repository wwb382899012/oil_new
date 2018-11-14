<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<?php
if ($this->moduleType == ConstantMap::FINAL_CONTRACT_MODULE) {
    include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/contractFile.php";
} else {
    include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/signContractFile.php";
}
?>
<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">请在下面填写</h3>
            <div class="pull-right box-tools">
                <?php if (!$this->isExternal) { ?>
                    <button type="button" class="btn btn-default" data-bind="click:back">返回</button>
                <?php } ?>
            </div>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <?php
            $attachType = $this->map["contract_file_attachment_type"][$this->moduleType];
            ?>
            <div class="box-body">
                <div class="form-group">
                    <label class="col-sm-1 control-label">项目编号</label>
                    <div class="col-sm-2 titleholder">
                        <p class="form-control-static">
                            <a href="/project/detail/?id=<?php echo $project["project_id"] ?>&t=1" target="_blank"><?php echo $project["project_code"] ?></a>
                        </p>
                    </div>
                    <label class="col-sm-2 control-label">业务类型</label>
                    <div class="col-sm-2 titleholder" >
                        <p class="form-control-static"><?php echo $project['project_type_desc'] ?></p>
                    </div>
                    <label class="col-sm-2 control-label">交易主体</label>
                    <div class="col-sm-3 titleholder">
                        <p class="form-control-static">
                            <a href="/corporation/detail/?id=<?php echo $project["corporation_id"] ?>&t=1" target="_blank"><?php echo Corporation::getCorporationName($project["corporation_id"]) ?></a>
                        </p>
                    </div>
                </div>
                <!-- ko foreach: contracts -->
                <!-- ko if: $data.length > 0 -->
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <?php
                        $styleWidth = '200px';
                        if($this->moduleType == ConstantMap::FINAL_CONTRACT_MODULE) {
                            $styleWidth = '320px';
                        }
                        ?>
                        <th style="width:<?php echo $styleWidth ?>; text-align: left;">合同名称</th>
                        <th style="width:200px; text-align: left;">我方合同编号</th>
                        <th style="width:150px; text-align: left;">对方合同编号</th>
                        <?php if ($this->moduleType > ConstantMap::FINAL_CONTRACT_MODULE) { ?>
                            <th style="width:80px; text-align: left;">最终合同</th>
                        <?php } ?>
                        <?php if ($this->moduleType == ConstantMap::PAPER_DOUBLE_SIGN_CONTRACT_MODULE) { ?>
                            <th style="width:120px; text-align: left;">电子双签合同</th>
                        <?php } ?>
                        <th style="width:120px; text-align: center;">状态</th>
                        <?php if ($this->moduleType == ConstantMap::ELECTRON_DOUBLE_SIGN_CONTRACT_MODULE) { ?>
                            <th style="width:200px; text-align: left;">合同签订日期</th>
                        <?php } ?>
                        <th style="text-align: left;">操作</th>
                    </tr>
                    </thead>
                </table>
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
                <hr>
                <!-- /ko -->
                <!-- /ko -->
            </div>
            <div class="box-footer">
                <button type="button" id="saveButton" class="btn btn-default" data-bind="click:back">返回</button>
            </div>
        </form>
    </div>
</section>
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