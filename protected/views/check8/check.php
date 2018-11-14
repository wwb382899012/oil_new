<?php
$stockNotice = StockNotice::model()->with("contract", "contract.contractGoods", "create_user")->findByPk($data['obj_id']);

$stockNoticeGoods = StockNoticeDetail::model()->with('sub', 'goods', 'store', 'lock')->findAllToArray('t.batch_id=:batch_id', array('batch_id'=>$data['obj_id']));

$stockBatchSettlements = StockBatchSettlement::model()->with('sub', 'goods')->findAllToArray(array('condition'=>'batch_id=:batch_id', 'params'=>array('batch_id'=>$data['obj_id'])));

$stockIns = StockIn::model()->with("store", "details", "details.sub", "details.goods")->findAllToArray('t.batch_id=:batch_id',array("batch_id"=>$data['obj_id'],));
$attachments = StockBatchSettlementService::getAttachment($stockNotice->batch_id);
include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/checkItems.php";
?>

<section class="content">
<?php

$this->renderPartial("/common/stockNoticeBriefInfo", array('stockNotice'=>$data['stockInBatch'], 'stockNoticeGoods'=>$data['stockInBatch']['items']));

$stockIns = $data['stockIn'];
if(is_array($stockIns))
    foreach ($stockIns as $stockIn) {
        $this->renderPartial("/common/stockInBriefInfo", array('stockIn'=>$stockIn));
    }


?>
  <?php
        $this->renderPartial("/common/settlementDetail", array('settlement'=>$data['stockInBatchBalance'], 'type'=>1,'isHideBack'=>true));

        if(!empty($data['stockInBatchBalance']['settle_id'])) {
            $checkLogs = FlowService::getCheckLog($data['stockInBatchBalance']['batch_id'], 8);
            if (Utility::isNotEmpty($checkLogs))
                $this->renderPartial("/common/checkLogList", array('checkLogs' => $checkLogs));
        }
        ?>
</section>
<section class="content" id="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">入库通知单结算审核</h3>
        </div><!--end box box-header-->
        <div class="box-body">
            <form class="form-horizontal" role="form" id="mainForm">
            <?php
            ?>

                <!-- ko component: {
                              name: "check-items",
                              params: {
                                          items: items

                                          }
                          } -->
                <!-- /ko -->
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">审核意见<span class="text-red fa fa-asterisk"></span></label>
                	<div class="col-sm-10">
                		<textarea class="form-control" data-bind="value:remark"></textarea>
                	</div>
                </div>

            </form>
        </div><!--end box-border-->

        <div class="box-footer">
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="button" class="btn btn-success"  data-bind="click:submit">通过</button>
                    <button type="button" class="btn btn-danger"  data-bind="click:rollback">驳回</button>
                    <button type="button" class="btn btn-default"  data-bind="click:back">返回</button>
                </div>
            </div>
        </div>
    </div><!--end box box-primary-->
</section><!--end content-->

<div class="modal fade draggable-modal" id="quotaModal" tabindex="-1" role="dialog" aria-labelledby="modal" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="childContent">
        </div>
    </div>
</div>
<script type="text/javascript">
    var view;
    $(function () {
        view=new ViewModel(<?php echo json_encode(array('items'=>$this->map['stock_in_settlement_checkitems_config'], 'check_id'=>$data['check_id'], 'batch_id'=>$stockBatchSettlement->batch_id));?>);
        ko.applyBindings(view, $("#content")[0]);
    });

    function ViewModel(option){
        var defaults={
            items:null,
            remark : '',
            status:1,
            check_id:'',
        };
        var o=$.extend(defaults,option);
        var self=this;
        self.items=ko.observableArray(o.items);
        self.status=ko.observable(o.status);
        self.check_id=o.check_id;
        self.batch_id=o.batch_id;
        self.is_main=o.is_main;
        self.remark=ko.observable(o.remark).extend({
            custom:{
                params: function (v) {
                    if (v!='') {
                        return true;
                    }
                    else
                        return false;
                },
                message: '不得为空'
            },maxLength:512
        });
        self.errors = ko.validation.group(self);
        self.submitting = ko.observable(0);
        self.isValid = function () {
            return self.errors().length === 0;
        };
        self.submit = function() {
            self.status(1);
            self.save();
        }
        self.rollback = function() {
            self.status(-1);
            self.save();
        }
        self.save=function (checkStatus) {
            if(self.isValid() && self.submitting() == 0) {
                var confirmString = '';
                if(self.status()>0) {
                    confirmString = '通过入库通知单结算审核？';
                } else {
                    confirmString = '驳回入库通知单结算?';
                }
                layer.confirm("是否确认"+confirmString, {icon: 3, title: '提示'}, function(){
                    /*var extraValues = {};
                    $(self.items()).each(function(ind, item) {
                        if(item.key())
                            extraValues[item.key()] = item.value();
                    });*/
                    var data ={
                        items:self.items.getValues(),
                        //items:extraValues,
                        obj:{
                            remark : self.remark(),
                            check_id : self.check_id,
                            checkStatus : self.status(),
                        }
                    }
                    self.submitting(1);
                    $.ajax({
                        type:"POST",
                        url:"/check8/save",
                        data:data,
                        dataType:"json",
                        success:function (json) {
                            if(json.state==0){
                                layer.msg(json.data, {icon: 6, time:1000},function() {
                                    self.back();
                                });
                            }else{
                                layer.alert(json.data);
                                self.submitting(0);
                            }
                        },
                        error:function (data) {
                            layer.alert("保存失败！"+data.responseText);
                            self.submitting(0);
                        }
                    });
                });

            } else {
                self.errors.showAllMessages();
            }
        }
        
        self.back=function() {
            window.location.href="/check8/?search[checkStatus]=1";
        }
    }
</script>

