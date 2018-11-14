<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">请在下面填写</h3>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label">用户名</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="user_name" name="obj[user_name]" placeholder="用户名" data-bind="value:user_name">
                    </div>
                    <label class="col-sm-2 control-label">企业微信Id</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="identity" name="obj[identity]" placeholder="企业微信Id" data-bind="value:identity">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">密码</label>
                    <div class="col-sm-4">
                        <input type="password" class="form-control" id="password" name="obj[password]" placeholder="密码"  data-bind="value:password">
                        <span class="help-block text-red" data-bind="visible:userId()>0">如果不修改密码，请留空！</span>
                    </div>

                    <label class="col-sm-2 control-label">确认密码</label>
                    <div class="col-sm-4">
                        <input type="password" class="form-control" id="confirmPassword" name="obj[confirmPassword]" placeholder="确认密码"  data-bind="value:confirmPassword">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">姓名</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="name" name="obj[name]" placeholder="姓名" data-bind="value:name">
                    </div>
                    <label class="col-sm-2 control-label">微信号</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="weixin" name="obj[weixin]" placeholder="微信号" data-bind="value:weixin">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">手机</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="phone" name="obj[phone]" placeholder="手机" data-bind="value:phone">
                    </div>
                    <label class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="email" name="obj[email]" placeholder="Email" data-bind="value:email">
                    </div>
                </div>
                <div class="form-group">
                    <label for="category" class="col-sm-2 control-label">主角色</label>
                    <div class="col-sm-4">
                        <select class="form-control" title="请选择主角色" id="main_role_id" name="obj[main_role_id]" data-bind="selectpicker:main_role_id,valueAllowUnset: true">
                            <option value="-1">请选择主角色</option>
                            <?php
                            foreach($roles as $v) {
                                echo "<option value='" . $v["role_id"] . "'>" . $v["role_name"] . "</option>";
                            }?>
                        </select>
                    </div>
                    <label for="status" class="col-sm-2 control-label">状态</label>
                    <div class="col-sm-4">
                        <select class="form-control selectpicker" id="status" name="obj[status]" data-bind="value:status,valueAllowUnset: true">
                            <option value="-1">请选择状态</option>
                            <?php
                            foreach ($this->map["user_status"] as $key=>$val){
                                echo "<option value='$key'>$val</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="category" class="col-sm-2 control-label">所属角色</label>
                    <div class="col-sm-4">
                        <select multiple="" class="form-control" title="请选择所属角色" id="roleIds" name="obj[roleIds]" data-bind="selectpicker:roleIds,valueAllowUnset: true">
                            <!-- <option value="-1">请选择所属角色</option> -->
                            <?php
                            foreach($roles as $v) {
                                echo "<option value='" . $v["role_id"] . "'>" . $v["role_name"] . "</option>";
                            }?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="paymentType" class="col-sm-2 control-label">权限变更</label>
                    <div class="col-sm-10 skin skin-flat checkbox">
                        <input type="checkbox" id="isRoleRight" name="obj[isRoleRight]" data-bind="checked:isRoleRight" />
                        &nbsp;选中为根据所选角色变更用户权限，否则保持用户权限不变
                    </div>
                </div>
                <div class="form-group">
                    <label for="category" class="col-sm-2 control-label">主体公司</label>
                    <div class="col-sm-10">
                        <select multiple="" class="form-control" title="请选择主体公司" id="corpIds" name="obj[corpIds]" data-bind="selectpicker:corpIds,valueAllowUnset: true">
                            <!-- <option value="-1">请选择主体公司</option> -->
                            <?php
                            foreach($corps as $v) {
                                echo "<option value='" . $v["corporation_id"] . "'>" . $v["name"] . "</option>";
                            }?>
                        </select>
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
                        <input type='hidden' name='obj[userId]' data-bind="value:userId" />
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
        $('.skin-flat input').iCheck({
          checkboxClass: 'icheckbox_flat-green',
          radioClass: 'iradio_flat-green'
        });
    });
    function ViewModel(option)
    {
        var defaults = {
            user_id:0,
            user_name:"",
            name:"",
            role_ids:"0",
            corp_ids:"0",
            main_role_id:"-1",
            phone:"",
            email:"",
            is_right_role:0,
            /*status:"1",*/
            identity:"",
            status:"-1",
            weixin:"",
            remark: ""
        };
        var o = $.extend(defaults, option);
        var self=this;

        self.userId=ko.observable(o.user_id);
        self.weixin=ko.observable(o.weixin);
        self.identity=ko.observable(o.identity);
        self.roleIds=ko.observable(o.role_ids);
        self.corpIds=ko.observable(o.corp_ids).extend({custom: {
            params: function (v) {
                if (v != 0) {
                    return true;
                }
                else
                    return false;
            },
            message: "请选择主体公司"
        }
        });
        self.main_role_id=ko.observable(o.main_role_id).extend({custom: {
            params: function (v) {
                if (v > -1) {
                    return true;
                }
                else
                    return false;
            },
            message: "请选择主角色"
        }
        });
        self.phone=ko.observable(o.phone).extend({required:true});
        self.email=ko.observable(o.email).extend({email:true});
        self.isRoleRight=ko.observable((o.is_right_role==1));
        self.remark=ko.observable(o.remark);

        self.user_name=ko.observable(o.user_name).extend({required:true});
        self.password=ko.observable("").extend({custom: {
            params: function (v) {
                if (self.userId()>0 || v!="") {
                    return true;
                }
                else
                    return false;
            },
            message: "密码不得为空"
        }
        });
        self.name=ko.observable(o.name).extend({required:true});
        self.confirmPassword=ko.observable("").extend({custom: {
            params: function (v) {
                if (v==self.password()) {
                    return true;
                }
                else
                    return false;
            },
            message: "确认密码必须与密码一致"
        }
        });

        self.status=ko.observable(o.status).extend({custom: {
            params: function (v) {
                if (v > -1) {
                    return true;
                }
                else
                    return false;
            },
            message: "请选择状态"
        }
        });

        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };

        self.init=function(){
            self.type.isModified(false);
            self.status.isModified(false);
        }

        self.save=function(){
            if(!self.isValid())
            {
                self.errors.showAllMessages();
                return;
            }
            var formData=$("#mainForm").serialize();
            formData=formData+"&obj[rIds]="+self.roleIds()+"&obj[cIds]="+self.corpIds();
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