<section class="el-container is-vertical">

    <?php
    $menus = [['text' => '出库管理'],['text'=>'出库单列表','link'=>'/stockOutList/'], ['text' => $this->pageTitle]];
    $this->loadHeaderWithNewUI($menus, [], '/stockOutList/');
    ?>

    <div class="card-wrapper">
        <?php
        $this->renderPartial("/stockOut/partial/new_stockOutOrderInfoCard", array('outOrders' => [$stockOutOrder], 'is_unfold'=> true));
        ?>
    </div>
</section>
