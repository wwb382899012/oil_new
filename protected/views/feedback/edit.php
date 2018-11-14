<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css" />
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">下游收款计划</h3>
            <div class="pull-right box-tools">
                <button type="button"  class="btn btn-default" data-bind="click:back">返回</button>
            </div>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
				<div class="form-group">
                    <label for="type" class="col-sm-2 control-label">销售单价</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">￥ <?php echo number_format($data["down_price"]/100,2) ?></p>
                    </div>
                    <label for="type" class="col-sm-2 control-label">项目名称</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">
                            <a href="/project/detail/?id=<?php echo $data["project_id"] ?>&t=1" target="_blank"><?php echo $data["project_name"] ?></a>
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">销售数量</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $data["down_quantity"] ?></p>
                    </div>
                    <label for="type" class="col-sm-2 control-label">企业名称</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">
                            <a href="/partner/detail/?id=<?php echo $data["partner_id"]?>&t=1" target="_blank"><?php echo $data["customer_name"]?></a>
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">销售金额</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">￥ <?php echo number_format($data["down_amount"]/100,2) ?></p>
                    </div>
                    <label for="type" class="col-sm-2 control-label">结算金额</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">￥ <?php echo number_format($data["settle_amount"]/100,2) ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-1">
                    </div>
                    <div class="col-sm-8">
                        <?php
                        if(!empty($plans))
                        {?>
                            <table class="table table-striped table-bordered table-condensed table-hover">
                                <thead>
                                <tr>
                                    <th style="width:80px;text-align:center">期数</th>
                                    <th style="width:140px;text-align:center">收款间隔</th>
                                    <th style="width:180px;text-align:center">收款日期</th>
                                    <th style="width:140px;text-align:center">收款形式</th>
                                    <th style="width:140px;text-align:center">收款比例</th>
                                    <th style="width:240px;text-align:center">金额(元)</th>
                                    <th style="width:100px;text-align:center">状态</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($plans as $v){ ?>
                                    <tr>
                                        <td style="text-align:center"><?php echo empty($v["period"])?"-":$v["period"] ?></td>
                                        <td style="text-align:center"><?php echo $v["receive_days"] ?></td>
                                        <td style="text-align:center"><?php echo $v["return_date"] ?></td>
                                        <td style="text-align:center">
                                            <?php echo $this->map["receive_time"][$v["receive_type"]] ?>
                                        </td>
                                        <td style="text-align:center"><?php echo $v["rate"]*100 ?>%</td>
                                        <td style="text-align:right" <?php if($v["period"]==$data['period']) echo "class='text-red'" ?>>
                                            <?php echo number_format($v["amount"]/100,2) ?>
                                        </td>
                                        <td style="text-align:center">
                                            <?php echo $this->map["return_plan_status"][$v["status"]] ?>
                                        </td>
                                    </tr>
                                <?php  } ?>
                                </tbody>
                            </table>
                        <?php  }
                        ?>
                    </div>
                </div>
                <div class="form-group"></div>
                <div class="box-header with-border">
                </div>
                <h4 class="box-title">开票计划</h4>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">发票申请日期</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $data['invoice_date'] ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">纳税识别号</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $data['tax_code'] ?></p>
                    </div>
                </div>
                <!-- <div class="form-group">
                    <label class="col-sm-2 control-label">开票对象</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">下游</p>
                    </div>
                </div> -->
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">开票名称</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $data["invoice_name"] ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">开户银行</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $data["bank_name"] ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">银行账号</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $data["bank_account"] ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">开票内容</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $data['content'] ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">含税开票金额</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">￥ <?php echo number_format($data["amount"]/100,2) ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">地址</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $data['address'] ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">联系方式</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $data['phone'] ?></p>
                    </div>
                </div>
                <div class="box-header with-border">
                </div>
                <h4 class="box-title">反馈结果</h4>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">反馈说明</label>
                    <div class="col-sm-7">
                        <textarea class="form-control" id="feedback" name= "obj[feedback]" rows="3" placeholder="请填写税票沟通结果" data-bind="value:feedback"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="content" class="col-sm-2 control-label">
                    <?php 
                        echo "<span>";
                        if(!empty($data['file_url']))
                            echo "<span class='glyphicon glyphicon-ok text-green'></span>&emsp;";
                        else
                            echo '<span class="glyphicon glyphicon glyphicon-remove text-red"></span>&emsp;';
                        echo "</span>";
                        echo '<span class="file-name" type="'. $this->map['feedback_attachment_type'][121]['id'].'" name="'.$this->map['feedback_attachment_type'][121]['name'].'" maxSize="'.$this->map['feedback_attachment_type'][121]['maxSize'].'" fileType="'.$this->map['feedback_attachment_type'][121]['fileType'].'">';
                        if(!empty($data["file_url"])){
                            echo "<a href='/feedback/getFile/?id=".$data["attachment_id"]."&fileName=".$this->map["feedback_attachment_type"][121]['name']."'  target='_blank'>".$this->map["feedback_attachment_type"][121]['name']."</a>";
                        }else
                            echo $this->map["feedback_attachment_type"][121]['name'] ; 
                        echo "</span>";
                    ?>
                    </label>
                    <div class="col-sm-10">
                        <span class="btn btn-success fileinput-button">
                            <?php 
                                if(!empty($data["file_url"])) 
                                    echo '<span class="btn-text">重新上传</span>';
                                else
                                    echo '<span class="btn-text">选择上传文件</span>';
                            ?>
                            <input class="file-upload" type="file"  />
                        </span>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button" id="saveButton" class="btn btn-danger" placeholder="提交" data-bind="click:pass,text:buttonText">提交</button>
                        <button type="button"  class="btn btn-default" data-bind="click:back">返回</button>
                        <input type='hidden' name='obj[invoice_id]' data-bind="value:invoice_id" />
                        <input type='hidden' name='obj[project_id]' data-bind="value:project_id" />
                    </div>
                </div>
            </div>
        </form>
    </div>

