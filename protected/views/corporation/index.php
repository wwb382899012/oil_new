<?php
$form_array=array(
    'form_url'=>'/corporation/',
    'input_array'=>array(
        array('key'=>'name*','type'=>'text','text'=>'企业名称'),
    ),
    'buttonArray'=>array(
        array('text'=>'添加','buttonId'=>'addButton'),
    ),
);


$array=array(
    array('key'=>'corporation_id','type'=>'href','style'=>'width:100px;text-align:center;','text'=>'操作','href_text'=>'operation'),
    array('key'=>'corporation_id','type'=>'','style'=>'width:80px;text-align:center','text'=>'序号'),
    array('key'=>'corporation_id,name','type'=>'href','style'=>'text-align:left;','text'=>'企业名称','href_text'=>'<a id="t_{1}" title="{2}" href="/corporation/detail/?id={1}&url=/corporation/" >{2}</a>'),
    array('key'=>'code','type'=>'','text'=>'企业编码','style'=>'width:80px;text-align:center'),
    array('key'=>'credit_code','type'=>'','text'=>'信用代码','style'=>'width:180px;text-align:left'),
    array('key'=>'ownership','type'=>'map_val','text'=>'企业性质','map_name'=>'ownership','style'=>'width:80px;text-align:center'),
    array('key'=>'corporate','type'=>'','text'=>'法人代表','style'=>'width:90px;text-align:left'),
    array('key'=>'start_date','type'=>'','text'=>'成立日期','style'=>'width:120px;text-align:center'),
);

function operation($row){
    return '<a href="/corporation/edit/?id='.$row["corporation_id"].'" title="修改">修改</a>&nbsp;|&nbsp;
    <a id="i_'.$row["corporation_id"].'" onclick="del('.$row["corporation_id"].')" title="删除">删除</a>';
}


$this->loadForm($form_array,$_GET);
$this->show_table($array,$_data_[data],"","min-width:1050px;","table-bordered table-layout");
?>

<script>
    $(function () {
        $("#addButton").click(function () {
            location.href="/corporation/add/";
        });
    });

    function del(id) {
        if(confirm("您确定要删除该条记录，此操作不可逆？")){
            var formData="id="+id;
            $.ajax({
                type:"POST",
                url:"/corporation/del",
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
