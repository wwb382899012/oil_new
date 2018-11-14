<!-- <script src="/js/jquery.bankInput.js"></script> -->
<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">仓库详情</h3>
        </div><!--end box box-header-->
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <div class="form-group">
                    <label for="tgitype" class="col-sm-2 control-label">仓库名称<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="name" name= "obj[name]" placeholder="仓库名称" data-bind="value:name, event: {blur:$root.checkName}" >
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">所属公司<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="code" name= "obj[company_name]" placeholder="所属公司" data-bind="value:company().company_name">
                    </div>
                    <div class="col-sm-1">
                        <button type="button" id="retrieveButton" class="btn btn-primary" data-bind="click:retrieve,html:retrieveBtnText">检索</button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">统一信用代码</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="credit_code" name= "obj[credit_code]" placeholder="信用代码" data-bind="value:company().credit_code">
                    </div>
                </div>
                <div class="form-group">
                    <label for="registration_code" class="col-sm-2 control-label">工商注册号</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="registration_code" name="obj[registration_code]" placeholder="工商注册号" data-bind="value:company().registration_code, disable: isParamsNonEdit">
                    </div>
                </div>
                <div class="form-group">
                    <label for="corporate" class="col-sm-2 control-label">法定代表人</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="corporate" name="obj[corporate]" placeholder="法定代表人" data-bind="value:company().corporate">
                    </div>
                </div>
                <div class="form-group">
                    <label for="start_date" class="col-sm-2 control-label">成立日期</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="start_date" name="obj[start_date]" placeholder="成立日期" data-bind="value:company().start_date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="registration_address" class="col-sm-2 control-label">注册地址</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="registration_address" name="obj[registration_address]" placeholder="注册地址" data-bind="value:company().address">
                    </div>
                </div>
                <div class="form-group">
                    <label for="registration_authority" class="col-sm-2 control-label">登记机关</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="registration_authority" name="obj[registration_authority]" placeholder="登记机关" data-bind="value:company().registration_authority">
                    </div>
                </div>
                <div class="form-group">
                    <label for="registered_capital" class="col-sm-2 control-label">注册资本（万元）</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="registered_capital" name="obj[registered_capital]" placeholder="注册资本（万元）" data-bind="value:company().registered_capital">
                    </div>
                </div>
                <div class="form-group">
                    <label for="business_scope" class="col-sm-2 control-label">经营范围</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="business_scope" name="obj[business_scope]" rows="3" placeholder="经营范围" data-bind="value:company().business_scope"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="ownership" class="col-sm-2 control-label">企业所有制</label>
                    <div class="col-sm-10">
                        <select class="form-control" title="请选择企业所有制" id="ownership" name="obj[ownership]" data-bind="options:ownerships,optionsText:'name',optionsCaption: '请选择企业所有制',value: company().ownership, optionsValue:'id',valueAllowUnset: true">
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="runs_state" class="col-sm-2 control-label">经营状态</label>
                    <div class="col-sm-10">
                        <select class="form-control" title="请选择经营状态" id="runs_state" name="obj[runs_state]" data-bind="optionsCaption: '请选择经营状态',value: company().runs_state,valueAllowUnset: true">
                            <option value=''>请选择经营状态</option>
                            <?php foreach ($this->map["runs_state"] as $k => $v) {
                                echo "<option value='" . $k . "'>" . $v . "</option>";
                            } ?>
                        </select>
                    </div>
                </div>
                <hr>
                <div class="form-group">
                    <label for="address" class="col-sm-2 control-label">仓库地址</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="address" name="obj[address]" placeholder="仓库地址" data-bind="value:address">
                    </div>
                </div>
                <div class="form-group">
                    <label for="capacity" class="col-sm-2 control-label">仓库面积</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="capacity" name="obj[capacity]" placeholder="仓库面积" data-bind="value:capacity">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">仓库类型<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-10">
                        <select class="form-control" title="仓库类型"  id="ownership" name="obj[type]" data-bind="value: type,valueAllowUnset: true">
                            <option value='' disabled="disabled">请选择仓库类型</option>
                            <?php foreach ($this->map["storehouse_type"] as $k => $v) {
                                echo "<option value='" . $k . "'>" . $v . "</option>";
                            } ?>
                        </select>
                    </div>
                </div>
            </div><!--end box-border-->



            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:save">保存</button>
                        <button type="button"  class="btn btn-default" data-bind="click:back">返回</button>
                        <input type='hidden' name='obj[store_id]' data-bind="value:store_id" />
                    </div>
                </div>
            </div>
        </form>
    </div><!--end box box-primary-->


    <!-- partner retrieve modal -->
    <div class="modal fade draggable-modal" id="companyModal" tabindex="-1" role="dialog" aria-labelledby="modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="partnerRetrieve">自动检索企业信息</h4>
                </div>
                <div class="modal-body">
                    <div class="box box-primary">
                        <div class="box-body">
                            <form class="search-form">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="input-group-addon">企业名称</div>
                                                <input type="text" class="form-control input-sm" name="name" id="search"
                                                       placeholder="企业名称" value=""
                                                       data-bind="textInput:companyKeyWord"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <table id="companies" class="table table-condensed table-hover table-bordered table-layout">
                                <thead>
                                <tr>
                                    <th style='text-align:center;'>企业名称</th>
                                    <th style='width: 10%; text-align:center'>法人</th>
                                    <th style='width: 15%; text-align:center;'>成立日期</th>
                                    <th style='width: 10%; text-align:center;'>经营状态</th>
                                    <th style='width: 10%; text-align:center;'>操作</th>
                                </tr>
                                </thead>

                                <tbody id="partnerBody" data-bind="foreach: companies">
                                <tr class="item">
                                    <td style='text-align:left;' data-bind="text:name"></td>
                                    <td style='text-align:center' data-bind="text:corporate"></td>
                                    <td style='text-align:center;' data-bind="text:start_date"></td>
                                    <td style='text-align:center;' data-bind="text:runs_state"></td>
                                    <td style='text-align:center;'><a data-bind="click:function(){$parent.select($index());}">选择</a></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>
