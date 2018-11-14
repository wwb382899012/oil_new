<?php
/**
 * Created by youyi000.
 * DateTime: 2016/6/21 15:25
 * Describe：
 */
function checkRowPrint($row, $self)
{
    $links = array();

    if(in_array($row['pay_status'],array(1,3)))
        $links[] = '<input type="checkbox" style="width: auto" class="printInput" name="printItem[]" data-id="'.$row['apply_id'].'" />';
    else
        $links[] = '<input type="checkbox" style="width: auto" disabled class="printInput" />';

    $s = !empty($links) ? implode("&nbsp;|&nbsp;", $links) : '';
    return $s;

}
function checkRowEditAction($row, $self)
{
    $links = array();
    $data = PayService::getAllPayComfirmInfo($row['apply_id']);
    if(count($data)>0)
        $links[] = '<a href="/'.$self->getId().'/detail?id='.$row["apply_id"].'" title="查看详情">详情</a>';

    if (!in_array($row['pay_stop_status'], array(PayApplicationExtra::STATUS_CHECKING, PayApplicationExtra::STATUS_PASS))) {
        if (empty($data[0]['status']) || ($data[0]['status'] == Payment::STATUS_SUBMITED && bccomp($row['amount'], $row['amount_paid']) == 1))
            $links[] = '<a href="/' . $self->getId() . '/add?id=' . $row["apply_id"] . '" title="付款实付">实付</a>';
        else if ($data[0]['status'] == Payment::STATUS_SAVED)
            $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["apply_id"] . '" title="付款实付">实付</a>';
    }
    
    $s = !empty($links) ? implode("&nbsp;&nbsp;", $links) : '';
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
function showStopAmount($row, $self)
{
    $stopAmount = PayService::getStopPayAmount($row['apply_id']);
    $str=$self->map["currency"][$row["currency"]]["ico"].number_format($stopAmount/100,2);
    return $str;
}

//查询区域
$form_array = array('form_url'=>'/'.$this->getId().'/',
	'items'=>array(
        array('type' => 'corpName', 'key' => 'a.corporation_id', 'text' => '交易主体'),
        array('type'=>'text','key'=>'p.project_code','text'=>'项目编号'),
        array('type'=>'select','key'=>'status','map_name'=>'pay_confirm_status','text'=>'实付状态'),
        array('type'=>'text','key'=>'a.apply_id','text'=>'付款编号'),
        array('type'=>'text','key'=>'a.payee*','text'=>'收款单位'),
        array('type'=>'select','key'=>'a.sub_contract_type','map_name'=>'contract_category','text'=>'付款合同类型'),
        array('type'=>'text','key'=>'a.sub_contract_code*','text'=>'付款合同编号'),
        array('type'=>'date','key'=>'start_date','id'=>'datepicker','text'=>'开始日期'),
        array('type'=>'date','key'=>'end_date','id'=>'datepicker2','text'=>'结束日期'),
        
    ),
);
$buttonArray[0]=array(
    'text' => '批量打印',
    'attr' => [
        'id' => 'printButton',
        //'onclick' => "location.href='/".$this->getId()."/add'",
    ]
);

//列表显示
$array =array(
    array('key'=>'apply_id','type'=>'href','style'=>'width:40px;text-align:left;','text'=>'<input type="checkbox" style="width: auto;vertical-align: middle;" class="checkAll" /><span>全选</span>','href_text'=>'checkRowPrint'),
    array('key'=>'apply_id','type'=>'href','style'=>'width:130px;text-align:left','text'=>'付款编号','href_text'=>'<a id="t_{1}" title="{1}" target="_blank" href="/pay/detail/?id={1}&t=1" >{1}</a>'),
    array('key'=>'pay_status','type'=>'map_val','text'=>'实付状态','map_name'=>'pay_confirm_status','style'=>'width:120px;text-align:left'),
    array('key'=>'corporation_name','type'=>'','style'=>'text-align:left;','text'=>'交易主体'),
    array('key'=>'payee','type'=>'','style'=>'width:140px;text-align:left','text'=>'收款单位'),
    array('key'=>'project_id,project_code','type'=>'href','style'=>'width:150px;text-align:left;','text'=>'项目编号', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/project/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'sub_contract_type','type'=>'map_val','style'=>'width:100px;text-align:left;','text'=>'付款合同类型','map_name'=>'contract_category'),
    array('key'=>'contract_id,sub_contract_code','type'=>'href','style'=>'width:150px;text-align:left;','text'=>'付款合同编号','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/businessConfirm/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'create_time','type'=>'','style'=>'width:140px;text-align:left','text'=>'付款申请时间'),
    array('key'=>'currency','type'=>'map_val','style'=>'width:80px;text-align:left','text'=>'付款币种', 'map_name'=>'pay_currency'),
    array('key' => 'amount', 'type' => 'href', 'style' => 'width:140px;text-align:right;', 'text' => '付款申请金额', 'href_text' => 'showPayAmount'),
    array('key' => 'amount_paid', 'type' => 'href', 'style' => 'width:140px;text-align:right;', 'text' => '实付金额', 'href_text' => 'showPaidAmount'),
    array('key' => 'amount_paid', 'type' => 'href', 'style' => 'width:140px;text-align:right;', 'text' => '止付金额', 'href_text' => 'showStopAmount'),
    array('key'=>'user_name','type'=>'','style'=>'width:100px;text-align:left','text'=>'申请人'),
    array('key'=>'apply_id','type'=>'href','style'=>'width:80px;text-align:left;','text'=>'操作','href_text'=>'checkRowEditAction'),
);
$headerArray = ['button_config' => $buttonArray];
$searchArray = ['search_config' => $form_array];
$tableArray = ['column_config' => $array,"attr"=>["data-config"=>'{fixedColumns: { leftColumns: 1, rightColumns: 1 }}']];
$this->showIndexViewWithNewUI($_data_, $headerArray, $searchArray, $tableArray);
?>
<script type="text/javascript">
    $(function(){
        $('#DataTables_Table_0 tbody td:first-child').html('');
    });
    

    $(document).ready(function(){
        //全选、反选
        if($('.printInput:enabled').length==0) {
            $(".checkAll").attr("disabled", true);
        }

        $('.checkAll').change(function(){
            if($(this).is(':checked')){
                /*if($('.printInput').length==$('.printInput').filter(':not(:checked)').length){    // 复选框长度和没选中的个数一样，全选

                    $('.printInput:enabled').prop('checked',true);
                }else{     // 如果有选中个数，反选
                    $('.printInput:enabled').each(function(){
                        $(this).prop('checked',$(this).is(':checked')?false:true);
                    });
                }*/
                $('.printInput:enabled').prop('checked',true);
            }else {
                $('.printInput').prop('checked',false);    // 如控制键取消选中，剩余的checkbox也取消选中
            }

        });

        $('.printInput:enabled').change(function(){
            if($('.printInput').length>$('.printInput:checked').length && $('.checkAll').is(':checked')){
                $('.checkAll').prop('checked', false);
            }else if($('.printInput').length==$('.printInput:checked').length && !$('.checkAll').is(':checked')){
                $('.checkAll').prop('checked', true);
            }
        });


        //打印
        $("#printButton").click(function(){
            if($(".printInput:checked").length<=0) {
                inc.vueAlert('请勾选需要打印的付款编号');
                //layer.alert('请勾选要打印的付款项');
                return ;
            }

            var arr = [];
            $(".printInput:enabled").each(function () {
                if($(this).is(':checked'))
                    arr.push($(this).attr("data-id"));
            });
            
            var pageSize=$(".main-content_footer .filter-option").html();
            location.href = "/<?php echo $this->getId(); ?>/print?search="+arr+"&pageSize="+pageSize;

        })
    })
</script>