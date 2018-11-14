<section class="el-container is-vertical">
    <?php
    $menus = [['text' => '入库管理'],['text'=>'添加入库单','link'=>'/stockIn/'], ['text' => $this->pageTitle]];
    $this->loadHeaderWithNewUI($menus, [], '/stockIn/');
    ?>

    <div class="card-wrapper">
        <?php
        $this->renderPartial('/stockIn/partial/new_stockInInfoCard',['stockIns'=>[$stockIn],'is_unfold'=> true]);

        $checkLog = FlowService::getCheckLog($stockIn['stock_in_id'], "7");
        if (Utility::isNotEmpty($stockDetail)) {
            $flag = 0;
            foreach ($stockDetail as $key => $row) {
                if (Utility::isNotEmpty($row['stock_detail'])) {
                    $flag = 1;
                    break;
                }
            }
            if ($flag) {
                $this->renderPartial('/stockIn/partial/new_stockChange',['stockDetail'=>$stockDetail]);
            }
        }
        ?>
    </div>
</section>