</section><!--end content-->

<script>
    var view;
    $(function () {
        view=new ViewModel(<?php echo json_encode($storehouse) ?>);
        view.ownerships(<?php echo json_encode(Ownership::getOwnerships()) ?>);
        view.storehouseType(<?php echo json_encode($this->map['storehouse_type']) ?>);

        ko.applyBindings(view);
        $("#start_date").datetimepicker({format:'yyyy-mm-dd',minView:'month'});
       // $("#bank_account").bankInput({min:1,max:50,deimiter:' '});
    });

    function ViewModel(option){
        var dt=new Date();
        var year=dt.getFullYear();
        var month=dt.getMonth()+1;
        var day=dt.getDate();
        var d=year+"-"+(month<10?"0"+month:month)+"-"+(day<10?"0"+day:day);
        var defaults={
            name:"",
            // code:"", 仓库并不需要code
            corporate:"",
            ownership:2,
            start_date:d,
            status:1,
            remark:"",
            type:"",
            address:"",
            capacity:"",
            store_id:"",
        };
        var o=$.extend(defaults,option);
        var self=this;
        self.store_id=ko.observable(o.store_id);
        self.name=ko.observable(o.name).extend({required:true});
        self.errors=ko.validation.group(self);
        self.actionState = ko.observable(0);

        // 检索用的名称
        self.retrieveBtnText = ko.observable("检索");
        self.companyKeyWord = ko.observable();
        self.isCanEditName = ko.observable(1);
        self.company = ko.observable(new CompanyModel(option));
        self.ownerships = ko.observableArray();
        self.companies = ko.observableArray();

        // 仓库字段
        self.storehouseType = ko.observableArray();
        self.type = ko.observable(o.type).extend({required:true});
        self.store_address = ko.observable(o.store_address);
        self.capacity = ko.observable(o.capacity);
        self.address=ko.observable(o.address);

        self.isParamsNonEdit = ko.computed(function () {
            return self.isCanEditName()!=1;
        }, self);

        self.isValid=function () {
            return self.errors().length===0;
        }

        // 企业信息检索
        self.retrieve = function () {
            self.company().company_name.isModified(true);
            if (!self.company().company_name.isValid())
                return;
            if (self.actionState() == 1)
                return;

            self.actionState(1);
            self.retrieveBtnText("检索" + inc.loadingIco);
            $.ajax({
                type: "GET",
                url: "/partnerApply/getCompanies",
                data: {name: self.company().company_name()},
                dataType: "json",
                success: function (json) {
                    self.actionState(0);
                    self.retrieveBtnText("检索");
                    if (json.state == 0) {
                        var companyValues = json.data.partnerInfo;
                        var ownerships = json.data.ownerships;
                        self.ownerships(ownerships);
                        if (companyValues.length > 1) {
                            self.companies(companyValues);
                            $("#companyModal").modal({
                                backdrop: true,
                                keyboard: false,
                                show: true
                            });
                        }
                        else {
                            if (companyValues.length == 1) {
                                var companyValue = companyValues[0];
                                self.setCompany(companyValues[0]);
                            } else {
                                layer.alert("企业信息不存在或查询接口繁忙", {icon: 5});
                            }
                        }
                    }
                    else {
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
                    self.actionState(0);
                    self.retrieveBtnText("检索");
                    layer.alert("检索失败：" + data.responseText, {icon: 5});
                }
            });
        }

        //公司选择
        self.select = function (index) {
            if (index >= self.companies().length || index < 0)
                layer.alert("选择有误，请重新选择", {icon: 5});
            $("#companyModal").modal("hide");
            var selectedCompany = self.companies()[index];
            self.setCompany(selectedCompany);
        }

        self.setCompany = function (company) {
            if (company.hasOwnProperty("name") && company.name != null) {
                company.company_name = company.name;
            }
            // ko.setObservablesValue(self.company(), company);
            self.company(new CompanyModel(company));
        }

        self.save=function () {
            if(!self.isValid()){
                self.errors.showAllMessages();
                return;
            }
            if(!self.company().isValid()) {
                self.company().errors.showAllMessages();
                return;
            }
            var formData=$("#mainForm").serialize();
            $.ajax({
                type:"POST",
                url:"/storehouse/save",
                data:formData,
                dataType:"json",
                success:function (json) {
                    if(json.state==0){
                        layer.msg("保存成功", {icon: 6, time:1000},function() {
                            location.href="/storehouse/detail?store_id="+json.data;
                        });
                    }else{
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error:function (data) {
                    layer.alert("保存失败！"+data.responseText, {icon: 5});
                }
            });
        }

        self.checkName=function(obj, event) {
            // obj 是this, event 是事件
            if(this.name.isValid()) {
                $.ajax({
                    type: "POST",
                    data: {
                        store_id:this.store_id(),
                        name:this.name()
                    },
                    url: "/storehouse/ajaxCheckName",
                    dataType: "json",
                    success: function (json) {
                        if (json.state == 0) {
                            $(event.target).parents('div.form-group').removeClass('has-error').addClass('has-success');
                        }
                        else {
                            layer.alert(json.data, {icon: 5});
                            $(event.target).parents('div.form-group').removeClass('has-success').addClass('has-error');
                        }
                    },
                    error: function (data) {
                        layer.alert("检查重名失败：" + data.responseText, {icon: 5});
                    }
                });
            }
        }
        
        self.back=function () {
            window.location.href="/storehouse/";
        }

        // 对话框企业搜索
        self.companySearch = function () {
            var trs = $("#companies > tbody > tr.item");
            trs.each(function (index, row) {
                var found = false;
                var allCells = $(this).children('td').each(function () {
                    var regExp = new RegExp(self.companyKeyWord(), 'i');
                    if (regExp.test($(this).text())) {
                        found = true;
                        return false;
                    }
                });
                if (found) $(this).show(); else $(this).hide();
            });
        }

        self.companyKeyWord.subscribe(function (v) {
            self.companySearch();
        });
    }

    function CompanyModel(option) {
        var defaults = {
            id: 0,
            company_name: "",//企业名称
            credit_code: "",//统一社会信用代码
            registration_code: "",//工商注册号
            address:'',
            corporate: "",//法人代表
            start_date: "",//成立日期
            address: "",//注册地址
            registration_authority: "",//登记机关
            registered_capital: "",//注册资本
            business_scope: "",//经营范围
            ownership: 2,//企业所有制
            runs_state: "1",//经营状态
        };
        var o = $.extend(defaults, option);
        o.registration_address = (o.registration_address)?o.registration_address:o.address;
        var self = this;
        self.id = ko.observable(o.id);
        self.company_name = ko.observable(o.company_name).extend({required: true});
        self.credit_code = ko.observable(o.credit_code)
        self.registration_code = ko.observable(o.registration_code)
        self.corporate = ko.observable(o.corporate);
        self.start_date = ko.observable(o.start_date).extend({date: true});
        self.address = ko.observable(o.registration_address);
        self.registration_authority = ko.observable(o.registration_authority);
        self.registered_capital = ko.observable(o.registered_capital);
        self.business_scope = ko.observable(o.business_scope);
        self.ownership = ko.observable(o.ownership);
        self.runs_state = ko.observable(o.runs_state);
        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        }
    }
</script>