</section>

<script>
    var view;
    var upStatus=0;
    var count=0;
    $(function(){
        view=new ViewModel(<?php echo json_encode($data) ?>);
        ko.applyBindings(view);
        //$("#invoice_date").datetimepicker({format: 'yyyy-mm-dd',minView: 'month'});

        $(".file-upload").each(function () {
            var self=$(this);
            var btnText=self.prev();
            var fileNameTd=self.parent().parent().prev().find("span.file-name");
            var maxSize=parseInt(fileNameTd.attr("maxSize"))*1024*1024;
            var type=fileNameTd.attr("type");
            var permitFileType=fileNameTd.attr("fileType");
            self.fileupload({
                url: '/feedback/saveFile/',
                dataType: 'json',
                autoUpload: true,
                add: function (e, data) {
                    upStatus=1;
                    if (!inc.checkFileType(data.files[0].name, permitFileType)) {
                        alert("只能上传指定类型的文件："+permitFileType);
                        return;
                    }
                    if(data.files[0].size>maxSize)
                    {
                        alert("文件大小超出最大限制："+fileNameTd.attr("maxSize")+"M");
                        return;
                    }
                    btnText.html("正在上传文件。。。");
                    data.formData={id:<?php echo $data['project_id'] ?>,relation_id:<?php echo $data['invoice_id'] ?>,type:fileNameTd.attr("type")};
                    data.submit();
                },
                done: function (e, data) {
                    upStatus=0;
                    if (data.result.state == 0) {
                        //count=1;
                        inc.setCookie("<?php echo $data['invoice_id'] . '_' . $data['project_id']; ?>_"+type+"_invoice",1,1);
                        fileNameTd.prev().html('<span class="glyphicon glyphicon-ok text-green"></span>&emsp;');
                        fileNameTd.html($("<a target='_blank'></a>").attr("href","/feedback/getFile/?id="+data.result.data+"&fileName="+fileNameTd.attr("name")).html(fileNameTd.attr("name")));
                        btnText.html("重新上传");
                    }
                    else {
                        alert(data.result.data);
                        btnText.html("选择上传文件");
                    }
                    view.checkCount();
                },
                fail:function(){
                    upStatus=0;
                    alert("上传出错，请稍后重试！");
                    btnText.html("选择上传文件");
                }
            });
        });
    });
    function ViewModel(option)
    {
        var defaults = {
            invoice_id:"0",
            project_id: "0",
            feedback:"",
        };
        var o = $.extend(defaults, option);
        var self=this;
        self.invoice_id=ko.observable(o.invoice_id);
        self.project_id=ko.observable(o.project_id);
        self.feedback=ko.observable(o.feedback).extend({required:true});
        self.buttonText=ko.observable("提交");
        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };

        self.actionState=0;
        self.checkCount=function()
        {
            count=inc.getCookie("<?php echo $data['invoice_id'] . '_' . $data['project_id']; ?>_51_invoice");
        }

        self.pass=function(){
            if(confirm("您确定要提交当前反馈结果信息，该操作不可逆？")) {
                /*self.checkCount();
                if(count!=1)
                {
                    alert("开票凭证必须上传！");
                    return;
                }*/
                if(upStatus!=1)
                    self.save();
                else
                    self.buttonText("文件正在上传，请稍后。。。");
            }
        }


        self.save=function(){

            if(!self.isValid())
            {
                self.errors.showAllMessages();
                return;
            }

            var formData=$("#mainForm").serialize();
            if(self.actionState==1)
                return;
            self.actionState=1;
            self.buttonText("提交中。。。");

            $.ajax({
                type: 'POST',
                url: '/feedback/save',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        if(document.referrer)
                            location.href=document.referrer;
                        else
                            location.href="/feedback/";
                    }
                    else {
                        self.buttonText("提交");
                        self.actionState=0;
                        alertModel(json.data);
                    }
                },
                error:function (data) {
                    self.buttonText("提交");
                    self.actionState=0;
                    alertModel(" 保存失败！"+data.responseText);
                }
            });
        }

        self.back=function(){
            history.back();
        }
    }

</script>