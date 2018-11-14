<?php
$menus = [['text' => '前端UI规范', 'link' => '#']];
$this->loadHeaderWithNewUI($menus, [], true);
?>
<style>
    .box-row {
        padding: 1rem;
        position: relative;
        box-sizing: border-box;
        min-height: 1rem;
        background: #ff6200;
        border: 1px solid #FFF;
        border-radius: 2px;
        overflow: hidden;
        text-align: center;
        color: #fff;
    }
</style>
<section class="el-container is-vertical">
    <div class="card-wrapper">
        <div class="z-card">
            <h3 class="z-card-header">
                布局1
            </h3>
            <div class="z-card-body">
                <p>
                    bootstrap和element的栅格系统不符合现在的ui要求，因此对栅格系统进行了自定义:
                </p>
                <xmp class="prettyprint">
                    <div class="flex-grid form-group">
                        <label class="col col-count-3 field">
                            <p class="form-cell-title">这是带货币符号的输入框:</p>
                            <div class="input-group">
                                <span class="input-group-addon">$</span>
                                <input type="text" class="form-control"  placeholder="付款金额" value="1000.0000">
                                <span class="input-group-addon">元</span>
                            </div>
                        </label>
                    </div>
                </xmp>
                <p>以下是对容器元素、子元素上class定义的说明：</p>
                <p style="margin-left: 15px;">
                    flex-grid 在容器上定义子元素多列排布. form-group让每行之间保持一定间距
                </p>
                <p style="margin-left: 15px;">
                    子元素上定义col 和 field表示该元素参与栅格系统的布局， col-count-3代表栅格共有3列.
                </p>
            </div>
        </div>
        <div class="z-card">
            <h3 class="z-card-header">
                布局2
            </h3>
            <div class="z-card-body">
                <p>
                    依然可以使用类似bootstrap栅格系统语法, 更详细示例可参看 <a href="http://flexboxgrid.com/" class="text-link" target="_blank">flexbox栅格系统</a>:
                </p>
                <div class="o-row form-group"><div class="o-col-xs-offset-11 o-col-xs-1"><div class="box-row"></div></div></div>
                <div class="o-row form-group"><div class="o-col-xs-offset-9 o-col-xs-3"><div class="box-row"></div></div></div>
                <div class="o-row form-group">
                    <div class="o-col-sm-2"><div class="box-row"></div></div>
                    <div class="o-col-sm-10"><div class="box-row"></div></div>
                </div>
                <div class="o-row form-group">
                    <div class="o-col-sm"><div class="box-row"></div></div>
                    <div class="o-col-sm"><div class="box-row"></div></div>
                    <div class="o-col-sm"><div class="box-row"></div></div>
                </div>
            </div>
        </div>
        <div class="z-card">
            <h3 class="z-card-header">
                下拉菜单
            </h3>
            <div class="z-card-body">
                <div class="flex-grid form-group">
                    <div class="col field">
                        <div class="dropdown link-more common-dropdown">
                            <a href="javascript: void 0" data-toggle="dropdown">
                                点击查看 <i class="icon icon-xiala icon--shrink"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="drop1">
                                <li>
                                    <a href="javascript: void 0" class="text-link"><span>Action</span></a>
                                </li>
                                <li>
                                    <a href="javascript: void 0" class="text-link">Another action</a>
                                </li>
                                <li>
                                    <a href="javascript: void 0" class="text-link">Something else here</a>
                                </li>
                                <li>
                                    <a href="javascript: void 0" class="text-link">Separated link</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col field">
                        <div class="dropdown action-more common-dropdown">
                            <a href="#" class="oil-btn" data-toggle="dropdown">
                                点击查看 <i class="icon icon-xiala icon--shrink" style="color: white;margin-left: -4px;"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="drop1">
                                <li>
                                    <a href="javascript: void 0"><span>Action</span></a>
                                </li>
                                <li>
                                    <a href="javascript: void 0">Another action</a>
                                </li>
                                <li>
                                    <a href="javascript: void 0">Something else here</a>
                                </li>
                                <li>
                                    <a href="javascript: void 0">Separated link</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col field">
                        <div class="dropdown action-more action-more--adjust common-dropdown">
                            <a href="#" data-toggle="dropdown">
                                点击查看 <i class="icon icon-xiala icon--shrink" style="margin-left: -4px;"></i>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="drop1">
                                <li>
                                    <a href="javascript: void 0"><span>Action</span></a>
                                </li>
                                <li>
                                    <a href="javascript: void 0">Another action</a>
                                </li>
                                <li>
                                    <a href="javascript: void 0">Something else here</a>
                                </li>
                                <li>
                                    <a href="javascript: void 0">Separated link</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="flex-grid form-group">
                    <div class="col field">
                        <select class="form-control selectpicker show-menu-arrow">
                            <option value="1">选项内容一</option>
                            <option value="1">选项内容二</option>
                            <option value="1">选项内容三</option>
                            <option value="1">选项内容四</option>
                            <option value="1">选项内容五</option>
                            <option value="1">选项内容六</option>
                            <option value="1">选项内容七</option>
                            <option value="1">选项内容八</option>
                            <option value="1">选项内容九</option>
                            <option value="1">选项内容十</option>
                        </select>
                    </div>
                    <div class="col field">
                        <select class="form-control selectpicker show-menu-arrow" data-live-search="true">
                            <option value="1">选项内容一</option>
                            <option value="1">选项内容二</option>
                            <option value="1">选项内容三</option>
                            <option value="1">选项内容四</option>
                            <option value="1">选项内容五</option>
                            <option value="1">选项内容六</option>
                            <option value="1">选项内容七</option>
                            <option value="1">选项内容八</option>
                            <option value="1">选项内容九</option>
                            <option value="1">选项内容十</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="z-card">
            <h3 class="z-card-header">
                按钮
            </h3>
            <div class="z-card-body">
                <div class="form-group">
                    <p style="margin-bottom: 10px;">重要按钮</p>
                    <a href="javascript: void 0" class="o-btn o-btn-primary">查看</a>
                    <a href="javascript: void 0" class="o-btn o-btn-primary">宽度可变</a>
                    <a href="javascript: void 0" class="o-btn o-btn-primary">默认最小宽度88px</a>

                </div>
                <div class="form-group">
                    <p style="margin-bottom: 10px;">操作区域按钮</p>
                    <a href="javascript: void 0" class="o-btn o-btn-primary action">新增</a>
                    <a href="javascript: void 0" class="o-btn o-btn-default">默认按钮</a>
                    <a href="javascript: void 0" class="o-btn o-btn-action">修改</a>
                    <a href="javascript: void 0" class="o-btn o-btn-action w-base">宽度88px</a>
                    <a href="javascript: void 0" class="o-btn o-btn-action primary">提交</a>
                    <a href="javascript: void 0" class="o-btn o-btn-action primary w-base">宽88px</a>
                </div>
                <ul class="el-button-group form-group">
                    <li type="button" class="el-button el-button--default active">
                        合同下付款
                    </li>
                    <li type="button" class="el-button el-button--default">
                        多合同合并付款
                    </li>
                    <li type="button" class="el-button el-button--default">
                        项目下付款
                    </li>
                    <li type="button" class="el-button el-button--default">
                        交易主体下付款
                    </li>
                    <li type="button" class="el-button el-button--default">
                        后补项目合同付款
                    </li>
                </ul>
                <div class="form-group">
                    <p style="margin-bottom: 10px;">单选按钮</p>
                    <label class="o-control o-control--radio inline-flex">
                        <input type="radio" style="width: auto" >
                        <span style="margin-left: 10px;">单选按钮一</span>
                        <div class="o-control__indicator"></div>
                    </label>
                    <p style="margin-bottom: 10px;">多选样式</p>
                    <label class="o-control o-control--checkbox inline-flex">
                        <input type="checkbox" style="width: auto" >
                        <span style="margin-left: 10px;">多选样式</span>
                        <div class="o-control__indicator"></div>
                    </label>
                </div>
            </div>
        </div>
        <div class="z-card">
            <h3 class="z-card-header">
                消息提示、弹框
            </h3>
            <div class="z-card-body">
                <div class="form-group">
                    <div class="source">
                        <div role="alert" class="el-alert el-alert--success form-group">
                            <i class="el-alert__icon el-icon-success"></i>
                            <div class="el-alert__content">
                                <span class="el-alert__title">成功提示的文案</span>
                            </div>
                        </div>
                        <div role="alert" class="el-alert el-alert--info form-group">
                            <i class="el-alert__icon el-icon-info"></i>
                            <div class="el-alert__content">
                                <span class="el-alert__title">消息提示的文案</span>
                            </div>
                        </div>
                        <div role="alert" class="el-alert el-alert--warning form-group">
                            <i class="el-alert__icon el-icon-warning"></i>
                            <div class="el-alert__content">
                                <span class="el-alert__title">警告提示的文案</span>
                            </div>
                        </div>
                        <div role="alert" class="el-alert el-alert--error form-group">
                            <i class="el-alert__icon el-icon-error"></i>
                            <div class="el-alert__content">
                                <span class="el-alert__title">错误提示的文案</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <p style="margin-bottom: 10px;">消息提示</p>
                    <a href="javascript: void 0" class="z-btn-action" onclick="vue.$message({type: 'success', message: '成功'})">成功</a>
                    <a href="javascript: void 0" class="z-btn-action" onclick="vue.$message({type: 'warning', message: '警告'})">警告</a>
                    <a href="javascript: void 0" class="z-btn-action" onclick="vue.$message({type: 'info', message: '消息'})">消息</a>
                    <a href="javascript: void 0" class="z-btn-action" onclick="vue.$message({type: 'error', message: '错误'})">错误</a>
                    <p style="margin: 10px 0;">Options</p>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>参数</th>
                            <th>说明</th>
                            <th>类型</th>
                            <th>可选值</th>
                            <th>默认值</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>message</td>
                            <td>消息文字</td>
                            <td>string / VNode</td>
                            <td>—</td>
                            <td>—</td>
                        </tr>
                        <tr>
                            <td>type</td>
                            <td>主题</td>
                            <td>string</td>
                            <td>success/warning/info/error</td>
                            <td>info</td>
                        </tr>
                        <tr>
                            <td>duration</td>
                            <td>显示时间, 毫秒。设为 0 则不会自动关闭</td>
                            <td>number</td>
                            <td>—</td>
                            <td>3000</td>
                        </tr>
                        <tr>
                            <td>onClose</td>
                            <td>关闭时的回调函数, 参数为被关闭的 message 实例</td>
                            <td>function</td>
                            <td>—</td>
                            <td>—</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    <p style="margin-bottom: 10px;">弹框</p>
                    <a href="javascript: void 0" class="z-btn-action" onclick="alert()">alert1</a>
                    <a href="javascript: void 0" class="z-btn-action" onclick="confirm()">confirm</a>
                    <a href="javascript: void 0" class="z-btn-action" onclick="openModal('#example-modal')">自定义弹框内容</a>
                </div>
            </div>
        </div>
        <div class="z-card">
            <h3 class="z-card-header">
                输入框
            </h3>
            <div class="z-card-body">
                <div class="flex-grid form-group">
                    <label class="col col-count-3 field">
                        <p class="form-cell-title">这是带货币符号的输入框:</p>
                        <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input type="text" class="form-control"  placeholder="付款金额" value="1000.0000">
                            <span class="input-group-addon">元</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>
        <div class="z-card">
            <h3 class="z-card-header">
                这里是卡片的标题
            </h3>
            <div class="z-card-body">
                <div class="flex-grid align-start form-group">
                    <label class="col col-count-3 field flex-grid align-start">
                        <span class="line-h--text w-fixed">这里是文本的抬头:</span>
                        <span class="form-control-static line-h--text">
                            这里是填充的数据
                        </span>
                    </label>
                    <label class="col col-count-3 field flex-grid align-start">
                        <span class="line-h--text w-fixed">这里是文本的抬头:</span>
                        <span class="form-control-static line-h--text">
                            这是三列, 文本的行高是22px, 文本的行高是22px
                        </span>
                    </label>
                    <label class="col col-count-3 field flex-grid align-start">
                        <span class="line-h--text w-fixed">这里是文本的抬头:</span>
                        <span class="form-control-static line-h--text">
                            <a class="text-link" href="javascript: void 0">所有的超链接要加上 <span style="color: red;">class: text-link</span></a>
                        </span>
                    </label>
                </div>
                <div class="flex-grid align-start form-group text-table-gap">
                    <label class="col col-count-3 field flex-grid align-start">
                        <span class="line-h--text w-fixed">这里是文本的抬头:</span>
                        <span class="form-control-static line-h--text">
                            这里是填充的数据
                        </span>
                    </label>
                    <label class="col col-count-3 field flex-grid align-start">
                        <span class="line-h--text w-fixed">这里是文本的抬头:</span>
                        <span class="form-control-static line-h--text">
                            这是三列, 文本的行高是22px, 文本的行高是22px
                        </span>
                    </label>
                    <label class="col col-count-3 field flex-grid align-start">
                        <span class="line-h--text w-fixed">这里是文本的抬头:</span>
                        <span class="form-control-static line-h--text">
                            <a class="text-link" href="javascript: void 0">所有的超链接要加上 <span style="color: red;">class: text-link</span></a>
                        </span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="modal fade draggable-modal" id="example-modal" tabindex="-1" role="dialog" aria-labelledby="modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header--flex">
                <h4 class="modal-title">请输入配货信息</h4>
                <a type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">×</span></a>
            </div>
            <div class="modal-body">
                <form class="search-form">
                    <div class="flex-grid form-group">
                        <label class="col field col-count-3">
                            <p class="form-cell-title">采购合同编号</p>
                            <input type="text" class="el-input__inner" placeholder="采购合同编号"/>
                        </label>
                        <label class="col field col-count-3">
                            <p class="form-cell-title">上游合作方</p>
                            <input type="text" class="el-input__inner" placeholder="上游合作方"/>
                        </label>
                        <label class="col field col-count-3">
                            <p class="form-cell-title">配货来源</p>
                            <input type="text" class="el-input__inner" placeholder="配货来源"/>
                        </label>
                    </div>
                    <div class="flex-grid form-group">
                        <label class="col field col-count-3">
                            <p class="form-cell-title">入库单编号</p>
                            <input type="text" class="el-input__inner" placeholder="入库单编号"/>
                        </label>
                        <label class="col field col-count-3">
                            <p class="form-cell-title">仓库名称</p>
                            <input type="text" class="el-input__inner" placeholder="仓库名称"/>
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer flex-center">
                <a href="javascript: void 0" role="button" class="o-btn o-btn-primary" data-dismiss="modal">确定</a>
                <a href="javascript: void 0" role="button" class="o-btn o-btn-action w-base" data-dismiss="modal">关闭</a>
            </div>
        </div>
    </div>
</div>
<script>
    function alert () {
        vue.$alert('这是一段内容', '标题名称', {
            type: 'warning',
            callback: action => {
                vue.$message({
                    type: 'info',
                    message: `action: ${ action }`
                });
            }
        });
    }
    function confirm() {
        vue.$confirm('此操作将永久删除该文件, 是否继续?', '提示', {
            type: 'warning'
        }).then(() => {
            vue.$message({
                type: 'success',
                message: '删除成功!'
            });
        }).catch(() => {
            vue.$message({
                type: 'info',
                message: '已取消删除'
            });
        });
    }

    function openModal(selector) {
        $(selector).modal()
    }
</script>