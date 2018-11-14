<?php foreach ($data as $key => $value) { ?>
<div class="box">
    <div class="box-header with-border <?php if($key>=1) echo 'link' ?>">
        <h3 class="box-title"><b><span class="text-red"><?php echo $value['start_time']//echo $key==0 ? '最新一次' : '第'.(count($data)-$key).'次'; ?></span>&nbsp;会议评审信息</b></h3>
        <?php if($key==0){ ?>
        <div class="pull-right box-tools">
            <?php if(!empty($data) && $value['status']==PartnerReview::STATUS_REVIEW_NEW) { ?>
            <button type="button" class="btn btn-sm btn-primary" onclick="edit()">修改</button>&nbsp;
            <button type="button" class="btn btn-sm btn-success" onclick="submit(<?php echo PartnerReview::STATUS_REVIEW_PASS ?>)">同意</button>&nbsp;
            <button type="button" class="btn btn-sm btn-danger" onclick="submit(<?php echo PartnerReview::STATUS_REVIEW_REJECT ?>)">否决</button>&nbsp;
            <button type="button" class="btn btn-sm btn-warning" onclick="submit(<?php echo PartnerReview::STATUS_NOT_REVIEW ?>)">补资料无需再评</button>&nbsp;
            <button type="button" class="btn btn-sm btn-danger" onclick="submit(<?php echo PartnerReview::STATUS_NEED_REVIEW ?>)">补资料需再评审</button>&nbsp;
            <?php } ?>
            <button type="button" class="btn btn-sm btn-default history-back" onclick="back()">返回</button>
        </div>
        <?php } ?>
    </div>
    <div class="box-body <?php if($key>=1) echo 'hide1' ?>">
        <table width="850" border="0" align="center">
            <?php if($key==0){ ?>
            <tr>
                <td height="35" style="font-weight: 700;font-size: 14px;">企业名称</td>
                <td style="text-align:left;" colspan="5"><a href="/PartnerApply/detail/?id=<?php echo $partner['partner_id'] ?>&t=1" target="_blank"><?php echo $partner["partner_id"]."&emsp;&emsp;".$partner["partner_name"] ?></a></td>
            </tr>
            <?php } ?>
            <tr>
                <td height="35" width="65" style="font-weight: 700;font-size: 14px;">企业类别</td>
                <td width="120"><?php echo PartnerApplyService::getPartnerType($partner["type"]) ?></td>
                <td width="70" style="font-weight: 700;font-size: 14px;">商务分类</td>
                <td width="70"><?php echo $this->map['partner_level'][$partner["custom_level"]] ?></td>
                <td width="100" style="font-weight: 700;font-size: 14px;">拟申请额度(万元)</td>
                <td width="140"><?php echo '￥ '.number_format($partner["apply_amount"]/10000/10,2) ?></td>
            </tr>
            <tr>
                <td height="35" style="font-weight: 700;font-size: 14px;">系统分类</td>
                <td><?php echo $this->map['partner_level'][$partner["auto_level"]] ?></td>
                <td style="font-weight: 700;font-size: 14px;">风控分类</td>
                <td><?php echo $this->map['partner_level'][$partner["risk_level"]] ?></td>
                <td style="font-weight: 700;font-size: 14px;">风控初审额度(万元)</td>
                <td><?php echo '￥ '.number_format($partner["o_credit_amount"]/10000/100,2) ?></td>
            </tr>
        </table>
        <hr/> 
        <table width="850" border="0" align="center">
            <tr>
                <td height="35" style="font-weight: 700;font-size: 14px;">评审时间<span class="text-red fa fa-asterisk"></span></td>
                <td colspan="3" style="text-align:left;"><?php echo $value['start_time'] ?></td>
            </tr>
            <tr>
                <td height="35" width="100" style="font-weight: 700;font-size: 14px;">评审人员<span class="text-red fa fa-asterisk"></span></td>
                <td width="500">
                <?php 
                $ids = explode(',',$value['user_ids']);
                unset($ids[0]);
                foreach ($ids as $k => $v) {
                    $name[] = UserService::getUsernameById($v);
                }
                $user = "";
                if(!empty($name))
                    $user = implode($name,'&nbsp;|&nbsp;');
                echo $user;
                ?>
                </td>
                <td style="font-weight: 700;font-size: 14px;">我方资金来源<span class="text-red fa fa-asterisk"></span></td>
                <td><?php echo $this->map['mine_fund_type'][$value['level3']] ?></td>
            </tr>
            <tr>
                <td height="35" style="font-weight: 700;font-size: 14px;">业务类型<span class="text-red fa fa-asterisk"></span></td>
                <td><?php echo $this->map['partner_business_type'][$value['level2']] ?></td>
                <td style="font-weight: 700;font-size: 14px;">企业付款方式<span class="text-red fa fa-asterisk"></span></td>
                <td><?php echo $this->map['partner_pay_type'][$value['level4']] ?></td>
            </tr>
            <tr>
                <td height="35" style="font-weight: 700;font-size: 14px;">业务操作条件</td>
                <td colspan="3" style="text-align:left;"><?php echo $value['condition1'] ?></td>
            </tr>
            <tr>
                <td height="35" style="font-weight: 700;font-size: 14px;">管理要求</td>
                <td colspan="3" style="text-align:left;"><?php echo $value['condition2'] ?></td>
            </tr>
            <tr>
                <td height="35" style="font-weight: 700;font-size: 14px;">其他意见</td>
                <td colspan="3" style="text-align:left;"><?php echo $value['content'] ?></td>
            </tr>
            <tr>
                <td height="35" style="font-weight: 700;font-size: 14px;">拟授予额度</td>
                <td colspan="3" style="text-align:left;">￥ <?php echo number_format($value['credit_amount']/10000/100,2); ?>&nbsp;(万元)</td>
            </tr>
            <tr>
                <td height="35" style="font-weight: 700;font-size: 14px;">评审记录</td>
                <td colspan="3" style="text-align:left;">
                <?php 
                $attachements = PartnerReview::getReviewAttachments($value['review_id']);
                if(!empty($attachements[3001]))
                   echo "<a href='/partnerReview/getFile/?id=".$attachements[3001][0]["id"]."&fileName=".$attachements[3001][0]['name']."'  target='_blank' class='btn btn-primary btn-xs'>点击查看</a>";
               else
                 echo "无";
                ?>
                </td>
            </tr>
            <tr>
                <td height="35" style="font-weight: 700;font-size: 14px;vertical-align:middle;">其他附件</td>
                <td colspan="3" style="text-align:left;vertical-align:middle;">
                <?php 
                if(!empty($attachements[3002]) && count($attachements[3002])>=1){
                    foreach ($attachements[3002] as $attach) {
                       // echo "<br/>";
                       echo "<a href='/partnerReview/getFile/?id=".$attach["id"]."&fileName=".$attach['name']."'  target='_blank' class='btn btn-primary btn-xs'>".$attach['name']."</a>";
                       echo "<br/><br/>";
                    }
                }else{
                    echo "无";
                }
                ?>
                </td>
            </tr>
        </table>
        <?php if(!empty($supplyAttachments[$value['review_id']][3201]) && count($supplyAttachments[$value['review_id']][3201])>=1){ ?>
        <hr/>
        <table width="850" border="0" align="center">
            <tr>
                <td height="35" width="100" style="font-weight: 700;font-size: 14px;vertical-align:middle;">补充资料</td>
                <td colspan="3" style="text-align:left;vertical-align:middle;">
                <?php 
                if(!empty($supplyAttachments[$value['review_id']][3201]) && count($supplyAttachments[$value['review_id']][3201])>=1){
                    foreach ($supplyAttachments[$value['review_id']][3201] as $attach) {
                       // echo "<br/>";
                       echo "<p><a href='/partnerReview/getFile/?id=".$attach["id"]."&fileName=".$attach['name']."'  target='_blank' class='btn btn-primary btn-xs'>".$attach['name']."</a></p>";
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
<?php } ?>

<script>
    $("div.link").each(function(){
        $(this).click(function(){
            $(this).next().toggle();
        });
    });

    function back()
    {
        location.href="/partnerReview/";
    }

    function edit()
    {
        location.href="/partnerReview/edit?flag=1&id=<?php echo $partner['partner_id'] ?>";
    }

    function submit(status) {
        layer.confirm("您确定要提交当前会议评审信息，该操作不可逆？", {icon: 3, title: '提示'}, function(index){
            var formData="id=<?php echo $data[0]['review_id'] ?>&partner_id=<?php echo $partner['partner_id'] ?>&status="+status;
            $.ajax({
                type:"POST",
                url:"/partnerReview/submit",
                data:formData,
                dataType:"json",
                success:function (json) {
                    if (json.state == 0) {
                        inc.showNotice("操作成功");
                        location.reload();
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