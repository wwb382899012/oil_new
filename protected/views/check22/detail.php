<?php

$id=empty($model)?0:$model['contract_id'];
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
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#detail" data-toggle="tab">基本信息</a></li>
            <li><a href="#flow" data-toggle="tab">审核记录</a></li>
            
            <li class="pull-right"><button type="button" class="btn btn-sm btn-default" onclick="back()">返回</button></li>
           
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="detail">

                <section class="content">
                 <?php 
                    $this->renderPartial("../sellContractSettlement/contractInfo", array('contract'=>$data['contract'], 'hideBackBtn'=>true));
                 ?>
                <?php if(!empty($data['contractSettlement'])):?>
                    <div class="box" data-bind="with:settlement">
                        <div class="box-body form-horizontal">
                            <label for="settle_date" class="col-sm-2 control-label">结算日期</label>
                            <p class="form-control-static col-sm-3"><?php echo $data['contractSettlement']["settle_date"] ?></p>
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
                  <?php endif;?>

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
                    <div class="box-footer">
                        <div class="form-group">
                            <div class="pull-right">
                                <?php if(!$this->isExternal){ ?>
                                <button type="button"  class="btn btn-default history-back" onclick="back()">返回</button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </section>
            </div><!--end tab1-->

            <div class="tab-pane" id="flow">
              <?php 
              if (Utility::isNotEmpty($data['checkLogs']))
                  $this->renderPartial("/common/checkLogList", array('checkLogs' => $data['checkLogs']));
              ?>
            </div>
        </div>
    </div>
</section><!--end content-->
<script type="text/javascript">
    var back = function() {
        history.go(-1);
    }
</script>