<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<?php

include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/contractFile.php";

$contract_status=array(
        "0"=>"未上传",
        "1"=>"未提交",
        "3"=>"待审核",
        "6"=>"审核通过",

);

$contracts=array(
        array(
                "id"=>1,"contract_code"=>"Z20170913_1","type"=>1,
                "files"=>array(
                        array("file_id"=>1,"is_main"=>1,"contract_id"=>1,"code"=>"Z20170913_1"),
                        array("file_id"=>10,"is_main"=>0,"contract_id"=>1,"code"=>"Z20170913_1","status"=>6),
                ),
        ),
        array(
            "id"=>2,"contract_code"=>"Z20170913_2","type"=>1,
            "files"=>array(
                array("file_id"=>2,"is_main"=>0,"contract_id"=>2,"code"=>"Z20170913_2"),
            ),
        ),
        array(
            "id"=>3,"contract_code"=>"Z20170914_1","type"=>2,
            "files"=>array(
                array("file_id"=>3,"is_main"=>1,"contract_id"=>3,"code"=>"Z20170914_1"),
            ),
        ),
);
?>
<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">请在下面填写</h3>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <?php
            $attachType = $this->map["contract_attachment_type"][1];
            ?>
            <div class="box-body">
                <table class="table table-hover">
                    <thead>
                    <tr>

                        <th style="width:300px; text-align: left;">合同名称</th>
                        <th style="width:150px; text-align: left;">我方合同编号</th>
                        <th style="width:150px; text-align: left;">对方合同编号</th>
                        <th style="width:80px; text-align: center;">状态</th>
                        <th style="text-align: left;"></th>
                    </tr>
                    </thead>
                </table>
                <!-- ko foreach: contracts -->
                <!-- ko component: {
                              name: "contract-files",
                              params: {
                                            contract_code:contract_code,
                                            type:type,
                                            file_status:$parent.contractFileStatus,
                                            categories:$parent.categories,
                                           version_types:$parent.version_types,
                                         controller:"<?php echo $this->getId() ?>",
                                         fileConfig:<?php echo json_encode($attachType) ?>,
                                         files:files,
                                         baseId: id

                                          }
                          } -->
                <!-- /ko -->
                <!-- /ko -->

            </div>
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <span data-bind="text:itemsIsValid"></span>
                        <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:show">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

</section>
<script>

    var view;
    $(function () {
        view=new ViewModel();

        view.formatCategories(<?php echo json_encode($this->map["contract_buy_file_categories"])?>);
        view.formatVersionTypes(<?php echo json_encode($this->map["contract_buy_file_categories"])?>);
        view.formatContract(<?php echo json_encode($contracts)?>);
        view.contractFileStatus(<?php echo json_encode($contract_status)?>);

        ko.applyBindings(view);
    });

    function ViewModel()
    {
        var self=this;
        self.items=ko.observableArray(<?php echo json_encode($items)?>);
        self.itemsIsValid=ko.observable(false);
        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };

        self.show=function () {
            console.log(ko.toJS(self));
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }
        }

        self.categories=ko.observableArray();
        self.version_types=ko.observableArray();
        self.contracts=ko.observableArray();

        self.contractFileStatus=ko.observable();

        self.formatContract=function (data) {
            self.contracts(data);
        }

        self.formatCategories=function (data) {
            var items=inc.objectToArray(data);
            self.categories(items);
        }

        self.formatVersionTypes=function (data) {
            var items=inc.objectToArray(data);
            self.version_types(items);
        }



    }

</script>