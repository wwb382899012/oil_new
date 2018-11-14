<script src="/js/bootstrap3-typeahead.min.js"></script>
<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<?php
$buttons = [];
$this->loadHeaderWithNewUI([], $buttons, true);
?>

<section class="content sub-container">

    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>合作方详细信息</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <ul class="item-com">
            <li>
                <label>企业名称（全称）：</label>
                <p><?php echo $data["name"] ?></p>
            </li>
            <li>
                <label>统一社会信用代码：</label>
                <p><?php echo $data["credit_code"] ?></p>
            </li>
            <li>
                <label>工商注册号：</label>
                <p><?php echo $data["registration_code"] ?></p>
            </li>
            <li>
                <label>法定代表人：</label>
                <p><?php echo $data["corporate"] ?></p>
            </li>
            <li>
                <label>成立日期：</label>
                <p><?php echo $data["start_date"] ?></p>
            </li>
            <li>
                <label>注册地址：</label>
                <p><?php echo $data["address"] ?></p>
            </li>
            <li>
                <label>登记机关：</label>
                <p><?php echo $data["registration_authority"] ?></p>
            </li>
            <li>
                <label>注册资本（万元）：</label>
                <p><?php echo $data["registered_capital"] ?></p>
            </li>
            <li>
                <label>实收（万元））：</label>
                <p><?php echo $data["paid_up_capital"] ?></p>
            </li>
            <li>
                <label>经营范围：</label>
                <p><?php echo $data["business_scope"] ?></p>
            </li>
            <li>
                <label>企业所有制：</label>
                <p class="form-control-static"><?php $owner = Ownership::model()->findByPk($data["ownership"]);
                    echo $owner->name; ?></p>
            </li>
            <li>
                <label>经营状态：</label>
                <p><?php echo $this->map["runs_state"][$data["runs_state"]] ?></p>
            </li>
            <li>
                <label>是否上市：</label>
                <p class="form-control-static">
                    <?php
                    if ($data['is_stock']) {
                        echo "是";
                    } else {
                        echo "否";
                    }
                    ?>
                </p>
            </li>
            <li>
                <label>上市编号：</label>
                <p class="form-control-static"><?php echo $data["stock_code"] ?></p>
            </li>
            <li>
                <label>上市名称：</label>
                <p><?php echo $data["stock_name"] ?></p>
            </li>
            <li>
                <label>上市板块：</label>
                <p><?php echo $data["stock_type"] ?></p>
            </li>
            <hr/>
            <li>
                <label>客户联系人：</label>
                <p><?php echo $data["contact_person"] ?></p>
            </li>
            <li>
                <label>联系方式：</label>
                <p><?php echo $data["contact_phone"] ?></p>
            </li>
            <li>
                <label>企业类型：</label>
                <p><?php echo $this->map["business_type"][$data["business_type"]] ?></p>
            </li>
            <li>
                <label><?php
                    if ($data['business_type'] == 1) {
                        echo "生产产品";
                    } elseif ($data['business_type'] == 2) {
                        echo "主营产品";
                    }
                    ?>：</label>
                <p><?php echo $data["product"] ?></p>
            </li>
            <li>
                <label><?php
                    if ($data['business_type'] == 1) {
                        echo "生产装置";
                    } elseif ($data['business_type'] == 2) {
                        echo "贸易规模";
                    }
                    ?>：</label>
                <p><?php echo $data["equipment"] ?></p>
            </li>
            <li>
                <label><?php
                    if ($data['business_type'] == 1) {
                        echo "生产规模";
                    } elseif ($data['business_type'] == 2) {
                        echo "行业口碑";
                    }
                    ?>：</label>
                <p><?php echo $data["production_scale"] ?></p>
            </li>
            <li>
                <label>类型：</label>
                <p><?php echo PartnerApplyService::getPartnerType($data["type"]) ?></p>
            </li>
            <li>
                <label>业务员：</label>
                <p><?php echo UserService::getUsernameById($data['user_id']) ?></p>
            </li>
            <li>
                <label>历史合作情况：</label>
                <p><?php echo $data["trade_info"] ?></p>
            </li>
            <li>
                <label>拟合作产品：</label>
                <p><?php
                    $goods_info = GoodsService::getSpecialGoods($data['goods_ids']);
                    $html = array();
                    if (count($goods_info) > 0) {
                        foreach ($goods_info as $row) {
                            $html[] = $row['name'];
                        }
                        echo implode($html, '&nbsp;|&nbsp;');
                    }
                    ?></p>
            </li>
            <li>
                <label>银行名称：</label>
                <p><?php echo $data["bank_name"] ?></p>

            </li>
            <li>
                <label>银行账号：</label>
                <p>
                <p class="form-control-static"><?php echo preg_replace("/(\d{4})(?=\d)/", "$1 ", $data['bank_account']) ?></p>
                </p>
            </li>
            <li>
                <label>纳税识别号：</label>
                <p><?php echo $data["tax_code"] ?></p>
            </li>
            <li>
                <label>电话：</label>
                <p><?php echo $data["phone"] ?></p>
            </li>
            <li>
                <label>商务强制分类：</label>
                <p><?php echo $this->map["partner_level"][$data["custom_level"]] ?></p>
            </li>
            <li>
                <label>风控评审分类：</label>
                <p><?php echo $this->map["partner_level"][$data["level"]] ?></p>
            </li>
            <li>
                <label>系统检测分类：</label>
                <p><?php echo $this->map["partner_level"][$data["auto_level"]] ?></p>
            </li>
            <li>
                <label>备注：</label>
                <p><?php echo $data["remark"] ?></p>
            </li>
            <hr>
            <li>
                <label>确认额度(万元)：</label>
                <p class="form-control-static">
                    ￥ <?php echo number_format($data['credit_amount'] / 10000 / 100, 2) ?></p>
            </li>
            <li>
                <label>创建时间：</label>
                <p><?php echo $data['create_time'] ?></p>
            </li>
            <li>
                <label>更新时间：</label>
                <p><?php echo $data['update_time'] ?></p>
            </li>
        </ul>

    </div>

    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>附件信息</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <?php
        if (empty($attachments)) {
            $attachments = PartnerApplyService::getAttachment($data["partner_id"]);
        }
        $attachmentTypeKey = "partner_apply_attachment_type";
        $this->showAttachmentsEditMultiNew($data["partner_id"], $data, $attachmentTypeKey, $attachments);
        ?>
    </div>

    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>操作日志</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <table id="operationLogs" class="table table-condensed  table-layout">
            <thead>
            <tr>
                <th style='width: 10%;text-align:center;'>序号</th>
                <th style='width: 20%;text-align:center;'>操作人</th>
                <th style='width: 30%; text-align:center'>时间</th>
                <th style='width: 30%; text-align:center;'>操作</th>
                <th style='text-align:center;'>操作详情</th>
            </tr>
            </thead>

            <tbody id="partnerBody" data-bind="foreach: operationLogs">
            <tr class="item">
                <td style='text-align:center;' data-bind="text:id"></td>
                <td style='text-align:center;' data-bind="text:create_user_name"></td>
                <td style='text-align:center' data-bind="text:create_time"></td>
                <td style='text-align:center;' data-bind="text:remark"></td>
                <td style='text-align:center;'><a class="text-link" data-bind="click:function(){$parent.showLogDetailModal($index())}">变更详情</a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>审核记录</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <table class="table table-striped table-hover">
            <tbody>
            <tr>
                <th style="width: 10px">#</th>
                <th>审核意见</th>
                <th style="width: 130px;">审核节点</th>
                <th style="width: 100px;">审核人</th>
                <th style="width: 60px;">结果</th>
                <!-- <th style="width: 100px;">审核详情</th> -->
                <th style="width: 150px;">审核时间</th>

            </tr>
            <?php
            if (Utility::isNotEmpty($checkLogs)) {
                $k = 0;
                foreach ($checkLogs as $v) {
                    $k++;
                    ?>
                    <tr>
                        <td><?php echo $k ?>.</td>
                        <td><?php echo $v["remark"] ?></td>
                        <td><?php echo $v["node_name"] ?></td>
                        <td><?php echo $v["name"] ?></td>
                        <td><?php echo $this->map["check_status"][$v["check_status"]] ?></td>
                        <!-- <td><?php echo '<a href="/check' . $v['business_id'] . '/detail?t=1&check_id=' . $v['check_id'] . '&uid=' . $v['user_id'] . '&b=' . $v['business_id'] . '&id=' . $v['obj_id'] . '" target="_blank">' ?>点击查看</a></td> -->
                        <td><?php echo $v["check_time"] ?></td>

                    </tr>
                    <?php
                }
            }

            ?>

            </tbody>
        </table>

    </div>
    <div class="modal fade draggable-modal" id="logModel" tabindex="-1" role="dialog" aria-labelledby="modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                    <h4 class="modal-title">变更详情</h4>
                </div>
                <div class="modal-body">
                    <div class="content-wrap">
                        <div class="content-wrap-title">
                            <div>
                                <p>变更详情</p>
                            </div>
                        </div>
                        <table id="changeDetails"
                               class="table table-condensed table-hover table-bordered table-layout">
                            <thead>
                            <tr>
                                <th style='width: 20%;text-align:center;'>字段</th>
                                <th style='width: 20%; text-align:center'>字段名</th>
                                <th style='width: 30%; text-align:center;'>旧值</th>
                                <th style='text-align:center;'>新值</th>
                            </tr>
                            </thead>

                            <tbody id="partnerBody" data-bind="foreach: changeDetails">
                            <tr class="item">
                                <td style='text-align:center;' data-bind="text:field"></td>
                                <td style='text-align:center' data-bind="text:field_name"></td>
                                <td style='text-align:center;' data-bind="text:oldValue"></td>
                                <td style='text-align:center;' data-bind="text:newValue"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="javascript: void 0" role="button" class="o-btn o-btn-action w-base" data-dismiss="modal">确定</a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>

    // var operationLogs = <?php echo json_encode($logData['rows']) ?>

    function back() {
        location.href = "/partner/";
    }

    var view;
    $(function () {
        view = new OperationLogsModel(<?php echo json_encode($logData['rows']) ?>);
        ko.applyBindings(view);
    })

    function OperationLogsModel(option) {
        var self = this;
        self.operationLogs = ko.observableArray(option);
        self.changeDetails = ko.observableArray();

        //变更详情
        self.showLogDetailModal = function (index) {
            if (index >= self.operationLogs().length || index < 0) {
                inc.vueAlert("选择有误，请重新选择");
            }
            self.changeDetails(self.operationLogs()[index]['content']);
            $("#logModel").modal({
                backdrop: true,
                keyboard: false,
                show: true
            });
        }
    }
</script>