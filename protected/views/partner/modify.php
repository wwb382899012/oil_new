<script src="/js/jquery.bankInput.js"></script>
<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">请在下面填写</h3>
        </div><!--end box box-header-->
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">企业名称</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="name" name= "data[name]" placeholder="企业名称" data-bind="value:name">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">企业编码</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="code" name= "data[code]" placeholder="企业编码" data-bind="value:code">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">信用代码</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="credit_code" name= "data[credit_code]" placeholder="信用代码" data-bind="value:credit_code">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">法人代表</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="corporate" name= "data[corporate]" placeholder="法人代表" data-bind="value:corporate">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">纳税识别号</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="tax_code" name= "data[tax_code]" placeholder="纳税识别号" data-bind="value:tax_code">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">地址</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="address" name= "data[address]" placeholder="地址" data-bind="value:address">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">银行名称</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="bank_name" name= "data[bank_name]" placeholder="银行名称" data-bind="value:bank_name">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">银行账号</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="bank_account" name= "data[bank_account]" placeholder="银行账号" data-bind="value:bank_account">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">联系电话</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="phone" name= "data[phone]" placeholder="联系电话" data-bind="value:phone">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">类别</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="type" name="data[type]" data-bind="value:type,valueAllowUnset: true">
                            <option value='-1'>请选择类别</option>
                            <?php foreach($this->map["partner_type"] as $k=>$v)
                            {
                                echo "<option value='".$k."'>".$v."</option>";
                            }?>
                        </select>
                    </div>
                    <label class="col-sm-2 control-label">企业所有制</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="ownership" name="data[ownership]" data-bind="value:ownership">
                            <?php foreach($this->map["ownership"] as $k=>$v)
                            {
                                echo "<option value='".$k."'>".$v."</option>";
                            }?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">成立日期</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="start_date" name= "data[start_date]" placeholder="成立日期" data-bind="value:start_date">
                    </div>
                    <label class="col-sm-2 control-label">状态</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="status" name="data[status]" data-bind="value:status">
                            <?php foreach($this->map["partner_status"] as $k=>$v)
                            {
                                echo "<option value='".$k."'>".$v."</option>";
                            }?>
                        </select>
                    </div>
                </div>
                <!--div class="form-group">
                    <label for="type" class="col-sm-2 control-label">省</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="provice" name="data[provice]" data-bind="value:provice">
                            <!--?php foreach($this->map["provice"] as $k=>$v)
                            {
                                echo "<option value='".$k."'>".$v."</option>";
                            }?>
                        </select>
                    </div>
                    <label class="col-sm-2 control-label">市</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="city" name="data[city]" data-bind="value:city">
                            <!--?php foreach($this->map["city"] as $k=>$v)
                            {
                                echo "<option value='".$k."'>".$v."</option>";
                            }?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">县区</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="region" name="data[region]" data-bind="value:region">
                            <!--?php foreach($this->map["region"] as $k=>$v)
                            {
                                echo "<option value='".$k."'>".$v."</option>";
                            }?>
                        </select>
                    </div>
                    <label class="col-sm-2 control-label">联系人</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="contact_person" name="data[contact_person]" data-bind="value:contact_person">
                            <!--?php foreach($this->map["contact_person"] as $k=>$v)
                            {
                                echo "<option value='".$k."'>".$v."</option>";
                            }?>
                        </select>
                    </div>
                </div-->
                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">备注</label>
                    <div class="col-sm-8">
                        <textarea class="form-control" id="remark" name= "data[remark]" rows="3" placeholder="备注" data-bind="value:remark"></textarea>
                    </div>
                </div>
            </div><!--end box-border-->
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:save">保存</button>
                        <button type="button"  class="btn btn-default" data-bind="click:back">返回</button>
                        <input type='hidden' name='data[partner_id]' data-bind="value:partner_id" />
                    </div>
                </div>
            </div>
        </form>
    </div><!--end box box-primary-->
</section><!--end content-->

