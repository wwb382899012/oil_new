<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css" />
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">请在下面填写</h3>
        </div>
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
                <?php if($this->type==1){ ?>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">采购单价</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">￥ <?php echo number_format($data["up_price"]/100,2) ?></p>
                    </div>
                    <label for="type" class="col-sm-2 control-label">项目名称</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">
                            <a href="/project/detail/?id=<?php echo $data["project_id"] ?>&t=1" target="_blank"><?php echo $data["project_name"] ?></a>
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">采购数量</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo $data["up_quantity"] ?></p>
                    </div>
                    <label for="type" class="col-sm-2 control-label">企业名称</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">
                            <a href="/partner/detail/?id=<?php echo $data["partner_id"]?>&t=1" target="_blank"><?php echo $data["customer_name"]?></a>
                        </p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">采购金额</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">￥ <?php echo number_format($data["up_amount"]/100,2) ?></p>
                    </div>
                    <label for="type" class="col-sm-2 control-label">付款期数</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo '第'.$data["period"].'期' ?></p>
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
                                    <th style="width:140px;text-align:center">付款间隔</th>
                                    <th style="width:180px;text-align:center">付款日期</th>
                                    <th style="width:140px;text-align:center">付款形式</th>
                                    <th style="width:140px;text-align:center">付款比例</th>
                                    <th style="width:240px;text-align:center">金额(元)</th>
                                    <th style="width:100px;text-align:center">状态</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach($plans as $v){ ?>
                                    <tr>
                                        <td style="text-align:center"><?php echo empty($v["period"]) ? '-' : $v["period"] ?></td>
                                        <td style="text-align:center"><?php echo $v["pay_days"] ?></td>
                                        <td style="text-align:center"><?php echo $v["pay_date"] ?></td>
                                        <td style="text-align:center">
                                            <?php echo $this->map["pay_time"][$v["pay_type"]] ?>
                                        </td>
                                        <td style="text-align:center"><?php echo $v["rate"]*100 ?>%</td>
                                        <td style="text-align:right" <?php if($v["period"]==$data['period']) echo "class='text-red'" ?>>
                                            <?php echo number_format($v["amount"]/100,2) ?>
                                        </td>
                                        <td style="text-align:center">
                                            <?php 
                                                if($v['type']==1)
                                                    echo $this->map["return_plan_status"][$v["status"]];
                                                else
                                                    echo $this->map["pay_plan_status"][$v["status"]]; 
                                            ?>
                                        </td>
                                    </tr>
                                <?php  } ?>
                                </tbody>
                            </table>
                        <?php  }
                        ?>
                    </div>
                </div>
                <?php }else{ ?>
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
                    <label for="type" class="col-sm-2 control-label">付款期数</label>
                    <div class="col-sm-4">
                        <p class="form-control-static"><?php echo '第'.$data["period"].'期' ?></p>
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
                <?php } ?>
                <div class="form-group"></div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">第<span class="text-red"><?php echo $data["period"] ?></span>期还款</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">第&nbsp;<b class="text-red"><?php echo $data["times"] ?></b>&nbsp;次催收</p>
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">通话时间</label>
                    <div class="col-sm-6">
                            <input type="text" class="form-control date" id="call_time" name= "obj[call_time]" placeholder="通话时间" data-bind="value:call_time">
                    </div>
                </div>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">备注</label>
                    <div class="col-sm-6">
                        <textarea class="form-control" id="remark" name= "obj[remark]" rows="3" placeholder="备注" data-bind="value:remark"></textarea>
                    </div>
                </div>
				
                <div class="form-group">
                    <label for="content" class="col-sm-2 control-label">
                    <?php 
                        if($this->type==1)
                            $k = 101;
                        else
                            $k = 1;
                        echo "<span>";
                        if(!empty($data['file_url']))
                            echo "<span class='glyphicon glyphicon-ok text-green'></span>&emsp;";
                        else
                            echo '<span class="glyphicon glyphicon glyphicon-remove text-red"></span>&emsp;';
                        echo "</span>";
                        echo '<span class="file-name" type="'. $this->map['remind_attachment_type'][$k]['id'].'" name="'.$this->map['remind_attachment_type'][$k]['name'].'" maxSize="'.$this->map['remind_attachment_type'][$k]['maxSize'].'" fileType="'.$this->map['remind_attachment_type'][$k]['fileType'].'">';
                        if(!empty($data["file_url"])){
                            echo "<a href='/".$this->getId()."/getFile/?id=".$data["attachment_id"]."&fileName=".$this->map["remind_attachment_type"][$k]['name']."'  target='_blank'>".$this->map["remind_attachment_type"][$k]['name']."</a>";
                        }else
                            echo $this->map["remind_attachment_type"][$k]['name'] ; 
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
                        <input type='hidden' name='obj[plan_id]' data-bind="value:plan_id" />
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
        $("#call_time").datetimepicker({format: 'yyyy-mm-dd hh:ii'});

        $(".file-upload").each(function () {
            var self=$(this);
            var btnText=self.prev();
            var fileNameTd=self.parent().parent().prev().find("span.file-name");
            var maxSize=parseInt(fileNameTd.attr("maxSize"))*1024*1024;
            var type=fileNameTd.attr("type");
            var permitFileType=fileNameTd.attr("fileType");
            self.fileupload({
                url: '/<?php echo $this->getId() ?>/saveFile/',
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
                    data.formData={id:<?php echo $data['project_id'] ?>,relation_id:0,plan_id:<?php echo $data['plan_id'] ?>,type:fileNameTd.attr("type")};
                    data.submit();
                },
                done: function (e, data) {
                    upStatus=0;
                    if (data.result.state == 0) {
                        count=1;
                        //inc.setCookie("<?php echo $user_id . '_' . $data['project_id']; ?>_"+type+"_remind",1);
                        fileNameTd.prev().html('<span class="glyphicon glyphicon-ok text-green"></span>&emsp;');
                        fileNameTd.html($("<a target='_blank'></a>").attr("href","/<?php echo $this->getId() ?>/getFile/?id="+data.result.data+"&fileName="+fileNameTd.attr("name")).html(fileNameTd.attr("name")));
                        btnText.html("重新上传");
                    }
                    else {
                        alert(data.result.data);
                        btnText.html("选择上传文件");
                    }
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
            plan_id:"0",
            project_id: "0",
            call_time:"",
            remark:"",
        };
        var o = $.extend(defaults, option);
        var self=this;
        self.plan_id=ko.observable(o.plan_id);
        self.project_id=ko.observable(o.project_id);
        self.call_time=ko.observable(o.call_time).extend({required:true});
        self.remark=ko.observable(o.remark);
        self.buttonText=ko.observable("提交");
        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };

        self.actionState=0;

        self.pass=function(){
            //self.checkCount();
            if(count!=1)
            {
                alert("通话凭证必须上传！");
                return;
            }
            if(confirm("您确定要保存当前催收信息，该操作不可逆？")) {
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
            formData=formData+"&obj[times]=<?php echo $data['times'] ?>";
            if(self.actionState==1)
                return;
            self.actionState=1;
            self.buttonText("提交中。。。");

            $.ajax({
                type: 'POST',
                url: '/<?php echo $this->getId() ?>/save',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        if(document.referrer)
                            location.href=document.referrer;
                        else
                            location.href="/<?php echo $this->getId() ?>/";
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