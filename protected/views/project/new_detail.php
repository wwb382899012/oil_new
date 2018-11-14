<link href="/css/style/projectdetail.css?key=20180112" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="/js/clipboard.js"></script>
<script type="text/javascript" src="/js/resize.js"></script>
<?php
$notTonNum=0;//单位不为吨的数量
foreach($transactions as $good){
    if($good['unit']!=ConstantMap::CONTRACT_GOODS_UNIT_CONVERT_VALUE)
        $notTonNum++;
}

$menus = [
    ['text' => '项目管理'],
    ['text' => '项目列表', 'link' => '/project/'],
    ['text' => '项目详情']
];
$buttons = [];
if (empty($_GET['t']) || $_GET['t'] != 1) {
    if ($data["status"] < Project::STATUS_SUBMIT && $data["status"] > Project::STATUS_STOP) {
        $buttons[] = ['text' => '提交', 'attr' => ['onclick' => 'submit()']];
    }
    if ($this->checkIsCanEdit($data["status"])) {
        $buttons[] = ['text' => '修改', 'attr' => ['onclick' => 'edit()', 'class_abbr' => 'action-default-base']];
    }
}
$this->loadHeaderWithNewUI($menus, $buttons, '/project/');
?>
<section class="el-container is-vertical">

    <?php if ($data["status"] == Project::STATUS_BACK) {
        $backLog = ProjectBackLog::model()->find(array("condition" => "project_id=" . $data["project_id"], "order" => "id desc"));
        if (!empty($backLog)) {
            ?>
            <div>
                <div class="form-group text-danger" id="detail-back-title"
                     style="margin: 20px 20px 0;border: 1px solid #dcdcdc;border-radius: 3px;height: 50px;line-height: 50px;padding: 0;padding-left: 24px;color: #333;">
                    <span style="color:#FA4D4E;font-size:14px !important;vertical-align: baseline;"
                          class="fa fa-info-circle"></span>&nbsp;驳回备注:
                    <span class="form-control-static"><?php echo $backLog["remark"] ?></span>
                </div>
            </div>
        <?php }
    } ?>
    <!-- 详情综述 -->
    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>
                    <?php
                    $typeDesc = $this->map["project_type"][$data["type"]];
                    if (!empty($data['buy_sell_type'])) {
                        $typeDesc .= '-' . $this->map["purchase_sale_order"][$data["buy_sell_type"]];
                    }
                    echo $typeDesc;
                    ?>
                    <span class="project-detail">项目编号：<?php echo $data["project_code"] ?></span>
                    <span onclick="copy()" data-clipboard-text="<?php echo $data['project_code']; ?>"
                          style="color:#FF6E34;font-size:14px;margin-left:10px;text-decoration: underline;font-size: 14px;cursor: pointer;"
                          class="copy-project-num">复制</span>
                </p>
            </div>
        </div>
        <ul class="item-com">
            <li>
                <label>交易主体：</label>
                <span><?php echo $data["corporation_name"] ?></span>
            </li>
            <li>
                <label>项目负责人：</label>
                <span><?php echo $data["manager_name"] ?></span>
            </li>
            <?php
            if (!empty($data['agent_id'])) { ?>
                <li>
                    <label>采购代理商：</label>
                    <span>
                <?php
                echo '<a href="/partner/detail/?id=' . $data["agent_id"] . '&t=1" target="_blank">' . $data["agent_name"] . '</a>';
                ?>
            </span>
                </li>
            <?php } ?>
            <li>
                <label>价格方式：</label>
                <span><?php echo $this->map["price_type"][$data['price_type']] ?></span>
            </li>
            <?php
            if (!empty($data["storehouse_name"])) {
                ?>
                <li>
                    <label>仓库名称：</label>
                    <span><?php echo $data["storehouse_name"] ?></span>
                </li>
            <?php } ?>
            <?php if (!empty($data['buy_sell_type'])) { ?>
                <li>
                    <label>购销顺序：</label>
                    <span><?php echo $this->map["purchase_sale_order"][$data['buy_sell_type']] ?></span>
                </li>
            <?php } ?>
        </ul>
    </div>

    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>交易明细</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>

        <?php
        if (!empty($data['up_partner_id']) || !empty($data['down_partner_id'])) {
            ?>
            <?php
            if (!empty($data['up_partner_id'])) {
                ?>
                <ul class="item-com item-com-for-table">
                    <li>
                        <label>上游合作方：</label>
                        <span>
                        <?php
                        echo '<a href="/partner/detail/?id=' . $data["up_partner_id"] . '&t=1" target="_blank">' . $data["up_partner_name"] . '</a>';
                        ?>
                    </span>
                    </li>
                    <li>
                        <label>采购币种：</label>
                        <span><?php echo $this->map["currency_type"][$data["purchase_currency"]] ?></span>
                    </li>
                </ul>
                <ul class="table-com">
                    <li>
                        <span>采购品名</span>
                        <span>数量</span>
                        <?php if($notTonNum>0) :?>
                            <span>单位换算比</span>
                        <?php endif;?>
                        <span>采购单价</span>
                        <span>采购总价</span>
                    </li>
                    <?php foreach ($transactions as $v) { ?>
                        <li>
                            <span><?php echo $v["goods_name"] ?></span>
                            <span><?php echo $v["quantity"], $this->map["goods_unit"][$v["unit"]]['name']; ?></span>
                            <?php if($notTonNum>0) :?><span><?php if($v["unit"]!=ConstantMap::CONTRACT_GOODS_UNIT_CONVERT_VALUE) echo $this->map["goods_unit"][$v["unit"]]['name'].'/'.ConstantMap::CONTRACT_GOODS_UNIT_CONVERT.'='.$v["unit_convert_rate"]; ?></span><?php endif;?>
                            <span><?php echo $this->map["currency"][$v["purchase_currency"]]['ico'] . Utility::numberFormatFen2Yuan($v["purchase_price"]) ?></span>
                            <span><?php echo $this->map["currency"][$v["purchase_currency"]]['ico'] . Utility::numberFormatFen2Yuan($v["purchase_amount"]) ?></span>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>

            <?php
            if (!empty($data['down_partner_id'])) {
                ?>
                <ul class="item-com item-com-for-table" style="margin-top:20px;">
                    <li>
                        <label>下游合作方：</label>
                        <span>
                    <?php
                    echo '<a href="/partner/detail/?id=' . $data["down_partner_id"] . '&t=1" target="_blank">' . $data["down_partner_name"] . '</a>';
                    ?>
                </span>
                    </li>
                    <li>
                        <label>销售币种：</label>
                        <span><?php echo $this->map["currency_type"][$data["sell_currency"]] ?></span>
                    </li>
                </ul>
                <ul class="table-com">
                    <li>
                        <span>销售品名</span>
                        <span>数量</span>
                        <?php if($notTonNum>0) :?>
                            <span>单位换算比</span>
                        <?php endif;?>
                        <span>销售单价</span>
                        <span>销售总价</span>
                    </li>
                    <?php foreach ($transactions as $v) { ?>
                        <li>
                            <span><?php echo $v["goods_name"] ?></span>
                            <span><?php echo $v["quantity"], $this->map["goods_unit"][$v["unit"]]['name']; ?></span>
                            <?php if($notTonNum>0) :?><span><?php if($v["unit"]!=ConstantMap::CONTRACT_GOODS_UNIT_CONVERT_VALUE) echo $this->map["goods_unit"][$v["unit"]]['name'].'/'.ConstantMap::CONTRACT_GOODS_UNIT_CONVERT.'='.$v["unit_convert_rate"]; ?></span><?php endif;?>
                            <span><?php echo $this->map["currency"][$v["sell_currency"]]['ico'] . Utility::numberFormatFen2Yuan($v["sale_price"]) ?></span>
                            <span><?php echo $this->map["currency"][$v["sell_currency"]]['ico'] . Utility::numberFormatFen2Yuan($v["sale_amount"]) ?></span>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>

        <?php } ?>

        <?php
        if (!empty($data['plan_describe'])) {
            ?>
            <ul class="item-com">
                <li style="width:100%;height:unset;margin-top:10px;margin-right:0;">
                    <label style="width: unset;">市场分析及<?php echo $this->map['project_buy_sell_type'][$data['buy_sell_type']]; ?>计划：</label>
                    <p class="form-control-static form-control-static-custom"><?php echo $data["plan_describe"] ?></p>
                </li>
            </ul>
        <?php } ?>
    </div>
    <!-- 交易明细 -->

    <!-- 附件信息 -->
    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>附件信息</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <div>
            <ul class="item-com file-info">
                <?php
                $itemHead = '<li>';
                $itemEnd = '</li>';
                $attachs = $this->map["project_launch_attachment_type"];
                if (Utility::isNotEmpty($attachs)) {
                    $index = 0;
                    foreach ($attachs as $key => $row) {
                        if ($index % 1 == 0) echo $itemHead;
                        ?>
                        <label><?php echo $row["name"] ?>：</label>
                        <div>
                            <ul data-bind="foreach:files">
                                <?php if (Utility::isNotEmpty($attachments[$key])) {
                                    foreach ($attachments[$key] as $val) {
                                        if (!empty($val['file_url'])) { ?>
                                            <li class="list-unstyled__upload-list">
                                                <a class="text-name-custom" target="_blank"
                                                   href="/<?php echo $this->getId() ?>/getFile/?id=<?php echo $val['id'] ?>&fileName=<?php echo $val['name'] ?>"><?php echo $val['name'] ?></a>
                                            </li>
                                            <?php
                                        } else {
                                            echo '无';
                                        }
                                    }
                                } else {
                                    echo '<p>无</p>';
                                }
                                ?>
                            </ul>
                        </div>
                        <?php
                        if ($index % 1 != 0) echo $itemEnd;
                        ++$index;
                    }
                }
                ?>
            </ul>
            <ul class="item-com item-com-1">
                <li>
                    <label>备注：</label>
                    <p><?php echo $data['remark']; ?></p>
                </li>
            </ul>
        </div>
    </div>
    <!-- 附件信息 -->

    <!-- 采购合同信息 -->
    <?php if (Utility::isNotEmpty($purchaseData)) { ?>
        <div class="content-wrap">
            <div class="content-wrap-title">
                <div>
                    <p>采购合同信息</p>
                    <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
                </div>
            </div>
            <ul class="table-com">
                <li>
                    <span>采购合同编号</span>
                    <span>外部合同编号</span>
                    <span>上游合作方</span>
                    <span>状态</span>
                    <span>合同签订日期</span>
                    <span>合同文本</span>
                </li>
                <?php foreach ($purchaseData as $v) { ?>
                    <li>
                <span>
                    <?php if (!empty($v['code'])) {
                        echo "<a href='/businessConfirm/detail?id=" . $v['contract_id'] . "'  target='_blank'>" . $v['code'] . "</a>";
                    } else {
                        echo '无';
                    } ?>
                </span>
                        <span><?php echo !empty($v["code_out"]) ? $v["code_out"] : '无' ?></span>
                        <span>
                    <a href="/partner/detail/?id=<?php echo $v['partner_id'] ?>&t=1"
                       title="<?php echo $v['partner_name'] ?>" target="_blank"><?php echo $v['partner_name'] ?></a>
                </span>
                        <span><?php echo $this->map["contract_status"][$v["status"]] ?></span>
                        <span><?php echo $v["contract_date"] ?></span>
                        <span><a href="/contractUpload/detail/?id=<?php echo $data['project_id'] ?>&t=1"
                                 target="_blank">合同文本</a></span>
                    </li>
                <?php } ?>
            </ul>

        </div>

    <?php } ?>
    <!-- 采购合同信息 -->

    <!-- 销售合同信息 -->
    <?php if (Utility::isNotEmpty($saleData)) { ?>


        <div class="content-wrap">
            <div class="content-wrap-title">
                <div>
                    <p>销售合同信息</p>
                    <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
                </div>
            </div>
            <ul class="table-com">
                <li>
                    <span>销售合同编号</span>
                    <span>外部合同编号</span>
                    <span>下游合作方</span>
                    <span>状态</span>
                    <span>合同签订日期</span>
                    <span>合同文本</span>
                </li>
                <?php foreach ($saleData as $v) { ?>
                    <li>
                <span>
                    <?php if (!empty($v['code'])) {
                        echo "<a href='/businessConfirm/detail?id=" . $v['contract_id'] . "'  target='_blank'>" . $v['code'] . "</a>";
                    } else {
                        echo '无';
                    } ?>
                </span>
                        <span><?php echo !empty($v["code_out"]) ? $v["code_out"] : '无' ?></span>
                        <span>
                        <a href="/partner/detail/?id=<?php echo $v['partner_id'] ?>&t=1"
                           title="<?php echo $v['partner_name'] ?>" target="_blank"><?php echo $v['partner_name'] ?></a>
                    </span>
                        <span><?php echo $this->map["contract_status"][$v["status"]] ?></span>
                        <span><?php echo $v["contract_date"] ?></span>
                        <span><a href="/contractUpload/detail/?id=<?php echo $data['project_id'] ?>&t=1"
                                 target="_blank">合同文本</a></span>
                    </li>
                <?php } ?>
            </ul>

        </div>

    <?php } ?>
    <!-- 销售合同信息 -->


    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>创建人信息</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <ul class="item-com item-com-2">
            <li>
                <label style="width:unset;">项目创建人/时间：</label>
                <span><?php echo $data["create_name"] ?> / <?php echo $data["create_time"] ?></span>
            </li>
        </ul>
    </div>

</section>

<script type="text/javascript">
    $(function () {
        var clipboard = new Clipboard('.copy-project-num');
        $('span.box-title__hiden').on('click', function (event) {
            var ele = $(this);
            $(ele).html('');
            var toggle1 = $(ele).parents("div.sub-container__box").find("div.box-content-custom:visible");
            var toggle2 = $(ele).parents("div.sub-container__box").find("div.form-horizontal:visible");
            var toggle3 = $(ele).parents("div.sub-container__box").find("div.box-body-overflow:visible");

            if (toggle1.length > 0 || toggle2.length > 0 || toggle3.length > 0) {
                $(ele).parents("div.sub-container__box").find("div.box-content-custom").hide('slow');
                $(ele).parents("div.sub-container__box").find("table").hide('slow');
                $(ele).parents("div.sub-container__box").find("div.form-horizontal").hide('slow');
                $(ele).parents("div.sub-container__box").find("div.box-body-overflow").hide('slow');
                $(ele).parents("div.sub-container__box").find("div.line-dot").hide('slow');

                var eleI = $('<i class="fa fa-angle-double-down"></i>');
                $(ele).html(' 展开');
                eleI.prependTo($(ele));
            } else {
                $(ele).parents("div.sub-container__box").find("div.box-content-custom").show('slow');
                $(ele).parents("div.sub-container__box").find("table").show('slow');
                $(ele).parents("div.sub-container__box").find("div.form-horizontal").show('slow');
                $(ele).parents("div.sub-container__box").find("div.box-body-overflow").show('slow');
                $(ele).parents("div.sub-container__box").find("div.line-dot").show('slow');


                var eleI = $('<i class="fa fa-angle-double-up"></i>');
                $(ele).html(' 收起');
                eleI.prependTo($(ele));

            }
        });
    });

    function back() {
        location.href = '/<?php echo $this->getId() ?>/';
    }

    function edit() {
        location.href = "/<?php echo $this->getId() ?>/edit/?t=<?php echo $this->isExternal ?>&id=<?php echo $data["project_id"] ?>";
    }

    function copy() {
        inc.vueMessage('复制成功');
    }

    function submit() {
        inc.vueConfirm({
            content: "您确定要提交当前项目信息吗，该操作不可逆？",
            onConfirm: function () {
                var formData = "id=<?php echo $data["project_id"] ?>";
                $.ajax({
                    type: 'POST',
                    url: '/<?php echo $this->getId() ?>/submit',
                    data: formData,
                    dataType: "json",
                    success: function (json) {
                        if (json.state == 0) {
                            inc.vueMessage({
                                message: json.data
                            });
                            location.href = '/<?php echo $this->getId() ?>/';
                        }
                        else {
                            inc.vueAlert(json.data);
                        }
                    },
                    error: function (data) {
                        inc.vueAlert("操作失败！" + data.responseText);
                    }
                });
            }
        })

    }
</script>