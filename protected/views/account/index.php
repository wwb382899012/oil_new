<?php
$form_array=array(
    'form_url'=>'/account/',
    'input_array'=>array(
        array('key'=>'b.name*','type'=>'text','text'=>'公司名称'),
    ),
    'buttonArray'=>array(
        array('text'=>'添加','buttonId'=>'addButton'),
    ),
);


$array=array(
    array('key'=>'account_id','type'=>'href','style'=>'width:100px;text-align:center;','text'=>'操作','href_text'=>'operation'),
    array('key'=>'account_id','type'=>'','style'=>'width:80px;text-align:center','text'=>'账户编号'),
    array('key'=>'account_no','type'=>'','text'=>'银行账号','style'=>'width:180px;text-align:left'),
    array('key'=>'bank_name','type'=>'','text'=>'开户行','style'=>'text-align:left'),
    array('key'=>'corporation_id,corporation_name','type'=>'href','style'=>'width:300px;text-align:left;','text'=>'公司名称','href_text'=>'<a id="t_{1}" title="查看详细" href="/corporation/detail/?id={1}&url=/corporation/" >{2}</a>'),
    array('key'=>'status','type'=>'map_val','text'=>'账户状态','map_name'=>'account_status','style'=>'width:80px;text-align:center'),
    // array('key'=>'create_time','type'=>'','text'=>'创建时间','style'=>'width:150px;text-align:center'),
);

function operation($row){
    return '<a href="/account/edit/?id='.$row["account_id"].'" title="修改">修改</a>&nbsp;|&nbsp;
    <a href="/account/detail/?id='.$row["account_id"].'" title="查看详情">详情</a>';
    //<a id="i_'.$row["account_id"].'" onclick="del('.$row["account_id"].')" title="删除">删除</a>
}


$this->loadForm($form_array,$_GET);
$this->show_table($array,$_data_[data],"","min-width:1050px;");
?>

<script>
    $(function () {
        $("#addButton").click(function () {
            location.href="/account/add/";
        });
    });

    function del(id) {
        if(confirm("您确定要删除该条记录，此操作不可逆？")){
            var formData="id="+id;
            $.ajax({
                type:"POST",
                url:"/account/del",
                data:formData,
                dataType:"json",
                success:function (json) {
                    if(json.state==0){
                        inc.showNotice("删除成功！");
                        $("#i_"+id).parent().parent().remove();
                    }else{
                        alertModel(json.data);
                    }
                },
                error:function (data) {
                    alertModel("删除失败！"+data.responseText);
                }
            });
        }
    }
</script>
