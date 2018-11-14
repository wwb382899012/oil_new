<?php 

$order_id = empty($data['obj_id'])?0:$data['obj_id'];
//发货单
$DeliveryOrderService = new \ddd\application\stock\DeliveryOrderService();
$deliveryOrder = $DeliveryOrderService->getDeliveryOrder($order_id);
$data['deliveryOrder']=$deliveryOrder;
//出库单
$StockOutService = new \ddd\application\stock\StockOutService();
$stockOut = $StockOutService->getStockOutByOrderId($order_id);
$data['stockOut']=$stockOut;
//审核记录
$checkLogs=FlowService::getCheckLog($order_id,10);
$data['checkLogs']=$checkLogs;

//发货单商品结算
$DeliveryOrderSettlementService = new \ddd\application\contractSettlement\DeliveryOrderSettlementService();
$deliveryOrderSettlement=$DeliveryOrderSettlementService->getDeliveryOrderSettlement($order_id);
$data['deliveryOrderBalance']=$deliveryOrderSettlement;

?>

<section class="content">
    <div class="form-horizontal">
        <?php 
        $this->renderPartial("/common/deliverySettlementHead", array('deliveryOrder'=>$data['deliveryOrder']));
        $this->renderPartial("/common/outSettlementHead", array('outOrders'=>$data['stockOut']));
        
        ?>
           <?php $this->renderPartial("/common/settlementDetail", array('settlement'=>$data['deliveryOrderBalance'],'type'=>3,'isHideBack'=>true));

      
        ?>
    </div>
    <div class="box box-primary">
      
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
              
                <div class="box-header with-border">
                </div>
                <h4>审核信息</h4>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">审核意见
                        <span class="text-red fa fa-asterisk"></span></label>
                    <div class="col-sm-10">
                        <textarea rows="3" class="form-control" data-bind="value:remark"></textarea>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <?php if($this->checkButtonStatus["pass"]==1){ ?>
                            <button type="button" id="passButton" class="btn btn-success" data-bind="click:doPass,html:passText">通过</button>
                        <?php } ?>
                        <?php if($this->checkButtonStatus["back"]==1){ ?>
                            <button type="button" id="checkBackButton" class="btn btn-danger" data-bind="click:doBack,html:backText">驳回</button>
                        <?php } ?>
                        <?php if($this->checkButtonStatus["reject"]==1){ ?>
                            <button type="button" id="rejectButton" class="btn btn-danger" data-bind="click:doReject,html:rejectText">拒绝</button>
                        <?php } ?>
                        <button type="button" class="btn btn-default" data-bind="click:back">返回</button>
                    </div>
                </div>
            </div>
        </form>        
    </div>
    <?php
    $checkLogs = FlowService::getCheckLog($data['obj_id'], $this->businessId);
    $this->renderPartial("/common/checkLogList", array('checkLogs' => $checkLogs));
    ?>
</section>
<script type="text/javascript">
    var view;
    $(function () {
        view = new ViewModel(<?php echo json_encode($data) ?>);
        ko.applyBindings(view);
    });

    function ViewModel(option) {
        var defaults = {
            check_id: 0,
            detail_id: 0,
            remark: ''
        };
        var o = $.extend(defaults, option);
        var self = this;
        self.check_id = o.check_id;
        self.detail_id = o.detail_id;
        self.status = ko.observable(o.status);
        self.remark=ko.observable(o.remark).extend({required:true,maxLength:512});
        self.errors = ko.validation.group(self);

        self.passText = ko.observable('通过');
        self.backText = ko.observable('驳回');
        self.rejectText = ko.observable('拒绝');

        self.actionState = 0;
        self.isValid = function () {
            return self.errors().length === 0;
        };


        self.confirmText="";

        self.doPass = function () {
            self.confirmText="通过";
            self.status(1);
            self.save();
        }
        self.doBack = function () {
            self.confirmText="驳回";
            self.status(-1);
            self.save();
        }
        self.doReject = function () {
            self.confirmText="拒绝";
            self.status(0);
            self.save();
        }

        self.updateButtonText=function(){
            if(self.actionState==1)
            {
                switch (self.status())
                {
                    case 1:
                        self.passText("通过 "+inc.loadingIco);
                        break;
                    case 0:
                        self.backText("驳回 "+inc.loadingIco);
                        break;
                    case -1:
                        self.rejectText("拒绝 "+inc.loadingIco);
                        break;
                }
            }
            else
            {
                switch (self.status())
                {
                    case 1:
                        self.passText("通过");
                        break;
                    case 0:
                        self.backText("驳回");
                        break;
                    case -1:
                        self.rejectText("拒绝");
                        break;
                }
            }

        }

        self.save = function () {
            if(!self.isValid())
            {
                self.errors.showAllMessages();
                return;
            }

            if(self.actionState==1)
                return;

            layer.confirm("您确定要" + self.confirmText + "该信息的审核，该操作不可逆？", {icon: 3, title: '提示'}, function () {

                var formData = {
                    data: {
                        check_id: self.check_id,
                        detail_id: self.detail_id,
                        checkStatus: self.status(),
                        remark: self.remark()
                    }
                };
                self.actionState = 1;
                self.updateButtonText();
                $.ajax({
                    type: "POST",
                    url: "/<?php echo $this->getId() ?>/save",
                    data: formData,
                    dataType: "json",
                    success: function (json) {
                        self.updateButtonText();
                        self.actionState = 0;
                        if (json.state == 0) {
                            layer.msg('操作成功', {icon: 6, time: 1000}, function () {
                                location.href = "/<?php echo $this->getId() ?>";
                            });
                        } else {
                            layer.alert(json.data);
                        }

                    },
                    error: function (data) {
                        self.updateButtonText();
                        self.actionState = 0;
                        layer.alert("操作失败：" + data.responseText, {icon: 5});
                    }
                });
            });
                    }

        self.back = function () {
            location.href = "/<?php echo $this->getId() ?>/?search[checkStatus]=1";
        }
    }
</script>
