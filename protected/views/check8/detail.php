
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

                <section class="content">
               <?php

                $this->renderPartial("/common/stockNoticeBriefInfo", array('stockNotice'=>$data['stockInBatch'], 'stockNoticeGoods'=>$data['stockInBatch']['items'],'hideBackBtn'=>true));
                
                $stockIns = $data['stockIn'];
                if(is_array($stockIns))
                    foreach ($stockIns as $stockIn) {
                        $this->renderPartial("/common/stockInBriefInfo", array('stockIn'=>$stockIn));
                    }
                
                
                ?>
                  <?php
                        $this->renderPartial("/common/settlementDetail", array('settlement'=>$data['stockInBatchBalance'], 'type'=>1));
                
                      
                        ?>
                </section>
            </div><!--end tab1-->

            <div class="tab-pane" id="flow">
                <?php 
                if(!empty($data['stockInBatchBalance']['settle_id'])) {
                    $checkLogs = FlowService::getCheckLog($data['stockInBatchBalance']['batch_id'], 8);
                    if (Utility::isNotEmpty($checkLogs))
                        $this->renderPartial("/common/checkLogList", array('checkLogs' => $checkLogs));
                }
                ?>
            </div>
        </div>
    </div>
</section><!--end content-->
<script type="text/javascript">
    var back = function() {
        history.go(-1);
    }
</script>