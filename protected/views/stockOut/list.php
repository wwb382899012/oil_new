<?php
/**
 * Desc: 出库单列表
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
//查询区域
$form_array = array(
   'form_url' => '/' . $this->getId() . '/',
   'input_array' => array(
       array('type' => 'text', 'key' => 'soo.code', 'text' => '出库单编号'),
       array('type' => 'text', 'key' => 'p.name*', 'text' => '下游合作方'),
       array('type' => 'text', 'key' => 's.name*', 'text' => '出库'),
       array('type' => 'date', 'key' => 'soo.out_date', 'id'=>'entry_date', 'text' => '出库日期&emsp;'),
       array('type' => 'select', 'key' => 'soo.status', 'map_name' => 'stock_out_status', 'text' => '状态&emsp;&emsp;&emsp;'),
       array('type' => 'select', 'key' => 'soo.is_virtual', 'map_name' => 'split_type_enum', 'text' => '是否平移生成'),
   )
);

//列表显示
$array = array(
    array('key' => 'out_order_id', 'type' => 'href', 'style' => 'width:80px;text-align:center;', 'text' => '操作', 'href_text' => 'getRowActions'),
    array('key' => 'out_order_id,code', 'type' => 'href', 'style' => 'width:100px;text-align:center', 'text' => '出库单编号', 'href_text' => '<a target="_blank" id="t_{1}" title="出库单详情" href="/' . $this->getId() . '/view/?id={1}&t=1">{2}</a>'),
    //array('key' => 'delivery_order_id,delivery_code', 'type' => 'href', 'style' => 'width:100px;text-align:center', 'text' => '发货单编号', 'href_text' => '<a target="_blank" id="t_{1}" title="发货单详情" href="/deliveryOrder/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'style' => 'width:100px;text-align:center', 'text' => '销售合同编号', 'href_text' => '<a target="_blank" id="t_{1}" title="销售合同详情" href="/contract/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:160px;text-align:center', 'text' => '下游合作方', 'href_text' => '<a target="_blank" id="t_{1}" title="合作方详情" href="/partner/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'store_name', 'type' => '', 'style' => 'width:160px;text-align:center', 'text' => '出库'),
    array('key' => 'out_date', 'type' => 'date', 'style' => 'width:60px;text-align:center', 'text' => '出库日期'),
    array('key' => 'status', 'type' => 'map_val', 'map_name' => 'stock_out_status', 'style' => 'width:100px;text-align:center', 'text' => '状态'),
    array('key' => 'is_virtual', 'type' => 'map_val', 'map_name' => 'split_type_enum', 'style' => 'width:65px;text-align:center', 'text' => '是否平移生成'),
);

function getRowActions($row, $self) {
    $links = array();
    if (StockOutService::isCanEdit($row['status'])) {
      $links[] = '<a href="/stockOut/edit?out_order_id=' . $row["out_order_id"] . '&id='.$row['order_id'].'" title="修改">修改</a>';
    }
    if(StockOutService::isCanRevocation($row['status'])) {
        $links[] = '<a data-bind="click:revocation.bind($data,\''.$row['out_order_id'].'\',\''.$row['code'].'\')" title="撤回">撤回</a>';
    }
    if(StockOutService::isCanInvalid($row['status'])) {
        $links[] = '<a data-bind="click:invalid.bind($data,\''.$row['out_order_id'].'\',\''.$row['code'].'\')" title="作废">作废</a>';
    }
    $links[] = '<a href="/' . $self->getId() . '/view?id=' . $row["out_order_id"] . '" title="查看详情">查看</a>';

    $s = Utility::isNotEmpty($links) ? implode("&nbsp;|&nbsp;", $links) : '';

    return $s;
}

$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_[data], "", "min-width:1050px;", "table-bordered table-layout");
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
                        <span for="recipient-name" class="control-span">您要作废<span data-bind="text:stockInCode"></span>出库单吗？作废之后，不可撤销和修改。</span>
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
        self.stockOutId = ko.observable("");
        self.stockInCode = ko.observable("");
        self.isEnable = ko.observable(true);
        self.errors = ko.validation.group(self);
        self.remark = ko.observable("").extend({required:true,maxLength:512});
        self.isValid = function () {
            return self.errors().length === 0;
        };

        self.revocation = function (stockOutId,code) {
            self.stockOutId(stockOutId);
            self.stockInCode(code);

            layer.confirm("您确定要撤回当前【"+ code +"】出库单吗？", {icon: 3, title: '提示'}, function(index){
                self.isEnable(false);
                $.ajax({
                    type: "GET",
                    url: "/stockOutList/revocation",
                    data: {
                        out_order_id: self.stockOutId(),
                    },
                    dataType: "json",
                    success: function (json) {
                        self.isEnable(true);

                        if (json.state == 0) {
                            ``

                            layer.msg('撤回出库操作成功', {icon: 6, time: 1000}, function () {
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
                        layer.alert("撤回出库单失败！" + data.responseText, {icon: 5});
                        layer.close(index);
                    }
                });
            });
        };

        self.invalid = function (stockOutId,code) {
            self.stockOutId(stockOutId);
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
                url: "/stockOutList/invalid",
                data: {
                    out_order_id: self.stockOutId(),
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

                        layer.msg('作废出库单操作成功', {icon: 6, time: 1000}, function () {
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
                    layer.alert("作废出库单失败！" + data.responseText, {icon: 5});
                }
            })
        };
    }
</script>
