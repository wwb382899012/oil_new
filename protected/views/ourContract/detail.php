<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css" />
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title"><?php echo $this->checkIsCanEdit($data["status"]) ? "上传" : "查看"  ?>我方盖章合同</h3>
            <div class="pull-right box-tools">
                <?php if($this->checkIsCanEdit($data["status"])){ ?>
                    <button type="button" id="submitButton" class="btn btn-primary" placeholder="设置付款条件" data-bind="click:pass,text:buttonText">设置付款条件</button>
                <?php } ?>
                <?php if(!$this->isExternal){ ?>
                    <button type="button"  class="btn btn-default" data-bind="click:back">返回</button>
                <?php } ?>
            </div>
        </div>
        <div class="box-body form-horizontal">
            <div class="form-group">
                <label for="type" class="col-sm-2 control-label">项目编号</label>
                <div class="col-sm-4">
                    <p class="form-control-static">
                        <a href="/project/detail/?id=<?php echo $data["project_id"] ?>&t=1" target="_blank"><?php echo $data["project_id"] ?></a>
                    </p>
                </div>
                <label for="type" class="col-sm-2 control-label">上游合作方</label>
                <div class="col-sm-4">
                    <p class="form-control-static">
                        <a href="/partner/detail/?id=<?php echo $data["up_partner_id"]?>&t=1" target="_blank"><?php echo $data["up_name"]?></a>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label for="type" class="col-sm-2 control-label">项目名称</label>
                <div class="col-sm-4">
                    <p class="form-control-static">
                        <a href="/project/detail/?id=<?php echo $data["project_id"] ?>&t=1" target="_blank"><?php echo $data["project_name"] ?></a>
                    </p>
                </div>

                <label class="col-sm-2 control-label">下游合作方</label>
                <div class="col-sm-4">
                    <p class="form-control-static">
                        <a href="/partner/detail/?id=<?php echo $data["down_partner_id"]?>&t=1" target="_blank"><?php echo $data["down_name"]?></a>
                    </p>
                </div>
            </div>
            <table id="attachments" class="table table-striped table-hover ">
            <tbody>
                <tr>
                    <th style="width: 60px" class="text-green">&nbsp;</th>
                    <!-- <th style="">序号</th> -->
                    <th style="" >文件名</th>
                    <th style="width: 260px">上下游签章合同</th>
                    <th style="width: 300px">我方盖章合同</th>
                    <th style="width: 200px"></th>
                </tr>
                <?php
                $attachmentTypeKey="mine_contract_attachment_type";
                $idFieldName="contract_id";
                //$index=1;
                //$controller="Contract";
                if(is_array($this->map[$attachmentTypeKey]))
                {
    
                    foreach ($this->map[$attachmentTypeKey] as $k => $v)
                    {
                        ?>
                        <tr data-file-id="<?php echo $contractAttachments[$k][$idFieldName] ?>">
                            <td style="font-size: 18px;">
                                <?php if (!empty($contractAttachments[$k]["file_url"]))
                                    echo '<span class="glyphicon glyphicon-ok text-green"></span>';
                                else
                                    echo '<span class="glyphicon glyphicon glyphicon-remove text-red"></span>'; ?>
                            </td>
                            <!-- <td><?php echo $index ?>.</td> -->
                            <td class="file-name" type="<?php echo $k ?>" name="<?php echo $v["name"] ?>"
                                maxSize="<?php echo $v["maxSize"] ?>" fileType="<?php echo $v["fileType"] ?>">
                                <?php 
                                    echo $v["name"];
                                if (($k-100==201 && $data["check1"]=="on") || ($k-100==202 && $data["check2"]=="on"))
	                                echo '<span class="text-red">&emsp;&emsp;定制化</span>';
                                ?>
                            </td>
                            <td>
                                <?php 
                                    if (!empty($contractAttachments[$k-100]["file_url"]))
                                        echo "<a href='/contractCommon/getFile/?id=" . $contractAttachments[$k-100][$idFieldName] . "&fileName=".$this->map["stamp_contract_attachment_type"][$k-100]["name"]."'  target='_blank' class='btn btn-primary btn-xs'>点击查看</a>";
                                    else
                                        echo '无';
                                ?>
                            </td>
                            <td class="file-name2">
                            <?php 
                                    if (!empty($contractAttachments[$k]["file_url"]))
                                        echo "<a href='/contractCommon/getFile/?id=" . $contractAttachments[$k][$idFieldName] . "&fileName=".$v["name"]."'  target='_blank' class='btn btn-primary btn-xs'>点击查看</a>";
                                    else
                                        echo '无';
                            ?>
                            </td>
                            <td>
                                <?php if ($this->checkIsCanEdit($data["status"]) && !empty($contractAttachments[$k-100]["file_url"]))
                                { ?>
                                    <span class="btn btn-success btn-xs fileinput-button">
                                        <?php
                                        if (!empty($contractAttachments[$k]["file_url"]))
                                            echo '<span class="btn-text">重新上传</span>';
                                        else
                                            echo '<span class="btn-text">上传</span>';
                                        ?>
                                        <input class="file-upload" type="file"/>
                                    </span>
                                <?php } ?>
                                <?php
                                if ($this->checkIsCanEdit($data["status"]) && UserService::checkActionRight($this->rightCode, "delFile"))
                                {
                                    $cccc = "hide1";
                                    if (!empty($contractAttachments[$k]["file_url"]))
                                    {
                                        $cccc = "";
                                    }
                                    echo '<a class="btn btn-danger btn-xs del-btn ' . $cccc . '" onclick="delFile(this)">删除</a>';
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                        //$index++;
                    }
                }
                ?>
            </tbody>
            </table>
            <?php if(!empty($conditions)){ ?>
            <div class="box-header with-border">
            </div>
            <h4 class="box-title">收付款条件</h4>
            <div class="form-group ">
                <label for="type" class="col-sm-2 control-label"><h4>上游放款条件</h4></label>
                <div class="col-sm-4">
                </div>
                <label for="type" class="col-sm-2 control-label"><h4>下游票款顺序</h4></label>
            </div>
            <div class="form-group ">
                <label for="type" class="col-sm-2 control-label">下游收货确认书</label>
                <div class="col-sm-4">
                    <p class="form-control-static">
                        <?php echo $this->map["confirmation_type"][$conditions['is_down_receive']];?>
                    </p>
                </div>
                <label for="type" class="col-sm-2 control-label">下游票款</label>
                <div class="col-sm-4">
                    <p class="form-control-static">
                        <?php echo $this->map["down_pay_type"][$conditions['invoice_type']];?>
                    </p>
                </div>
            </div>
            <div class="form-group ">
                <label for="type" class="col-sm-2 control-label">下游保证金</label>
                <div class="col-sm-4">
                    <p class="form-control-static">
                        <?php echo $this->map["first_pay_type"][$conditions['is_down_first']];?>
                    </p>
                </div>
            </div>
            <div class="form-group ">
                <label for="type" class="col-sm-2 control-label">合同双签</label>
                <div class="col-sm-4">
                    <p class="form-control-static">
                        <?php echo $this->map["contract_stamp_type"][$conditions['is_contract']];?>
                    </p>
                </div>
            </div>
            <div class="form-group ">
                <label for="type" class="col-sm-2 control-label">履约保函</label>
                <div class="col-sm-4">
                    <p class="form-control-static">
                        <?php echo $this->map["guarantee_type"][$conditions['is_bond']];?>
                    </p>
                </div>
            </div>
            <div class="form-group ">
                <label for="type" class="col-sm-2 control-label">担保协议</label>
                <div class="col-sm-4">
                    <p class="form-control-static">
                        <?php echo $this->map["assure_type"][$conditions['is_guarantee']];?>
                    </p>
                </div>
            </div>
            <div class="form-group ">
                <label for="type" class="col-sm-2 control-label">货权转移证明</label>
                <div class="col-sm-4">
                    <p class="form-control-static">
                        <?php echo $this->map["cargo_transfer_type"][$conditions['is_goods']];?>
                    </p>
                </div>
            </div>
            <div class="form-group ">
                <label for="type" class="col-sm-2 control-label">其他付款条件1</label>
                <div class="col-sm-4">
                    <p class="form-control-static">
                        <?php echo $conditions['pay_remark1'];?>
                    </p>
                </div>
            </div>
            <div class="form-group ">
                <label for="type" class="col-sm-2 control-label">其他付款条件2</label>
                <div class="col-sm-4">
                    <p class="form-control-static">
                        <?php echo $conditions['pay_remark2'];?>
                    </p>
                </div>
            </div>
            <?php } ?>
        </div>
        <div class="box-footer">
            <?php //if($this->checkIsCanEdit($data["status"]) &&  UserService::checkActionRight($this->rightCode,"submit")){ ?>
                <!-- <button type="button" id="submitButton" class="btn btn-danger" placeholder="提交审核" data-bind="click:pass,text:buttonText">提交审核</button> -->
            <?php //} ?>
            <?php if($this->checkIsCanEdit($data["status"])){ ?>
                <button type="button" id="submitButton" class="btn btn-primary" placeholder="设置付款条件" data-bind="click:pass,text:buttonText">设置付款条件</button>
            <?php } ?>
            <?php if(!$this->isExternal){ ?>
                <button type="button"  class="btn btn-default" data-bind="click:back">返回</button>
            <?php } ?>
        </div>

    </div>
</section>

<script>
    var view;
    var upStatus;
    $(function(){
        view=new ViewModel(<?php echo json_encode($data) ?>);
        ko.applyBindings(view);
        $(".file-upload").each(function () {
            var self=$(this);
            var btnText=self.prev();
            var tr=self.parent().parent().parent();
            var delBtn=self.parent().parent().find(".del-btn");
            var fileNameTd=tr.find("td.file-name");
            var fileNameTd2=tr.find("td.file-name2");
            var maxSize=parseInt(fileNameTd.attr("maxSize"))*1024*1024;
            var permitFileType=fileNameTd.attr("fileType");
            self.fileupload({
                url: '/contractCommon/saveFile/',
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

                    data.formData={id:<?php echo !empty($data['project_id']) ? $data['project_id'] : 0; ?>,type:fileNameTd.attr("type")};
                    data.submit();
                },
                done: function (e, data) {
                    upStatus=0;
                    if (data.result.state == 0) {
                        //fileNameTd.prev().prev().html('<span class="glyphicon glyphicon-ok text-green"></span>');
                        fileNameTd.prev().html('<span class="glyphicon glyphicon-ok text-green"></span>');
                        //fileNameTd.html($("<a target='_blank'></a>").attr("href","/contract/getFile/?id="+data.result.data).html(fileNameTd.attr("name")));
                        fileNameTd2.html($("<a target='_blank' class='btn btn-primary btn-xs'></a>").attr("href","/contractCommon/getFile/?id="+data.result.data+"&fileName="+fileNameTd.attr("name")).html("点击查看"));
                        btnText.html("重新上传");
                        tr.attr("data-file-id",data.result.data);
                        delBtn.show();
                    }
                    else {
                        alert(data.result.data);
                        btnText.html("上传");
                    }
                },
                fail:function(){
                    upStatus=0;
                    alert("上传出错，请稍后重试！");
                    btnText.html("上传");
                }
            });
        });
    });


    function ViewModel(option)
    {
        var defaults = {
            project_id:0,
        };
        option=inc.clearNullProperty(option);
        var o = $.extend(defaults, option);
        var self=this;
        self.project_id=ko.observable(o.project_id);
        self.buttonText=ko.observable("设置付款条件");

        /*self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };*/

        self.pass=function(){
            var formData = "id=<?php echo $data["project_id"] ?>";
            $.ajax({
                type: 'POST',
                url: '/ourContract/finUpload',
                data: formData,
                dataType: "json",
                success: function (json) {
                    console.log(json.state);
                    if (json.state == 0) {
                        location.href="/ourContract/pay?id=<?php echo $data['project_id']; ?>";
                    }
                    else {
                        alertModel(json.data);
                    }
                },
                error:function (data) {
                    self.actionState=0;
                    alertModel("保存失败！"+data.responseText);
                }
            });
                
        }
        /*self.pass=function(){
            if(confirm("您确定要提交当前信息，该操作不可逆？")) {
                if(upStatus!=1)
                    self.submit();
                else
                    self.buttonText("文件正在上传，请稍后。。。");
            }
        }*/

        /*self.submit=function(){
            if(!self.isValid())
            {
                self.errors.showAllMessages();
                return;
            }
            var formData = "id=<?php echo $data["project_id"] ?>";
            if(self.actionState==1)
                return;
            if(self.actionState==1)
                return;
            self.actionState=1;
            $.ajax({
                type: 'POST',
                url: '/ourContract/submit',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        inc.showNotice("操作成功");
                        location.reload();
                    }
                    else {
                        alertModel(json.data);
                    }
                },
                error:function (data) {
                    self.actionState=0;
                    alertModel("保存失败！"+data.responseText);
                }
            });
        }*/

        self.back=function(){
            location.href="/ourContract/";
            //history.back();
        }
    }

    function delFile(target)
    {
        if(confirm("您确定删除当前已经上传的文件吗，该操作不可逆？")) {
            var tr=$(target).parent().parent();
            var id=tr.attr("data-file-id");
            var formData = "id="+id;
            $.ajax({
                type: 'POST',
                url: '/contractCommon/delFile',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        inc.showNotice("操作成功");
                        var btnText=tr.find(".btn-text");
                        var fileNameTd=tr.find("td.file-name");
                        var fileNameTd2=tr.find("td.file-name2");
                        //fileNameTd.prev().prev().html('<span class="glyphicon glyphicon glyphicon-remove text-red"></span>');
                        fileNameTd.prev().html('<span class="glyphicon glyphicon glyphicon-remove text-red"></span>');
                        //fileNameTd.html(fileNameTd.attr("name"));
                        fileNameTd2.html("无");
                        btnText.html("上传");
                        $(target).hide();
                    }
                    else {
                        alertModel(json.data);
                    }
                },
                error: function (data) {
                    alertModel("操作失败！" + data.responseText);
                }
            });
        }

    }

</script>