<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<div class="box">
    <div class="box-header with-border <?php if($data['type']!=1) echo 'link'; ?>">
        <h3 class="box-title"><b>风控初审详情</b></h3>
        <div class="pull-right box-tools">
            <button type="button" class="btn btn-sm btn-default history-back" onclick="back()">返回</button>
        </div>
    </div>
    <div class="box-body form-horizontal">
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
                <p class="form-control-static"><?php echo $info['business_describe'] ?></p>
            </div>
        </div>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">业务合作风险点 <span class="text-red fa fa-asterisk"></span></label>
            <div class="col-sm-10">
                <p class="form-control-static"><?php echo $info['risk_content'] ?></p>
            </div>
        </div>
        <?php
        $type=$data["type"];
        $tArr = explode(',', $data['type']);
        if(in_array(2, $tArr)){
            $type=2;
            $checkArr = $this->checkInfo[$type];
        }else{
            $type=1;
            $checkArr = $this->checkInfo[$type][$data["business_type"]];
        }
        $content = json_decode($info['content'],true);
        // print_r($content);die;
        foreach ($checkArr as $v)
        {
            ?>
            <div class="form-group">
                <!-- <label class="col-sm-2 control-label"><?php echo $v["name"] ?><?php if($v['label']!="1" && $type==2) echo ' <span class="text-red fa fa-asterisk"></span>'; ?></label> -->
                <label class="col-sm-2 control-label"><?php echo $v["name"] ?>
                <?php 
                if($v['label']!="1" && $type==2 && ((((!empty($data["custom_level"]) && $data["custom_level"]==3) || (empty($data["custom_level"]) && $data["auto_level"]==3)) && ($v["key"]==208 || $v["key"]==209)) || ($v["key"]!=208 && $v["key"]!=209 && $v["key"]!=201))) 
                    echo ' <span class="text-red fa fa-asterisk"></span>'; 
                ?>
                </label>
                <div class="col-sm-10">
                    <p class="form-control-static"><?php echo $content[$v['key']]['value'] ?></p>
                </div>
            </div>
            <?php
        }
        ?>
        <div class="form-group">
            <label for="runs_state" class="col-sm-2 control-label">评级 <span class="text-red fa fa-asterisk"></span></label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $this->map["partner_level"][$info["level"]] ?></p>
            </div>
        </div>
        <?php if($type==2){ ?>
        <div class="form-group">
            <label for="paid_up_capital" class="col-sm-2 control-label">拟授予额度（万元）<span class="text-red fa fa-asterisk"></span></label>
            <div class="col-sm-4">
                <p class="form-control-static">￥ <?php echo number_format($info['credit_amount']/10000/100,2) ?></p>
            </div>
        </div>
        <?php } ?>
        <?php if(!empty($this->checkDetailFile)) { include $this->checkDetailFile;} ?>
        <div class="form-group">
            <label for="remark" class="col-sm-2 control-label">风控结论 <span class="text-red fa fa-asterisk"></span></label>
            <div class="col-sm-10">
                <p class="form-control-static"><?php echo $info['conclusion'] ?></p>
            </div>
        </div>
        <div class="form-group">
            <label for="remark" class="col-sm-2 control-label">审核人</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo UserService::getUsernameById($info['create_user_id']) ?></p>
            </div>
            <label for="remark" class="col-sm-2 control-label">审核时间</label>
            <div class="col-sm-4">
                <p class="form-control-static"><?php echo $info['create_time'] ?></p>
            </div>
        </div>
    </div>
    <?php
    if(empty($attachments))
    {
        $attachments=PartnerService::getCheckAttachments($data["detail_id"]);
    }
    $attachmentTypeKey="partner_check_main_attachment_type";
    $this->showAttachmentsEditMulti($data["detail_id"],$data,$attachmentTypeKey,$attachments);
    ?>
</div>
<?php if($type==2){ ?>
<div class="box">
    <div class="box-header with-border link">
        <h3 class="box-title"><b>额度计算详情</b></h3>
    </div>
    <div class="box-body form-horizontal">
        <?php
            foreach ($this->amountInfo as $v)
            {
                ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $v["name"] ?><?php if($v['key']>=301 && $v['key']<=307) echo "（万元）";else echo " "; if($v['label']!="1") echo '<span class="text-red fa fa-asterisk"></span>'; ?></label>
                    <div class="col-sm-4">
                        <?php if($v['key']>=301 && $v['key']<=307){ ?>
                        <p class="form-control-static">￥ <?php echo number_format($amount[$v['fieldName']]/10000/100,2) ?></p>
                        <?php }else{ ?>
                        <p class="form-control-static"><?php echo $amount[$v['fieldName']] ?></p>
                        <?php } ?>
                    </div>
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
</div>
<?php } ?>
<script>
    $("div.link").each(function(){
        $(this).click(function(){
            $(this).next().toggle();
        });
    });

    function back()
    {
        history.back();
    }

</script>