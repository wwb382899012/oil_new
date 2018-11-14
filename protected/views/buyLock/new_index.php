<?php
/**
 * Created by vector.
 * DateTime: 2017/08/31 18:16
 * Describe：
 */
function checkRowEditAction($row, $self) {
    $links = array();
    if(empty($row['lock_type'])){
        $links[] = '<a title="确认锁价维度" data-bind="click:function(){confirmModel(' . $row["detail_id"] . ')}">确认锁价维度</a>';
    }else{
        $links[] = '<a href="/' . $self->getId() . '/lock?id=' . $row["detail_id"] . '" title="锁价">锁价</a>';
        $links[] = '<a href="/' . $self->getId() . '/rollover?id=' . $row["detail_id"] . '" title="转月">转月</a>';
    }

    $isDisplyDetail = BuyLockService::isHaveLockDetail($row['detail_id']);
    if ($isDisplyDetail) {
        $links[] = '<a href="/' . $self->getId() . '/detail?id=' . $row["detail_id"] . '" title="查看详情">详情</a>';
    }
    $s = !empty($links) ? implode("&nbsp;&nbsp;", $links) : '';
    return $s;
}


//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'items' => array(
        array('type' => 'text', 'key' => 'c.contract_code*', 'text' => '采购合同编号'),
        array('type' => 'text', 'key' => 'cf.code_out*', 'text' => '外部合同编号'),
        array('type' => 'text', 'key' => 'pa.name*', 'text' => '上游合作方'),
        // array('type' => 'text', 'key' => 'pa.name*', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 'g.name*', 'text' => '品名'),
    )
);

//列表显示
$array = array(
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'style' => 'width:160px;text-align:center', 'text' => '采购合同编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/businessConfirm/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'code_out', 'type'=> '', 'style' => 'width:160px;text-align:center', 'text' => '外部合同编号'),
    array('key' => 'partner_id,partner_name', 'type' => 'href', 'style' => 'width:200px;text-align:left', 'text' => '上游合作方', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/partner/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'goods_name', 'type' => '', 'style' => 'width:120px;text-align:left', 'text' => '品名'),
    array('key' => 'price', 'type' => '', 'style' => 'width:100px;text-align:right', 'text' => '单价'),
    array('key' => 'contract_quantity', 'type' => '', 'style' => 'width:140px;text-align:right', 'text' => '合同数量'),
    array('key' => 'lock_quantity', 'type' => '', 'style' => 'width:110px;text-align:right', 'text' => '已锁价数量'),
    array('key' => 'lock_type', 'type' => '', 'style' => 'width:130px;text-align:left', 'text' => '锁价维度'),
    array('key' => 'detail_id', 'type' => 'href', 'style' => 'width:120px;text-align:center;', 'text' => '操作', 'href_text' => 'checkRowEditAction'),
);


$searchArray = ['search_config' => $form_array];
$tableArray = ['column_config' => $array];
$this->showIndexViewWithNewUI($_data_, [], $searchArray, $tableArray);
?>

<div class="modal fade draggable-modal" id="lockModal" tabindex="-1" role="dialog" aria-labelledby="modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header"><h4 class="modal-title">请确认锁价维度，后面不可更改</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" role="form" id="lockModalForm">
                    <div class="form-group">
                        <label for="remark" class="col-sm-2 control-label">锁价维度<span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-6">
                            <select class="form-control" id="lock_type" name="obj[lock_type]" data-bind="value:lock_type,valueAllowUnset: true">
                                <option value="">请选择锁价维度</option>
                                <?php
                                foreach ($this->map["lock_type"] as $key=>$val){
                                    echo "<option value='$key'>$val</option>";
                                }
                                ?>
                            </select>
                            <input type="hidden" name="obj[detail_id]" data-bind="value:detail_id"/>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a href="javascript: void 0" role="button" id="confirmSubmitBtnText" class="o-btn o-btn-primary" data-dismiss="modal" data-bind="click:confirm,html:confirmSubmitBtnText"></a>
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
    })

    function ViewModel() {
        var self=this;
        self.lock_type = ko.observable("").extend({required:true});
        self.detail_id = ko.observable(0);

        self.confirmSubmitBtnText = ko.observable('确认');
        self.actionState = 0;

        self.confirmModel = function (detail_id) {
            self.detail_id(detail_id);
            $("#lockModal").modal({
                backdrop: true,
                keyboard: false,
                show: true
            });
        }


        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };
        self.confirm = function(){
            if(!self.isValid())
            {
                self.errors.showAllMessages();
                return;
            }

			inc.vueConfirm({
				content: "您确定要提交当前锁价维度吗，该操作不可更改？", onConfirm: function() {
					self.submit();
				}
            });
        }
        

        self.submit = function () {
            if (self.actionState == 1)
                return;
            self.actionState = 1;
            self.confirmSubmitBtnText("确认" + inc.loadingIco);
            var formData = $('#lockModalForm').serialize();

            $.ajax({
                type: "POST",
                url: "/buyLock/confirm",
                data: formData,
                dataType: "json",
                success: function (json) {
                    self.actionState = 0;
                    self.confirmSubmitBtnText("确认");
                    if (json.state == 0) {
                        location.href=window.location.href;
                    } else {
                        inc.vueAlert(json.data);
                    }
                },
                error: function (data) {
                    self.confirmSubmitBtnText("确认");
                    self.actionState = 0;
                    inc.vueAlert("操作失败！" + data.responseText);
                }
            });
        }
    }
</script>
