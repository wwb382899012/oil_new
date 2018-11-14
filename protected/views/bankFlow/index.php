<?php
//查询区域
$form_array = array(
   'form_url' => '/' . $this->getId() . '/',
   'input_array' => array(
       array('type' => 'text', 'key' => 'a.code*', 'text' => '银行流水编号'),
       array('type' => 'text', 'key' => 'a.bank_name*', 'text' => '收款银行'),
       array('type' => 'select', 'key' => 'a.status', 'map_name' => 'bank_flow_status', 'text' => '状态'),
       array('type' => 'text', 'key' => 'a.account_name*', 'text' => '银行账户名'),
       array('type' => 'text', 'key' => 'b.name*', 'text' => '交易主体'),
       array('type' => 'text', 'key' => 'a.pay_partner*', 'text' => '付款公司'),
       array('type' => 'date', 'key' => 'a.receive_date>', 'text' => '收款开始时间','id'=>'startDate'),
       array('type' => 'date', 'key' => 'a.receive_date<', 'text' => '收款结束时间','id'=>'endDate'),
   ),
    'buttonArray' => array(
        array('text'=>'添加','buttonId'=>'addButton'),
        array('text'=>'重置','buttonId'=>'resetButton'),
        array('text' => '导入', 'buttonId' => 'import'),
        array('text' => '导出', 'buttonId' => 'export'),
    )
);
//列表显示
$array = array(
    array('key' => 'flow_id', 'type' => 'href', 'style' => 'width:100px;text-align:center;', 'text' => '操作', 'href_text' => 'getRowActions'),
    array('key' => 'status', 'type' => 'map_val', 'style' => 'width:80px;text-align:center', 'text' => '状态', 'map_name' => 'bank_flow_status'),
    array('key' => 'receive_date', 'type' => 'text', 'style' => 'width:140px;text-align:center', 'text' => '收款时间'),
    array('key' => 'amount', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '收款金额', 'href_text' => 'getRowAmount'),
    array('key' => 'pay_partner', 'type' => 'text', 'style' => 'width:240px;text-align:center', 'text' => '付款公司'),
    array('key' => 'corporation_id,corporation_name', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '交易主体', 'href_text' => '<a id="t_{1}" title="交易主体详情" href="/corporation/detail/?id={1}">{2}</a>'),
    array('key' => 'flow_id', 'type' => 'text', 'style' => 'width:140px;text-align:center', 'text' => 'ID'),
    array('key' => 'code', 'type' => 'text', 'style' => 'width:140px;text-align:center', 'text' => '银行流水编号'),
    array('key' => 'bank_name', 'type' => 'text', 'style' => 'width:120px;text-align:center', 'text' => '收款银行'),
    array('key' => 'account_name', 'type' => 'text', 'style' => 'width:120px;text-align:center', 'text' => '银行账户名'),
    array('key' => 'pay_bank', 'type' => 'text', 'style' => 'width:240px;text-align:center', 'text' => '付款银行'),
);

// 列表导出
/*$export_array = array(
    array('key' => 'status', 'type' => 'map_val', 'style' => 'width:80px;text-align:center', 'text' => '状态', 'map_name' => 'bank_flow_status'),
    array('key' => 'receive_date', 'type' => 'text', 'style' => 'width:140px;text-align:center', 'text' => '收款时间'),
    array('key' => 'amount', 'map_key'=>'currency', 'type' => 'amount_map_key', 'style' => 'width:140px;text-align:center', 'text' => '收款金额', 'map_name' => 'currency_ico'),
    array('key' => 'pay_partner', 'type' => 'text', 'style' => 'width:240px;text-align:center', 'text' => '付款公司'),
    array('key' => 'corporation_name', 'type' => 'text', 'style' => 'width:140px;text-align:center', 'text' => '交易主体'),
    array('key' => 'flow_id', 'type' => 'text', 'style' => 'width:140px;text-align:center', 'text' => 'ID'),
    array('key' => 'code', 'type' => 'text', 'style' => 'width:140px;text-align:center', 'text' => '银行流水编号'),
    array('key' => 'bank_name', 'type' => 'text', 'style' => 'width:120px;text-align:center', 'text' => '收款银行'),
    array('key' => 'account_name', 'type' => 'text', 'style' => 'width:120px;text-align:center', 'text' => '银行账户名'),
    array('key' => 'pay_bank', 'type' => 'text', 'style' => 'width:240px;text-align:center', 'text' => '付款银行'),
);*/

function getRowActions($row, $self) {
    $links = array();
    if($row['status']<BankFlow::STATUS_SUBMITED&&$row['create_user_id']==Utility::getNowUserId()) {
      $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["flow_id"] . '" title="修改">修改</a>';
    } elseif($row['status']==BankFlow::STATUS_SUBMITED&&$row['create_user_id']==Utility::getNowUserId()&&$row['receive_count']==0)  {
      $links[] = '<a href="javascript:void(0);" onclick="rollback('.$row['flow_id'].')" title="撤回">撤回</a>';
    }
    $links[] = '<a href="/' . $self->getId() . '/detail?id=' . $row["flow_id"] . '" title="查看">查看</a>';

    $s = Utility::isNotEmpty($links) ? implode("&nbsp;|&nbsp;", $links) : '';

    return $s;
}

function getRowAmount($row, $self) {
    return $self->map['currency'][$row['currency']]['ico'] . number_format($row["amount"]/100,2);
}

$style = empty($_data_['data']['rows']) ? "min-width:1050px;" : "min-width:1650px;";

$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_['data'], "", $style, "table-bordered table-layout");

?>


<script>
  $(function () {
    var fieldList = <?php echo json_encode($export_array)?>;
    $("#addButton").on('click', function() {
        window.location.href="/<?php echo $this->getId() ?>/add";
    });
    $("#resetButton").on('click', function() {
      for (var i = $("form.search-form input").length - 1; i >= 0; i--) {
        $($("form.search-form input[type=text]")[i]).val('');
      }
      for (var i = $("form.search-form select").length - 1; i >= 0; i--) {
        $($("form.search-form select")[i]).val('');
      }
    });
    $("#import").click(function () {
      location.href = "/<?php echo $this->getId() ?>/import";
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

  var rollback = function(flow_id) {
    layer.confirm('是否确认撤回本次流水记录?', function() {
      $.ajax({
        type:"POST",
        url:"/<?php echo $this->getId() ?>/ajaxRollback",
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
                self.isSubmit(0);
                layer.alert(json.data, {icon: 5});
            }
        },
        error:function (data) {
            layer.alert("保存失败！"+data.responseText, {icon: 5});
            self.isSubmit(0);
            layer.alert("操作失败！" + data.responseText, {icon: 5});
        }
      })
    });
  }
</script>