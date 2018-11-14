<?php

function checkRowEditAction($row, $self) {
    $links = array();

    if ($self->checkIsCanEdit($row["check_status"])) {
        $links[] = '<a title="停息" data-bind="click:function(){confirmModel(' . $row["id"] . ',\''.$row['contract_code'].'\')}">停息</a>';
    }

    $links[] = '<a href="/' . $self->getId() . '/detail?search[contract_id]=' . $row["contract_id"] . '" title="查看明细">查看明细</a>';
    
    $s = Utility::isNotEmpty($links) ? implode("&nbsp;|&nbsp;", $links) : '';

    return $s;
}

if($_data_['contract_type']==ConstantMap::BUY_TYPE){
    $contract_name = "采购合同编号";
    $amount_desc   = "已入库货值";
    $payment_desc  = "累计实付金额";
}else{
    $contract_name = "销售合同编号";
    $amount_desc   = "已出库货值";
    $payment_desc  = "累计收款金额";
}

$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'input_array' => array(
        array('type' => 'text', 'key' => 'p.corporation_name*', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 'p.user_name*', 'text' => '业务负责人'),
        array('type' => 'text', 'key' => 'p.project_code*', 'text' => '项目编号'),
        array('type' => 'text', 'key' => 'p.contract_code*', 'text' => '合同编号'),
        array('type' => 'date','id'=>'startTime',  'key' => 'p.stop_date>', 'text' => '停息日期&emsp;'),
        array('type' => 'date','id'=>'endTime',  'key' => 'p.stop_date<', 'text' => '到&emsp;&emsp;&emsp;'),
        array('type' => 'select', 'map_name'=>'payment_interest_status', 'key' => 'p.status', 'text' => '状态&emsp;&emsp;'),
    ),
    'buttonArray' => array(
        array('text' => '导出', 'buttonId' => 'exportButton')
    ),
);

//列表显示
$array = array(
    array('key' => 'contract_id', 'type' => 'href', 'style' => 'width:100px;text-align:center;', 'text' => '操作', 'href_text' => 'checkRowEditAction'),
    array('key' => 'corporation_id,corporation_name', 'type' => 'href', 'style' => 'width:120px;text-align:left;', 'text' => '交易主体', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'user_name', 'type' => '', 'style' => 'width:80px;text-align:center;', 'text' => '业务负责人'),
    array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:120px;text-align:center;', 'text' => '项目编号', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/project/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'contract_id,contract_code', 'type' => 'href', 'style' => 'width:120px;text-align:center;', 'text' => $contract_name, 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/contract/detail/?id={1}&t=1">{2}</a>'),    
    array('key' => 'amount_sign', 'type' => 'amount', 'style' => 'width:100px;text-align:right;', 'text' => '合同签约总额'),
    array('key' => 'amount_goods', 'type' => 'amount', 'style' => 'width:100px;text-align:right;', 'text' => $amount_desc),
    array('key' => 'amount_actual', 'type' => 'amount', 'style' => 'width:100px;text-align:right;', 'text' => $payment_desc),
    array('key' => 'days', 'type' => '', 'style' => 'width:80px;text-align:right;', 'text' => '计息天数'),
    array('key' => 'interest', 'type' => 'amount', 'style' => 'width:100px;text-align:right;', 'text' => '合计利息'),
    array('key' => 'stop_date', 'type' => 'date', 'style' => 'width:80px;text-align:center;', 'text' => '停息日期'),
    array('key' => 'status', 'type' => 'map_val', 'map_name'=>'payment_interest_status', 'style' => 'width:80px;text-align:center;', 'text' => '状态')
    
);

$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_['data'], "", "min-width:1050px;", "table-bordered table-layout", "", true);
?>

<div class="modal fade draggable-modal" id="stopModal" tabindex="-1" role="dialog" aria-labelledby="modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header"><h4 class="modal-title">停息确认</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" role="form" id="stopModalForm">
                    <div class="form-group">
                        <label for="remark" class="col-sm-2 control-label"></label>
                        <div class="col-sm-10">
                            <p class="form-control-static">确认停止<?php echo $contract_name ?>：<span data-bind="text:contract_code"></span>的利息计算吗？</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="remark" class="col-sm-2 control-label">停息日期<span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="stop_date" name="obj[stop_date]" placeholder="停息日期" data-bind="value:stop_date">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="remark" class="col-sm-2 control-label">停息理由<span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="stop_reason" name= "obj[stop_reason]" rows="3" placeholder="停息理由" data-bind="value:stop_reason"></textarea>
                        </div>
                    </div>
                    <input type='hidden' name='obj[id]' data-bind="value:id"/>
                    <input type='hidden' name='obj[contract_code]' data-bind="value:contract_code"/>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="confirmSubmitBtnText" class="btn btn-success" placeholder="确认停息" data-bind="click:confirm,html:confirmSubmitBtnText"></button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>

<script>
    var view;
    $(function () {
        view = new ViewModel();
        ko.applyBindings(view);

        $("#stop_date").datetimepicker({format: 'yyyy-mm-dd', minView: 'month'});

        $("#exportButton").click(function(){
            var formData= $(this).parents("form.search-form").serialize();
            location.href="/<?php echo $this->getId() ?>/export?"+formData;
        });
    })

    function ViewModel() {
        var self=this;
        self.id = ko.observable(0);
        self.contract_code = ko.observable("");
        self.stop_date     = ko.observable("").extend({required:true});
        self.stop_reason   = ko.observable("").extend({required:true});
        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };
        

        self.confirmSubmitBtnText = ko.observable('确认停息');
        self.actionState = 0;

        self.confirmModel = function (id, contract_code) {
            self.id(id);
            self.contract_code(contract_code);
            $("#stopModal").modal({
                backdrop: true,
                keyboard: false,
                show: true
            });
            $("#stopModal").on('show.bs.modal',function() {
                //打开前重置表单数据
                $("#stopModalForm")[0].reset();
            });
        }


        
        self.confirm = function(){
            if(!self.isValid())
            {
                self.errors.showAllMessages();
                return;
            }

            layer.confirm("您确定要提交当前停息操作吗，该操作不可更改？", {icon:3, title:'提示'}, function(index){
                self.submit();
                layer.close(index);
            });
        }
        

        self.submit = function () {
            if (self.actionState == 1)
                return;
            self.actionState = 1;
            self.confirmSubmitBtnText("确认停息" + inc.loadingIco);
            var formData = $('#stopModalForm').serialize();

            $.ajax({
                type: "POST",
                url: "/<?php echo $this->getId() ?>/stop",
                data: formData,
                dataType: "json",
                success: function (json) {
                    self.actionState = 0;
                    self.confirmSubmitBtnText("确认停息");
                    if (json.state == 0) {
                        // location.href=window.location.href;
                        location.reload();
                    } else {
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
                    self.confirmSubmitBtnText("确认停息");
                    self.actionState = 0;
                    layer.alert("操作失败！" + data.responseText, {icon: 5});
                }
            });
        }
    }
</script>
