<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">请在下面填写</h3>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">角色名</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="role_name" name= "obj[role_name]" placeholder="角色名" data-bind="value:role_name">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">是否启用</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="status" name="obj[status]" data-bind="value:status">
                            <?php foreach($this->map["role_status"] as $k=>$v)
                            {
                                echo "<option value='".$k."'>".$v."</option>";
                            }?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">备注</label>
                    <div class="col-sm-8">
                        <textarea class="form-control" id="remark" name= "obj[remark]" rows="3" placeholder="备注" data-bind="value:remark"></textarea>
                    </div>
                </div>

            </div>
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:save">保存</button>
                        <button type="button"  class="btn btn-default" data-bind="click:back">返回</button>
                        <input type='hidden' name='obj[role_id]' data-bind="value:role_id" />
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
    });
    function ViewModel(option)
    {
        var defaults = {
            role_id:0,
            role_name: "",
            status:1,
            remark: ""
        };
        var o = $.extend(defaults, option);
        var self=this;
        self.role_id=ko.observable(o.role_id);
        self.role_name=ko.observable(o.role_name).extend({required:true});
        self.remark=ko.observable(o.remark);
        self.status = ko.observable(o.status);
        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };

        self.save=function(){
            if(!self.isValid())
            {
                self.errors.showAllMessages();
                return;
            }
            var formData=$("#mainForm").serialize();
            $.ajax({
                type: 'POST',
                url: '/role/save',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        location.href="/role/";
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

        self.back=function(){
            history.back();
        }
    }

</script>