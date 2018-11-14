<!-- <script src="/js/jquery.bankInput.js"></script> -->
<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">请在下面填写</h3>
        </div><!--end box box-header-->
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">企业名称 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="name" name= "data[name]" placeholder="企业名称" data-bind="value:name">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">企业编码 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="code" name= "data[code]" placeholder="企业编码" data-bind="value:code">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">统一信用代码</label>
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
                <!-- <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">银行名称</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="bank_name" name= "data[bank_name]" placeholder="银行名称" data-bind="value:bank_name">
                    </div>
                </div> -->
                <div class="form-group">
                    <!-- <label for="type" class="col-sm-2 control-label">银行账号</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="bank_account" name= "data[bank_account]" placeholder="银行账号" data-bind="value:bank_account">
                    </div> -->
                    <label for="type" class="col-sm-2 control-label">联系电话</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="phone" name= "data[phone]" placeholder="联系电话" data-bind="value:phone">
                    </div>
                    <label class="col-sm-2 control-label">状态</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="status" name="data[status]" data-bind="value:status">
                            <?php foreach($this->map["corporation_status"] as $k=>$v)
                            {
                                echo "<option value='".$k."'>".$v."</option>";
                            }?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">企业所有制</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="ownership" name="data[ownership]" data-bind="value:ownership">
                            <?php foreach($this->map["ownership"] as $k=>$v)
                            {
                                echo "<option value='".$k."'>".$v."</option>";
                            }?>
                        </select>
                    </div>
                    <label for="type" class="col-sm-2 control-label">成立日期</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="start_date" name= "data[start_date]" placeholder="成立日期" data-bind="value:start_date">
                    </div>
                </div>
                <!-- <div class="form-group">
                    
                </div> -->
                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">备注</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="remark" name= "data[remark]" rows="3" placeholder="备注" data-bind="value:remark"></textarea>
                    </div>
                </div>
            </div><!--end box-border-->
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:save">保存</button>
                        <button type="button"  class="btn btn-default" data-bind="click:back">返回</button>
                        <input type='hidden' name='data[corporation_id]' data-bind="value:corporation_id" />
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
            code:"",
            credit_code:"",
            corporate:"",
            tax_code:"",
            address:"",
            //bank_name:"",
            //bank_account:"",
            phone:"",
            ownership:2,
            start_date:d,
            status:1,
            remark:""
        };
        var o=$.extend(defaults,option);
        var self=this;
        self.corporation_id=ko.observable(o.corporation_id);
        self.name=ko.observable(o.name).extend({required:true});
        self.code=ko.observable(o.code).extend({required:true});
        self.credit_code=ko.observable(o.credit_code);
        self.corporate=ko.observable(o.corporate);
        self.tax_code=ko.observable(o.tax_code);
        self.address=ko.observable(o.address);
        //self.bank_name=ko.observable(o.bank_name);
        //self.bank_account=ko.observable(o.bank_account);
        self.phone=ko.observable(o.phone);
        self.ownership=ko.observable(o.ownership);
        self.start_date=ko.observable(o.start_date);
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
                url:"/corporation/save",
                data:formData,
                dataType:"json",
                success:function (json) {
                    if(json.state==0){
                        if(document.referrer){
                            location.href=document.referrer;
                        }else{
                            location.href="/corporation/";
                        }
                    }else{
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