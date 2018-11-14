<?php
include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/checkItems.php";
$id=empty($data['obj_id'])?0:$data['obj_id'];
//合同信息
$ContractService = new \ddd\application\contractSettlement\ContractService();
$contract=$ContractService->getContract($id);
$data['contract']=$contract;
//审核记录
$checkLogs=FlowService::getCheckLog($id,22);
$data['checkLogs']=$checkLogs;
//合同结算
$SellContractSettlementService = new \ddd\application\contractSettlement\SellContractSettlementService();
$sellContractSettlement=$SellContractSettlementService->getSellContractSettlement($id);
$data['contractSettlement']=$sellContractSettlement;


?>

<section class="content">
<?php 
    $this->renderPartial("../sellContractSettlement/contractInfo", array('contract'=>$data['contract']));
 ?>
<?php if(!empty($data['contractSettlement'])):?>
    <div class="box" data-bind="with:settlement">
        <div class="box-body form-horizontal">
            <label for="settle_date" class="col-sm-2 control-label">结算日期</label>
            <p class="form-control-static col-sm-3"><?php echo isset($data['contractSettlement']["settle_date"])?$data['contractSettlement']["settle_date"]:''; ?></p>
        </div>
    </div>
  
  <?php 
  if(!empty($data['contractSettlement']['settlementGoods'])){//结算一
  $this->renderPartial("../sellContractSettlement/goodItems", array('contractSettlement'=>$data['contractSettlement']));//货款结算   
  }else{//结算二

      if(!empty($data['contractSettlement']['delivery_orders'])){
          foreach($data['contractSettlement']['delivery_orders'] as $k=>$v){

              $this->renderPartial("/common/settlementDetail", array('settlement'=>$v,'type'=>3, 'isContractSettlement'=>1));
          }
      }
  }
  
  ?>
  
    <div class="box"  data-bind="with:settlement">
        <div class="box-header with-border">
            <h3 class="box-title">非货款类应付金额</h3>
        </div>
 		<?php $this->renderPartial("../buyContractSettlement/otherGoods", array('contractSettlement'=>$data['contractSettlement'])); ?>
 		
        
    </div>
    <div class="box box-solid">
        <div class="box-body form-horizontal">
            <div class="pull-left">
                备注：<?php echo $data['contractSettlement']['remark'];?>
            </div>
        </div>
        <div class="box-footer">
            <div class="pull-left">
                应付合计：<span><?php echo number_format($data['contractSettlement']['amount_settle']/100,2);?></span>元
                <button class="btn btn-link" style="margin-left: 10px;" onclick="lookDetail()">查看明细</button>
            </div>
            <div class="pull-right">

            </div>
        </div>
    </div>
    <?php $this->renderPartial("../sellContractSettlement/amountDetail", array('contractSettlement'=>$data['contractSettlement'],'contract'=>$data['contract']));  ?>

<?php endif;?>
     <?php 
     if (Utility::isNotEmpty($data['checkLogs']))
        $this->renderPartial("/common/checkLogList", array('checkLogs' => $data['checkLogs']));
    ?>
</section>
<section class="content" id="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">销售合同结算审核</h3>
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
        view=new ViewModel(<?php echo json_encode(array('items'=>$this->map['stock_in_settlement_checkitems_config'],'detail_id'=>$data['detail_id'], 'check_id'=>$data['check_id'], 'batch_id'=>$stockBatchSettlement->batch_id));?>);
        ko.applyBindings(view, $("#content")[0]);
    });

    function ViewModel(option){
        var defaults={
            items:null,
            remark : '',
            status:1,
            check_id:'',
            detail_id:'',
        };
        var o=$.extend(defaults,option);
        var self=this;
        self.items=ko.observableArray(o.items);
        self.status=ko.observable(o.status);
        self.check_id=o.check_id;
        self.detail_id=o.detail_id;
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
                    confirmString = '通过销售合同结算？';
                } else {
                    confirmString = '驳回销售合同结算?';
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
                        data:{
                            remark : self.remark(),
                            check_id : self.check_id,
                            detail_id: self.detail_id,
                            checkStatus : self.status(),
                        }
                    }
                    self.submitting(1);
                    $.ajax({
                        type:"POST",
                        url:"/check22/save",
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
            window.location.href="/check22/?search[checkStatus]=1";
        }
    }
</script>

