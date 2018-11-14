<script src="/js/bootstrap3-typeahead.min.js"></script>
<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css" />
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">请在下面填写</h3>
        </div><!--end box box-header-->
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <table width="900" border="0" align="center">
                  <tr>
                    <td height="40" style="font-weight: 700;font-size: 14px;">&emsp;企业名称</td>
                    <td style="text-align:left;">&nbsp;<a href="/PartnerApply/detail/?id=<?php echo $data['partner_id'] ?>&t=1" target="_blank"><?php echo $data["partner_id"]."&emsp;&emsp;".$data["partner_name"] ?></a></td>
                    <td width="80" style="font-weight: 700;font-size: 14px;">企业类别</td>
                    <td width="120">&emsp;<?php echo PartnerApplyService::getPartnerType($data["type"]) ?></td>
                    <td width="150" style="font-weight: 700;font-size: 14px;">拟申请额度(万元)</td>
                    <td width="110" style="text-align:right;"><?php echo '￥ '.number_format($data["apply_amount"]/10000/100,2) ?></td>
                  </tr>
                  <tr>
                    <td width="100" height="40"><span style="font-weight: 700;font-size: 14px;">&emsp;系统分类</span></td>
                    <td width="480">
                        <span style="text-align:left;">&nbsp;<?php echo $this->map['partner_level'][$data["auto_level"]] ?></span>
                        <span style="font-weight: 700;font-size: 14px;">
                        &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;商务分类</span>
                        &emsp;<?php echo $this->map['partner_level'][$data["custom_level"]] ?>
                    </td>
                    <td style="font-weight: 700;font-size: 14px;">风控分类</td>
                    <td>&emsp;<?php echo $this->map['partner_level'][$data["risk_level"]] ?></td>
                    <td style="font-weight: 700;font-size: 14px;">风控初审额度(万元)</td>
                    <td style="text-align:right;"><?php echo '￥ '.number_format($data["o_credit_amount"]/10000/100,2) ?></td>
                  </tr>
                </table>
                <hr/>
                <?php if(!empty($supplyAttachments[3201]) && count($supplyAttachments[3201])>=1){ ?>
                <table width="900" border="0" align="center">
                    <tr>
                        <td height="40" width="100" style="font-weight: 700;font-size: 14px;">&emsp;补充资料</td>
                        <td colspan="3" style="text-align:left;vertical-align:middle;">
                        <?php 
                        if(!empty($supplyAttachments[3201]) && count($supplyAttachments[3201])>=1){
                            foreach ($supplyAttachments[3201] as $attach) {
                               // echo "<br/>";
                               echo "&nbsp;<p><a href='/partnerReview/getFile/?id=".$attach["id"]."&fileName=".$attach['name']."'  target='_blank' class='btn btn-primary btn-xs'>".$attach['name']."</a></p>";
                               // echo "<br/><br/>";
                            }
                        }else{
                            echo "无";
                        }
                        ?>
                        </td>
                    </tr>
                </table>
                <hr/>
                <?php } ?>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">评审时间<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="start_time" name= "obj[start_time]" placeholder="评审时间" data-bind="value:start_time">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">评审人员<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <select multiple="" class="form-control selectpicker" title="请选择评审人员" id="uIds" name="obj[uIds]" data-live-search="true" data-bind="selectedOptions:uIds,valueAllowUnset: true">
                            <?php
                            $temps = UserService::getPartnerReviewUsers();
                            foreach ($temps as $key => $value) {
                                $temps[$key]['pinyin'] = PartnerService::getFirstWord($value['name']);
                            }
                            $users = PartnerService::array_msort($temps,array('pinyin'=>SORT_ASC,'name'=>SORT_ASC));
                            // print_r($users);die;
                            foreach($users as $v) {
                                echo "<option value='" . $v["user_id"] . "'>" . $v["name"] . "</option>";
                            }?>
                        </select>
                    </div>
                    <label for="type" class="col-sm-2 control-label">我方资金来源<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <select class="form-control" id="level3" name="obj[level3]" data-bind="value:level3">
                            <option value=''>请选择资金来源类型</option>
                            <?php foreach($this->map["mine_fund_type"] as $k=>$v)
                            {
                                echo "<option value='".$k."'>".$v."</option>";
                            }?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">业务类型<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <select class="form-control" id="level2" name="obj[level2]" data-bind="value:level2">
                            <option value=''>请选择业务类型</option>
                            <?php foreach($this->map["partner_business_type"] as $k=>$v)
                            {
                                echo "<option value='".$k."'>".$v."</option>";
                            }?>
                        </select>
                    </div>
                    <label for="type" class="col-sm-2 control-label">企业付款方式<span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <select class="form-control" id="level4" name="obj[level4]" data-bind="value:level4">
                            <option value=''>请选择付款方式</option>
                            <?php foreach($this->map["partner_pay_type"] as $k=>$v)
                            {
                                echo "<option value='".$k."'>".$v."</option>";
                            }?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">业务操作条件</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="condition1" name= "obj[condition1]" rows="3" placeholder="业务操作条件" data-bind="value:condition1"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">管理要求</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="condition2" name= "obj[condition2]" rows="3" placeholder="管理要求" data-bind="value:condition2"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">其他意见</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="content" name= "obj[content]" rows="3" placeholder="其他意见" data-bind="value:content"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">拟授予额度</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <input type="text" class="form-control" id="credit_amount" name="obj[credit_amount]" placeholder="确认最终额度" data-bind="moneyWan:credit_amount">
                            <span class="input-group-addon">万元</span>
                        </div>

                    </div>
                </div>
                <?php
                    if(empty($partnerAttachments))
                    {
                        $partnerAttachments=PartnerReview::getReviewAttachments($data["review_id"]);
                    }
                    $attachmentTypeKey="partner_review_attachment_type";
                    $this->showAttachmentsEditMulti($data["review_id"],$data,$attachmentTypeKey,$partnerAttachments);
                ?>
            </div><!--end box-border-->
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="saveButton" class="btn btn-primary" data-bind="click:temp">保存</button>&nbsp;
                        <button type="button" class="btn btn-success" data-bind="click:pass">同意</button>&nbsp;
                        <button type="button" class="btn btn-danger" data-bind="click:reject">否决</button>&nbsp;
                        <button type="button" class="btn btn-warning" data-bind="click:unreview">补资料无需再评</button>&nbsp;
                        <button type="button" class="btn btn-danger" data-bind="click:review">补资料需再评审</button>&nbsp;
                        <button type="button" class="btn btn-default" data-bind="click:back">返回</button>
                        <input type='hidden' name='obj[partner_id]' data-bind="value:partner_id" />
                        <input type='hidden' name='obj[review_id]' data-bind="value:review_id" />
                        <input type='hidden' name='obj[is_temp_save]' data-bind="value:is_temp_save"/>
                    </div>
                </div>
            </div>
        </form>
    </div><!--end box box-primary-->
