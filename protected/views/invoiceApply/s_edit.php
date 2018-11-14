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
    <ul class="form-com">
        <li>
            <label>交易主体<i class="must-logo">*</i></label>
            <select>
                <option value="1">11111</option>
                <option value="2">22222</option>
                <option value="3">33333</option>
            </select>
        </li>
        <li>
            <label>货款合同类型</label>
            <span></span>
        </li>
        <li>
            <label>货款合同编号<i class="must-logo">*</i></label>
            <select>
                <option value="1">11111</option>
                <option value="2">22222</option>
                <option value="3">33333</option>
            </select>
        </li>
        <li>
            <label>项目编号<i class="must-logo">*</i></label>
            <select>
                <option value="1">11111</option>
                <option value="2">22222</option>
                <option value="3">33333</option>
            </select>
        </li>
        <li>
            <label>发票合同类型<i class="must-logo">*</i></label>
            <select>
                <option value="1">11111</option>
                <option value="2">22222</option>
                <option value="3">33333</option>
            </select>
        </li>
        <li>
            <label>发票合同编号<i class="must-logo">*</i></label>
            <input class="form-control" type="text">
        </li>
        <li>
            <label>公司名称<i class="must-logo">*</i></label>
            <input class="form-control" type="text">
        </li>
        <li>
            <label>纳税人识别号<i class="must-logo">*</i></label>
            <input class="form-control" type="text">
        </li>
    </ul>
</div>
<div class="content-wrap">
    <div class="content-wrap-title">
        <div>
            <p>发票信息</p>
        </div>
    </div>
    <ul class="form-com">
        <li>
            <label>税票类型<i class="must-logo">*</i></label>
            <select>
                <option value="1">11111</option>
                <option value="2">22222</option>
                <option value="3">33333</option>
            </select>
        </li>
        <li>
            <label>汇率<i class="must-logo">*</i></label>
            <input class="form-control" type="text">
        </li>
        <li>
            <label>发票日期<i class="must-logo">*</i></label>
            <input class="form-control" type="text">
        </li>
        <li>
            <label>发票数量<i class="must-logo">*</i></label>
            <input class="form-control" type="text">
        </li>
    </ul>
    <ul class="table-com">
        <li>
            <span>品名<i class="must-logo">*</i></span>
            <span>数量<i class="must-logo">*</i></span>
            <span>单位<i class="must-logo">*</i></span>
            <span>单价<i class="must-logo">*</i></span>
            <span>税率<i class="must-logo">*</i></span>
            <span>金额<i class="must-logo"></i></span>
            <span>操作</span>
        </li>
        <li>
            <select>
                <option value="1">11111</option>
                <option value="2">22222</option>
                <option value="3">33333</option>
            </select>
            <input class="form-control" type="text">
            <select>
                <option value="1">11111</option>
                <option value="2">22222</option>
                <option value="3">33333</option>
            </select>
            <span></span>
            <select>
                <option value="1">11111</option>
                <option value="2">22222</option>
                <option value="3">33333</option>
            </select>
            <div class="input-with-logo-left">
                <span>¥</span>
                <input class="form-control" type="text">
            </div>
            <button>删除</button>
        </li>
        <li class="li-add">
            <button>新增</button>
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
            <label>付款计划明细<i class="must-logo">*</i></label>
            <ul class="table-com">
                <li>
                    <input type="checkbox">
                    <span>预计付款日期</span>
                    <span>付款类别</span>
                    <span>计划付款金额</span>
                    <span>币种</span>
                    <span>已收票金额</span>
                    <span>未收票金额</span>
                    <span>收票金额<i class="must-logo">*</i></span>
                </li>
                <li>
                    <input type="checkbox">
                    <span>2018-01-17</span>
                    <span>预付款</span>
                    <span>￥  200,000.00</span>
                    <span>人民币</span>
                    <span>￥  200,000.00</span>
                    <span>￥  0.00</span>
                    <div class="input-with-logo-left">
                        <span>¥</span>
                        <input class="form-control" type="text">
                    </div>
                </li>
            </ul>
        </li>
        <li>
            <label>附件</label>
            <button>选择上传文件</button>
        </li>
        <li>
            <label>备注</label>
            <textarea></textarea>
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
