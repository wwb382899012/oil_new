<?php
$menus = [['text' => '合同管理', 'link' => '/check4/'], ['text' => $this->pageTitle]];
$this->loadHeaderWithNewUI($menus, [], true);
?>
<section class="el-container is-vertical">
    <div class="card-wrapper">
        <!-- <div class="box-header">
            <h3 class="box-title">请在下面操作</h3>
        </div> -->
        <div class="z-card">
            <div class="z-card-body">
                <div class="flex-grid form-group text-table-gap">
                    <label class="col col-count-3 field flex-grid">
                        <span class="line-h--text cell-title">项目编号：</span>
                        <span class="form-control-static line-h--text">
                            <a title="<?php echo $data["project_code"] ?>" class="text-link" href="/project/detail/?id=<?php echo $data["project_id"] ?>&t=1"
                               target="_blank"><?php echo $data["project_code"] ?></a>
                        </span>
                    </label>
                    <label class="col col-count-3 field flex-grid">
                        <span class="line-h--text cell-title">项目类型：</span>
                        <span class="form-control-static line-h--text">
                            <?php echo $data['project_type_desc'] ?>
                        </span>
                    </label>
                    <label class="col col-count-3 field flex-grid">
                        <span class="line-h--text cell-title">交易主体：</span>
                        <span class="form-control-static line-h--text">
                            <a class="text-link"
                               href="/corporation/detail/?id=<?php echo $data["corporation_id"] ?>&t=1"
                               target="_blank"><?php echo Corporation::getCorporationName($data["corporation_id"]) ?></a>
                        </span>
                    </label>
                </div>
                <form role="form" id="mainForm" style="overflow: auto;">
                    <div class="children-gap--top">
                        <?php
                        foreach ($infoArr as $key => $value) {
                            ?>
                            <div style="position: relative;">
                                <table class="table table-in-table table-fixed">
                                    <thead>
                                    <tr>
                                        <th style="width:132px">合同名称</th>
                                        <th style="width:80px;">标准</th>
                                        <th style="width:210px">我方合同编号</th>
                                        <th style="width:210px">对方合同编号</th>
                                        <th style="width:90px">商务上传</th>
                                        <th style="width:190px">法务审核</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td colspan="6">
                                            <?php
                                            echo $this->renderPartial("/check4/new_filesDetail", array('contract' => $value,'count'=>$count,'key'=>$key));
                                            ?>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                    </div>
                </form>

            </div>
        </div>
    </div>
</section>

<div class="modal fade draggable-modal" id="contractModal" tabindex="-1" role="dialog" aria-labelledby="modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header"><h4 class="modal-title">请选择参考合同</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" role="form" id="contractModalForm">
                    <div class="form-group">
                        <label for="remark" class="col-sm-2 control-label">待审合同</label>
                        <div class="col-sm-6">
                            <p class="form-control-static">
                                <span data-bind="text: contract_name"></span>
                                <input type="hidden" name="obj[origin_file_id]" data-bind="value:origin_file_id"/>
                                <input type="hidden" name="obj[type]" data-bind="value:type"/>
                                <input type="hidden" name="obj[buy_sell_type]" data-bind="value:buy_sell_type"/>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="category" class="col-sm-2 control-label">参考合同</label>
                        <div class="col-sm-6">
                            <select class="form-control input-sm" title="请选择参考合同" name="c[file_id]" id="file_id"
                                    data-bind="
                                    optionsText: 'contract_name',
                                    optionsValue: 'file_id',
                                    options:attachments,
                                    selectpicker:{value:file_id}">
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a href="javascript: void 0" role="button" id="confirmSubmitBtnText" class="o-btn o-btn-primary" data-dismiss="modal" data-bind="click:confirm,html:confirmSubmitBtnText"></a>
                <a href="javascript: void 0" role="button" class="o-btn o-btn-action w-base" data-dismiss="modal">取消</a>
            </div>
        </div>
    </div>
</div>

<script>
    var view;
    $(function () {
        view = new ViewModel();
        ko.applyBindings(view);
        page.positionInTable();
    })

    function ViewModel() {
        var self = this;
        self.origin_file_id = ko.observable(0);
        self.contract_name = ko.observable("");
        self.type = ko.observable(0);
        self.buy_sell_type = ko.observable(0);
        self.files = <?php $attachments = ContractService::getAllContractFile($data['project_id']); echo json_encode($attachments); ?>;
        self.attachments = ko.computed(function () {
            if (self.files.length > 0) {
                for (var i in self.files) {
                    if (self.files[i]['file_id'] == self.origin_file_id()) {
                        self.files.splice(i, 1);
                    }
                }
                return self.files;
            }
        }, self);

        self.file_id = ko.observable(0);
        self.init_file_id = function () {
            if (self.attachments().length > 0) {
                for (var i in self.attachments()) {
                    if (self.buy_sell_type() > 0 && self.attachments()[i]['type'] != self.buy_sell_type() && self.attachments()[i]['is_main'] == 1) {
                        self.file_id(self.attachments()[i]['file_id']);
                        break;
                    } else {
                        self.file_id(0);
                    }
                }
            }
        };


        self.confirmSubmitBtnText = ko.observable('确认');
        self.actionState = 0;

        self.compareModal = function (file_id, buy_sell_type, type, contract_name) {
            self.origin_file_id(file_id);
            self.buy_sell_type(buy_sell_type);
            self.type(type);
            self.contract_name(contract_name);
            self.init_file_id();
            $("#file_id").selectpicker('refresh');
            $("#contractModal").modal({
                backdrop: true,
                keyboard: false,
                show: true
            });
            $("#contractModal").on('hidden.bs.modal', function () {
                location.reload();
            });
        }


        self.confirm = function () {
            location.href = "/<?php echo $this->getId() ?>/check?id=" + self.origin_file_id() + "&type=" + self.type() + "&compare_id=" + self.file_id();
        }

        self.back = function () {
            location.href = '/<?php echo $this->getId() ?>';
        }
    }
</script>

