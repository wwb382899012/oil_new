<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">请在下面填写</h3>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">

                <?php include ROOT_DIR.DIRECTORY_SEPARATOR."protected/views/project/detailBody.php" ?>


                <div class="form-group-title">额度占用申请</div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <table class="table table-striped table-bordered table-condensed table-hover">
                            <thead>
                            <tr>
                                <th style="width:200px;text-align:center">业务员</th>
                                <th style="width:300px;text-align:right;">金额</th>
                                <th style="text-align:left;">状态</th>
                            </tr>
                            </thead>
                            <tbody >
                            <?php
                            if(is_array($apply["items"]))
                            {
                                foreach ($apply["items"] as $item)
                                {
                                    ?>
                                    <tr>
                                        <td style="text-align:center">
                                            <?php echo UserService::getNameById($item["user_id"]) ?>
                                        </td>
                                        <td style="text-align:right;">
                                            <?php echo number_format($item["amount"]/1000000,2) ?> 万元
                                        </td>
                                        <td style="">
                                            <?php echo $this->map["project_credit_apply_detail_status"][$item["status"]] ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">申请说明</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $apply["remark"] ?></p>
                    </div>
                </div>

                <div class="form-group-title">额度占用确认</div>
                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">备注</label>
                    <div class="col-sm-4">
                        <textarea class="form-control" id="remark" name= "data[remark]" rows="3" placeholder="备注" data-bind="value:remark"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">当前可用额度</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><span  data-bind="moneyWanText:balance_amount,css:{'text-danger':!isCanPass()}"></span> 万元</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">申请占用额度</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><span  data-bind="moneyWanText:amount,css:{'text-danger':!isCanPass()}"></span> 万元</p>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <span data-bind="html:buttonText"></span>
                        <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:pass,enable:isCanPass">同意</button>
                        <button type="button" id="saveButton" class="btn btn-danger" data-bind="click:reject">拒绝</button>
                        <button type="button"  class="btn btn-default" data-bind="click:back">返回</button>
                        <input type='hidden' name='data[project_id]' data-bind="value:detail_id" />
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
    var view;
    $(function () {
        <?php $params=$model->getAttributes(array("detail_id","amount"));
        $params["balance_amount"]=$userBalanceAmount;
        ?>
        view=new ViewModel(<?php echo json_encode($params) ?>);
        ko.applyBindings(view);
    });

    function ViewModel(option){
        var defaults={
            detail_id:0,
            amount:0,
            balance_amount:0,
            remark:""
        };
        var o=$.extend(defaults,option);
        var self=this;
        self.detail_id=ko.observable(o.detail_id);
        self.status=ko.observable();
        self.amount=ko.observable(o.amount);
        self.balance_amount=ko.observable(o.balance_amount);
        self.remark=ko.observable(o.remark);
        self.errors=ko.validation.group(self);
        self.isValid=function () {
            return self.errors().length===0;
        }

        self.buttonText=ko.observable();


        self.isCanPass=ko.computed(function () {
            return self.amount()<=self.balance_amount();
        },self);

        self.pass=function()
        {
            if(confirm("您确定要通过当前额度占用信息？该操作不可逆！")){
                self.status(1);
                self.save();
            }

        }
        self.reject=function()
        {
            if(confirm("您确定要拒绝当前额度占用信息？该操作不可逆！")){
                self.status(-1);
                self.save();
            }
        }

        self.save=function () {
            if(!self.isValid()){
                self.errors.showAllMessages();
                return;
            }
            self.buttonText("提交中 "+inc.loadingIco);
            var formData="data="+JSON.stringify(ko.toJS(self));
            $.ajax({
                type:"POST",
                url:"/creditConfirm/save",
                data:formData,
                dataType:"json",
                success:function (json) {
                    self.buttonText("");
                    if(json.state==0){
                        if(document.referrer){
                            location.href=document.referrer;
                        }else{
                            location.href="/creditConfirm/";
                        }
                    }else{
                        alert(json.data);
                    }
                },
                error:function (data) {
                    self.buttonText("");
                    alert("提交失败："+data.responseText);
                }
            });
        }

        self.back=function () {
            history.back();
        }

    }

</script>