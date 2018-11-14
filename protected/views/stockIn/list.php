<?php
/**
 * Desc: 入库单列表
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
//查询区域
$form_array = array(
   'form_url' => '/' . $this->getId() . '/',
   'input_array' => array(
       array('type' => 'text', 'key' => 'a.code', 'text' => '入库单编号&emsp;'),
       array('type' => 'text', 'key' => 'e.code', 'text' => '入库通知单编号'),
       array('type' => 'text', 'key' => 'b.contract_code', 'text' => '采购合同编号'),
       array('type' => 'text', 'key' => 'f.code_out*', 'text' => '外部合同编号'),
       array('type' => 'text', 'key' => 'c.name*', 'text' => '上游合作方&emsp;&emsp;'),
       array('type' => 'text', 'key' => 'd.name*', 'text' => '入库&emsp;&emsp;&emsp;&emsp;'),
       array('type' => 'date', 'key' => 'a.entry_date', 'id'=>'entry_date', 'text' => '入库日期&emsp;&emsp;'),
       array('type' => 'select', 'key' => 'a.status', 'map_name' => 'stock_in_status', 'text' => '状态&emsp;&emsp;&emsp;&emsp;&emsp;'),
       array('type' => 'select', 'key' => 'a.is_virtual', 'map_name' => 'is_virtual', 'text' => '是否平移生成'),
   )
);

//列表显示
$array = array(
    array('key' => 'stock_in_id', 'type' => 'href', 'style' => 'width:140px;text-align:center;', 'text' => '操作', 'href_text' => 'getRowActions'),
    array('key' => 'stock_in_id,code', 'type' => 'href', 'style' => 'width:160px;text-align:center', 'text' => '入库单编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/' . $this->getId() . '/view/?id={1}&t=1">{2}</a>'),
    array('key' => 'batch_id,stock_batch_code', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '入库通知单编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/stockIn/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '采购合同编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/contract/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'code_out', 'type' => '', 'style' => 'width:140px;text-align:center', 'text' => '外部合同编号'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:180px;text-align:center', 'text' => '上游合作方', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/partner/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'store_id,store_name', 'type' => 'href', 'style' => 'width:160px;text-align:center', 'text' => '入库', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/storehouse/detail/?store_id={1}&t=1">{2}</a>'),
    array('key' => 'entry_date', 'type' => 'date', 'style' => 'width:100px;text-align:center', 'text' => '入库日期'),
    array('key' => 'status', 'type' => 'map_val', 'map_name' => 'stock_in_status', 'style' => 'width:100px;text-align:center', 'text' => '状态'),
    array('key' => 'is_virtual', 'type' => 'map_val', 'map_name' => 'split_type_enum', 'style' => 'width:100px;text-align:center', 'text' => '是否平移生成'),
);

function getRowActions($row, $self) {
    $links = array();
    if(StockInService::isCanEdit($row['status'])) {
        $links[] = '<a href="/stockIn/edit?id=' . $row["stock_in_id"] . '" title="修改">修改</a>';
    }
    if(StockInService::isCanRevocation($row['status'])) {
        $links[] = '<a data-bind="click:revocation.bind($data,\''.$row['stock_in_id'].'\',\''.$row['code'].'\')" title="撤回">撤回</a>';
    }
    if(StockInService::isCanInvalid($row['status'])) {
        $links[] = '<a data-bind="click:invalid.bind($data,\''.$row['stock_in_id'].'\',\''.$row['code'].'\')" title="作废">作废</a>';
    }
    $links[] = '<a  href="/' . $self->getId() . '/view?id=' . $row["stock_in_id"] . '" title="查看详情">查看</a>';
    $s = Utility::isNotEmpty($links) ? implode("&nbsp;|&nbsp;", $links) : '';

    return $s;
}

$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_['data'], '', 'min-width:1250px;', 'table-bordered table-layout');
?>

<div class="modal fade" id="myModalInvalid" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">作废确认</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <span for="recipient-name" class="control-span">您要作废<span data-bind="text:stockInCode"></span>入库单吗？作废之后，不可撤销和修改。</span>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" id="message-text" placeholder="请填写作废理由(必填)" data-bind="value:remark"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bind="enable: isEnable,click:submitInvalid">确认作废</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>

<script>
    var view;
    $(function () {
        view = new ViewModel();
        ko.applyBindings(view);
    });

    function ViewModel() {
        var self = this;
        self.stockInId = ko.observable("");
        self.stockInCode = ko.observable("");
        self.isEnable = ko.observable(true);
        self.errors = ko.validation.group(self);
        self.remark = ko.observable("").extend({required:true,maxLength:512});
        self.isValid = function () {
            return self.errors().length === 0;
        };

        self.revocation = function (stockInId,code) {
            self.stockInId(stockInId);
            self.stockInCode(code);

            layer.confirm("您确定要撤回当前【"+ code +"】入库单吗？", {icon: 3, title: '提示'}, function(index){
                self.isEnable(false);
                $.ajax({
                    type: "GET",
                    url: "/stockInList/revocation",
                    data: {
                        stock_in_id: self.stockInId()
                    },
                    dataType: "json",
                    success: function (json) {
                        self.isEnable(true);

                        if (json.state == 0) {
                            layer.msg('撤回入库操作成功', {icon: 6, time: 1000}, function () {
                                location.href = "/<?php echo $this->getId() ?>";
                            });
                        }else{
                            layer.alert(json.data, {icon: 5,yes:function(){
                                location.href = "/<?php echo $this->getId() ?>";
                            }},);
                        }

                        layer.close(index);
                    },
                    error: function (data) {
                        self.isEnable(true);
                        layer.alert("撤回入库单失败！" + data.responseText, {icon: 5});
                        layer.close(index);
                    }
                });
            });
        };

        self.invalid = function (stockInId,code) {
            self.stockInId(stockInId);
            self.stockInCode(code);

            $("#myModalInvalid").modal({
                backdrop: true,
                keyboard: false,
                show: true
            });

        };

        self.submitInvalid = function () {
            if (!self.isValid()) {
                self.errors.showAllMessages();
                return;
            }

            self.isEnable(false);
            $.ajax({
                type: "GET",
                url: "/stockInList/invalid",
                data: {
                    stock_in_id: self.stockInId(),
                    remark:self.remark()
                },
                dataType: "json",
                success: function (json) {
                    self.isEnable(true);

                    if (json.state == 0) {
                        $("#myModalInvalid").modal({
                            backdrop: true,
                            keyboard: false,
                            show: false
                        });

                        layer.msg('作废入库单操作成功', {icon: 6, time: 1000}, function () {
                            location.href = "/<?php echo $this->getId() ?>";
                        });
                    }else{
                        layer.alert(json.data, {icon: 5,yes:function(){
                            location.href = "/<?php echo $this->getId() ?>";
                        }},);
                    }
                },
                error: function (data) {
                    self.isEnable(true);
                    layer.alert("作废入库单失败！" + data.responseText, {icon: 5});
                }
            })
        };
    }
</script>

