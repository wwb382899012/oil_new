<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/23 11:11
 * Describe：
 */

function checkRowEditAction($row, $self) { 
    $links = array();
    $payApply = PayApplication::model()->findByPk($row['apply_id']);
    if($payApply->isCanClaim($row["status"]))
    {
        $links[] = '<a href="/' . $self->getId() . '/add?id=' . $row["apply_id"] . '" title="认领">认领</a>';
    }
    if(bccomp($row['amount_claim'], 0, 2) ===1) {
        $links[] = '<a href="/' . $self->getId() . '/view?apply_id=' . $row["apply_id"] . '" title="查看">查看</a>';
    }


    $s = Utility::isNotEmpty($links) ? implode("&nbsp;|&nbsp;", $links) : '';

    return $s;
}

function showPayAmount($row, $self)
{
    $str=$self->map["currency"][$row["currency"]]["ico"].number_format($row["amount"]/100,2);
    return $str;
}

function showPaidAmount($row, $self)
{
    $str=$self->map["currency"][$row["currency"]]["ico"].number_format($row["amount_paid"]/100,2);
    return $str;
}
function showContractType($row, $self)
{
    $str=$self->map["contract_config"][$row['contract_type']][$row['contract_category']]["name"];
    return $str;
}

function showPayClaimStatus($row) {

}


//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'input_array' => array(
        array('type' => 'text', 'key' => 'a.apply_id', 'text' => '付款编号'),
        array('type' => 'corpName', 'key' => 'a.corporation_id', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 'a.payee*', 'text' => '收款单位'),
        array('type' => 'select', 'key' => 'claim_status', 'map_name' => 'pay_claim_status', 'text' => '认领状态'),
        array('type' => 'text', 'key' => 'su.name', 'text' => '申请人'),
    ),
    "buttonArray"=>array(
    ),
);


//列表显示
$array = array(
    array('key' => 'project_id', 'type' => 'href', 'style' => 'width:80px;text-align:center;', 'text' => '操作', 'href_text' => 'checkRowEditAction'),
    array('key' => 'apply_id,apply_id', 'type' => 'href', 'style' => 'width:120px;text-align:center', 'text' => '付款编号', 'href_text'=>'<a id="t_{1}" title="{2}"  href="/pay/detail/?id={1}">{1}</a>'),
    array('key' => 'amount', 'type' => 'href', 'style' => 'width:120px;text-align:right;', 'text' => '付款金额', 'href_text' => 'showPayAmount'),
    array('key' => 'amount_paid', 'type' => 'href', 'style' => 'width:120px;text-align:right', 'text' => '实付金额', 'href_text' => 'showPaidAmount'),
    array('key' => 'corporation_id,corp_name', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '交易主体', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
//    array('key' => 'type', 'type' => 'map_val', 'map_name' => 'pay_application_type', 'style' => 'width:120px;text-align:center', 'text' => '付款类型'),
    array('key' => 'claim_status', 'type' => '', 'style' => 'width:80px;text-align:center', 'text' => '认领状态'),
    array('key' => 'payee', 'type' => 'href', 'style' => 'width:120px;text-align:center', 'text' => '收款单位', 'href_text' => '<span title="{1}">{1}</span>'),
    array('key' => 'subject_name', 'type' => '', 'style' => 'width:80px;text-align:center', 'text' => '用途'),
    array('key' => 'create_name', 'type' => '', 'style' => 'width:80px;text-align:center', 'text' => '申请人'),
    array('key' => 'create_time', 'type' => '', 'style' => 'width:140px;text-align:center', 'text' => '付款申请时间'),

);


$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_[data], "", "min-width:1250px;", "table-bordered table-layout");
?>
<script>
    $(function () {
        $("#add").click(function () {
            location.href = "/subContract/";
        });
    });
    function trash(id) {
        layer.confirm("您确定要作废当前信息吗，该操作不可逆？", {icon: 3, title: '提示'}, function(index){
            var formData = {id: id};
            $.ajax({
                type: 'POST',
                url: '/pay/trash',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        layer.msg("操作成功", {icon: 6, time:1000}, function(){
                            $("td[value="+id+"]").parent().remove();
                        });
                    }
                    else {
                        layer.alert(json.data, {icon: 5});
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
