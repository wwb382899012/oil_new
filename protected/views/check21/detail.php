<?php
$stockNotice = StockNotice::model()->with("contract", "contract.contractGoods", "create_user")->findByPk($data['obj_id']);

$stockNoticeGoods = StockNoticeDetail::model()->with('sub', 'goods', 'store', 'lock')->findAllToArray('t.batch_id=:batch_id', array('batch_id'=>$data['obj_id']));

$stockBatchSettlements = StockBatchSettlement::model()->with('sub', 'goods')->findAllToArray(array('condition'=>'batch_id=:batch_id', 'params'=>array('batch_id'=>$data['obj_id'])));

$stockIns = StockIn::model()->with("store", "details", "details.sub", "details.goods")->findAllToArray('t.batch_id=:batch_id',array("batch_id"=>$data['obj_id'],));
$attachments = StockBatchSettlementService::getAttachment($stockNotice->batch_id);
$checkLogs = FlowService::getCheckLog($stockNotice->batch_id,"8");
?>

   <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#detail" data-toggle="tab">基本信息</a></li>
            <li><a href="#flow" data-toggle="tab">审核记录</a></li>
            <?php if(!$this->isExternal){ ?>
            <li class="pull-right"><button type="button" class="btn btn-sm btn-default" onclick="back()">返回</button></li>
            <?php } ?>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="detail">
            
            
                <section class="content">
                
                <?php 
                    $this->renderPartial("../sellContractSettlement/contractInfo", array('contract'=>$data['contract'],'hideBackBtn'=>true));
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
                  $this->renderPartial("../buyContractSettlement/goodItems", array('contractSettlement'=>$data['contractSettlement']));//货款结算   
                  }else{//结算二
                      //$this->renderPartial("../buyContractSettlement/ladingBillList", array('lading_bills'=>$data['contractSettlement']['lading_bills']));
                      if(!empty($data['contractSettlement']['lading_bills'])){
                          foreach($data['contractSettlement']['lading_bills'] as $k=>$v){

                              $this->renderPartial("/common/settlementDetail", array('settlement'=>$v,'type'=>1, 'isContractSettlement'=>1));
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
                </section><!--end content-->
         </div>
        <div class="tab-pane" id="flow">
               <?php 
                     if (Utility::isNotEmpty($data['checkLogs']))
                        $this->renderPartial("/common/checkLogList", array('checkLogs' => $data['checkLogs']));
               ?>
        </div>
   </div>
</div>
<script type="text/javascript">
    var back = function() {
        history.go(-1);
    }
</script>