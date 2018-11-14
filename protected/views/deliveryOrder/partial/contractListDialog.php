<template id='component-template-contrace-list'>
    <div class="modal fade draggable-modal" id="contractListModal" tabindex="-1" role="dialog" aria-labelledby="modal">
        <div class="modal-dialog modal-lg" role="document" style="width: 1150px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                    <h4 class="modal-title">请选择销售合同</h4>
                </div>
                <div class="modal-body" style="padding-bottom: 0px;">
                    <div class="box box-primary">
                        <div class="box-body">
                            <form class="search-form">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-addon">销售合同编号</div>
                                                <input type="text" class="form-control input-sm" placeholder="销售合同编号" data-bind="textInput:contractCodeKeyWord"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-addon">项目编号</div>
                                                <input type="text" class="form-control input-sm" placeholder="项目编号" data-bind="textInput:projectCodeKeyWord"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-addon">下游合作方&emsp;</div>
                                                <input type="text" class="form-control input-sm" placeholder="下游合作方" data-bind="textInput:partnerNameKeyWord"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-addon">交易主体&emsp;&emsp;</div>
                                                <input type="text" class="form-control input-sm" placeholder="交易主体" data-bind="textInput:corporationNameKeyWord"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-addon">项目类型</div>
                                                <select data-bind="value:projectTypeKeyWord" class="form-control input-sm">
                                                    <option value="">全部</option>
                                                    <?php foreach(Map::$v['project_type'] as $item):?>
                                                        <option value="<?php echo $item;?>"><?php echo $item;?></option>
                                                    <?php endforeach;?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="input-group">
                                                <div class="input-group-addon">外部合同编号</div>
                                                <input type="text" class="form-control input-sm" placeholder="外部合同编号" data-bind="textInput:codeOutKeyWord"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        </form>
                        <div style="height: 360px;overflow: scroll;">
                            <table id="contractListTable" class="table table-condensed table-hover table-bordered table-layout" data-bind="visible:saleContractList().length > 0">
                                <thead>
                                <tr>
                                    <th style='width: 40px; text-align:center;'>操作</th>
                                    <th style='text-align:center;'>销售合同编号</th>
                                    <th style='width: 100px; text-align:center'>外部合同编号</th>
                                    <th style='width: 155px; text-align:center;'>项目编号</th>
                                    <th style='width: 220px; text-align:center;'>下游合作方</th>
                                    <th style='width: 60px; text-align:center;'>项目类型</th>
                                    <th style='text-align:center;'>交易主体</th>
                                </tr>
                                </thead>
                                <tbody id="buyContractBody" data-bind="foreach: saleContractList" style="height:300px;overflow-y:scroll;">
                                <tr class="item">
                                    <td style='text-align:center;' data-bind="click:$parent.addSaleContractFunc" ><span class="a">选择</span></td>
                                    <td style='text-align:center;' data-bind="html:contract_code_link,attr:{title:contract_code}"/>
                                    <td style='text-align:center;' data-bind="text:code_out,attr:{title:code_out}"/>
                                    <td style='text-align:center;' data-bind="html:project_code_link,attr:{title:project_code}"/>
                                    <td style='text-align:center;' data-bind="html:partner_name_link,attr:{title:partner_name}"/>
                                    <td style='text-align:center;'><span data-bind="text:project_type_name"/>
                                    <td style='text-align:center;'><span data-bind="html:corporation_name_link"/>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="padding-top: 0px;">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
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