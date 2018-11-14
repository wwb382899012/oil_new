<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<script src="/js/jquery.bankInput.js"></script>
<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">请在下面填写</h3>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">企业名称（全称）<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="name" name="data[name]" placeholder="企业名称" data-bind="value:company().name, disable: isParamsNonEdit">
                    </div>
                    <div class="col-sm-4">
                        <button type="button" id="retrieveButton" class="btn btn-primary" data-bind="click:retrieve,html:retrieveBtnText">检索</button>
                        <?php
                        $keyNo = PartnerService::getKeyNo($data['name']);
                        if (!empty($keyNo)) {
                            echo '&emsp;&emsp;<a data-bind="visible:nameIsChanged()==false" href="http://www.qichacha.com/firm_' . $keyNo . '.shtml" target="_blank" class="btn btn-warning" id="qiChachaLink">点击跳转到企查查详情页</a>';
                        } 
                        ?>
                        <span data-bind="html:gotoQiChaChaText"></span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="credit_code" class="col-sm-2 control-label">统一社会信用代码</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="credit_code" name="data[credit_code]" placeholder="统一社会信用代码" data-bind="value:company().credit_code, disable: isParamsNonEdit">
                    </div>
                </div>
                <div class="form-group">
                    <label for="registration_code" class="col-sm-2 control-label">工商注册号</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="registration_code" name="data[registration_code]" placeholder="工商注册号" data-bind="value:company().registration_code, disable: isParamsNonEdit">
                    </div>
                </div>
                <div class="form-group">
                    <label for="corporate" class="col-sm-2 control-label">法定代表人</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="corporate" name="data[corporate]" placeholder="法定代表人" data-bind="value:company().corporate">
                    </div>
                </div>
                <div class="form-group">
                    <label for="start_date" class="col-sm-2 control-label">成立日期</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="start_date" name="data[start_date]" placeholder="成立日期" data-bind="value:company().start_date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="address" class="col-sm-2 control-label">注册地址</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="address" name="data[address]" placeholder="注册地址" data-bind="value:company().address">
                    </div>
                </div>
                <div class="form-group">
                    <label for="registration_authority" class="col-sm-2 control-label">登记机关</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="registration_authority" name="data[registration_authority]" placeholder="登记机关" data-bind="value:company().registration_authority">
                    </div>
                </div>
                <div class="form-group">
                    <label for="registered_capital" class="col-sm-2 control-label">注册资本（万元）</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="registered_capital" name="data[registered_capital]" placeholder="注册资本（万元）" data-bind="value:company().registered_capital">
                    </div>
                    <label for="paid_up_capital" class="col-sm-2 control-label">实收（万元）</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="paid_up_capital" name="data[paid_up_capital]" placeholder="实收资本" data-bind="value:company().paid_up_capital">
                    </div>
                </div>
                <div class="form-group">
                    <label for="business_scope" class="col-sm-2 control-label">经营范围</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="business_scope" name="data[business_scope]" rows="3" placeholder="经营范围" data-bind="value:company().business_scope"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="ownership" class="col-sm-2 control-label">企业所有制</label>
                    <div class="col-sm-4">
                        <select class="form-control" title="请选择企业所有制" id="ownership" name="data[ownership]" data-bind="options:ownerships,optionsText:'name',optionsCaption: '请选择企业所有制',value: company().ownership, optionsValue:'id',valueAllowUnset: true">
                        </select>
                    </div>
                    <label for="runs_state" class="col-sm-2 control-label">经营状态</label>
                    <div class="col-sm-4">
                        <select class="form-control" title="请选择经营状态" id="runs_state" name="data[runs_state]" data-bind="optionsCaption: '请选择经营状态',value:company().runs_state,valueAllowUnset: true">
                            <option value=''>请选择经营状态</option>
                            <?php foreach ($this->map["runs_state"] as $k => $v) {
                                echo "<option value='" . $k . "'>" . $v . "</option>";
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="describe" class="col-sm-2 control-label">公司简介 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="describe" name="data[describe]" rows="3" placeholder="公司简介" data-bind="value:describe"></textarea>
                    </div>
                </div>

                <hr/>
                <div class="form-group">
                    <label for="is_stock" class="col-sm-2 control-label">是否上市</label>
                    <!-- <div class="col-sm-4 skin skin-flat checkbox"> -->
                    <div class="col-sm-4 skin checkbox">
                        <!-- <input type="checkbox" id="is_stock" name="data[is_stock]" data-bind="checked:company().is_stock">&nbsp;已上市 -->
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="is_stock" name="data[is_stock]" data-bind="checked:company().is_stock" style="margin-right:10px;">已上市
                    </div>
                </div>
                <div class="form-group">
                    <label for="stock_code" class="col-sm-2 control-label">上市编号</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="stock_code" name="data[stock_code]" placeholder="上市编号" data-bind="value:company().stock_code">
                    </div>
                </div>
                <div class="form-group">
                    <label for="stock_name" class="col-sm-2 control-label">上市名称</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="stock_name" name="data[stock_name]" placeholder="上市名称" data-bind="value:company().stock_name">
                    </div>
                </div>
                <div class="form-group">
                    <label for="stock_type" class="col-sm-2 control-label">上市板块</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="stock_type" name="data[stock_type]" placeholder="上市板块" data-bind="value:company().stock_type">
                    </div>
                </div>

                <hr/>
                <div class="form-group">
                    <label for="contact_person" class="col-sm-2 control-label">客户联系人
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="contact_person" name="data[contact_person]" placeholder="客户联系人" data-bind="value:company().contact_person">
                    </div>
                    <label for="contact_phone" class="col-sm-2 control-label">联系方式
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="contact_phone" name="data[contact_phone]" placeholder="联系方式" data-bind="value:company().contact_phone">
                    </div>
                </div>
                <div class="form-group">
                    <label for="business_type" class="col-sm-2 control-label">企业类型
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-10">
                        <select class="form-control" title="请选择企业类型" id="business_type" name="data[business_type]" data-bind="optionsCaption: '请选择企业类型',value: company().business_type,valueAllowUnset: true">
                            <?php foreach ($this->map["business_type"] as $k => $v) {
                                echo "<option value='" . $k . "'>" . $v . "</option>";
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="product" class="col-sm-2 control-label">
                        <span data-bind="visible:company().business_type() == 1">生产产品</span>
                        <span data-bind="visible:company().business_type() == 2">主营产品</span>
                    </label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" id="product" name="data[product]" placeholder=""
                               data-bind="value:company().product">
                    </div>
                    <label for="equipment" class="col-sm-2 control-label">
                        <span data-bind="visible:company().business_type() == 1">生产装置</span>
                        <span data-bind="visible:company().business_type() == 2">贸易规模</span>
                    </label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" id="equipment" name="data[equipment]" placeholder=""
                               data-bind="value:company().equipment">
                    </div>
                    <label for="production_scale" class="col-sm-2 control-label">
                        <span data-bind="visible:company().business_type() == 1">生产规模</span>
                        <span data-bind="visible:company().business_type() == 2">行业口碑</span>
                    </label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" id="production_scale" name="data[production_scale]" placeholder="" data-bind="value:company().production_scale">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">类型<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4 checkbox">
                        <span name='data[type]' data-bind="value:type">
                         <?php foreach ($this->map["partner_type"] as $key => $value) {?>
                             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="type_<?php echo $key ?>" name="data[type_<?php echo $key ?>]" data-bind="checked:type_<?php echo $key ?>" style="margin-right:10px;"><?php echo $value ?>&emsp;&emsp;&emsp;
                         <?php } ?>
                         </span>
                     </div>
                    <!-- <div class="col-sm-4">
                        <select class="form-control" title="请选择类型" id="user_id" name="data[user_id]" data-bind="optionsCaption: '请选择业务员',value:user_id,valueAllowUnset: true">
                                <option value="">请选择类型</option>
                                <?php foreach ($this->map["partner_type"] as $key => $value) {
                                    echo "<option value='" . $key . "'>" . $value . "</option>";
                                } ?>
                        </select>
                    </div> -->
                    <label for="user_id" class="col-sm-2 control-label">业务员<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <select class="form-control" title="请选择业务员" id="user_id" name="data[user_id]" data-bind="optionsCaption: '请选择业务员',value:user_id,valueAllowUnset: true">
                            <option value="">请选择业务员</option>
                            <?php
                            $users = UserService::getBusinessDirectors();
                            foreach ($users as $v) {
                                echo "<option value='" . $v["user_id"] . "'>" . $v["name"] . "</option>";
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="trade_info" class="col-sm-2 control-label">历史合作情况
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="trade_info" name="data[trade_info]" placeholder="历史合作情况" data-bind="value:trade_info">
                    </div>
                </div>
                <div class="form-group">
                    <label for="category" class="col-sm-2 control-label">拟合作产品
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-10">
                        <select multiple="" class="form-control" title="请选择拟合作产品" id="gIds" name="data[gIds]" data-bind="selectpicker:gIds,valueAllowUnset: true">
                            <?php
                            $goods = GoodsService::getAllActiveGoods();
                            foreach ($goods as $v) {
                                echo "<option value='" . $v["goods_id"] . "'>" . $v["name"] . "</option>";
                            } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="bank_name" class="col-sm-2 control-label">银行名称</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="bank_name" name="data[bank_name]" placeholder="银行名称" data-bind="value:bank_name">
                    </div>
                    <label for="bank_account" class="col-sm-2 control-label">银行账号</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="bank_account" name="data[bank_account]" placeholder="银行账号" data-bind="value:bank_account">
                    </div>
                </div>
                <div class="form-group">
                    <label for="tax_code" class="col-sm-2 control-label">纳税识别号</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="tax_code" name="data[tax_code]" placeholder="纳税识别号" data-bind="value:company().tax_code">
                    </div>
                    <label for="phone" class="col-sm-2 control-label">电话</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="phone" name="data[phone]" placeholder="联系电话" data-bind="value:phone">
                    </div>
                </div>
                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">备注</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="remark" name="data[remark]" rows="3" placeholder="备注" data-bind="value:company().remark"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="custom_level" class="col-sm-2 control-label">商务强制分类</label>
                    <div class="col-sm-4">
                        <select class="form-control" title="请选择商务强制分类" id="custom_level" name="data[custom_level]" data-bind="optionsCaption: '请选择商务强制分类',value:custom_level,enable: white_level()==0">
                            <option value="">系统默认</option>
                            <?php
                            foreach ($this->map["partner_level"] as $k => $v) {
                                echo "<option value='" . $k . "'>" . $v . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <label for="custom_level" class="col-sm-2 control-label" data-bind="visible: auto_level() > 0">系统检测分类</label>
                    <div class="col-sm-4">
                        <p class="form-control-static" data-bind="visible: auto_level() > 0">
                            <span class="text-red" data-bind="html: auto_level_desc"></span></p>
                    </div>
                </div>
                <div data-bind="visible:(type_2() == 1)">
                    <hr/>
                    <div class="form-group">
                        <label for="type" class="col-sm-2 control-label">拟申请额度
                            <span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input type="text" class="form-control" id="apply_amount" name="data[apply_amount]" placeholder="拟申请额度" data-bind="moneyWan:apply_amount">
                                <span class="input-group-addon">万元</span>
                            </div>
                        </div>
                        <div class="col-sm-4" data-bind="visible:nameIsChanged()==false">
                            <?php if ($data['credit_amount'] > 0) { ?>
                                <label for="type" class="col-sm-6 control-label">确认额度（万元）</label>
                                <div class="col-sm-6">
                                    <p class="form-control-static">￥ <?php echo number_format($data['credit_amount'] / 10000 / 100, 2) ?>万元</p>
                                </div>
                            <?php } ?>
                        </div>

                    </div>
                </div>
            </div><!--end box-border-->
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="checkButton" class="btn btn-primary" data-bind="click:checkLevel,html:checkBtnText,enable: white_level()==0"></button>
                        <button type="button" id="tempSaveButton" class="btn btn-primary" data-bind="click:tempSave,html:tempSaveBtnText"></button>
                        <button type="button" id="saveButton" class="btn btn-danger" data-bind="visible:isShowSave,click:save,html:saveBtnText">提交</button>
                        <button type="button" id="nextButton" class="btn btn-primary" data-bind="visible:isShowNext,click:showAttachUpload">下一步</button>
                        <button type="button" class="btn btn-default" data-bind="click:back">返回</button>
                        <input type='hidden' name='data[partner_id]' data-bind="value:company().partner_id"/>
                        <input type='hidden' name='data[is_temp_save]' data-bind="value:is_temp_save"/>
                    </div>
                </div>
            </div>
        </form>
    </div><!--end box box-primary-->

    <!-- partner retrieve modal -->
    <div class="modal fade draggable-modal" id="partnerModel" tabindex="-1" role="dialog" aria-labelledby="modal">
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
                                    <td style='text-align:center;' data-bind="text:runs_state_desc"></td>
                                    <td style='text-align:center;'>
                                        <a data-bind="click:function(){$parent.select($index());}">选择</a></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</section><!--end content-->

<script>
    var view;
    $(function () {
        view = new ViewModel(<?php echo json_encode($data) ?>);
        ko.applyBindings(view);
        view.isCanEditName(<?php echo $isCanEditName ?>);
        view.attachmentsLength(<?php echo count(PartnerApplyService::getAttachment($data['partner_id'])) ?>);
        view.levelAttachs(<?php echo json_encode($this->getLevelAttach($this->map)) ?>);
        view.ownerships(<?php echo json_encode(Ownership::getOwnerships()) ?>);
        view.partnerName(<?php echo json_encode($data['name']) ?>);
        $("#gIds").selectpicker();
        $("#gIds").selectpicker('val', [<?php echo $data["goods_ids"] ?>]);
        $("#start_date").datetimepicker({format: 'yyyy-mm-dd', minView: 'month'});
        $("#bank_account").bankInput({min: 1, max: 50, deimiter: ' '});
        $('.skin-flat input').iCheck({
          checkboxClass: 'icheckbox_flat-green'
        });
    });
    function ViewModel(option) {
        var defaults = {
            type:"-1",
            /*type_1:false,
            type_2:false,
            type_3:false,*/
            apply_amount: "",//拟申请额度
            user_id: "",//业务员
            trade_info: "",//历史合作情况
            goods_ids: "",//拟合作产品
            bank_name: "",//银行名称
            bank_account: "",//银行账号
            phone: "",//电话
            custom_level: 0,//商务强制分类
            auto_level: 0,//系统分类
            auto_level_desc: "",
            white_level: 0,//白名单分级
            is_temp_save: 0, //是否暂存
            describe: "", //公司简介
        };
        var o = $.extend(defaults, option);
        var self = this;
        // self.partner_id = ko.observable(o.partner_id);
        
        self.type_1 = ko.observable(o.type_1);
        self.type_2 = ko.observable(o.type_2);
        self.type_3 = ko.observable(o.type_3);
        self.type = ko.observable(o.type).extend({
            custom: {
                params: function (v) {
                    // console.log(self.type_1());
                    if (self.type_1()==1 || self.type_2()==1 || self.type_3()==1) {
                        return true;
                    }
                    else
                        return false;
                },
                message: "请选择类别"
            }
        });
        
        self.apply_amount = ko.observable(o.apply_amount).extend({
            custom: {
                params: function (v) {
                    if (self.type_2() != true || v > 0) {
                        return true;
                    }
                    else
                        return false;
                },
                message: "请填写拟申请额度"
            }
        });
        self.checkLevelState = ko.observable(0);

        self.user_id = ko.observable(o.user_id).extend({
            custom: {
                params: function (v) {
                    if (v > 0) {
                        return true;
                    }
                    else
                        return false;
                },
                message: "请选择业务员"
            }
        });
        self.trade_info = ko.observable(o.trade_info).extend({required: true});
        self.gIds = ko.observable(o.goods_ids).extend({
            custom: {
                params: function (v) {
                    if (($.isArray(v) && v.length > 0) || (v!=null && v!="") )
                    {
                        return true;
                    }
                    else
                        return false;
                },
                message: "请选择拟合作产品"
            }
        });
        self.bank_name = ko.observable(o.bank_name);
        self.bank_account = ko.observable(o.bank_account);
        self.phone = ko.observable(o.phone).extend({telephone: true});
        self.auto_level = ko.observable(o.auto_level);
        self.custom_level = ko.observable(o.custom_level).extend({
            custom: {
                params: function (v) {
                    if (self.checkLevelState() > 0) {
                        return self.auto_level() > 0 || v > 0;
                    }
                    return true;
                },
                message: "请选择分级或点击检测系统分级按钮"
            }
        });
        self.is_temp_save = ko.observable(o.is_temp_save);
        self.describe = ko.observable(o.describe).extend({required: true});
        self.ownerships = ko.observableArray();

        self.auto_level_desc = ko.observable(o.auto_level_desc);
        self.white_level = ko.observable(o.white_level);
        self.company = ko.observable(new CompanyModel(option));
        self.companies = ko.observableArray();
        self.companyKeyWord = ko.observable();
        self.actionState = ko.observable(0);
        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return (self.company().isValid() && (self.errors().length === 0));
        }
        self.isCanEditName = ko.observable(1);
        self.isParamsNonEdit = ko.computed(function () {
            return self.isCanEditName() != 1;
        }, self);

        self.retrieveBtnText = ko.observable("检索");
        self.saveBtnText = ko.observable("提交");
        self.tempSaveBtnText = ko.observable("暂存");
        self.checkBtnText = ko.observable("检测系统分级");

        //是否显示下一步按钮
        self.levelAttachs = ko.observableArray();
        self.isShowNext = ko.computed(function () {
            if (self.custom_level()) {
                return self.levelAttachs()[self.custom_level()];
            } else {
                if (self.auto_level()) {
                    return self.levelAttachs()[self.auto_level()];
                }
            }
        }, self);

        //是否显示提交按钮
        self.attachmentsLength = ko.observable(0);
        self.isShowSave = ko.computed(function () {
            if (self.attachmentsLength > 0) {
                return true;
            } else {
                if (self.custom_level()) {
                    return !self.levelAttachs()[self.custom_level()];
                } else {
                    if (self.auto_level()) {
                        return !self.levelAttachs()[self.auto_level()];
                    }
                }
            }
        }, self);

        self.partnerName = ko.observable('');
        self.nameIsChanged = ko.computed(function () {
            if(self.partnerName() != "" && self.company().name() != self.partnerName()) {
                return true;
            } else {
                return false;
            }
        }, self);

        self.gotoQiChaChaText = ko.observable("");
        //企业信息检索
        self.retrieve = function () {
            self.company().name.isModified(true);
            if (!self.company().name.isValid())
                return;
            if (self.actionState() == 1)
                return;

            self.actionState(1);
            self.retrieveBtnText("检索" + inc.loadingIco);
            $.ajax({
                type: "GET",
                url: "/partnerApply/getCompanies",
                data: {name: self.company().name()},
                dataType: "json",
                success: function (json) {
                    self.actionState(0);
                    self.retrieveBtnText("检索");
                    if (json.state == 0) {
                        self.ownerships(json.data.ownerships);
                        
                        if (json.data.partnerInfo.length > 1) {
                            self.companies(json.data.partnerInfo);
                            $("#partnerModel").modal({
                                backdrop: true,
                                keyboard: false,
                                show: true
                            });
                        }
                        else {
                            // self.setCompany(new CompanyModel(json.data[0]));
                            if (json.data.partnerInfo.length == 1) {
                                self.setCompany(json.data.partnerInfo[0]);
                                self.gotoQiChaCha();
                            } else {
                                self.gotoQiChaChaText("");
                                layer.alert("企业信息不存在！", {icon: 5});

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

        self.gotoQiChaCha = function () {
            self.company().name.isModified(true);
            if (!self.company().name.isValid())
                return;
            $.ajax({
                type: "GET",
                url: "/partnerApply/getKeyNo",
                data: {name: self.company().name()},
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        if (json.data != "") {
                            if($("#qiChachaLink").length == 0) {
                                self.gotoQiChaChaText('&emsp;&emsp;<a href="http://www.qichacha.com/firm_' + json.data + '.shtml" target="_blank" class="btn btn-warning">点击跳转到企查查详情页</a>');
                            }
                        }
                    }
                    else {
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
                    layer.alert("获取企业KeyNo失败：" + data.responseText, {icon: 5});
                }
            });
        }

        //合作方选择
        self.select = function (index) {
            if (index >= self.companies().length || index < 0)
                layer.alert("选择有误，请重新选择", {icon: 5});

            $("#partnerModel").modal("hide");
            // self.setCompany(new CompanyModel(self.companies()[index]));
            self.setCompany(self.companies()[index]);
            self.gotoQiChaCha();
        }

        self.setCompany = function (company) {
            if (company.hasOwnProperty("is_stock")) {
                if(company.is_stock == "0") {
                    company.is_stock = false;
                } else {
                    company.is_stock = true;
                }
                // delete company.is_stock;
            }
            if (company.hasOwnProperty("business_type") && company.business_type == "0") {
                company.business_type = 1;
            }
            if (company.hasOwnProperty("contact_person") && company.contact_person == null) {
                company.contact_person = "";
            }
            if (company.hasOwnProperty("contact_phone") && company.contact_phone == null) {
                company.contact_phone = "";
            }

            ko.setObservablesValue(self.company(), company);
            self.checkPartnerInWhite();

            self.resetObservables(self, ["ownerships", "company", "companies", "isCanEditName", "retrieveBtnText", "saveBtnText", "saveBtnText", "tempSaveBtnText", "checkBtnText", "levelAttachs", "checkedIsStock", "isParamsNonEdit", "isShowNext", "isShowSave", "errors", "nameIsChanged", "partnerName", "isValid", "gotoQiChaChaText", "attachmentsLength"]);
        }

        self.resetObservables = function (koObject, filters) {
            //console.log(koObject);
            for (var prop in koObject) {
                if(ko.isObservable(koObject[prop]) && $.inArray(prop, filters) < 0)
                {
                    // console.log(prop);
                    if($.inArray(prop,["type", "checkLevelState", "user_id", "custom_level", "auto_level", "white_level", "is_temp_save", "actionState"]) > -1) {
                        koObject[prop](0);
                    } else {
                        if(prop == "gIds") {
                            $("#gIds").selectpicker('val', []);
                        } else {
                            koObject[prop]("");
                        }
                    }
                    if (ko.validation.utils.isValidatable(koObject[prop]))
                        koObject[prop].isModified(false);
                }
            }
        }

        //检查是否在白名单中
        self.checkPartnerInWhite = function () {
            $.ajax({
                type: "GET",
                url: "/partnerApply/checkInWhite",
                data: {name: self.company().name()},
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        if (json.data !== null) {
                            self.white_level(json.data.level);
                            if (self.white_level() > 0) {
                                self.custom_level(self.white_level());
                            }
                        }
                    }
                    else {
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
                    layer.alert("检查是否在白名单中失败：" + data.responseText, {icon: 5});
                }
            });
        }

        //暂存
        self.tempSave = function () {
            self.is_temp_save(1);
            self.save();
        }

        self.save = function(){
           self.checkLevelState(1);
           if (!self.isValid()) {
               self.company().errors.showAllMessages();
               self.errors.showAllMessages();
               return;
           }
           if (self.actionState() == 1)
               return;
           if (self.is_temp_save() == 1){
               self.submit();
           }else{
               layer.confirm("您确定要执行当前操作，该操作不可逆？", {icon: 3, title: '提示'}, function(index){
                   self.submit();
                   layer.close(index);
               });
           }
        }

        //提交
        self.submit = function () {
            if (self.is_temp_save() == 1) { //执行暂存操作
                self.tempSaveBtnText("暂存中" + inc.loadingIco);
            } else {
                self.saveBtnText("提交中" + inc.loadingIco);
            }
            self.actionState(1);
            
            if (self.company().is_stock() == true) {
                self.company().is_stock(1);
            } else {
                self.company().is_stock(0);
            }
            var formData = $("#mainForm").serialize() + "&data[custom_level]=" + self.custom_level() + "&data[goods_ids]=" + self.gIds() + "&data[is_stock]=" + self.company().is_stock() + "&data[auto_level]=" + self.auto_level();
            $.ajax({
                type: "POST",
                url: "/partnerApply/save",
                data: formData,
                dataType: "json",
                success: function (json) {
                    self.actionState(0);
                    self.checkLevelState(0);
                    self.saveBtnText("提交");
                    self.tempSaveBtnText("暂存");
                    if (json.state == 0) {
                        self.company().partner_id(json.data);
                        inc.showNotice("操作成功");
                        if (self.is_temp_save() == 0) { //提交后跳转，暂存不需跳转
                            location.href = "/partnerApply/";
                        } else {
                            location.href = "/partnerApply/detail?id=" + self.company().partner_id();
                        }
                    }
                    else {
                        layer.alert(json.data, {icon: 5});
                    }
                    self.is_temp_save(0);
                },
                error: function (data) {
                    self.actionState(0);
                    self.checkLevelState(0);
                    self.is_temp_save(0);
                    self.saveBtnText("提交");
                    self.tempSaveBtnText("暂存");
                    layer.alert("操作失败：" + data.responseText, {icon: 5});
                }
            });
        }

        //检查系统分级
        self.checkLevel = function () {
            self.checkLevelState(0);
            if (!self.isValid()) {
                self.company().errors.showAllMessages();
                self.errors.showAllMessages();
                return;
            }
            if (self.actionState() == 1)
                return;
            self.actionState(1);
            self.checkBtnText("检测系统分级中" + inc.loadingIco);
            if (self.company().is_stock() == true) {
                self.company().is_stock(1);
            } else {
                self.company().is_stock(0);
            }
            var formData = $("#mainForm").serialize() + "&data[custom_level]=" + self.custom_level() + "&data[goods_ids]=" + self.gIds() + "&data[is_stock]=" + self.company().is_stock();
            $.ajax({
                type: "POST",
                url: "/partnerApply/checkLevel",
                data: formData,
                dataType: "json",
                success: function (json) {
                    self.actionState(0);
                    self.checkBtnText("检测系统分级");
                    if (json.state == 0) {
                        self.auto_level(json.data['system_level']);
                        self.auto_level_desc(json.data['level_desc']);
                    }
                    else {
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
                    self.actionState(0);
                    self.checkBtnText("检测系统分级");
                    layer.alert("检查失败：" + data.responseText, {icon: 5});
                }
            });
        }

        //下一步
        self.showAttachUpload = function () {
            if (!self.isValid()) {
                self.company().errors.showAllMessages();
                self.errors.showAllMessages();
                return;
            }
            if (self.actionState() == 1)
                return;
            self.actionState(1);
            self.is_temp_save(1);
            if (self.company().is_stock() == true) {
                self.company().is_stock(1);
            } else {
                self.company().is_stock(0);
            }
            var formData = $("#mainForm").serialize() + "&data[custom_level]=" + self.custom_level() + "&data[goods_ids]=" + self.gIds() + "&data[is_stock]=" + self.company().is_stock() + "&data[auto_level]=" + self.auto_level();
            ;
            $.ajax({
                type: "POST",
                url: "/partnerApply/save",
                data: formData,
                dataType: "json",
                success: function (json) {
                    self.actionState(0);
                    self.is_temp_save(0);
                    if (json.state == 0) {
                        location.href = "/partnerApply/attachments/?partner_id=" + json.data;
                    }
                    else {
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
                    self.actionState(0);
                    self.is_temp_save(0);
                    layer.alert("提交失败：" + data.responseText, {icon: 5});
                }
            });
        }

        //返回
        self.back = function () {
            history.back();
        }

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
            partner_id: 0,
            name: "",//企业名称
            credit_code: "",//统一社会信用代码
            registration_code: "",//工商注册号
            corporate: "",//法人代表
            start_date: "",//成立日期
            address: "",//注册地址
            registration_authority: "",//登记机关
            registered_capital: "",//注册资本
            paid_up_capital: "",//实收资本
            business_scope: "",//经营范围
            ownership: "",//企业所有制
            runs_state: "1",//经营状态
            runs_state_desc: "",//经营状态描述
            is_stock: "",//是否上市
            stock_code: "",//上市编号
            stock_name: "",//上市名称
            stock_type: "",//上市板块
            contact_person: "",//客户联系人
            contact_phone: "",//联系方式
            business_type: "1",//企业类型
            product: "",//生成产品
            equipment: "",//生产装置
            production_scale: "",//生产规模
            status: 0, //状态
            tax_code: "",//纳税识别号
            remark: "",//备注
        };
        var o = $.extend(defaults, option);
        var self = this;
        self.partner_id = ko.observable(o.partner_id);
        self.name = ko.observable(o.name).extend({required: true, maxLength: 128});
        self.credit_code = ko.observable(o.credit_code)
        self.registration_code = ko.observable(o.registration_code)
        self.corporate = ko.observable(o.corporate);
        self.start_date = ko.observable(o.start_date).extend({date: true});
        self.address = ko.observable(o.address);
        self.registration_authority = ko.observable(o.registration_authority);
        self.registered_capital = ko.observable(o.registered_capital);
        self.paid_up_capital = ko.observable(o.paid_up_capital);
        self.business_scope = ko.observable(o.business_scope);
        self.ownership = ko.observable(o.ownership);
        self.runs_state = ko.observable(o.runs_state);
        self.runs_state_desc = ko.observable(o.runs_state_desc);
        self.is_stock = ko.observable((o.is_stock == 1));
        self.stock_code = ko.observable(o.stock_code);
        self.stock_name = ko.observable(o.stock_name);
        self.stock_type = ko.observable(o.stock_type);
        self.contact_person = ko.observable(o.contact_person).extend({required: true});
        self.contact_phone = ko.observable(o.contact_phone).extend({required: true, telephone: true});
        self.business_type = ko.observable(o.business_type).extend({required: true});
        self.product = ko.observable(o.product);
        self.equipment = ko.observable(o.equipment);
        self.production_scale = ko.observable(o.production_scale);
        self.status = ko.observable(o.status);
        self.tax_code = ko.observable(o.tax_code);
        self.remark = ko.observable(o.remark);
        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        }

        self.checkedIsStock = ko.computed(function () {
            return self.is_stock() > 0;
        }, self);
    }
</script>
