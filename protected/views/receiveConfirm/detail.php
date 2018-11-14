<?php 
?>
<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">银行流水</h3>
            <div class="pull-right box-tools">
                <?php if($receiveConfirm->status < ReceiveConfirm::STATUS_SUBMITED && $receiveConfirm->status > ReceiveConfirm::STATUS_ABORTED):?>
                <button type="button" id="editButton" class="btn btn-danger" onclick="submit()">提交</button>
                <button type="button" id="editButton" class="btn btn-primary" onclick="edit()">修改</button>
                <?php endif;?>
                <button type="button"  class="btn btn-default" onclick="back()">返回</button>
            </div>
        </div><!--end box box-header-->
        <div class="form-horizontal" role="form" id="mainForm">
            <div class="form-group">
                <label class="col-sm-2 control-label">认领人</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $receiveConfirm->creator->name ?></p>
                </div>
                <label class="col-sm-2 control-label">可认领金额</label>
                <div class="col-sm-4">
                    <p class="form-control-static"><?php echo $this->map['currency'][$bankFlow->currency]['ico']?><?php echo number_format(($bankFlow->amount - $bankFlow->amount_claim)/100, 2) ?></p>
                </div>
            </div>
            <?php
            $this->renderPartial("/common/bankFlowDetail", array('bankFlow'=>$bankFlow));
            ?>

        </div>
    </div><!--end box box-primary-->
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">认领详情</h3>
        </div><!--end box box-header-->
        <div class="form-horizontal" role="form" id="mainForm">
            <?php
            $this->renderPartial("/common/receiveConfirmDetail", array('bankFlow'=>$bankFlow, 'receiveConfirm'=>$receiveConfirm, 'attachments'=>$attachs));
            ?>

            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                    <?php if($receiveConfirm->status == ReceiveConfirm::STATUS_NEW):?>
                        <button type="button" id="editButton" class="btn btn-danger" onclick="submit()">提交</button>
                        <button type="button" class="btn btn-primary" onclick="edit()">修改</button>
                    <?php endif;?>
                        <button type="button" class="btn btn-default" onclick="back()">返回</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    var back_url = "";
    <?php
    if(!empty($_GET['back_url'])) { ?>
        back_url = "<?php echo $_GET['back_url'] ?>";
    <?php } ?>
    var back=function () {
    	if(!inc.isEmpty(back_url)) {
    		location.href = "/" + back_url + "/"
        } else {
    		<?php
            if($receiveConfirm->status >= ReceiveConfirm::STATUS_SUBMITED) { ?>
			    location.href = "/<?php echo $this->getId() ?>";
            <?php } else { ?>
			    location.href = "/<?php echo $this->getId() ?>/view?flow_id=<?php echo $receiveConfirm->flow_id ?>";
            <?php } ?>
        }
    }
    var edit=function () {
        window.location.href="/<?php echo $this->getId() ?>/edit/?id=<?php echo $receiveConfirm->receive_id?>";
    }
    var submit=function() {
        layer.confirm("是否确认提交流水认领单?本操作无法撤回", function() {
            $.ajax({
                type:"POST",
                url:"/<?php echo $this->getId() ?>/submit",
                data:{
                    id:<?php echo $receiveConfirm->receive_id?>
                },
                dataType:"json",
                success:function (json) {
                    if(json.state==0){
                        layer.msg("提交成功", {icon: 6, time:1000},function() {
                        	var url = "/<?php echo $this->getId() ?>/detail?id="+json.data;
                        	if(!inc.isEmpty(back_url)) {
								url= url+"&back_url="+back_url;
							}
							location.href = url;
                        });
                    }else{
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error:function (data) {
                    layer.alert("操作失败！" + data.responseText, {icon: 5});
                }
            });
        });
    }
</script>