<?php
$checkLogs = FlowService::getCheckLog($data['obj_id'],"12");
include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/checkItems.php";
?>

<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#detail" data-toggle="tab">基本信息</a></li>
            <li><a href="#flow" data-toggle="tab">审核记录</a></li>
            <?php if(!$this->isExternal){ ?>
            <li class="pull-right"><button type="button" class="btn btn-sm btn-default" onclick="back()">返回</button></li>
            <?php } ?>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="detail">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">调货处理单审核</h3>
                    </div><!--end box box-header-->
                    <div class="box-body">
                        <form class="form-horizontal" role="form" id="mainForm">
                        <?php 
                            // $this->renderPartial('detailInfo', array('returnOrder'=>$returnOrder, 'relationOrder'=>$relationOrder));
                            $this->renderPartial('/transfer/head', array('data'=>$head));
                            $this->renderPartial('/transfer/tab', array('crossDetail'=>$crossDetail));
                            $this->renderPartial('/transfer/distribute', array('returnDetail'=>$returnDetail));
                        ?>
                        </form>
                    </div><!--end box-border-->
                </div><!--end box box-->
            </div><!--end tab1-->

            <div class="tab-pane" id="flow">
                <?php
                $this->renderPartial("/common/checkLogList", array('checkLogs'=>$checkLogs, 'map_name'=>'transection_check_status'));?>
            </div>
        </div>
    </div>
</section><!--end content-->
<script type="text/javascript">
    var back = function() {
        history.go(-1);
    }
</script>