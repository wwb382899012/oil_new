<section class="content">
    <div class="box box-default">
        <!-- <div class="box-header">
            <h3 class="box-title">会议评审补充资料详情</h3>
        </div> -->
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
                <table width="850" border="0" align="center">
                    <tr>
                        <td height="35" width="100" style="font-weight: 700;font-size: 14px;">审核说明</td>
                        <td colspan="3" style="text-align:left;vertical-align:middle;"><?php echo $data['remark'] ?></td>
                    </tr>
                    <tr>
                        <td height="35" width="100" style="font-weight: 700;font-size: 14px;">审核人</td>
                        <td colspan="3" style="text-align:left;vertical-align:middle;"><?php echo UserService::getUsernameById($data['create_user_id']) ?></td>
                    </tr>
                    <tr>
                        <td height="35" width="100" style="font-weight: 700;font-size: 14px;">审核时间</td>
                        <td colspan="3" style="text-align:left;vertical-align:middle;"><?php echo $data['create_time'] ?></td>
                    </tr>
                </table>
            </div>
            <div class="box-footer">
                <?php if(!$this->isExternal){ ?>
                    <button type="button"  class="btn btn-default" onclick="back()">返回</button>
                <?php } ?>
            </div>
        </form>
    </div>

</section>

<script>
    function back()
    {
        <?php if(!empty($_GET['url']))
        echo 'location.href="'.$this->getBackPageUrl().'";';
    else
        echo "history.back();";
        ?>
    }
</script>