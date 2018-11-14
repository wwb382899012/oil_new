<template id='component-template-contrace-list'>
    <div class="modal fade draggable-modal" id="contractListModal" tabindex="-1" role="dialog" aria-labelledby="modal">
        <div class="modal-dialog modal-lg" role="document" style="width: 1150px;">
            <div class="modal-content">
                <div class="modal-header--flex">
                    <h4 class="modal-title">请选择销售合同</h4>
                    <a type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></a>
                </div>
                <div class="modal-body">
                    <form class="search-form">
                        <div class="condition-fields" style="border:none;">
                            <div class="flex-grid form-group">
                                <label class="col field col-count-3">
                                    <p class="form-cell-title">销售合同编号:</p>
                                    <input type="text" class="el-input__inner" placeholder="销售合同编号" data-bind="textInput:contractCodeKeyWord"/>
                                </label>

                                <label class="col field col-count-3">
                                    <p class="form-cell-title">项目编号:</p>
                                    <input type="text" class="el-input__inner" placeholder="项目编号" data-bind="textInput:projectCodeKeyWord"/>
                                </label>
                                <label class="col field col-count-3">
                                    <p class="form-cell-title">下游合作方:</p>
                                    <input type="text" class="el-input__inner" placeholder="下游合作方" data-bind="textInput:partnerNameKeyWord"/>
                                </label>
                            </div>
                            <div class="flex-grid form-group">
                                <label class="col field col-count-3">
                                    <p class="form-cell-title">交易主体:</p>
                                    <input type="text" class="el-input__inner" placeholder="交易主体" data-bind="textInput:corporationNameKeyWord"/>
                                </label>

                                <label class="col field col-count-3">
                                    <p class="form-cell-title">项目类型:</p>
                                    <select data-bind="selectpicker:projectTypeKeyWord" class="selectpicker form-control show-menu-arrow">
                                        <option value="">全部</option>
                                        <?php foreach(Map::$v['project_type'] as $item):?>
                                            <option value="<?php echo $item;?>"><?php echo $item;?></option>
                                        <?php endforeach;?>
                                    </select>
                                </label>
                                <label class="col field col-count-3">
                                    <p class="form-cell-title">外部合同编号:</p>
                                    <input type="text" class="el-input__inner" placeholder="外部合同编号" data-bind="textInput:codeOutKeyWord"/>
                                </label>
                            </div>
                        </div>
                    </form>
                    <div style="max-height: 360px; overflow-y: auto;">
                        <table id="contractListTable" class="table table-fixed" data-bind="visible:saleContractList().length > 0">
                            <thead>
                            <tr>
                                <th style='width: 160px'>销售合同编号</th>
                                <th style='width: 160px;'>外部合同编号</th>
                                <th style='width: 160px; '>项目编号</th>
                                <th style='width: 220px; '>下游合作方</th>
                                <th style='width: 100px; '>项目类型</th>
                                <th style='width: 220px;'>交易主体</th>
                                <th style='width: 80px; '>操作</th>
                            </tr>
                            </thead>
                            <tbody id="buyContractBody" data-bind="foreach: saleContractList">
                            <tr class="item">
                                <td style='' data-bind="html:contract_code_link,attr:{title:contract_code}"/>
                                <td style='' data-bind="text:code_out,attr:{title:code_out}"/>
                                <td style='' data-bind="html:project_code_link,attr:{title:project_code}"/>
                                <td style='' data-bind="html:partner_name_link,attr:{title:partner_name}"/>
                                <td style=''><span data-bind="text:project_type_name"/>
                                <td style=''><span data-bind="html:corporation_name_link"/>
                                <td style='' data-bind="click:$parent.addSaleContractFunc" >
                                    <a role="button" href="javascript: void 0 " class="o-btn o-btn-primary action">选择</a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer flex-center">
                    <a href="javascript: void 0" role="button" class="o-btn o-btn-action w-base" data-dismiss="modal">关闭</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    ko.components.register('contrace-list', {
        template: {element: 'component-template-contrace-list'},
        viewModel: contractListComponent
    });

    function contractListComponent(parentParams) {
        var self = this;
        self.addSaleContractFunc = parentParams.addSaleContractFunc;
        //销售合同列表
        self.saleContractList = parentParams.saleContractList;
        self.goodsItems = parentParams.goodsItems;
        //
        self.corporation_id = parentParams.corporation_id;
        self.partner_id = parentParams.partner_id;
        self.stock_in_id = parentParams.stock_in_id;
        self.contract_code = parentParams.contract_code;
        self.partner_name = parentParams.partner_name;
        self.corporation_name = parentParams.corporation_name;

        //查询
        self.contractCodeKeyWord = ko.observable();
        self.projectCodeKeyWord = ko.observable();
        self.partnerNameKeyWord = ko.observable();
        self.corporationNameKeyWord = ko.observable();
        self.codeOutKeyWord = ko.observable();
        self.projectTypeKeyWord = ko.observable();

        //清理查询条件
        self.saleContractList.subscribe(function () {
            self.contractCodeKeyWord("");
            self.projectCodeKeyWord("");
            self.partnerNameKeyWord("");
            self.corporationNameKeyWord("");
            self.codeOutKeyWord("");
            self.projectTypeKeyWord("");
        });

        self.contractCodeKeyWord.subscribe(function () {
            self.formSearch("contractCode");
        });
        self.projectCodeKeyWord.subscribe(function () {
            self.formSearch("projectCode");
        });
        self.partnerNameKeyWord.subscribe(function () {
            self.formSearch("partnerName");
        });
        self.corporationNameKeyWord.subscribe(function () {
            self.formSearch("corporationName");
        });
        self.codeOutKeyWord.subscribe(function () {
            self.formSearch("codeOut");
        });
        self.projectTypeKeyWord.subscribe(function () {
            self.formSearch("projectType");
        });

        self.formSearch = function (name) {
            var val = "";
            switch (name){
                case "contractCode":
                    val = self.contractCodeKeyWord();
                    break;
                case "projectCode":
                    val = self.projectCodeKeyWord();
                    break;
                case "projectType":
                    val = self.projectTypeKeyWord();
                    break;
                case "partnerName":
                    val = self.partnerNameKeyWord();
                    break;
                case "codeOut":
                    val = self.codeOutKeyWord();
                    break;
                case "corporationName":
                    val = self.corporationNameKeyWord();
                    break;
                default:
            }

            var trs = $("#contractListTable > tbody > tr.item");
            trs.each(function (index, row) {
                var found = false;
                $(this).children('td').each(function () {
                    var regExpGoodsName = new RegExp(val, 'i');
                    if (regExpGoodsName.test($(this).text())) {
                        found = true;
                        return false;
                    }
                });
                if (found)
                    $(this).show();
                else
                    $(this).hide();
            });
        }
    }
</script>