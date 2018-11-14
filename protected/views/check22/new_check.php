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

<section class="content" id="content">
    <?php
    $menus = [['text' => '结算管理'],['text'=>'销售合同结算审核','link'=>'/check22/'], ['text' => $this->pageTitle]];
    $buttons = [];
    $buttons[] = ['text' => '通过', 'attr' => ['data-bind' => 'click:doPass, html:passText']];
    $buttons[] = ['text' => '驳回', 'attr' => ['data-bind' => 'click:doReject, html:backText', 'class_abbr'=>'action-default-base']];
    $this->loadHeaderWithNewUI($menus, $buttons, true);
    ?>
    <?php 
    $this->renderPartial("/sellContractSettlement/new_contractInfo", array('contract'=>$data['contract']));
    ?>
    <?php if(!empty($data['contractSettlement'])):?>
    <div class="z-card">
        <div class="z-card-body">
          <div class="flex-grid">
              <label class="col col-count-3 field flex-grid">
                  <p class="form-cell-title w-fixed">结算日期:</p>
                  <p class="form-control-static"><?php echo $data['contractSettlement']["settle_date"] ?></p>
              </label>
          </div>
      </div>
    </div>
  
    <?php 
        if(!empty($data['contractSettlement']['settlementGoods'])){//结算一
          $this->renderPartial("/sellContractSettlement/new_goodItems", array('contractSettlement'=>$data['contractSettlement']));//货款结算   
        }else{//结算二
            if(!empty($data['contractSettlement']['delivery_orders'])){
                foreach($data['contractSettlement']['delivery_orders'] as $k=>$v){
                    $this->renderPartial("/common/new_settlementDetail", array('settlement'=>$v,'type'=>3, 'isContractSettlement'=>1, 'isHiddenBtn'=>true));
                }
            }
        }
  
    ?>
  
    <div class="z-card">
        <div class="content-title-wrap">
          <h3 class="z-card-header">
              非货款类应收金额
              <p class="content-wrap-expand pull-right box-tools"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
          </h3>
        </div>
        <?php $this->renderPartial("/sellContractSettlement/new_otherGoods", array('contractSettlement'=>$data['contractSettlement'])); ?>
    </div>
    <div class="z-card">
        <div class="z-card-body">
            <div class="flex-grid form-group">
                <label class="col field col-count-1 flex-grid">
                    <span class="w-fixed">
                        备注:
                    </span>
                    <p class="form-control-static"><?php echo $data['contractSettlement']['remark'];?></p>
                </label>
            </div>
            <div>
                应收合计: <span><?php echo number_format($data['contractSettlement']['amount_settle']/100,2);?></span>元
                <a href="javascript: void 0" class="o-btn o-btn-action primary" style="margin-left: 10px;" onclick="lookDetail()">查看明细</a>
            </div>
        </div>
    </div>
    <?php $this->renderPartial("/sellContractSettlement/amountDetail", array('contractSettlement'=>$data['contractSettlement'],'contract'=>$data['contract'], 'contract_type'=>ConstantMap::SALE_TYPE));  ?>
    <?php endif;?>

    <div class="z-card">
        <h3 class="z-card-header">
            审核信息
        </h3>
        <div class="z-card-body">
            <form role="form" id="mainForm">
                <div class="flex-grid">
                    <label class="col col-count-1 field">
                        <p class="form-cell-title must-fill">审核意见</p>
                        <textarea class="form-control" cols="105" rows="3" data-bind="value:remark" placeholder="审核意见"></textarea>
                    </label>
                </div>
            </form>
        </div>
    </div>

    <?php 
     if (Utility::isNotEmpty($data['checkLogs']))
        $this->renderPartial("/common/new_checkLogList", array('checkLogs' => $data['checkLogs']));
    ?>
    
</section>

<script type="text/javascript">
    var view;
    $(function () {
        view=new ViewModel(<?php echo json_encode(array('items'=>$this->map['stock_in_settlement_checkitems_config'],'detail_id'=>$data['detail_id'], 'check_id'=>$data['check_id'], 'batch_id'=>$stockBatchSettlement->batch_id));?>);
        ko.applyBindings(view, $("#content")[0]);
    });

    function ViewModel(option) {
        var defaults = {
            check_id: 0,
            remark: '',
            status: 0,
            detail_id:'',
        };
        var o = $.extend(defaults, option);
        var self = this;
        self.check_id = o.check_id;
        self.detail_id=o.detail_id;
        self.status = ko.observable(o.status);
        self.remark = ko.observable(o.remark).extend({required:true,maxLength:512});
        self.errors = ko.validation.group(self);
        self.passText = ko.observable('通过');
        self.backText = ko.observable('驳回');
        self.actionState = 0;
        self.isValid = function () {
            return self.errors().length === 0;
        };
        self.doPass = function () {
            self.status(1);
            self.sendApprovalAjax();
        }
        self.doReject = function () {
            self.status(-1);
            self.sendApprovalAjax();
        }
        self.sendApprovalAjax = function () {
            if(!self.isValid()){
                self.errors.showAllMessages();
                return;
            }

            if(self.actionState==1)
                return;

            var confirmInfo = '通过销售合同结算审核';
            if (self.status() == -1) {
                confirmInfo = '驳回销售合同结算审核';
            }

            inc.vueConfirm({content:"您确定要" + confirmInfo + "，该操作不可逆？", type: 'warning',onConfirm:function(){
                var formData = {
                    data: {
                        remark : self.remark(),
                        check_id : self.check_id,
                        detail_id: self.detail_id,
                        checkStatus : self.status(),
                    }
                };

                if (self.status() == -1) {
                    self.backText('驳回' + inc.loadingIco);
                } else {
                    self.passText('通过' + inc.loadingIco);
                }
                self.actionState = 1;
                $.ajax({
                    type: "POST",
                    url: "/<?php echo $this->getId() ?>/save",
                    data: formData,
                    dataType: "json",
                    success: function (json) {
                        self.actionState = 0;
                        self.passText("通过");
                        self.backText("驳回");
                        if (json.state == 0) {
                            inc.vueMessage({type: 'success', message: '操作成功', duration: 500, onClose:function(){
                                    location.href = "/<?php echo $this->getId() ?>";
                                }});
                        } else {
                            inc.vueAlert({title:  '错误',content: json.data});
                        }
                    },
                    error: function (data) {
                        self.actionState = 0;
                        self.passText("通过");
                        self.backText("驳回");
                        inc.vueAlert({title:  '错误',content: "操作失败！" + data.responseText});
                    }
                });
            }});
        }

        self.back = function () {
            location.href = "/<?php echo $this->getId() ?>/?search[checkStatus]=1";
        }
    }
</script>

