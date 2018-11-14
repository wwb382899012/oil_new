<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">请在下面填写</h3>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label">项目信息</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $data["project_id"] ?> &emsp;<?php echo $data["project_name"] ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">需申请金额</label>
                    <div class="col-sm-2">
                        <p class="form-control-static"><span data-bind="moneyWanText:applyAmount"></span> 万元</p>
                    </div>
                    <label class="col-sm-2 control-label">已申请金额</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><span data-bind="moneyWanText:total"></span> 万元</p>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <table class="table table-striped table-bordered table-condensed table-hover">
                            <thead>
                            <tr>
                                <th style="width:200px;text-align:center">业务员</th>
                                <th style="width:300px;text-align:left;">金额</th>
                                <th style="text-align:left;">删除</th>
                            </tr>
                            </thead>
                            <tbody data-bind="foreach: items">
                            <tr>
                                <td style="text-align:center">
                                    <select class="form-control input-sm" name="i_user_id" data-bind="value:user_id">
                                        <?php
                                        $users=UserService::getProjectManageUsers();
                                        foreach($users as $v)
                                        {
                                            echo "<option value='".$v["user_id"]."'>".$v["name"]."</option>";
                                        }?>
                                    </select>
                                </td>
                                <td style="">
                                    <div class="input-group  input-group-sm">
                                        <input type="text" class="form-control"  name= "i_amount" placeholder="金额" data-bind="moneyWan:amount" >
                                        <span class="input-group-addon">万元</span>
                                    </div>
                                </td>
                                <td style="">
                                    <button type="button"  class="btn btn-danger btn-sm" data-bind="click:$parent.delItem">删除</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <button type="button"  class="btn btn-success" data-bind="click:addItem"><span class="glyphicon glyphicon-plus"></span>增加</button>
                    </div>

                </div>
                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">备注</label>
                    <div class="col-sm-4">
                        <textarea class="form-control" id="remark" name= "data[remark]" rows="3" placeholder="备注" data-bind="value:remark"></textarea>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:save,html:buttonText">保存</button>
                        <button type="button"  class="btn btn-default" data-bind="click:back">返回</button>
                        <input type='hidden' name='data[project_id]' data-bind="value:project_id" />
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
    var view;
    $(function () {
        view=new ViewModel(<?php echo json_encode($data) ?>);
        ko.applyBindings(view);
    });

    function ViewModel(option){
        var defaults={
            project_id:0,
            applyAmount:0,
            remark:""
        };
        var o=$.extend(defaults,option);
        var self=this;
        self.project_id=ko.observable(o.project_id);
        self.applyAmount=ko.observable(o.applyAmount);
        self.remark=ko.observable(o.remark);
        self.errors=ko.validation.group(self);
        self.isValid=function () {
            return self.errors().length===0;
        }

        self.items=ko.observableArray();

        self.buttonText=ko.observable("提交");
        self.save=function () {
            if(!self.isValid()){
                self.errors.showAllMessages();
                return;
            }
            self.buttonText("提交中 "+inc.loadingIco);
            //var formData=$("#mainForm").serialize();
            var formData="data="+JSON.stringify(ko.toJS(self));
            //formData+="&items="+JSON.stringify(ko.toJS(self.items()));
            $.ajax({
                type:"POST",
                url:"/creditApply/save",
                data:formData,
                dataType:"json",
                success:function (json) {
                    self.buttonText("提交");
                    if(json.state==0){
                        if(document.referrer){
                            location.href=document.referrer;
                        }else{
                            location.href="/creditApply/";
                        }
                    }else{
                        alert(json.data);
                    }
                },
                error:function (data) {
                    self.buttonText("提交");
                    alert("提交失败："+data.responseText);
                }
            });
        }

        self.back=function () {
            history.back();
        }
        self.total = ko.computed(function() {
            var total = 0;
            ko.utils.arrayForEach(self.items(), function(item) {
                var value = parseFloat(item.amount());
                if (!isNaN(value)) {
                    total += value;
                }
            });
            return total.toFixed(0);
        }, self);

        self.addItem=function()
        {
            self.items.push(new ItemModel());
        }
        self.delItem=function (data) {
            self.items.remove(data);
        }
    }

    function ItemModel(option){
        var defaults={
            user_id:0,
            amount:0
        };
        var o=$.extend(defaults,option);
        var self=this;
        self.user_id=ko.observable(o.user_id);

        self.amount=ko.observable(o.amount).extend({money:true});
        self.errors=ko.validation.group(self);
        self.isValid=function () {
            return self.errors().length===0;
        }

    }
</script>