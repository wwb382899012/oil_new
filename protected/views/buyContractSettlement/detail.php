
<section class="content">
<?php 
$this->renderPartial("../sellContractSettlement/contractInfo", array('contract'=>$data['contract']));

?>
    <div class="box" data-bind="with:settlement">
        <div class="box-body form-horizontal">
            <label for="settle_date" class="col-sm-2 control-label">结算日期</label>
            <p class="form-control-static col-sm-3"><?php echo $data['settlement']["settle_date"] ?></p>
        </div>
    </div>
  
  <?php 
  if(empty($data['settlement']['settlementGoods'])){//按入库通知单结算
      //$this->renderPartial("ladingBillList", array('lading_bills'=>$data['settlement']['lading_bills'],'contract_id'=>$data['settlement']['contract_id']));
      if(!empty($data['settlement']['lading_bills'])){
          foreach($data['settlement']['lading_bills'] as $k=>$v){

              $this->renderPartial("/common/settlementDetail", array('settlement'=>$v,'type'=>1, 'isContractSettlement'=>1));
          }
      }


  }else{//按采购合同结算
      $this->renderPartial("goodItems", array('contractSettlement'=>$data['settlement']));
  }
  
  
  //货款结算   ?>
  
    <div class="box"  data-bind="with:settlement">
        <div class="box-header with-border">
            <h3 class="box-title">非货款类应付金额</h3>
        </div>
 		<?php $this->renderPartial("otherGoods", array('contractSettlement'=>$data['settlement'])); ?>

    </div>
    
    <div class="box box-solid">
   		 <div class="box-body form-horizontal">
            <div class="pull-left">
           	    备注：<?php echo $data['settlement']['remark'];?>
            </div>
        </div>
        <div class="box-footer">
			<div class="pull-left">
                                           应付合计：<span><?php echo number_format($data['settlement']['amount_settle']/100,2);?></span>元
                     <button class="btn btn-link" style="margin-left: 10px;" onclick="lookDetail()">查看明细</button>
            </div>
            <div class="pull-right">
              <?php if($data['isCanEdit']):?> 
                <div class="btn btn-success" onclick="edit()">修改</div>
              <?php endif;?>
              <?php if($data['isCanSubmit']):?>
                <div class="btn btn-danger" onclick="submit(<?php echo $data['settlement']['contract_id'];?>)">提交</div>
              <?php endif;?>  

              <?php if(!$this->isExternal){ ?>
                <div class="btn btn-default" onclick="back()">返回</div>
              <?php } ?>
            </div>
        </div>
    </div>
    <?php 
     if (Utility::isNotEmpty($data['checkLogs']))
        $this->renderPartial("/common/checkLogList", array('checkLogs' => $data['checkLogs']));
    ?>
    
   <?php $this->renderPartial("../sellContractSettlement/amountDetail", array('contractSettlement'=>$data['settlement'],'contract'=>$data['contract']));  ?>
    
</section>





<script>

    function back() {
        location.href = '/<?php echo $this->getId() ?>';
    }
    function edit() {
       location.href = '/<?php echo $this->getId() ?>/edit?id='+"<?php echo $data['contract']['contract_id']; ?>";
    }

    function submit(contract_id){
        layer.confirm("您确定要提交采购合同结算单吗，该操作不可逆？", {icon: 3, 'title': '提示'}, function (index) {
            var formData = "contract_id=" + contract_id;
            $.ajax({
                type: "POST",
                url: "/<?php echo $this->getId() ?>/submit",
                data: formData,
                dataType: "json",
                success: function (json) {
                    if(json.code==0){
                        layer.msg('操作成功', {time: 1000}, function () {
                            location.reload();
                        });
                    }
                    else {
                        layer.alert(json.msg, {icon: 5},function(){
                            location.reload();
                        });
                    }
                },
                error: function (data) {
                    layer.alert("操作失败！" + data.responseText, {icon: 5});
                }
            });
            layer.close(index);
        });
    }


</script>