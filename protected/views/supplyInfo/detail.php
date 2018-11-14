<script src="/js/bootstrap3-typeahead.min.js"></script>
<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css" />
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title"><b>会议评审补充附件</b></h3>
        <div class="pull-right box-tools">
            <?php if($this->checkIsCanEdit($data['status'])) { ?>
            <button type="button" class="btn btn-sm btn-danger" onclick="submit()">提交</button>&nbsp;
            <?php } ?>
            <button type="button" class="btn btn-sm btn-default" onclick="back()">返回</button>
        </div>
    </div>
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
        <?php
            if($this->checkIsCanEdit($data['status'])) {
                if(empty($partnerAttachments))
                {
                    $partnerAttachments=PartnerReview::getReviewAttachments($data["review_id"]);
                }
                $attachmentTypeKey="partner_review_extra_attachment_type";
                $this->showAttachmentsEditMulti($data["review_id"],$data,$attachmentTypeKey,$partnerAttachments);
            }else{
        ?>
        <table width="850" border="0" align="center">
            <tr>
                <td height="35" width="100" style="font-weight: 700;font-size: 14px;">补充资料</td>
                <td colspan="3" style="text-align:left;vertical-align:middle;">
                <?php 
                if(!empty($partnerAttachments[3201]) && count($partnerAttachments[3201])>=1){
                    foreach ($partnerAttachments[3201] as $attach) {
                       // echo "<br/>";
                       echo "<p><a href='/supplyInfo/getFile/?id=".$attach["id"]."&fileName=".$attach['name']."'  target='_blank' class='btn btn-primary btn-xs'>".$attach['name']."</a></p>";
                       // echo "<br/><br/>";
                    }
                }else{
                    echo "无";
                }
                ?>
                </td>
            </tr>
        </table>
        <?php } ?>
    </div>
</div>


<script>
    function back()
    {
        location.href="/supplyInfo/";
    }

    function submit() {
        layer.confirm("您确定要提交当前会议评审附件信息，该操作不可逆？", {icon: 3, title: '提示'}, function(index){
            var formData="id=<?php echo $data['review_id'] ?>";
            $.ajax({
                type:"POST",
                url:"/supplyInfo/save",
                data:formData,
                dataType:"json",
                success:function (json) {
                    if (json.state == 0) {
                        inc.showNotice("操作成功");
                        location.href="/supplyInfo/detail/?id=<?php echo $data['review_id'] ?>";
                    }
                    else {
                        alertModel(json.data);
                    }
                },
                error:function (data) {
                    alertModel("操作失败！" + data.responseText);
                }
            });

            layer.close(index);
        });
    }

</script>