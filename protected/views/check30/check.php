<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">请在下面填写</h3>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">企业信息</label>
                    <div class="col-sm-6">
                        <p class="form-control-static">
                            <a href="/partnerApply/detail/?id=<?php echo $data["partner_id"] ?>&t=1" target="_blank"><?php echo $data['partner_id']."&emsp;&emsp;".$data["name"] ?></a>
                            <?php 
                                $keyNo = PartnerService::getKeyNo($data['name']);
                                if(!empty($keyNo))
                                    echo '&emsp;&emsp;<a href="http://www.qichacha.com/firm_'.$keyNo.'.shtml" target="_blank" class="btn btn-primary btn-xs">企查查详情页</a>';
                            ?>
                        </p>
                    </div>
                    <label for="type" class="col-sm-2 control-label">类别</label>
                    <div class="col-sm-2">
                        <p class="form-control-static"><?php echo PartnerApplyService::getPartnerType($data['type']); ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">商务强制分类</label>
                    <div class="col-sm-2">
                        <p class="form-control-static"><?php echo $this->map["partner_level"][$data["custom_level"]] ?></p>
                    </div>
                    <label class="col-sm-2 control-label">系统检测分类</label>
                    <div class="col-sm-2">
                        <p class="form-control-static"><?php echo $this->map["partner_level"][$data["auto_level"]] ?></p>
                    </div>
                    <label class="col-sm-2 control-label">企业类型</label>
                    <div class="col-sm-2">
                        <p class="form-control-static"><?php echo $this->map["business_type"][$data["business_type"]] ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">经营情况 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="business_describe" name="obj[business_describe]" rows="3" placeholder="经营情况" data-bind="value:business_describe"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">业务合作风险点 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="risk_content" name="obj[risk_content]" rows="3" placeholder="业务合作风险点" data-bind="value:risk_content"></textarea>
                    </div>
                </div>
                <?php
                $type =PartnerService::getPartnerRiskType($data["type"]) ;
                if($type==Partner::TYPE_DOWN)
                    $checkArr = $this->checkInfo[$type];
                else
                    $checkArr = $this->checkInfo[$type][$data["business_type"]];

                /*$tArr = explode(',', $data['type']);
                if(in_array(2, $tArr)){
                    $type = 2;
                    $checkArr = $this->checkInfo[$type];
                }else{
                    $type=1;
                    $checkArr = $this->checkInfo[$type][$data["business_type"]];
                }*/
                foreach ($checkArr as $v)
                {
                    ?>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $v["name"] ?>
                        <?php 
                        if($v['label']!="1" && $type==2 && ((((!empty($data["custom_level"]) && $data["custom_level"]==3) || (empty($data["custom_level"]) && $data["auto_level"]==3)) && ($v["key"]==208 || $v["key"]==209)) || ($v["key"]!=208 && $v["key"]!=209 && $v["key"]!=201))) 
                            echo ' <span class="text-red fa fa-asterisk"></span>'; 
                        ?>
                        </label>
                        <?php if($type==2 && $v['text']==1){ ?>
                        <div class="col-sm-10">
                            <textarea type="text" class="form-control" id="info_<?php echo $v["key"] ?>" name= "info[<?php echo $v["key"] ?>]" rows="3" placeholder="<?php echo $v["name"] ?>" data-bind="value:info().info_<?php echo $v["key"] ?>" ></textarea>
                        </div>
                        <?php }else{ ?>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="info_<?php echo $v["key"] ?>" name= "info[<?php echo $v["key"] ?>]" placeholder="<?php echo $v["name"] ?>" data-bind="value:info().info_<?php echo $v["key"] ?>" >
                        </div>
                        <?php } ?>
                    </div>
                    <?php
                }
                ?>
                <div class="form-group">
                    <label for="runs_state" class="col-sm-2 control-label">评级 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <select class="form-control" title="请选择分类" id="level" name="obj[level]" data-bind="optionsCaption: '请选择分类',value:level,valueAllowUnset: true">
                            <option value="0">请选择分类</option>
                            <?php
                            foreach ($this->map["partner_level"] as $k => $v) {
                                echo "<option value='" . $k . "'>" . $v . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <?php if($type==2){ ?>
                <div class="form-group">
                    <label for="paid_up_capital" class="col-sm-2 control-label">拟授予额度 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <input type="text" class="form-control" id="credit_amount" name="obj[credit_amount]" placeholder="公司授信额度" data-bind="moneyWan:credit_amount">
                            <span class="input-group-addon">万元</span>
                        </div>

                    </div>
                    <div class="col-sm-1">
                        <button type="button" id="calculateButton" class="btn btn-primary" data-bind="click:calculate,html:calcBtnText">点击计算额度</button>
                    </div>
                </div>
                <?php } ?>
                <?php if(!empty($this->checkDetailFile)) { include $this->checkDetailFile;} ?>
                <div class="form-group">
                    <label for="remark" class="col-sm-2 control-label">风控结论 <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="remark" name= "obj[remark]" rows="3" placeholder="风控结论" data-bind="value:remark"></textarea>
                    </div>
                </div>
                <?php if(!empty($this->extraCheckItemFile)) { include $this->extraCheckItemFile;} ?>
            </div>
            <?php
            if(empty($attachments))
            {
                $attachments=PartnerService::getCheckAttachments($data["detail_id"]);
            }
            $attachmentTypeKey="partner_check_main_attachment_type";
            $this->showAttachmentsEditMulti($data["detail_id"],$data,$attachmentTypeKey,$attachments);
            ?>
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <?php if($this->checkButtonStatus["pass"]==1){ ?>
                            <?php if($type==1){ ?>
                            <button type="button" id="passButton" class="btn btn-success" data-bind="click:pass">通过</button>&nbsp;
                            <?php }else{ ?>
                            <button type="button" id="passButton" class="btn btn-success" data-bind="click:pass,enable: isPass">通过</button>&nbsp;
                            <?php }?>
                        <?php }?>
                        <?php if($this->checkButtonStatus["back"]==1){ ?>
                            <button type="button" id="checkBackButton" class="btn btn-danger" data-bind="click:checkBack">驳回</button>&nbsp;
                        <?php } ?>
                        <?php if($this->checkButtonStatus["reject"]==1){ ?>
                            <button type="button" id="rejectButton" class="btn btn-danger" data-bind="click:reject,enable: isReject">拒绝</button>&nbsp;
                        <?php } ?>
                        <?php if($type==2) {?>
                            <button type="button" id="checkBackButton5" class="btn btn-danger" data-bind="click:check5,enable: isCheck5">需现场风控</button>&nbsp;

                            <button type="button" id="checkBackButton6" class="btn btn-danger" data-bind="click:check6,enable: isCheck6">需评审</button>&nbsp;
                        <?php } ?>
                        <button type="button"  class="btn btn-default" data-bind="click:back">返回</button>
                        <input type='hidden' name='obj[obj_id]' data-bind="value:obj_id" />
                        <input type='hidden' name='obj[check_id]' data-bind="value:check_id" />
                        <input type='hidden' name='obj[detail_id]' data-bind="value:detail_id" />
                    </div>
                </div>

            </div>
        </form>
    </div>
    <!-- amount calculate modal -->
    <div class="modal fade draggable-modal" id="calculateModel" tabindex="-1" role="dialog" aria-labelledby="modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="calculate">额度计算</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" role="form" id="childForm">
                        <div class="box-body">
                            <?php
                                foreach ($this->amountInfo as $v)
                                {
                                    ?>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label"><?php echo $v["name"] ?><?php if($v['label']!="1") echo ' <span class="text-red fa fa-asterisk"></span>'; ?></label>
                                        <div class="col-sm-4">
                                            <?php if($v['key']==310){ ?>
                                            <textarea type="text" class="form-control" id="amount_<?php echo $v["fieldName"] ?>" name= "amount[<?php echo $v["fieldName"] ?>]" rows="3" placeholder="<?php echo $v["name"] ?>" data-bind="value: amount().<?php echo $v['fieldName'] ?>" ></textarea>
                                            <?php }else if($v['key']==307){ ?>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="amount_<?php echo $v["fieldName"] ?>" name= "amount[<?php echo $v["fieldName"] ?>]" placeholder="<?php echo $v["name"] ?>" data-bind="moneyWan: amount().<?php echo $v['fieldName'] ?>" disabled>
                                                <span class="input-group-addon">万元</span>
                                            </div>
                                            <?php }else if($v['key']==308 || $v['key']==309){ ?>
                                                    <input type="text" class="form-control" id="amount_<?php echo $v["fieldName"] ?>" name= "amount[<?php echo $v["fieldName"] ?>]" placeholder="<?php echo $v["name"] ?>" data-bind="value: amount().<?php echo $v['fieldName'] ?>">
                                            <?php }else{ ?>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="amount_<?php echo $v["fieldName"] ?>" name= "amount[<?php echo $v["fieldName"] ?>]" placeholder="<?php echo $v["name"] ?>" data-bind="moneyWan: amount().<?php echo $v['fieldName'] ?>">
                                                    <span class="input-group-addon">万元</span>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <?php if($v['key']==307){ ?>
                                        <div class="col-sm-1">
                                            <button type="button" id="obtainButton" class="btn btn-primary" data-bind="click:obtain,html:obtBtnText">获取授信额度</button>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <?php
                                }
                            ?>
                        </div>
                        <?php
                        if(empty($attachments))
                        {
                            $attachments=PartnerService::getCheckAttachments($data["detail_id"]);
                        }
                        $attachmentTypeKey="partner_check_compute_attachment_type";
                        $this->showAttachmentsEditMulti($data["detail_id"],$data,$attachmentTypeKey,$attachments);
                        ?>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</section>

<script>
    var view;
    $(function(){
        view=new ViewModel(<?php echo json_encode($data) ?>,<?php echo json_encode($amount) ?>);
        ko.applyBindings(view);
        //view.formatAmounts(<?php echo json_encode($amount) ?>)
        // $("#amount_start_date,#amount_end_date").datetimepicker({format: 'yyyy-mm-dd', minView: 'month'});
    });
    function ViewModel(option,amounts)
    {
        var defaults = {
            obj_id:0,
            check_id:0,
            detail_id:0,
            business_describe:"",
            risk_content:"",
            level:"0",
            credit_amount:"",
            riskType:1,
            remark: ""
        };
        var o = $.extend(defaults, option);
        var self=this;
        self.obj_id=ko.observable(o.obj_id);
        self.check_id=ko.observable(o.check_id);
        self.detail_id=ko.observable(o.detail_id);
        self.state=ko.observable(1);
        self.riskType=ko.observable(o.riskType);
        self.level=ko.observable(o.level).extend({custom: {
            params: function (v) {
                if (self.state()==0 || v > 0) {
                    return true;
                }
                else
                    return false;
            },
            message: "请选择分类"
        }
        });
        <?php if($type==1){ ?>
        self.credit_amount=ko.observable(o.credit_amount);
        <?php }else{ ?>
        self.credit_amount=ko.observable(o.credit_amount).extend({custom: {
            params: function (v) {
                if (self.state()==0 || v > 0) {
                    return true;
                }
                else
                    return false;
            },
            message: "请填写授信额度"
        }
        });
        <?php } ?>
        self.business_describe=ko.observable(o.business_describe).extend({required:true});
        self.risk_content=ko.observable(o.risk_content).extend({required:true});
        self.remark=ko.observable(o.remark).extend({required:true});
        self.status = ko.observable(o.status);
        self.auto_level = ko.observable(<?php echo $data['auto_level'] ?>);
        self.amount = ko.observable(new AmountModel(amounts));
        self.info   = ko.observable(new InfoModel(option));
        self.actionState = ko.observable(0);
        self.calcBtnText = ko.observable("点击计算额度");
        self.obtBtnText = ko.observable("获取授信额度");
        //self.errors = ko.validation.group(self,{deep: false});
        self.errors = ko.validation.group(self,{deep: false});
        self.isValid = function () {
            return (self.info().isValid() && self.errors().length === 0);
        };

        self.pass=function(){
            layer.confirm("您确定要通过当前信息的审核，该操作不可逆？", {icon: 3, title: '提示'}, function(index){
                self.state(1);
                self.status(1);
                self.save();
                layer.close(index);
            });
        }
        self.reject=function(){
            layer.confirm("您确定要拒绝当前信息的审核，该操作不可逆？", {icon: 3, title: '提示'}, function(index){
                self.state(0);
                self.status(0);
                self.save();
                layer.close(index);
            });
        }
        self.check5=function(){
            layer.confirm("您确定要设置当前信息为需要现场风控，该操作不可逆？", {icon: 3, title: '提示'}, function(index){
                self.state(1);
                self.status(5);
                self.save();
                layer.close(index);
            });
        }
        self.check6=function(){
            layer.confirm("您确定要设置当前信息为需要评审，该操作不可逆？", {icon: 3, title: '提示'}, function(index){
                self.state(1);
                self.status(6);
                self.save();
                layer.close(index);
            });
        }

        self.checkBack=function(){
            layer.confirm("您确定要驳回当前信息的审核，该操作不可逆？", {icon: 3, title: '提示'}, function(index){
                self.state(0);
                self.status(-1);
                self.save();
                layer.close(index);
            });
        }

        self.save=function(){
            if(!self.isValid())
            {
                self.info().errors.showAllMessages();
                self.errors.showAllMessages();
                return;
            }
            if(self.riskType()==2 && self.amount().remark()!=''){
                self.obtain();
            }
            // return;
            var formData=$("#mainForm").serialize();
            formData+="&obj[checkStatus]="+self.status()+"&info="+JSON.stringify(ko.toJS(self.info()));
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

        self.level.subscribe(function(v){
            self.isPass();
            self.isCheck5();
            self.isCheck6();
        });

        self.credit_amount.subscribe(function(v){
            self.isPass();
            self.isCheck5();
            self.isCheck6();
        });

        self.isPass = ko.computed(function () {
            return  (self.auto_level()==1 && self.level()==1) || (self.level()<=2 && self.level()>=1 && self.credit_amount()<<?php echo $this->creditAmount[1]['amount'] ?>);
         },self);
        self.isReject = ko.computed(function () {
            return  (self.level()==4);
         },self);
        self.isCheck5 = ko.computed(function () {
            return  (self.level()!=1 && self.level()!=4 && self.level()>=1) || (self.level()==2 && self.credit_amount()>=<?php echo $this->creditAmount[1]['amount'] ?>);
         },self);
        self.isCheck6 = ko.computed(function () {
            return  (self.level()==2 && self.credit_amount()>=<?php echo $this->creditAmount[1]['amount'] ?> && self.credit_amount()<<?php echo $this->creditAmount[2]['amount'] ?>);
         },self);

        self.back=function(){
            history.back();
        }

        //计算合作方授信额度
        self.calculate = function () {
            $("#calculateModel").modal({
                backdrop: true,
                keyboard: false,
                show: true
            });
        }

        //获取合作方授信额度
        self.obtain = function () {
            // console.log(self.amount().isValid());
            if(!self.amount().isValid())
            {
                // console.log(self.amount().errors);
                self.amount().errors.showAllMessages();
                return;
            }
            var data=ko.toJS(self.amount());
            delete data.errors;
            delete data.isValid;
            var amountData={data:data,detail_id:self.detail_id()};
            amountData={amount:amountData};
            if (self.actionState() == 1)
                return;
            self.actionState(1);
            self.obtBtnText("获取授信额度" + inc.loadingIco);
            $.ajax({
                type: "POST",
                url: "/check30/calculate",
                data: amountData,
                dataType: "json",
                success: function (json) {
                    self.actionState(0);
                    self.obtBtnText("获取授信额度");
                    if (json.state == 0) {
                        self.amount().credit_amount(json.data);
                        self.credit_amount(json.data);
                    }
                    else {
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
                    self.actionState(0);
                    self.calcBtnText("获取授信额度");
                    layer.alert("额度获取失败：" + data.responseText, {icon: 5});
                }
            });
        }
    }


    function AmountModel(option) {
        var defaults = {
            <?php foreach ($this->amountInfo as $v) {?>
                <?php echo $v["fieldName"] ?>:"",
            <?php } ?>
        };
        var o = $.extend(defaults, option);
        var self = this;
        <?php
        if($type==Partner::RISK_TYPE_DOWN)
        {
            foreach ($this->amountInfo as $v)
            {
                if($v["key"] == 310 || $v["key"] == 307){ ?>
                self.<?php echo $v["fieldName"] ?>= ko.observable(o.<?php echo $v["fieldName"] ?>);
                <?php   }else if($v["key"] == 308 || $v["key"] == 309){ ?>
                self.<?php echo $v["fieldName"] ?>= ko.observable(o.<?php echo $v["fieldName"] ?>).extend({required: true});
                <?php }else{ ?>
                self.<?php echo $v["fieldName"] ?>= ko.observable(o.<?php echo $v["fieldName"] ?>).extend({
                    money: true,
                    positiveNumber: true
                });
                <?php }
            }
        }
        ?>
        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        }
    }

    function InfoModel(option) {
        var defaults = {
            <?php foreach ($checkArr as $v) {?>
                info_<?php echo $v["key"] ?>:"",
            <?php } ?>
        };
        var o = $.extend(defaults, option);
        var self = this;
        <?php foreach ($checkArr as $v) {
                if($type==1 || $v["key"]==201 || (((!empty($data["custom_level"]) && $data["custom_level"]!=3) || (empty($data["custom_level"]) && $data["auto_level"]!=3)) && ($v["key"]==208 || $v["key"]==209))){ ?>
                    self.info_<?php echo $v["key"] ?>=ko.observable(o.info_<?php echo $v["key"] ?>);
        <?php   }else{ ?>
                    self.info_<?php echo $v["key"] ?>=ko.observable(o.info_<?php echo $v["key"] ?>).extend({required:true});
        <?php }} ?>
        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        }
    }

</script>