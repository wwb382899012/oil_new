<section class="content">
    <div class="box box-primary">
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
				<?php 
                    include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/cross/head.php";
                    include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/cross/tab.php";
                ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label">本次调货原因</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $data['reason'] ?></p>
                    </div>
                </div>
                <?php 
                    include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/cross/nowDetail.php";
                ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label">调货日期</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $data['cross_date'] ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">审核意见 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="remark" name= "obj[remark]" rows="3" placeholder="审核意见" data-bind="value:remark"></textarea>
                    </div>
                </div>
				<div class="box-footer">
                    <div class="col-sm-offset-2 col-sm-10">
                        <?php if($this->checkButtonStatus["pass"]==1){ ?>
                            <button type="button" id="passButton" class="btn btn-success" data-bind="click:pass">通过</button>
                        <?php } ?>
                        <?php if($this->checkButtonStatus["back"]==1){ ?>
                            <button type="button" id="checkBackButton" class="btn btn-danger" data-bind="click:checkBack">驳回</button>
                        <?php } ?>
                        <?php if($this->checkButtonStatus["reject"]==1){ ?>
                            <button type="button" id="rejectButton" class="btn btn-danger" data-bind="click:reject">拒绝</button>
                        <?php } ?>

                        <button type="button"  class="btn btn-default" data-bind="click:back">返回</button>
                        <input type='hidden' name='obj[check_id]' data-bind="value:check_id" />
                        <input type='hidden' name='obj[project_id]' data-bind="value:project_id" />
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
    var view;
    $(function(){
        view=new ViewModel(<?php echo json_encode($data)?>);
        ko.applyBindings(view);
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
        self.remark=ko.observable(o.remark).extend({required:true,maxLength:512});
        self.actionState = ko.observable(0);

        self.status = ko.observable(o.status);
        self.errors = ko.validation.group(self,{deep: false});
        // self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };

        self.pass=function(){
            layer.confirm("您确定要通过当前信息的审核，该操作不可逆？", {icon: 3, title: '提示'}, function(index){
                self.status(1);
                self.save();
                layer.close(index);
            });
        }

        self.checkBack=function(){
            layer.confirm("您确定要驳回当前信息的审核，该操作不可逆？", {icon: 3, title: '提示'}, function(index){
                self.status(-1);
                self.save();
                layer.close(index);
            });
        }

        self.save=function(){
            if(!self.isValid())
            {
                self.errors.showAllMessages();
                return;
            }
            var formData=$("#mainForm").serialize();
            formData+="&obj[checkStatus]="+self.status();
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