</section><!--end content-->

<script>
    var view;
    $(function () {
        view=new ViewModel(<?php echo json_encode($data) ?>);
        ko.applyBindings(view);
        $("#start_time").datetimepicker({format:'yyyy-mm-dd',minView:'month'});
        $("#uIds").selectpicker();
        $("#uIds").selectpicker('val',[<?php echo $data["user_ids"] ?>]);
    });
    function ViewModel(option) {
        var defaults={
            review_id:"",
            start_time:"",
            partner_id:"",
            credit_amount:"",
            condition1:"",
            condition2:"",
            content:"",
            user_ids:"",
            level2:"",
            level3:"",
            level4:"",
            is_temp_save: 0 //是否暂存
        };
        var o=$.extend(defaults,option);
        var self=this;
        self.partner_id=ko.observable(o.partner_id);
        self.review_id=ko.observable(o.review_id);
        self.credit_amount=ko.observable(o.credit_amount).extend({money:true});
        self.condition1=ko.observable(o.condition1);
        self.condition2=ko.observable(o.condition2);
        self.content=ko.observable(o.content);
        self.uIds=ko.observable(o.user_ids).extend({required:{params:true,message:"请选择评审人员"}});
        self.level2=ko.observable(o.level2).extend({required:{params:true,message:"请选择业务类型"}});
        self.level3=ko.observable(o.level3).extend({required:{params:true,message:"请选择资金来源"}});
        self.level4=ko.observable(o.level4).extend({required:{params:true,message:"请选择付款方式"}});
        self.start_time=ko.observable(o.start_time).extend({required:{params:true,message:"请选择评审时间"}});
        // self.remark=ko.observable(o.remark);
        self.status = ko.observable(o.status);
        self.actionState = ko.observable(0);
        self.is_temp_save = ko.observable(o.is_temp_save);
        self.errors=ko.validation.group(self);
        self.isValid=function () {
            return self.errors().length===0;
        }

        self.pass=function(){
            layer.confirm("您确定同意当前会议评审，该操作不可逆？", {icon: 3, title: '提示'}, function(index){
                self.status(<?php echo PartnerReview::STATUS_REVIEW_PASS ?>);
                self.save();
                layer.close(index);
            });
        }
        self.reject=function(){
            layer.confirm("您确定否决当前会议评审，该操作不可逆？", {icon: 3, title: '提示'}, function(index){
                self.status(<?php echo PartnerReview::STATUS_REVIEW_REJECT ?>);
                self.save();
                layer.close(index);
            });
        }
        self.review=function(){
            layer.confirm("您确定补充资料后需再评审，该操作不可逆？", {icon: 3, title: '提示'}, function(index){
                self.status(<?php echo PartnerReview::STATUS_NEED_REVIEW ?>);
                self.save();
                layer.close(index);
            });
        }
        self.unreview=function(){
            layer.confirm("您确定只要补充资料，该操作不可逆？", {icon: 3, title: '提示'}, function(index){
                self.status(<?php echo PartnerReview::STATUS_NOT_REVIEW ?>);
                self.save();
                layer.close(index);
            });
        }

        self.temp=function(){
            self.is_temp_save(1);
            self.status(<?php echo PartnerReview::STATUS_REVIEW_NEW ?>);
            self.save();
        }

        self.save=function () {
            if(!self.isValid()){
                self.errors.showAllMessages();
                return;
            }

            if (self.actionState() == 1)
                return;

            self.actionState(1);

            var formData = $("#mainForm").serialize();
            formData += "&obj[checkStatus]="+self.status()+"&obj[uIds]="+self.uIds();
            $.ajax({
                type:"POST",
                url:"/partnerReview/save",
                data:formData,
                dataType:"json",
                success:function (json) {
                    self.actionState(0);
                    if (json.state == 0) {
                        location.href="/partnerReview/detail/?id=<?php echo $data['partner_id'] ?>";
                    }
                    else {
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error:function (data) {
                    self.actionState(0);
                    layer.alert("保存失败！"+data.responseText, {icon: 5});
                }
            });
        }

        self.back=function () {
            history.back();
        }

    }
</script>
