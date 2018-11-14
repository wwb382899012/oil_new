<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">数据展示及导出自动创建  <small>请在下面填写</small></h3>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">ControllerName</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="controller" name="data[controller]" placeholder="ControllerName"  data-bind="value:controller">
                    </div>
                </div>
                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">Sql</label>
                    <div class="col-sm-8">
                        <textarea class="form-control" id="sql" name= "data[sql]" rows="6" placeholder="Sql" data-bind="value:sql"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">要导出字段</label>
                    <div class="col-sm-8">
                        <textarea class="form-control" id="export" name= "data[export]" rows="6" placeholder="要导出字段" data-bind="value:exportFields"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">SearchFormItems</label>
                    <div class="col-sm-8">
                        <textarea class="form-control" id="search" name= "data[search]" rows="6" placeholder="SearchFormItems" data-bind="value:search"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">GridColumns</label>
                    <div class="col-sm-8">
                        <textarea class="form-control" id="columns" name= "data[columns]" rows="6" placeholder="GridColumns" data-bind="value:columns"></textarea>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:save">创建</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
<script>
    var view;
    $(function () {
        view=new ViewModel();
        ko.applyBindings(view);
    });

    function ViewModel(option){
        var defaults={
            sql:"select * from t_system_user {where} order by user_id desc",
            columns:"'name:text:姓名','user_name:text:用户名',",
            exportFields:"name 姓名,user_id"
        };
        var o=$.extend(defaults,option);
        var self=this;
        self.controller=ko.observable();
        self.sql=ko.observable(o.sql);
        self.search=ko.observable();
        self.columns=ko.observable(o.columns);
        self.exportFields=ko.observable(o.exportFields);

        self.errors=ko.validation.group(self);
        self.isValid=function () {
            return self.errors().length===0;
        }

        self.buttonText=ko.observable("创建");
        self.save=function () {
            if(!self.isValid()){
                self.errors.showAllMessages();
                return;
            }
            self.buttonText("保存中 "+inc.loadingIco);
            var formData=$("#mainForm").serialize();
            $.ajax({
                type:"POST",
                url:"/auto/create",
                data:formData,
                dataType:"json",
                success:function (json) {
                    self.buttonText("创建");
                    if (json.state == 0)
                    {
                        inc.alert("创建成功");
                    }
                   else
                       inc.warning(json.data);
                },
                error:function (data) {
                    self.buttonText("创建");
                    inc.warning("创建失败："+data.responseText);
                }
            });
        }
    }
</script>