<!-- <section class="content"> -->
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">请在下面操作</h3>
        </div>
        <form class="form-horizontal"  id="mainForm">
            <div class="box-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label">用户</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $data["user_account"]."     ".$data["user_name"] ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="category" class="col-sm-2 control-label">主角色</label>
                    <div class="col-sm-10">
                        <select class="form-control" title="请选择主角色" id="main_role_id" name="obj[main_role_id]" data-bind="value:main_role_id,valueAllowUnset: true">
                            <?php
                            foreach($roles as $v) {
                                echo "<option value='" . $v["role_id"] . "'>" . $v["role_name"] . "</option>";
                            }?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="category" class="col-sm-2 control-label">所属角色</label>
                    <div class="col-sm-10">
                        <select multiple="" class="form-control" title="请选择所属角色" id="roleIds" name="obj[roleIds]" data-bind="value:roleIds,valueAllowUnset: true">
                            <?php
                            foreach($roles as $v) {
                                echo "<option value='" . $v["role_id"] . "'>" . $v["role_name"] . "</option>";
                            }?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="paymentType" class="col-sm-2 control-label">权限变更</label>
                    <div class="col-sm-10">
                        <input type="checkbox" id="isRoleRight" name="obj[isRoleRight]" data-bind="checked:isRoleRight" />
                        选中为根据所选角色变更用户权限，否则保持用户权限不变
                    </div>
                </div>
                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">说明</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="remark" name="obj[remark]" rows="3" placeholder="说明" data-bind="value:remark"></textarea>
                    </div>
                </div>
            </div>

            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:save">保存</button>
                        <button type="button"  class="btn btn-default" data-bind="click:back">返回</button>
                        <input type="hidden"  id="userId" name="obj[userId]" data-bind="value:userId">
                    </div>
                </div>

            </div>
        </form>
    </div>

<!-- </section> -->

<script>
    var view;
    $(function(){
        view=new ViewModel(<?php echo json_encode($data) ?>);
        ko.applyBindings(view);
        $("#roleIds").selectpicker();
        $("#roleIds").selectpicker('val',[<?php echo $data["role_ids"] ?>]);
    });
    function ViewModel(option)
    {
        var defaults = {
            user_id:0,
            role_ids:"0",
            is_right_role:0,
            remark: ""
        };
        var o = $.extend(defaults, option);
        var self=this;
        self.userId=ko.observable(o.user_id);
        self.roleIds=ko.observable(o.role_ids);
        self.main_role_id=ko.observable(o.main_role_id);
        self.phone=ko.observable(o.phone);
        self.email=ko.observable(o.email);
        self.isRoleRight=ko.observable((o.is_right_role==1));
        self.remark=ko.observable(o.remark);

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
            formData=formData+"&obj[rIds]="+self.roleIds();
            $.ajax({
                type: 'POST',
                url: '/user/save',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        location.href="/user/";
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