<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/23 11:11
 * Describe：
 */

function checkRowEditAction($row, $self) { 
    $links = array();
    $links[] = '<a href="/' . $self->getId() . '/detail?id=' . $row["apply_id"] . '" title="查看详情">详情</a>';
    if(PayApplication::model()->isCanEdit($row["status"]))
    {
        $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["apply_id"] . '" title="修改">修改</a>';
    }
    if (PayService::canWithdraw($row['apply_id'], $row['create_user_id']))
        $links[] = '<a onclick="withdraw('.$row["apply_id"].')" title="撤回">撤回</a>';

    if (PayApplication::model()->isCanTrash($row['status']) && $row['create_user_id'] == Utility::getNowUserId())
        $links[] = '<a onclick="trash('.$row["apply_id"].')" title="作废">作废</a>';

    $s = Utility::isNotEmpty($links) ? implode("&nbsp;|&nbsp;", $links) : '';

    return $s;
}

function generateAdd($searchData,$searchItem)
{
    $str="&nbsp;<div class=\"btn-group \" role=\"group\">
            <button type=\"button\" class=\"btn btn-success btn-sm dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
              发起付款申请
              <span class=\"caret\"></span>
            </button>
            <ul class=\"dropdown-menu\">";

    foreach (Map::$v["pay_application_type"] as $k=>$v)
    {
        $str.="<li><a href=\"/pay/route?type=".$k."\">".$v."</a></li>";
    }
    $str.="</ul></div>";

    return $str;
}


function showPayAmount($row, $self)
{
    $str=$self->map["currency"][$row["currency"]]["ico"].number_format($row["amount"]/100,2);
    return $str;
}

function showStopAmount($row, $self)
{
    $stopAmount = PayService::getStopPayAmount($row['apply_id']);
    $str=$self->map["currency"][$row["currency"]]["ico"].number_format($stopAmount/100,2);
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
function getPayTime($row,$self)
{
    $checkDetail = FlowService::getCheckDetail($row['apply_id'],13);
    if(!empty($checkDetail)) {
        return $checkDetail[0]['create_time'];
    }else{
        return $row['create_time'];
    }
}

//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'input_array' => array(
        array('type' => 'text', 'key' => 'a.apply_id', 'text' => '付款申请编号'),
        array('type' => 'corpName', 'key' => 'a.corporation_id', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 'a.payee*', 'text' => '收款单位'),
        array('type' => 'select', 'key' => 'a.sub_contract_type', 'map_name' => 'contract_category', 'text' => '付款合同类型'),
        array('type' => 'select', 'key' => 'a.subject_id', 'map_name' => 'finance_subjects', 'text' => '用途'),
        array('type' => 'text', 'key' => 'p.project_code', 'text' => '项目编号'),
        array('type' => 'text', 'key' => 'co.contract_code', 'text' => '合同编号'),
        array('type' => 'text', 'key' => 'cf.code_out*', 'text' => '外部合同编号'),
        array('type' => 'text', 'key' => 'a.sub_contract_code', 'text' => '付款合同编号'),
        array('type' => 'select', 'key' => 'a.status', 'map_name' => 'pay_application_status', 'text' => '审核状态'),
        array('type' => 'select', 'key' => 'a.type', 'map_name' => 'pay_application_type', 'text' => '付款类型'),
        array('type' => 'text', 'key' => 'su.name', 'text' => '申请人'),
    ),
    "buttonArray"=>array(
            //array('text' => '发起付款申请', 'buttonId' => 'add')
            array('type' => 'custom', 'content' => 'generateAdd')
    ),
);


//列表显示
$array = array(
    array('key' => 'apply_id', 'type' => 'href', 'style' => 'width:160px;text-align:center;', 'text' => '操作', 'href_text' => 'checkRowEditAction'),
    array('key' => 'apply_id,apply_id', 'type' => 'href', 'style' => 'width:120px;text-align:center', 'text' => '付款申请编号', 'href_text'=>'<a id="t_{1}" title="{2}"  href="/pay/detail/?id={1}">{1}</a>'),
    array('key' => 'amount', 'type' => 'href', 'style' => 'width:140px;text-align:right;', 'text' => '付款金额', 'href_text' => 'showPayAmount'),
    array('key' => 'amount_paid', 'type' => 'href', 'style' => 'width:140px;text-align:right', 'text' => '实付金额', 'href_text' => 'showPaidAmount'),
    array('key' => 'amount_stop', 'type' => 'href', 'style' => 'width:140px;text-align:right', 'text' => '止付金额', 'href_text' => 'showStopAmount'),
    array('key' => 'corporation_id,corp_name', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '交易主体', 'href_text' => '<a target="_blank" id="t_{1}" title="{2}" href="/corporation/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'payee', 'type' => '', 'style' => 'width:120px;text-align:center', 'text' => '收款单位'),
    array('key' => 'status', 'type' => 'map_val', 'map_name' => 'pay_application_status', 'style' => 'width:80px;text-align:center', 'text' => '状态'),
    array('key' => 'type', 'type' => 'map_val', 'map_name' => 'pay_application_type', 'style' => 'width:120px;text-align:center', 'text' => '付款类型'),
    array('key' => 'contract_type', 'type' => 'href', 'style' => 'width:120px;text-align:center;', 'text' => '合同类型', 'href_text' => 'showContractType'),
    array('key' => 'contract_code', 'type' => '', 'style' => 'width:140px;text-align:center', 'text' => '合同编号'),
    array('key' => 'code_out', 'type'=> '', 'style' => 'width:140px;text-align:center', 'text' => '外部合同编号'),
    array('key' => 'sub_contract_type', 'type' => 'map_val', 'map_name' => 'contract_category', 'style' => 'width:140px;text-align:center', 'text' => '付款合同类别'),
    array('key' => 'sub_contract_code', 'type' => '', 'style' => 'width:140px;text-align:center', 'text' => '付款合同编号'),
    array('key' => 'subject_name', 'type' => '', 'style' => 'width:80px;text-align:center', 'text' => '用途'),
    array('key'=>'project_id,project_code','type'=>'href','style'=>'width:150px;text-align:center;','text'=>'项目编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/project/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'create_name', 'type' => '', 'style' => 'width:80px;text-align:center', 'text' => '申请人'),
    array('key' => 'create_time', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '付款申请时间','href_text'=>'getPayTime'),

);


$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_[data], "", "min-width:2550px;", "table-bordered table-layout");
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
                            location.href = "/<?php echo $this->getId() ?>/index";
//                            $("td[value="+id+"]").parent().remove();
                        });
                    }
                    else {
                        layer.confirm(json.data, {icon: 3, title: '提示', btn: ['确定']},function(){
                            location.href = "/<?php echo $this->getId() ?>/index";
                        });
//                        layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
                    layer.alert("操作失败！" + data.responseText, {icon: 5});
                }
            });

            layer.close(index);
        });
    }

    function withdraw(id) {
        layer.confirm("您确定要撤回当前审核吗，该操作不可逆？", {icon: 3, title: '提示'}, function(index){
            var formData = {id: id};
            $.ajax({
                type: 'POST',
                url: '/pay/withdraw',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        layer.msg("操作成功", {icon: 6, time:500}, function(){
                            location.href="/pay/index";
                        });
                    }
                    else {
                        layer.confirm(json.data, {icon: 3, title: '提示', btn: ['确定']},function(){
                            location.href = "/<?php echo $this->getId() ?>/index";
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
