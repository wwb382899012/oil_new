<?php if(!empty($contractDetailFile)) { include $contractDetailFile;} ?>
<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">请在下面操作</h3>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">项目编号</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">
                            <a href="/project/detail/?id=<?php echo $data["project_id"] ?>&t=1" target="_blank"><?php echo $data["project_id"] ?></a>
                        </p>
                    </div>
                    <label for="type" class="col-sm-2 control-label">上游合作方</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">
                            <a href="/partner/detail/?id=<?php echo $data["up_partner_id"]?>&t=1" target="_blank"><?php echo $data["up_name"]?></a>
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">项目名称</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">
                            <a href="/project/detail/?id=<?php echo $data["project_id"] ?>&t=1" target="_blank"><?php echo $data["project_name"] ?></a>
                        </p>
                    </div>
                    <label class="col-sm-2 control-label">下游合作方</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">
                            <a href="/partner/detail/?id=<?php echo $data["down_partner_id"]?>&t=1" target="_blank"><?php echo $data["down_name"]?></a>
                        </p>
                    </div>
                </div>
                <?php if(!empty($this->checkDetailFile)) { include $this->checkDetailFile;} ?>

                <!-- ko foreach: items -->
                <div class="form-group">
                    <div class="col-sm-12">
                        <h4>
                        <span data-bind="text:$index()+1"></span>、
                        <span data-bind="text:display_name"></span>
                        </h4>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-3">
                        <button class="btn btn-sm" data-bind="css:{'btn-success':check_status()==1,'btn-default':check_status()!=1},click:pass">通过</button>
                        <button class="btn btn-sm" data-bind="css:{'btn-danger':check_status()==0,'btn-default':check_status()!=0},click:reject">拒绝</button>
                        <input type="hidden" class="form-control" data-bind="value:check_status,attr: { name: 'extraItems[check_status_'+type()+']'}">
                    </div>
                    <div class="col-sm-9">
                        <input type="text" class="form-control"  placeholder="说明" data-bind="value:remark,attr: { name: 'extraItems[remark_'+type()+']'}">
                    </div>
                </div>
                <!-- /ko -->
                <div class="form-group">
                    <label for="remark" class="col-sm-1 control-label">备注</label>
                    <div class="col-sm-11">
                        <textarea class="form-control" id="remark" name= "obj[remark]" rows="3" placeholder="备注" data-bind="value:remark"></textarea>
                    </div>
                </div>
                <?php if(!empty($this->extraCheckItemFile)) { include $this->extraCheckItemFile;} ?>
            </div>
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="passButton" class="btn btn-danger" data-bind="click:doSubmit">提交</button>

                        <button type="button"  class="btn btn-default" data-bind="click:back">返回</button>
                        <input type='hidden' name='obj[project_id]' data-bind="value:project_id" />
                        <input type='hidden' name='obj[check_id]' data-bind="value:check_id" />
                    </div>
                </div>

            </div>
        </form>
    </div>
</section>
<script>
    var view;
    $(function(){
        view=new ViewModel(<?php echo json_encode($data) ?>);
        ko.applyBindings(view);
        view.formatItems(<?php echo json_encode($extraCheckItems) ?>);
    });
    function ViewModel(option)
    {
        var defaults = {
            project_id:0,
            check_id:0,
            remark: ""
        };
        var o = $.extend(defaults, option);
        var self=this;
        self.project_id=ko.observable(o.project_id);
        self.check_id=ko.observable(o.check_id);

        self.remark=ko.observable(o.remark);//.extend({required:true});
        self.status = ko.observable(o.status);

        self.items=ko.observableArray();

        self.errors = ko.validation.group(self);
        self.isValid = function () {
            if(self.errors().length === 0)
            {
                var unValid=ko.utils.arrayFilter(self.items(), function(item) {
                    if(!item.isValid())
                    {
                        item.errors.showAllMessages();
                        return true;
                    }
                    else
                        return false;
                });
                if(unValid.length>0)
                    return false;
                else
                    return true;
            }
            else
                return false;
        };

        self.doSubmit=function(){
            var unCheck=ko.utils.arrayFilter(self.items(), function(item) {
                return item.check_status()<0;
            });
            if(unCheck.length>0)
            {
                alert("请全部审核完再提交！");
                return;
            }
            if(confirm("您确定要提交当前信息的审核，该操作不可逆？")) {
                var rejects=ko.utils.arrayFilter(self.items(), function(item) {
                    return item.check_status()!=1;
                });
                if(rejects.length>0)
                    self.status(0);
                else
                    self.status(1);
                self.save();
            }
        }

        self.pass=function(){
            if(confirm("您确定要通过当前信息的审核，该操作不可逆？")) {
                self.status(1);
                self.save();
            }
        }
        self.reject=function(){
            if(confirm("您确定要拒绝当前信息的审核，该操作不可逆？")) {
                self.status(0);
                self.save();
            }
        }

        self.checkBack=function(){
            if(confirm("您确定要驳回当前信息的审核，该操作不可逆？")) {
                self.status(-1);
                self.save();
            }
        }

        self.save=function(){
            if(!self.isValid())
            {
                self.errors.showAllMessages();
                $(".validationElement").eq(0).focus();
                return;
            }
            var formData=$("#mainForm").serialize();
            formData+="&obj[checkStatus]="+self.status();
            formData+="&items="+JSON.stringify(ko.toJS(self.items()));
            $.ajax({
                type: 'POST',
                url: '/<?php echo $this->getId() ?>/save/',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        if(document.referrer)
                            location.href=document.referrer;
                        else
                            location.href="<?php echo $this->mainUrl ?>";
                    }
                    else {
                        alert(json.data);
                    }
                },
                error:function (data) {
                    alert("保存失败！"+data.responseText);
                }
            });
        }

        self.back=function(){
            history.back();
        }

        self.formatItems=function(data)
        {
            if (data == null || data == undefined)
                return;
            self.items.removeAll();

            for (var i = 0; i < data.length; i++)
            {
                self.items.push(new CheckItemModel({
                    type:data[i]["type"],
                    display_name:data[i]["display_name"],
                    check_status:-1,//data[i]["check_status"],
                    remark:data[i]["remark"]
                }));
            }
        }

    }


    function CheckItemModel(option)
    {
        var defaults = {
            type: 0,
            display_name:"",
            check_status: 0,
            remark: ""
        };
        var o = $.extend(defaults, option);
        var self = this;
        self.type = ko.observable(o.type);
        self.display_name = ko.observable(o.display_name);
        self.check_status = ko.observable(o.check_status);
        self.remark = ko.observable(o.remark).extend({custom: {
            params: function (v) {
                if (self.check_status()==1 || v.length>0) {
                    return true;
                }
                else
                    return false;
            },
            message: "拒绝时请填写说明！"
        }
        });

        self.pass=function(){
            self.check_status(1);
        }
        self.reject=function(){
            self.check_status(0);
        }

        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };
    }
</script>