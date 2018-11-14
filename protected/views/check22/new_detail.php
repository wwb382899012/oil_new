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

<section class="content" id="content">
  <?php
  $menus = [['text'=>'结算管理'],['text'=>'销售合同结算审核','link'=>'/check22/'], ['text' => $this->pageTitle]];
  $this->loadHeaderWithNewUI($menus, [], '/check22/');
  ?>
  <?php 
    $this->renderPartial("/sellContractSettlement/new_contractInfo", array('contract'=>$data['contract']));
    ?>
    <?php if(!empty($data['contractSettlement'])):?>
    <div class="z-card">
        <div class="z-card-body">
          <div class="flex-grid form-group">
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
    <?php $this->renderPartial("/common/new_checkLogList", array('checkLogs' => $data['checkLogs'])); ?>
</section>