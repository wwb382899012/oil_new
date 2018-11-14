<link rel="stylesheet" href="/css/common/index.css">
<link rel="stylesheet" href="/css/common/dataTables.css">
<script src="/js/bootstrap-select/bootstrap-select.min.js" type="text/javascript"></script>
<link href="/css/bootstrap-select/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
<script src="/js/plugins/jqPaginator-1.2.1/dist/jqpaginator.min.js"></script>

<section class="content-header menu-path is-fixed-bread">
    <div class="col flex-grid">
        <a href="javascript: void 0">
            <img src="/img/cc-arrow-left-circle.png" class="back-icon" alt="">
            返回
        </a>
        <ul class="flex-grid">
            <li>一级菜单</li>
            <li>二级菜单</li>
            <li class="active">三级菜单</li>
        </ul>
    </div>
    <div class="flex-grid col">
        <button type="button" class="el-button el-button--default col"><span>按钮</span></button>
        <button type="button" class="el-button el-button--theme col"><span>按钮</span></button>
    </div>
</section>
<section class="el-container is-vertical">
    <div class="main-content">
        <h3 class="header margin-b-30">项目列表</h3>
        <form action="">
            <div class="condition-fields">
                <div class="flex-grid form-group">
                    <label class="col field flex-grid">
                        <span class="w-100">合作方名称:</span>
                        <span class="form-control-static ellipsis"><a href="javascript: void 0" class="text-link" title="东菀市福润达商贸有限责任公司">东菀市福润达商贸有限责任公司</a></span>
                    </label>
                    <label class="col field flex-grid">
                        <span class="w-100">当前合同额度:</span>
                        <span class="form-control-static ellipsis">￥37.91万元</span>
                    </label>
                </div>
                <div class="flex-grid form-group">
                    <label class="col field flex-grid">
                        <span class="w-100">项目编号:</span>
                        <input type="text" autocomplete="off" placeholder="项目编号" class="el-input__inner">
                    </label>
                    <label class="col field flex-grid">
                        <span class="w-100">项目类型:</span>
                        <input type="text" autocomplete="off" placeholder="全部" class="el-input__inner">
                    </label>
                    <div class="col flex-grid">
                        <button type="button" class="el-button el-button--default col"><span>查询</span></button>
                        <button type="button" class="el-button el-button--theme col"><span>添加</span></button>
                        <a href="javascript: void 0" class="col" id="toggle-fields" onclick="toggleFields()">收起搜索 <img src="/img/arrow-top-o.png" style="width: 10px;vertical-align: middle;"></a>
                    </div>
                </div>
                <div class="flex-grid form-group is-hidden">
                    <label class="col field flex-grid">
                        <span class="w-100">状态:</span>
                        <select class="selectpicker show-menu-arrow form-control">
                            <option value="">全部</option>
                            <option value="1">已审核</option>
                            <option value="2">未审核</option>
                        </select>
                    </label>
                    <label class="col field flex-grid">
                        <span class="w-100">项目负责人:</span>
                        <select class="selectpicker show-menu-arrow form-control" data-live-search="true">
                            <option value="1">james</option>
                            <option value="2">jackie</option>
                            <option value="3">hello</option>
                            <option value="4">tom</option>
                        </select>
                    </label>
                    <label class="col field flex-grid">
                        <span class="w-100">上游合作方:</span>
                        <input type="text" autocomplete="off" placeholder="上游合作方" class="el-input__inner">
                    </label>
                </div>
                <div class="flex-grid form-group is-hidden">
                    <label class="col field flex-grid">
                        <span class="w-100">下游合作方:</span>
                        <input type="text" autocomplete="off" placeholder="下游合作方" class="el-input__inner">
                    </label>
                    <label class="col field flex-grid">
                        <span class="w-100">开始时间:</span>
                        <input type="text" autocomplete="off" placeholder="时间" class="el-input__inner form_datetime">
                    </label>
                    <label class="col field flex-grid">
                        <span class="w-100">结束时间:</span>
                        <input type="text" autocomplete="off" placeholder="时间" class="el-input__inner form_datetime">
                    </label>
                </div>
            </div>
            <div class="tabs margin-b-30">
                <div class="el-button-group">
                    <button type="button" class="el-button el-button--default active">
                        合同下付款
                    </button>
                    <button type="button" class="el-button el-button--default">
                        多合同合并付款
                    </button>
                    <button type="button" class="el-button el-button--default">
                        项目下付款
                    </button>
                    <button type="button" class="el-button el-button--default">
                        交易主体下付款
                    </button>
                    <button type="button" class="el-button el-button--default">
                        后补项目合同付款
                    </button>
                </div>
            </div>
        </form>
        <div class="main-content_inner">
            <table class="data-table dataTable stripe hover nowrap">
                <thead>
                <tr>
                    <th rowspan="1" colspan="1">合同ID</th>
                    <th rowspan="1" colspan="1">合同编号</th>
                    <th rowspan="1" colspan="1">外部合同编号</th>
                    <th rowspan="1" colspan="1">交易主体</th>
                    <th rowspan="1" colspan="1">合同状态</th>
                    <th rowspan="1" colspan="1">合同类型</th>
                    <th rowspan="1" colspan="1">合作方</th>
                    <th rowspan="1" colspan="1">合同签订日期</th>
                    <th rowspan="1" colspan="1">项目编号</th>
                    <th rowspan="1" colspan="1">项目类型</th>
                    <th rowspan="1" colspan="1">品名</th>
                    <th rowspan="1" colspan="1">合同总金额</th>
                    <th rowspan="1" colspan="1">合同人民币金额</th>
                    <th rowspan="1" colspan="1">项目负责人</th>
                    <th rowspan="1" colspan="1">操作</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <a href="/contract/detail?id=1159">1159</a>
                    </td>
                    <td>
                        <a href="/contract/detail?id=1159&amp;t=1"></a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/corporation/detail?id=4&amp;t=1">亚太能源（深圳）有限公司</a>
                    </td>
                    <td>风控审核中</td>
                    <td>采购合同</td>
                    <td>
                        <a href="/partner/detail?id=543&amp;t=1">东营市福润达商贸有限责任公司</a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/project/detail/?id=20180615017&amp;t=1">ZYT234324JQ18061503</a>
                    </td>
                    <td>进口渠道</td>
                    <td>成品油</td>
                    <td>$17,600.00</td>
                    <td>￥ 117,920.00</td>
                    <td>温文斌</td>
                    <td>
                        <a href="/contract/detail?id=1159">详情</a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="/contract/detail?id=1158">1158</a>
                    </td>
                    <td>
                        <a href="/contract/detail?id=1158&amp;t=1">YT234324JQ180615S04</a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/corporation/detail?id=4&amp;t=1">亚太能源（深圳）有限公司</a>
                    </td>
                    <td>业务审核通过</td>
                    <td>销售合同</td>
                    <td>
                        <a href="/partner/detail?id=546&amp;t=1">广州凯中石油化工有限公司</a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/project/detail/?id=20180615003&amp;t=1">ZYT234324JQ18061502</a>
                    </td>
                    <td>进口渠道</td>
                    <td>成品油&nbsp;|&nbsp;柴油</td>
                    <td>￥26,600.00</td>
                    <td>￥ 26,600.00</td>
                    <td>温文斌</td>
                    <td>
                        <a href="/contract/detail?id=1158">详情</a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a href="/contract/detail?id=1157">1157</a>
                    </td>
                    <td>
                        <a href="/contract/detail?id=1157&amp;t=1">YT234324JQ180615D03</a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/corporation/detail?id=4&amp;t=1">亚太能源（深圳）有限公司</a>
                    </td>
                    <td>合同结算中</td>
                    <td>采购合同</td>
                    <td>
                        <a href="/partner/detail?id=543&amp;t=1">东营市福润达商贸有限责任公司</a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/project/detail/?id=20180615003&amp;t=1">ZYT234324JQ18061502</a>
                    </td>
                    <td>进口渠道</td>
                    <td>成品油&nbsp;|&nbsp;柴油</td>
                    <td>￥23,200.00</td>
                    <td>￥ 23,200.00</td>
                    <td>温文斌</td>
                    <td>
                        <a href="/contract/detail?id=1157">详情</a>
                    </td>

                </tr>
                <tr>
                    <td>
                        <a href="/contract/detail?id=1156">1156</a>
                    </td>
                    <td>
                        <a href="/contract/detail?id=1156&amp;t=1">YT234324JQ180615S02</a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/corporation/detail?id=4&amp;t=1">亚太能源（深圳）有限公司</a>
                    </td>
                    <td>业务审核通过</td>
                    <td>销售合同</td>
                    <td>
                        <a href="/partner/detail?id=546&amp;t=1">广州凯中石油化工有限公司</a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/project/detail/?id=20180615002&amp;t=1">ZYT234324JQ18061501</a>
                    </td>
                    <td>进口渠道</td>
                    <td>成品油&nbsp;|&nbsp;柴油</td>
                    <td>￥232,000.00</td>
                    <td>￥ 232,000.00</td>
                    <td>温文斌</td>
                    <td>
                        <a href="/contract/detail?id=1156">详情</a>
                    </td>

                </tr>
                <tr>
                    <td>
                        <a href="/contract/detail?id=1155">1155</a>
                    </td>
                    <td>
                        <a href="/contract/detail?id=1155&amp;t=1">YT234324JQ180615D01</a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/corporation/detail?id=4&amp;t=1">亚太能源（深圳）有限公司</a>
                    </td>
                    <td>合同已结算</td>
                    <td>采购合同</td>
                    <td>
                        <a href="/partner/detail?id=543&amp;t=1">东营市福润达商贸有限责任公司</a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/project/detail/?id=20180615002&amp;t=1">ZYT234324JQ18061501</a>
                    </td>
                    <td>进口渠道</td>
                    <td>成品油&nbsp;|&nbsp;柴油</td>
                    <td>￥188,000.00</td>
                    <td>￥ 188,000.00</td>
                    <td>温文斌</td>
                    <td>
                        <a href="/contract/detail?id=1155">详情</a>
                    </td>

                </tr>
                <tr>
                    <td>
                        <a href="/contract/detail?id=1154">1154</a>
                    </td>
                    <td>
                        <a href="/contract/detail?id=1154&amp;t=1">YT234324JQ180614S10</a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/corporation/detail?id=4&amp;t=1">亚太能源（深圳）有限公司</a>
                    </td>
                    <td>业务审核通过</td>
                    <td>销售合同</td>
                    <td>
                        <a href="/partner/detail?id=546&amp;t=1">广州凯中石油化工有限公司</a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/project/detail/?id=20180614006&amp;t=1">ZYT234324JQ18061401</a>
                    </td>
                    <td>进口渠道</td>
                    <td>成品油</td>
                    <td>￥110,000.00</td>
                    <td>￥ 110,000.00</td>
                    <td>温文斌</td>
                    <td>
                        <a href="/contract/detail?id=1154">详情</a>
                    </td>

                </tr>
                <tr>
                    <td>
                        <a href="/contract/detail?id=1153">1153</a>
                    </td>
                    <td>
                        <a href="/contract/detail?id=1153&amp;t=1">YT234324JQ180614D09</a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/corporation/detail?id=4&amp;t=1">亚太能源（深圳）有限公司</a>
                    </td>
                    <td>业务审核通过</td>
                    <td>采购合同</td>
                    <td>
                        <a href="/partner/detail?id=543&amp;t=1">东营市福润达商贸有限责任公司</a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/project/detail/?id=20180614006&amp;t=1">ZYT234324JQ18061401</a>
                    </td>
                    <td>进口渠道</td>
                    <td>成品油</td>
                    <td>￥100,000.00</td>
                    <td>￥ 100,000.00</td>
                    <td>温文斌</td>
                    <td>
                        <a href="/contract/detail?id=1153">详情</a>
                    </td>

                </tr>
                <tr>
                    <td>
                        <a href="/contract/detail?id=1152">1152</a>
                    </td>
                    <td>
                        <a href="/contract/detail?id=1152&amp;t=1"></a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/corporation/detail?id=5&amp;t=1">深圳前海泰丰能源有限公司</a>
                    </td>
                    <td>风控审核中</td>
                    <td>销售合同</td>
                    <td>
                        <a href="/partner/detail?id=538&amp;t=1">中油广通(大连)石油化工有限公司</a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/project/detail/?id=20180417005&amp;t=1">ZTF41ZN18041702</a>
                    </td>
                    <td>内贸自营</td>
                    <td>轻质循环油</td>
                    <td>￥538,164.00</td>
                    <td>￥ 538,164.00</td>
                    <td>余芝文</td>
                    <td>
                        <a href="/contract/detail?id=1152">详情</a>
                    </td>

                </tr>
                <tr>
                    <td>
                        <a href="/contract/detail?id=1150">1150</a>
                    </td>
                    <td>
                        <a href="/contract/detail?id=1150&amp;t=1">TF41ZN180614S02</a>
                    </td>
                    <td>TF-SSW1588-18060802-B</td>
                    <td>
                        <a href="/corporation/detail?id=5&amp;t=1">深圳前海泰丰能源有限公司</a>
                    </td>
                    <td>最终文件已上传</td>
                    <td>销售合同</td>
                    <td>
                        <a href="/partner/detail?id=545&amp;t=1">宁波市东海长城石化有限公司</a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/project/detail/?id=20180522006&amp;t=1">ZTF41ZN18052202</a>
                    </td>
                    <td>内贸自营</td>
                    <td>纯生物柴油</td>
                    <td>￥5,430,000.00</td>
                    <td>￥ 5,430,000.00</td>
                    <td>余芝文</td>
                    <td>
                        <a href="/contract/detail?id=1150">详情</a>
                    </td>

                </tr>
                <tr>
                    <td>
                        <a href="/contract/detail?id=1149">1149</a>
                    </td>
                    <td>
                        <a href="/contract/detail?id=1149&amp;t=1">TF41ZN180614S03</a>
                    </td>
                    <td>TF-SXH1588-18061301-B</td>
                    <td>
                        <a href="/corporation/detail?id=5&amp;t=1">深圳前海泰丰能源有限公司</a>
                    </td>
                    <td>最终文件已上传</td>
                    <td>销售合同</td>
                    <td>
                        <a href="/partner/detail?id=538&amp;t=1">中油广通(大连)石油化工有限公司</a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/project/detail/?id=20180417005&amp;t=1">ZTF41ZN18041702</a>
                    </td>
                    <td>内贸自营</td>
                    <td>轻质循环油</td>
                    <td>￥356,796.00</td>
                    <td>￥ 356,796.00</td>
                    <td>余芝文</td>
                    <td>
                        <a href="/contract/detail?id=1149">详情</a>
                    </td>

                </tr>
                <tr>
                    <td>
                        <a href="/contract/detail?id=1148">1148</a>
                    </td>
                    <td>
                        <a href="/contract/detail?id=1148&amp;t=1">TF41ZN180614S04</a>
                    </td>
                    <td>TF-SSW15-18060801-B</td>
                    <td>
                        <a href="/corporation/detail?id=5&amp;t=1">深圳前海泰丰能源有限公司</a>
                    </td>
                    <td>最终文件已上传</td>
                    <td>销售合同</td>
                    <td>
                        <a href="/partner/detail?id=544&amp;t=1">广州公交集团能源有限公司</a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/project/detail/?id=20180522006&amp;t=1">ZTF41ZN18052202</a>
                    </td>
                    <td>内贸自营</td>
                    <td>纯生物柴油</td>
                    <td>￥48,600,000.00</td>
                    <td>￥ 48,600,000.00</td>
                    <td>余芝文</td>
                    <td>
                        <a href="/contract/detail?id=1148">详情</a>
                    </td>

                </tr>
                <tr>
                    <td>
                        <a href="/contract/detail?id=1145">1145</a>
                    </td>
                    <td>
                        <a href="/contract/detail?id=1145&amp;t=1">TF41ZN180614S06</a>
                    </td>
                    <td>TF-SSW1501-18060804-B</td>
                    <td>
                        <a href="/corporation/detail?id=5&amp;t=1">深圳前海泰丰能源有限公司</a>
                    </td>
                    <td>最终文件已上传</td>
                    <td>销售合同</td>
                    <td>
                        <a href="/partner/detail?id=542&amp;t=1">湛江鑫磊石化有限公司</a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/project/detail/?id=20180522006&amp;t=1">ZTF41ZN18052202</a>
                    </td>
                    <td>内贸自营</td>
                    <td>纯生物柴油</td>
                    <td>￥10,860,000.00</td>
                    <td>￥ 10,860,000.00</td>
                    <td>余芝文</td>
                    <td>
                        <a href="/contract/detail?id=1145">详情</a>
                    </td>

                </tr>
                <tr>
                    <td>
                        <a href="/contract/detail?id=1144">1144</a>
                    </td>
                    <td>
                        <a href="/contract/detail?id=1144&amp;t=1">TF41ZN180614S05</a>
                    </td>
                    <td>TF-SSW1588-18060803-B</td>
                    <td>
                        <a href="/corporation/detail?id=5&amp;t=1">深圳前海泰丰能源有限公司</a>
                    </td>
                    <td>最终文件已上传</td>
                    <td>销售合同</td>
                    <td>
                        <a href="/partner/detail?id=546&amp;t=1">广州凯中石油化工有限公司</a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/project/detail/?id=20180522006&amp;t=1">ZTF41ZN18052202</a>
                    </td>
                    <td>内贸自营</td>
                    <td>纯生物柴油</td>
                    <td>￥5,430,000.00</td>
                    <td>￥ 5,430,000.00</td>
                    <td>余芝文</td>
                    <td>
                        <a href="/contract/detail?id=1144">详情</a>
                    </td>

                </tr>
                <tr>
                    <td>
                        <a href="/contract/detail?id=1140">1140</a>
                    </td>
                    <td>
                        <a href="/contract/detail?id=1140&amp;t=1">TF41ZN180614S07</a>
                    </td>
                    <td>TF-SXH1588-18061201-B</td>
                    <td>
                        <a href="/corporation/detail?id=5&amp;t=1">深圳前海泰丰能源有限公司</a>
                    </td>
                    <td>最终文件已上传</td>
                    <td>销售合同</td>
                    <td>
                        <a href="/partner/detail?id=538&amp;t=1">中油广通(大连)石油化工有限公司</a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/project/detail/?id=20180417005&amp;t=1">ZTF41ZN18041702</a>
                    </td>
                    <td>内贸自营</td>
                    <td>轻质循环油</td>
                    <td>￥529,788.00</td>
                    <td>￥ 529,788.00</td>
                    <td>余芝文</td>
                    <td>
                        <a href="/contract/detail?id=1140">详情</a>
                    </td>

                </tr>
                <tr>
                    <td>
                        <a href="/contract/detail?id=1139">1139</a>
                    </td>
                    <td>
                        <a href="/contract/detail?id=1139&amp;t=1">YT41ZN180612S03</a>
                    </td>
                    <td>YT-SXH150118061101-A</td>
                    <td>
                        <a href="/corporation/detail?id=4&amp;t=1">亚太能源（深圳）有限公司</a>
                    </td>
                    <td>纸质双签文件已上传</td>
                    <td>销售合同</td>
                    <td>
                        <a href="/partner/detail?id=541&amp;t=1">中铁中宇能源有限公司</a>
                    </td>
                    <td>2018-06-11</td>
                    <td>
                        <a href="/project/detail/?id=20180312002&amp;t=1">ZYT41ZN031202</a>
                    </td>
                    <td>内贸自营</td>
                    <td>0#车用柴油（V）</td>
                    <td>￥1,950,000.00</td>
                    <td>￥ 1,950,000.00</td>
                    <td>林婷婷</td>
                    <td>
                        <a href="/contract/detail?id=1139">详情</a>
                    </td>

                </tr>
                <tr>
                    <td>
                        <a href="/contract/detail?id=1137">1137</a>
                    </td>
                    <td>
                        <a href="/contract/detail?id=1137&amp;t=1">TF41ZN180612S01</a>
                    </td>
                    <td>TF-SXH1501-18061101-B</td>
                    <td>
                        <a href="/corporation/detail?id=5&amp;t=1">深圳前海泰丰能源有限公司</a>
                    </td>
                    <td>电子双签文件已上传</td>
                    <td>销售合同</td>
                    <td>
                        <a href="/partner/detail?id=538&amp;t=1">中油广通(大连)石油化工有限公司</a>
                    </td>
                    <td>2018-06-11</td>
                    <td>
                        <a href="/project/detail/?id=20180417005&amp;t=1">ZTF41ZN18041702</a>
                    </td>
                    <td>内贸自营</td>
                    <td>轻质循环油</td>
                    <td>￥2,675,000.00</td>
                    <td>￥ 2,675,000.00</td>
                    <td>余芝文</td>
                    <td>
                        <a href="/contract/detail?id=1137">详情</a>
                    </td>

                </tr>
                <tr>
                    <td>
                        <a href="/contract/detail?id=1136">1136</a>
                    </td>
                    <td>
                        <a href="/contract/detail?id=1136&amp;t=1">YT41ZN180612S02</a>
                    </td>
                    <td>TJ-P-2018-117</td>
                    <td>
                        <a href="/corporation/detail?id=4&amp;t=1">亚太能源（深圳）有限公司</a>
                    </td>
                    <td>电子双签文件已上传</td>
                    <td>销售合同</td>
                    <td>
                        <a href="/partner/detail?id=536&amp;t=1">珠海港通江物资供应有限公司</a>
                    </td>
                    <td>2018-06-11</td>
                    <td>
                        <a href="/project/detail/?id=20180312002&amp;t=1">ZYT41ZN031202</a>
                    </td>
                    <td>内贸自营</td>
                    <td>0#车用柴油</td>
                    <td>￥5,868,000.00</td>
                    <td>￥ 5,868,000.00</td>
                    <td>林婷婷</td>
                    <td>
                        <a href="/contract/detail?id=1136">详情</a>
                    </td>

                </tr>
                <tr>
                    <td>
                        <a href="/contract/detail?id=1130">1130</a>
                    </td>
                    <td>
                        <a href="/contract/detail?id=1130&amp;t=1">TF41ZN180607S02</a>
                    </td>
                    <td>TF-SSW1586-18060501-B</td>
                    <td>
                        <a href="/corporation/detail?id=5&amp;t=1">深圳前海泰丰能源有限公司</a>
                    </td>
                    <td>电子双签文件已上传</td>
                    <td>销售合同</td>
                    <td>
                        <a href="/partner/detail?id=539&amp;t=1">珠海九洲能源有限公司</a>
                    </td>
                    <td>2018-06-05</td>
                    <td>
                        <a href="/project/detail/?id=20180522003&amp;t=1">ZTF41ZN18052201</a>
                    </td>
                    <td>内贸自营</td>
                    <td>纯生物柴油</td>
                    <td>￥29,672,500.00</td>
                    <td>￥ 29,672,500.00</td>
                    <td>余芝文</td>
                    <td>
                        <a href="/contract/detail?id=1130">详情</a>
                    </td>

                </tr>
                <tr>
                    <td>
                        <a href="/contract/detail?id=1129">1129</a>
                    </td>
                    <td>
                        <a href="/contract/detail?id=1129&amp;t=1">TF41ZN180607S01</a>
                    </td>
                    <td>TF-SXH1501-16060601-B</td>
                    <td>
                        <a href="/corporation/detail?id=5&amp;t=1">深圳前海泰丰能源有限公司</a>
                    </td>
                    <td>电子双签文件已上传</td>
                    <td>销售合同</td>
                    <td>
                        <a href="/partner/detail?id=538&amp;t=1">中油广通(大连)石油化工有限公司</a>
                    </td>
                    <td>2018-06-06</td>
                    <td>
                        <a href="/project/detail/?id=20180417005&amp;t=1">ZTF41ZN18041702</a>
                    </td>
                    <td>内贸自营</td>
                    <td>轻质循环油</td>
                    <td>￥2,682,500.00</td>
                    <td>￥ 2,682,500.00</td>
                    <td>余芝文</td>
                    <td>
                        <a href="/contract/detail?id=1129">详情</a>
                    </td>

                </tr>
                <tr>
                    <td>
                        <a href="/contract/detail?id=1125">1125</a>
                    </td>
                    <td>
                        <a href="/contract/detail?id=1125&amp;t=1">YT41ZN180605S03</a>
                    </td>
                    <td>TJ-P-2018-110</td>
                    <td>
                        <a href="/corporation/detail?id=4&amp;t=1">亚太能源（深圳）有限公司</a>
                    </td>
                    <td>电子双签文件已上传</td>
                    <td>销售合同</td>
                    <td>
                        <a href="/partner/detail?id=536&amp;t=1">珠海港通江物资供应有限公司</a>
                    </td>
                    <td>2018-06-04</td>
                    <td>
                        <a href="/project/detail/?id=20180312002&amp;t=1">ZYT41ZN031202</a>
                    </td>
                    <td>内贸自营</td>
                    <td>0#车用柴油</td>
                    <td>￥3,036,000.00</td>
                    <td>￥ 3,036,000.00</td>
                    <td>林婷婷</td>
                    <td>
                        <a href="/contract/detail?id=1125">详情</a>
                    </td>

                </tr>
                <tr>
                    <td>
                        <a href="/contract/detail?id=1124">1124</a>
                    </td>
                    <td>
                        <a href="/contract/detail?id=1124&amp;t=1">TF41ZN180605S01</a>
                    </td>
                    <td>TF-SXH86-18060401-B</td>
                    <td>
                        <a href="/corporation/detail?id=5&amp;t=1">深圳前海泰丰能源有限公司</a>
                    </td>
                    <td>电子双签文件已上传</td>
                    <td>销售合同</td>
                    <td>
                        <a href="/partner/detail?id=523&amp;t=1">广州杰盛石油化工有限公司</a>
                    </td>
                    <td>2018-06-04</td>
                    <td>
                        <a href="/project/detail/?id=20180417001&amp;t=1">ZTF41ZN18041701</a>
                    </td>
                    <td>内贸自营</td>
                    <td>轻质循环油</td>
                    <td>￥2,312,800.00</td>
                    <td>￥ 2,312,800.00</td>
                    <td>余芝文</td>
                    <td>
                        <a href="/contract/detail?id=1124">详情</a>
                    </td>

                </tr>
                <tr>
                    <td>
                        <a href="/contract/detail?id=1123">1123</a>
                    </td>
                    <td>
                        <a href="/contract/detail?id=1123&amp;t=1">HH17ZN180605S02</a>
                    </td>
                    <td>ZYHH-JSAEF20180402502</td>
                    <td>
                        <a href="/corporation/detail?id=7&amp;t=1">中油海化石油化工（大连）有限公司</a>
                    </td>
                    <td>最终文件已上传</td>
                    <td>销售合同</td>
                    <td>
                        <a href="/partner/detail?id=138&amp;t=1">江苏阿尔法船舶燃料贸易有限公司</a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/project/detail/?id=20180316003&amp;t=1">ZHH17ZN18031603</a>
                    </td>
                    <td>内贸自营</td>
                    <td>92#车用汽油（V）</td>
                    <td>￥21,540,000.00</td>
                    <td>￥ 21,540,000.00</td>
                    <td>张鹏</td>
                    <td>
                        <a href="/contract/detail?id=1123">详情</a>
                    </td>

                </tr>
                <tr>
                    <td>
                        <a href="/contract/detail?id=1122">1122</a>
                    </td>
                    <td>
                        <a href="/contract/detail?id=1122&amp;t=1">HH17ZN180604S08</a>
                    </td>
                    <td>ZYHH-JSAEF2018050301</td>
                    <td>
                        <a href="/corporation/detail?id=7&amp;t=1">中油海化石油化工（大连）有限公司</a>
                    </td>
                    <td>最终文件已上传</td>
                    <td>销售合同</td>
                    <td>
                        <a href="/partner/detail?id=138&amp;t=1">江苏阿尔法船舶燃料贸易有限公司</a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/project/detail/?id=20180402001&amp;t=1">ZHH17ZN18040201</a>
                    </td>
                    <td>内贸自营</td>
                    <td>92#车用汽油（V）</td>
                    <td>￥21,780,000.00</td>
                    <td>￥ 21,780,000.00</td>
                    <td>张鹏</td>
                    <td>
                        <a href="/contract/detail?id=1122">详情</a>
                    </td>

                </tr>
                <tr>
                    <td>
                        <a href="/contract/detail?id=1120">1120</a>
                    </td>
                    <td>
                        <a href="/contract/detail?id=1120&amp;t=1">HH17ZN180604S07</a>
                    </td>
                    <td>SHXH5-64</td>
                    <td>
                        <a href="/corporation/detail?id=7&amp;t=1">中油海化石油化工（大连）有限公司</a>
                    </td>
                    <td>最终文件已上传</td>
                    <td>销售合同</td>
                    <td>
                        <a href="/partner/detail?id=222&amp;t=1">上海兴海石油化工有限公司</a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/project/detail/?id=20180507002&amp;t=1">ZHH17ZN18050702</a>
                    </td>
                    <td>内贸自营</td>
                    <td>0#车用柴油（V）</td>
                    <td>￥20,040,000.00</td>
                    <td>￥ 20,040,000.00</td>
                    <td>张鹏</td>
                    <td>
                        <a href="/contract/detail?id=1120">详情</a>
                    </td>

                </tr>
                <tr>
                    <td>
                        <a href="/contract/detail?id=1160">1160</a>
                    </td>
                    <td>
                        <a href="/contract/detail?id=1160&amp;t=1"></a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/corporation/detail?id=4&amp;t=1">亚太能源（深圳）有限公司</a>
                    </td>
                    <td>风控审核中</td>
                    <td>销售合同</td>
                    <td>
                        <a href="/partner/detail?id=546&amp;t=1">广州凯中石油化工有限公司</a>
                    </td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="/project/detail/?id=20180615017&amp;t=1">ZYT234324JQ18061503</a>
                    </td>
                    <td>进口渠道</td>
                    <td>成品油</td>
                    <td>$19,800.00</td>
                    <td>￥ 132,660.00</td>
                    <td>温文斌</td>
                    <td>
                        <a href="/contract/detail?id=1160">详情</a>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="main-content_footer flex-grid">
                <div>共 <span>800 </span> 条</div>
                <div class="margin-lr-15">
                    <span>每页</span>
                    <select class="show-menu-arrow" data-width="80px">
                        <option selected="selected">10</option>
                        <option>20</option>
                        <option>30</option>
                    </select>
                    <span>条</span>
                </div>
                <div class="el-pagination is-background" align="left" id="pagination">
                </div>
                <div class="route-to margin-lr-15">
                    前往
                    <input type="text" size="1" style="width: 50px;" value="1">
                    页
                </div>
            </div>

        </div>
    </div>
</section>

<script>
    function toggleFields() {
        $('.condition-fields .is-hidden').toggle()
    }
    $(function(){
        // 下拉框控件
      $('.selectpicker').selectpicker();
      // 日期控件， 可自由配置年月日时分显示; docs: http://www.bootcss.com/p/bootstrap-datetimepicker/index.htm
      $(".form_datetime").datetimepicker({format: 'yyyy-mm-dd', minView: '2', startView: '2'});
    if (!page.checkFieldHasValue('.condition-fields')) {
      // 搜索区域没有值， 隐藏指定的隐藏区域
      toggleFields();
    }
    $('.tabs').on('click', function(e) {
      let targetElem = e.target;
      if (targetElem.tagName === 'BUTTON') {
        $('.tabs button').removeClass('active');
        $(targetElem).addClass('active');
      }
    });

    page.initTableAndPagination({
      totalPages: 100,
      currentPage: 1,
      onPageChange: function(n) {
        console.log('当前第' + n + '页');
      }
    })
  })
</script>
