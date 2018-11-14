
<section class="content" id="content">
    <?php
    $menus = [['text'=>'结算管理'],['text'=>'入库通知单结算审核','link'=>'/check8/'], ['text' => $this->pageTitle]];
    $this->loadHeaderWithNewUI($menus, [], '/check8/');
    ?>

    <div class="card-wrapper">
        <?php
        $this->renderPartial("/common/new_stockNoticeBriefInfo", array('stockNotice'=>$data['stockInBatch'], 'stockNoticeGoods'=>$data['stockInBatch']['items']));
        
        $stockIns = $data['stockIn'];
        if(is_array($stockIns))
            foreach ($stockIns as $stockIn) {
                $this->renderPartial("/common/new_stockInBriefInfo", array('stockIn'=>$stockIn, 'is_close_card' => true));
            }

        $this->renderPartial("/common/new_settlementDetail", array('settlement'=>$data['stockInBatchBalance'], 'type'=>1,'isHideBack'=>true, 'isHiddenBtn'=>true));
        
        $checkLogs = FlowService::getCheckLog($data['stockInBatchBalance']['batch_id'], 8);
        $this->renderPartial("/common/new_checkLogList", array('checkLogs' => $checkLogs, 'map_name' => 'transection_check_status'));
        ?>
    </div>
</section>