<script>
    var view;
    $(function () {
        view=new ViewModel(<?php echo json_encode($data) ?>);
        ko.applyBindings(view);
        $("#start_date").datetimepicker({format:'yyyy-mm-dd',minView:'month'});
        $("#bank_account").bankInput({min:1,max:50,deimiter:' '});
    });
    function ViewModel(option) {
        var defaults={
            name:"",//企业名称
            code:"",//企业编号
            credit_code:"",//信用代码
            corporate:"",//法人代表
            tax_code:"",//纳税识别号
            address:"",//地址
            bank_name:"",//银行名称
            bank_account:"",//银行账号
            phone:"",//电话号码
            type:"-1",//类别
            ownership:2,//企业所有制
            start_date:"",//成立日期
            provice:1,//省
            city:1,//市
            region:1,//县区
            status:1,//状态
            remark:""//备注
        };
        var o=$.extend(defaults,option);
        var self=this;
        self.partner_id=ko.observable(o.partner_id);
        self.name=ko.observable(o.name).extend({required:{params:true,message:"请输入企业名称"}});
        self.code=ko.observable(o.code);
        self.credit_code=ko.observable(o.credit_code);
        self.corporate=ko.observable(o.corporate);
        self.tax_code=ko.observable(o.tax_code);
        self.address=ko.observable(o.address);
        /*self.name=ko.observable(o.name).extend({required:{params:true,message:"请输入企业名称"}});
        self.code=ko.observable(o.code).extend({required:{params:true,message:"请输入企业编号"}});
        self.credit_code=ko.observable(o.credit_code).extend({required:{params:true,message:"请输入信用代码"}});
        self.corporate=ko.observable(o.corporate).extend({required:{params:true,message:"请输入法人代表"}});
        self.tax_code=ko.observable(o.tax_code).extend({required:{params:true,message:"请输入纳税识别码"}});
        self.address=ko.observable(o.address).extend({required:{params:true,message:"请输入地址"}});*/
        //self.phone=ko.observable(o.phone).extend({required:{params:true,message:"请输入电话号码"}});
        self.phone=ko.observable(o.phone);
        //self.type=ko.observable(o.type).extend({required:{params:true,message:"请选择类别"}});
        self.type=ko.observable(o.type).extend({custom: {
            params: function (v) {
                if (v > -1) {
                    return true;
                }
                else
                    return false;
            },
            message: "请选择类别"
        }
        });
        self.ownership=ko.observable(o.ownership).extend({required:{params:true,message:"请选择企业所有制"}});
        self.start_date=ko.observable(o.start_date);
        //self.start_date=ko.observable(o.start_date).extend({required:{params:true,message:"请选择成立日期"}});
        //self.contact_person=ko.observable(o.contact_person).extend({required:{params:true,message:"请选择联系人"}});
        //self.provice=ko.observable(o.provice).extend({required:{params:true,message:"请选择省"}});
        //self.city=ko.observable(o.city).extend({required:{params:true,message:"请选择市"}});
        //self.region=ko.observable(o.region).extend({required:{params:true,message:"请选择县区"}});
        self.bank_name=ko.observable(o.bank_name);
        self.bank_account=ko.observable(o.bank_account);
        self.status=ko.observable(o.status);
        self.remark=ko.observable(o.remark);
        self.errors=ko.validation.group(self);
        self.isValid=function () {
            return self.errors().length===0;
        }

        self.save=function () {
            if(!self.isValid()){
                self.errors.showAllMessages();
                return;
            }
            var formData=$("#mainForm").serialize();
            $.ajax({
                type:"POST",
                url:"/partner/save",
                data:formData,
                dataType:"json",
                success:function (json) {
                    if (json.state == 0) {
                        if(document.referrer)
                            location.href=document.referrer;
                        else
                            location.href="/partner/";
                    }
                    else {
                        alertModel(json.data);
                    }
                },
                error:function (data) {
                    alertModel("保存失败！"+data.responseText);
                }
            });
        }

        self.back=function () {
            history.back();
        }

    }
</script>
