<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">请在下面填写</h3>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">父类商品</label>
                    <div class="col-sm-4">
                        <p class="form-control-static" data-bind="html:parent_name"></p>
                        <input type="hidden" class="form-control" id="parent_id" name= "data[parent_id]" data-bind="value:parent_id">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">商品名称</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="name" name= "data[name]" placeholder="商品名称" data-bind="value:name">
                    </div>
                </div>
                <div class="form-group hide">
                    <label for="type" class="col-sm-2 control-label">商品编码</label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="code" name= "data[code]" placeholder="商品编码" data-bind="value:code">
                    </div>
                </div>


                <div class="form-group">

                    <label class="col-sm-2 control-label">单位</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="unit" name="data[unit]" data-bind="value:unit">
                            <?php foreach($this->map["goods_unit"] as $k=>$v)
                            {
                                echo "<option value='".$v['id']."'>".$v['name']."</option>";
                            }?>
                        </select>
                    </div>
                </div>

                <div class="form-group">

                    <label class="col-sm-2 control-label">状态</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="status" name="data[status]" data-bind="value:status">
                            <?php foreach($this->map["goods_status"] as $k=>$v)
                            {
                                echo "<option value='".$k."'>".$v."</option>";
                            }?>
                        </select>
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
                        <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:save">保存</button>
                        <button type="button"  class="btn btn-default" data-bind="click:back">返回</button>
                        <input type='hidden' name='data[goods_id]' data-bind="value:goods_id" />
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
            goods_id:0,
            name:"",
            parent_id:0,
            parent_name:"",
            code:"",
            unit:1,
            status:1,
            remark:""
        };
        var o=$.extend(defaults,option);
        var self=this;
        self.goods_id=ko.observable(o.goods_id);
        self.name=ko.observable(o.name).extend({required:true});
        self.parent_id=ko.observable(o.parent_id);
        self.parent_name=ko.observable(o.parent_name);
        self.code=ko.observable(o.code);
        self.unit=ko.observable(o.unit);
        self.status=ko.observable(o.status);
        self.remark=ko.observable(o.remark);
        self.errors=ko.validation.group(self);
        self.isValid=function () {
            return self.errors().length===0;
        }

        self.buttonText=ko.observable("保存");
        self.save=function () {
            if(!self.isValid()){
                self.errors.showAllMessages();
                return;
            }
            self.buttonText("保存中 "+inc.loadingIco);
            var formData=$("#mainForm").serialize();
            $.ajax({
                type:"POST",
                url:"/goods/save",
                data:formData,
                dataType:"json",
                success:function (json) {
                    self.buttonText("保存");
                    if(json.state==0){
                        if(document.referrer){
                            location.href=document.referrer;
                        }else{
                            location.href="/goods/";
                        }
                    }else{
                        alert(json.data);
                    }
                },
                error:function (data) {
                    self.buttonText("保存");
                    alert("保存失败："+data.responseText);
                }
            });
        }

        self.back=function () {
            history.back();
        }
    }
</script>