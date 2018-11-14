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
   'items' => array(
       array('type' => 'text', 'key' => 'p.name*', 'text' => '下游合作方'),
       array('type' => 'select', 'key' => 'soo.status', 'map_name' => 'stock_out_status', 'text' => '状态'),
       array('type' => 'text', 'key' => 'soo.code', 'text' => '出库单编号'),
       array('type' => 'text', 'key' => 's.name*', 'text' => '出库'),
       array('type' => 'date', 'key' => 'soo.out_date', 'id'=>'entry_date', 'text' => '出库日期'),
       array('type' => 'select', 'key' => 'soo.is_virtual', 'map_name' => 'split_type_enum', 'text' => '是否平移生成'),
   )
);

function getStockOutCode($row) {
    $glue = ($row['is_virtual'] == \ddd\Split\Domain\Model\SplitEnum::IS_VIRTUAL) ? '虚拟-' : '';
    return '<a target="_blank" id="t_{1}" title="{2}" href="/stockOutList/view/?id={1}&t=1">'. $glue .'{2}</a>';
}

//列表显示
$array = array(
    array('key' => 'out_order_id,code', 'type' => 'href', 'class' => 'no-ellipsis', 'style' => 'min-width:100px;text-align:left', 'text' => '出库单编号', 'href_text' => 'getStockOutCode'),
    //array('key' => 'delivery_order_id,delivery_code', 'type' => 'href', 'style' => 'min-width:100px;text-align:left', 'text' => '发货单编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/deliveryOrder/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'class' => 'no-ellipsis', 'style' => 'min-width:100px;text-align:left', 'text' => '销售合同编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/contract/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'min-width:160px;text-align:left', 'text' => '下游合作方', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/partner/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'store_name', 'type' => '', 'style' => 'min-width:160px;text-align:left', 'text' => '出库'),
    array('key' => 'out_date', 'type' => 'date', 'style' => 'min-width:60px;text-align:left', 'text' => '出库日期'),
    array('key' => 'status', 'type' => 'map_val', 'map_name' => 'stock_out_status', 'style' => 'min-width:80px;text-align:left', 'text' => '状态'),
    array('key' => 'is_virtual', 'type' => 'map_val', 'map_name' => 'split_type_enum', 'style' => 'width:55px;text-align:center', 'text' => '是否平移生成'),
    array('key' => 'out_order_id', 'type' => 'href', 'style' => 'min-width:80px;text-align:left;', 'text' => '操作', 'href_text' => 'getRowActions'),
);

function getRowActions($row, $self) {
    $links = array();
    if (StockOutService::isCanEdit($row['status'])) {
      $links[] = '<a href="/stockOut/edit?out_order_id=' . $row["out_order_id"] . '&id='.$row['order_id'].'" title="修改">修改</a>';
    }
    if(StockOutService::isCanRevocation($row['status'])) {
        $links[] = '<a href="javascript: void 0" data-bind="click:revocation.bind($data,\''.$row['out_order_id'].'\',\''.$row['code'].'\')" title="撤回">撤回</a>';
    }
    if(StockOutService::isCanInvalid($row['status'])) {
        $links[] = '<a href="javascript: void 0" data-bind="click:invalid.bind($data,\''.$row['out_order_id'].'\',\''.$row['code'].'\')" title="作废">作废</a>';
    }
    $links[] = '<a href="/' . $self->getId() . '/view?id=' . $row["out_order_id"] . '" title="查看详情">查看</a>';

    $s = Utility::isNotEmpty($links) ? implode("&nbsp;&nbsp;", $links) : '';

    return $s;
}

$searchArray = ['search_config' => $form_array];
$tableArray = ['column_config' => $array];
$this->showIndexViewWithNewUI($_data_, [], $searchArray, $tableArray);
?>

<div class="modal fade draggable-modal" id="myModalInvalid" tabindex="-1" role="dialog" aria-labelledby="modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header--flex">
                <h4 class="modal-title">作废确认</h4>
                <a type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">×</span></a>
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
            <div class="modal-footer flex-center">
                <a href="javascript: void 0" role="button" class="o-btn o-btn-primary" data-bind="enable: isEnable,click:submitInvalid">确认作废</a>
                <a href="javascript: void 0" role="button" class="o-btn o-btn-action w-base" data-dismiss="modal">关闭</a>
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

			inc.vueConfirm({
				content: "您确定要撤回当前【"+ code +"】出库单吗？", onConfirm: function() {
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
								inc.vueMessage({duration: 500,message: '撤回出库操作成功', onClose: function () {
										location.href = "/<?php echo $this->getId() ?>";
									}
								});
							} else {
								inc.vueMessage({duration: 500,message: json.data, onClose: function () {
										location.href = "/<?php echo $this->getId() ?>";
									}
								});
							}
						},
						error: function (data) {
							self.isEnable(true);
							inc.vueAlert({content: "撤回出库单失败！" + data.responseText});
						}
					});
				}
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

						inc.vueMessage({duration: 500,message: '作废出库单操作成功', onClose: function () {
                            location.href = "/<?php echo $this->getId() ?>";
                        }});
                    }else{
						inc.vueMessage({message: json.data, onClose: function(){
                            location.href = "/<?php echo $this->getId() ?>";
                        }});
                    }
                },
                error: function (data) {
                    self.isEnable(true);
					inc.vueAlert({content: "作废出库单失败！" + data.responseText});
                }
            })
        };
    }
</script>
