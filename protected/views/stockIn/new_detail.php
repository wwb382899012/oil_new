<section class="el-container is-vertical">
    <?php
    $menus = [['text' => '入库管理'],['text'=>'添加入库单','link'=>'/stockIn/'], ['text' => $this->pageTitle]];
    $this->loadHeaderWithNewUI($menus, [], '/stockIn/');
    ?>

    <div class="card-wrapper">
        <?php
        $this->renderPartial('partial/new_stockNoticeInfo',['stockNotice'=>$stockNotice]);
        $this->renderPartial('partial/new_stockInInfoCard',['stockIns'=>$stockIns]);
        ?>
    </div>
</section>