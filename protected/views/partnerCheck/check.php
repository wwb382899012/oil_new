<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">请在下面填写</h3>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <table width="850" border="0" align="center">
                    <tr>
                        <td height="35" style="font-weight: 700;font-size: 14px;">企业名称</td>
                        <td style="text-align:left;" colspan="5"><a href="/PartnerApply/detail/?id=<?php echo $data['partner_id'] ?>&t=1" target="_blank"><?php echo $data["partner_id"]."&emsp;&emsp;".$data["partner_name"] ?></a></td>
                    </tr>
                    <tr>
                        <td height="35" width="65" style="font-weight: 700;font-size: 14px;">企业类别</td>
                        <td width="100"><?php echo PartnerApplyService::getPartnerType($data["type"]) ?></td>
                        <td width="70" style="font-weight: 700;font-size: 14px;">商务分类</td>
                        <td width="70"><?php echo $this->map['partner_level'][$data["custom_level"]] ?></td>
                        <td width="100" style="font-weight: 700;font-size: 14px;">拟申请额度(万元)</td>
                        <td width="140"><?php echo '￥ '.number_format($data["apply_amount"]/10000/100,2) ?></td>
                    </tr>
                    <tr>
                        <td height="35" style="font-weight: 700;font-size: 14px;">系统分类</td>
                        <td><?php echo $this->map['partner_level'][$data["auto_level"]] ?></td>
                        <td style="font-weight: 700;font-size: 14px;">风控分类</td>
                        <td><?php echo $this->map['partner_level'][$data["risk_level"]] ?></td>
                        <td style="font-weight: 700;font-size: 14px;">拟授予额度(万元)</td>
                        <td><?php echo '￥ '.number_format($data["o_credit_amount"]/10000/100,2) ?></td>
                    </tr>
                </table>
                <hr/>
                <table width="850" border="0" align="center">
                    <tr>
                        <td height="35" width="100" style="font-weight: 700;font-size: 14px;">补充资料</td>
                        <td colspan="3" style="text-align:left;vertical-align:middle;">
                        <?php 
                        if(!empty($partnerAttachments[3201]) && count($partnerAttachments[3201])>=1){
                            foreach ($partnerAttachments[3201] as $attach) {
                               // echo "<br/>";
                               echo "<p><a href='/".$this->getId()."/getFile/?id=".$attach["id"]."&fileName=".$attach['name']."'  target='_blank' class='btn btn-primary btn-xs'>".$attach['name']."</a></p>";
                               // echo "<br/>";
                            }
                        }else{
                            echo "无";
                        }
                        ?>
                        </td>
                    </tr>
                </table>
                <hr/>
                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">审核说明</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="remark" name= "obj[remark]" rows="3" placeholder="审核说明" data-bind="value:remark"></textarea>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="form-group">
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
                        <input type='hidden' name='obj[obj_id]' data-bind="value:obj_id" />
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
    });
    function ViewModel(option)
    {
        var defaults = {
            obj_id:0,
            check_id:0,
            remark: ""
        };
        var o = $.extend(defaults, option);
        var self=this;
        self.obj_id=ko.observable(o.obj_id);
        self.check_id=ko.observable(o.check_id);

        self.remark=ko.observable(o.remark).extend({required:true});
        self.status = ko.observable(o.status);
        self.errors = ko.validation.group(self);
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
        self.reject=function(){
            layer.confirm("您确定要拒绝当前信息的审核，该操作不可逆？", {icon: 3, title: '提示'}, function(index){
                self.status(0);
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
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error:function (data) {
                    layer.alert("保存失败！"+data.responseText, {icon: 5});
                }
            });
        }

        self.back=function(){
            history.back();
        }
    }

</script>