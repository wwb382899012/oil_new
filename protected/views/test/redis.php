<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">请在下面填写</h3>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Key</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="key" name= "obj[key]" placeholder="Key" data-bind="value:key">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">Field</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="field" name= "obj[field]" placeholder="Field" data-bind="value:field">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">Value</label>
                    <div class="col-sm-10">
                        <p class="form-control-static" data-bind="html:value"></p>
                    </div>
                </div>
                
            </div>
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:save">保存</button>
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
            account_id:"",
            corporation_id:"",
            account_no:"",
            bank_name:"",
            status:1,
            remark:""
        };
        var o=$.extend(defaults,option);
        var self=this;
        self.key=ko.observable(o.key).extend({required:true});
        self.field=ko.observable(o.field);
        self.value=ko.observable(o.value);

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
                url:"/test/getRedis",
                data:formData,
                dataType:"json",
                success:function (json) {
                    if(json.state==0){
                        self.value(json.data);

                    }else{
                        alertModel(json.data);
                    }
                },
                error:function (data) {
                    alertModel("操作失败："+data.responseText);
                }
            });
        }

    }
</script>