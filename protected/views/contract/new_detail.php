<?php
$menus = [
    ['text' => '合同管理'],
    ['text' => '合同信息', 'link' => '/contract/'],
    ['text' => '合同详情']
];
$buttons = [];
$this->loadHeaderWithNewUI($menus, $buttons, '/contract/');
?>
<div class="el-container is-vertical">

    <?php if(Utility::isNotEmpty($contract['split'])){ ?>
    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>平移明细</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <table class="table table-striped table-hover">
            <tbody>
            <tr>
                <th>合同详情</th>
                <th>合作方名称</th>
                <th>商品名称</th>
                <th>商品数量</th>
                <th>合同审核状态</th>
                <th>操作</th>
            </tr>
            <tr>
                <td>原：<a class="text-link" href="/contract/detail?id=<?php echo $contract['contract_id'] ?>&original=1"><?php echo $contract['contract_code']?></a></td>
                <td><?php echo $contract['partner']->name?></td>
                <td>
                    <?php foreach ($contract['originalContractGoods'] as $originalContractGoods){ ?>
                    <p><?php echo $originalContractGoods['goods']->name?></p>
                    <?php } ?>
                </td>
                <td>
                    <?php foreach ($contract['originalContractGoods'] as $originalContractGoods){ ?>
                        <p><?php echo $originalContractGoods->quantity.Map::$v['goods_unit_enum'][$originalContractGoods->unit] ?></p>
                    <?php } ?>
                </td>
                <td><?php echo Map::$v['contract_status'][$contract->status] ?></td>
                <td></td>
            </tr>
            <?php foreach ($contract['split'] as $split){?>
            <tr>
                <td>新：<a class="text-link" href="/contract/detail?id=<?php echo $split->contract_id ?>"><?php echo $split->contract_code ?></a></td>
                <td><?php echo $split->partner->name ?></td>
                <td>
                    <?php foreach ($split->contractGoods as $splitContractGoods){ ?>
                        <p><?php echo $splitContractGoods->goods->name?></p>
                    <?php } ?>
                </td>
                <td>
                    <?php foreach ($split->contractGoods as $splitContractGoods){ ?>
                        <p><?php echo $splitContractGoods->quantity.Map::$v['goods_unit_enum'][$splitContractGoods->unit] ?></p>
                    <?php } ?>
                </td>
                <td><?php echo Map::$v['contract_status'][$split->status] ?></td>
                <td>
                    <?php if($split->status<=Contract::STATUS_SAVED){ ?>
                        <a href="/subContract/edit?id=<?php echo $split->contract_id ?>" class="o-btn o-btn-primary">商务确认</a>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } ?>
    <?php
    $this->renderPartial("/common/new_contractDetail", array('contract' => $contract));
    ?>

    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>审核记录</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <?php
        $checkLogs = FlowService::getCheckLogModel($contract->contract_id, ContractService::getContractBusinessIds());
        if (Utility::isEmpty($checkLogs)) {
            $checkLogs = FlowService::getCheckLogModel($contract->relation_contract_id, ContractService::getContractBusinessIds());
        }
        $this->renderPartial("/check/new_checkLogs", array('checkLogs' => $checkLogs));
        ?>
    </div>

    <?php
    $contractFiles = ContractService::getContractFiles($contract->contract_id);
    if (Utility::isNotEmpty($contractFiles)) {
        ?>
        <div class="content-wrap">
            <div class="content-wrap-title">
                <div>
                    <p>合同文本</p>
                    <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
                </div>
            </div>

            <?php
            $this->renderPartial("/contractFile/new_contractFiles", array('contractFiles' => $contractFiles, 'contract' => $contract));
            ?>
        </div>
    <?php } ?>

</div>

<script>
    function back() {
        location.href = "<?php echo $this->getBackPageUrl() ?>";
    }

    function copy() {
        inc.vueMessage('复制成功');
    }

    $(document).ready(function () {
        var clipboard = new Clipboard('.copy-project-num');
        $("section.content").trigger('resize');
    });

</script>
