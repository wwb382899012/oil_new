<section class="el-container is-vertical">

    <?php
    $menus = [['text' => '入库管理'],['text' => '添加入库通知单', 'link' => '/stockNotice/'], ['text' => $this->pageTitle]];
    $this->loadHeaderWithNewUI($menus, [], '/stockNotice/');
    ?>

    <div class="card-wrapper">
        <?php $this->renderPartial('partial/new_contractInfoCard', ['contract' => $contract, 'transactions' => $transactions]); ?>
        <?php $this->renderPartial('partial/new_stockNoticeCard', ['stockNotices' => $stockNotices]); ?>
    </div>
</section>