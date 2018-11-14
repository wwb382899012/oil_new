<?php
//查询区域
$form_array = array(
   'form_url' => '/' . $this->getId() . '/',
   'input_array' => array(
       array('type' => 'text', 'key' => 'a.code*', 'text' => '银行流水编号'),
       array('type' => 'text', 'key' => 'a.bank_name*', 'text' => '银行名称'),
       array('type' => 'select', 'key' => 'r.status', 'map_name' => 'receive_confirm_status', 'text' => '状态'),
       array('type' => 'text', 'key' => 'a.account_name*', 'text' => '银行账户名'),
       array('type' => 'text', 'key' => 'b.name*', 'text' => '交易主体'),
       array('type' => 'text', 'key' => 'a.pay_partner*', 'text' => '付款公司'),
       array('type' => 'date', 'key' => 'a.receive_date>','id'=>'datepicker', 'text' => '收款开始时间'),
       array('type' => 'date', 'key' => 'a.receive_date<','id'=>'datepicker2', 'text' => '收款结束时间'),
       array('type' => 'text', 'key' => 'c.contract_code*', 'text' => '货款合同编号'),
       array('type' => 'text', 'key' => 'cf.code_out*', 'text' => '外部合同编号'),
   ),
    'buttonArray' => array(
        array('text'=>'重置','buttonId'=>'resetButton'),
        array('text' => '导出', 'buttonId' => 'export'),
    )
);
//列表显示
$array = array(
    array('key' => 'flow_id', 'type' => 'href', 'style' => 'width:130px;text-align:center;', 'text' => '操作', 'href_text' => 'getRowActions'),
    array('key' => 'receive_id', 'type' => 'text', 'style' => 'width:140px;text-align:center', 'text' => '收款编号'),
    array('key' => 'flow_id', 'type' => 'text', 'style' => 'width:140px;text-align:center', 'text' => '系统流水号'),
    array('key' => 'code', 'type' => 'text', 'style' => 'width:140px;text-align:center', 'text' => '银行流水编号'),
    array('key' => 'corporation_id,corporation_name', 'type' => 'href', 'style' => 'width:180px;text-align:center', 'text' => '交易主体', 'href_text' => '<a id="t_{1}" title="交易主体详情" href="/corporation/detail/?id={1}">{2}</a>'),
    array('key' => 'bank_name', 'type' => 'text', 'style' => 'width:140px;text-align:center', 'text' => '收款账户名'),
    array('key' => 'pay_partner', 'type' => 'text', 'style' => 'width:140px;text-align:center', 'text' => '付款公司'),
    array('key' => 'user_name', 'type' => 'text', 'style' => 'width:140px;text-align:center', 'text' => '认领人'),
    array('key'=>'receive_status','style'=>'width:100px;','text'=>'状态', 'type'=>'map_val', 'map_name'=>'receive_confirm_status'),
    array('key' => 'project_code', 'type' => 'text', 'style' => 'width:140px;text-align:center', 'text' => '项目编号'),
    array('key' => 'project_type', 'type' => 'map_val', 'style' => 'width:80px;text-align:center', 'text' => '项目类型', 'map_name' => 'project_type'),
    array('key' => 'contract_type', 'type' => 'map_val', 'style' => 'width:80px;text-align:center', 'text' => '合同类型', 'map_name' => 'buy_sell_type'),
    array('key' => 'contract_code', 'type' => 'text', 'style' => 'width:140px;text-align:center', 'text' => '货款合同编号'),
    array('key' => 'code_out', 'type'=> '', 'style' => 'width:140px;text-align:center', 'text' => '外部合同编号'),
    array('key' => 'sub_contract_code', 'type' => 'text', 'style' => 'width:140px;text-align:center', 'text' => '收款合同编号'),
    array('key' => 'subject_name', 'type' => 'text', 'style' => 'width:80px;text-align:center', 'text' => '用途'),
    array('key' => 'currency', 'type' => 'map_val', 'style' => 'width:80px;text-align:center', 'text' => '币种', 'map_name' => 'currency_type'),
    array('key' => 'flow_id', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '认领金额', 'href_text' => 'getRowAmount'),
    array('key' => 'receive_date', 'type' => 'text', 'style' => 'width:140px;text-align:center', 'text' => '收款时间'),
    array('key' => 'create_time', 'type' => 'text', 'style' => 'width:140px;text-align:center', 'text' => '认领时间'),
);

function getRowActions($row, $self) {
    $links = array();

    if($row['receive_status']==ReceiveConfirm::STATUS_NEW&&$row['create_user_id']==Utility::getNowUserId()) {
      $links[] = '<a href="/receiveConfirm/edit?id=' . $row["receive_id"] . '" title="修改">修改</a>';
      $links[] = '<a href="javascript:void(0);" onclick="del('.$row['receive_id'].')" title="作废">作废</a>';
    }
    $links[] = '<a href="/receiveConfirm/detail?id=' . $row["receive_id"] . '&back_url=receiveConfirmList" title="查看">查看</a>';

    $s = Utility::isNotEmpty($links) ? implode("&nbsp;|&nbsp;", $links) : '';

    return $s;
}

function getRowAmount($row, $self) {
    return $self->map['currency'][$row['currency']]['ico'] . number_format($row["received_amount"]/100,2);
}


$style = empty($_data_['data']['rows']) ? "min-width:1050px;" : "min-width:2750px;";
$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_[data], "", $style, "table-bordered table-layout scrolltable");

?>


<script>
  $(function () {
    var fieldList = <?php echo json_encode($export_array)?>;
    $("#resetButton").on('click', function() {
      $("form.search-form")[0].reset();
    });
    $("#export").click(function () {
      /*var export_str = JSON.stringify(fieldList);
      var export_input = $('<input type="hidden">');
      export_input.val(export_str).attr('name', 'export_str');
      $("form.search-form").append(export_input);
      $("form.search-form").submit();
      setTimeout(function() {
        export_input.remove();
      }, 10);*/
		var formData= $(this).parents("form.search-form").serialize();
		location.href="/<?php echo $this->getId() ?>/export?"+formData;
    }); 
  });
  var del = function(flow_id) {
    layer.confirm('是否确认作废本次收款记录?', function() {
      $.ajax({
        type:"POST",
        url:"/receiveConfirm/ajaxDel",
        data:{
          id:flow_id
        },
        dataType:"json",
        success:function (json) {
            if(json.state==0){
                layer.msg("撤回成功", {icon: 6, time:1000},function() {
                  window.location.reload();
                });
            }else{
                layer.alert(json.data, {icon: 5});
                self.submitBtnText("提交");
                self.saveBtnText("保存");
                self.isSubmit(0);
                layer.alert(json.data, {icon: 5});
            }
        },
        error:function (data) {
            layer.alert("保存失败！"+data.responseText, {icon: 5});
            self.submitBtnText("提交");
            self.saveBtnText("保存");
            self.isSubmit(0);
            layer.alert("操作失败！" + data.responseText, {icon: 5});
        }
      })
    });
  }
</script>
