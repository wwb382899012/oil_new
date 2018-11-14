<section class="content-header menu-path is-fixed-bread">
    <div class="col flex-grid">
        <a href="javascript: void 0"  onclick="back()">
            <img src="/img/cc-arrow-left-circle.png" class="back-icon" alt="">
            返回
        </a>
        <?php echo empty($this->pageTitle)?$this->moduleName:$this->pageTitle ?>
    </div>
</section>
<div class="content-wrap">
    <div class="content-wrap-title">
        <div>
            <p>货款进项票</p>
        </div>
    </div>
    <ul class="item-com">
        <li>
            <label>交易主体：</label>
            <span>亚太能源（深圳）有限公司</span>
        </li>
        <li>
            <label>货款合同类型：</label>
            <span>采购合同</span>
        </li>
        <li>
            <label>货款合同编号：</label>
            <a href="#">ZYT25NQ18011701</a>
        </li>
        <li>
            <label>项目编号：</label>
            <span>亚太能源（深圳）有限公司</span>
        </li>
        <li>
            <label>发票合同类型：</label>
            <span>国内采购合同</span>
        </li>
        <li>
            <label>发票合同编号：</label>
            <span>YT25NQ180117N02</span>
        </li>
        <li>
            <label>公司名称：</label>
            <span>天津济盛船舶燃料有限公司</span>
        </li>
        <li>
            <label>纳税人识别号：</label>
            <span>911201166794095490</span>
        </li>
    </ul>
</div>
<div class="content-wrap">
    <div class="content-wrap-title">
        <div>
            <p>发票信息</p>
        </div>
    </div>
    <ul class="item-com">
        <li>
            <label>税票类型</label>
            <span>常规增值税专用票</span>
        </li>
        <li>
            <label>汇率</label>
            <span>1.000000</span>
        </li>
        <li>
            <label>发票日期</label>
            <span>2018-01-18</span>
        </li>
        <li>
            <label>发票数量</label>
            <span>9 张</span>
        </li>
    </ul>
    <ul class="form-com form-com-1">
        <li>
            <label>发票明细</label>
            <ul class="table-com">
                <li>
                    <span>品名</span>
                    <span>数量</span>
                    <span>单位</span>
                    <span>单价</span>
                    <span>税率</span>
                    <span>金额<i class="must-logo"></i></span>
                </li>
                <li>
                    <span>120#燃料油</span>
                    <span>245.0000</span>
                    <span>吨</span>
                    <span>￥ 3,765.53</span>
                    <span>17%</span>
                    <span>￥ 922,554.00</span>
                </li>
            </ul>
        </li>
    </ul>
</div>
<div class="content-wrap">
    <div class="content-wrap-title">
        <div>
            <p>付款计划</p>
        </div>
    </div>
    <ul class="form-com form-com-1">
        <li>
            <label>付款计划明细</label>
            <ul class="table-com">
                <li>
                    <span>付款日期</span>
                    <span>付款类别</span>
                    <span>币种</span>
                    <span>计划付款金额</span>
                    <span>已收票金额</span>
                    <span>未收票金额</span>
                    <span>收票金额/span>
                </li>
                <li>
                    <span>2018-01-17</span>
                    <span>预付款</span>
                    <span>人民币</span>
                    <span>200,000.00</span>
                    <span>200,000.00</span>
                    <span>0.00</span>
                    <span>200,000.00</span>
                </li>
                <li>
                    <span>2018-01-17</span>
                    <span>预付款</span>
                    <span>人民币</span>
                    <span>200,000.00</span>
                    <span>200,000.00</span>
                    <span>0.00</span>
                    <span>200,000.00</span>
                </li>
            </ul>
        </li>
    </ul>
    <ul class="item-com item-com-1">
        <li>
            <label>备注</label>
            <span>xxxxxxxxxxxxx</span>
        </li>
    </ul>
    <ul class="form-com form-com-1">
        <li>
            <label>付款计划明细</label>
            <ul class="table-com">
                <li>
                    <span>#</span>
                    <span>审核意见</span>
                    <span>审核节点</span>
                    <span>审核人</span>
                    <span>结果</span>
                    <span>审核时间</span>
                </li>
                <li>
                    <span>1</span>
                    <span>重提</span>
                    <span>税务审核</span>
                    <span>罗玉滢</span>
                    <span>审核驳回</span>
                    <span>2018-01-19 18:52:20</span>
                </li>
            </ul>
        </li>
    </ul>
</div>


<script>
    function back()
    {
        location.href="/<?php echo $this->getId() ?>/";
    }

    function edit(apply_id) {
        location.href = "/<?php echo $this->getId() ?>/edit?id=" + apply_id;
    }

    function submit(apply_id) {
        layer.confirm("您确定要提交当前发票申请信息吗，该操作不可逆？", {icon: 3, 'title': '提示'}, function (index) {
            var formData = "id=" + apply_id;
            $.ajax({
                type: "POST",
                url: "/<?php echo $this->getId() ?>/submit",
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        layer.msg('操作成功', {icon: 6, time: 1000}, function () {
                            location.reload();
                        });
                    }
                    else {
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
                    layer.alert("操作失败！" + data.responseText, {icon: 5});
                }
            });
            layer.close(index);
        });
    }

</script>
