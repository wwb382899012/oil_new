<?php
/**
 * Created by PhpStorm.
 * User: youyi000
 * Date: 2015/12/10
 * Time: 9:39
 * Describe：
 */

function checkRowEditAction($row)
{
    return
        '<a href="/role/edit?id='.$row["role_id"].'" title="修改"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>
        &nbsp;<a href="/role/right?id='.$row["role_id"].'" title="授权"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span></a>
        &nbsp;<a id="i_'. $row["role_id"] .'" onclick="del('. $row["role_id"] .')" title="删除"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>';
}

//查询区域
$form_array = array('form_url'=>'/role/',
    'input_array'=>array(
        array('type'=>'text','key'=>'role_name','text'=>'角色名'),
    ),
    'buttonArray'=>array(
        array('text'=>'添加','buttonId'=>'addButton'),
    ),
);

//列表显示
$array =array(
    array('key'=>'role_id','type'=>'href','style'=>'width:100px;text-align:center;','text'=>'操作','href_text'=>'checkRowEditAction'),
    array('key'=>'role_id','type'=>'','style'=>'width:60px;text-align:center','text'=>'编号'),
    array('key'=>'role_id,role_name','type'=>'href','style'=>'text-align:left;','text'=>'角色名','href_text'=>'<a id="t_{1}" title="查看详细" href="/role/detail/?id={1}" >{2}</a>'),
    array('key'=>'status','type'=>'map_val','text'=>'状态','map_name'=>'user_status','style'=>'width:80px;text-align:center'),
    array('key'=>'update_time','type'=>'','text'=>'更新时间','style'=>'width:150px;text-align:center'),
);



$this->loadForm($form_array,$_GET);
$this->show_table($array,$_data_[data],"","min-width:900px;");


?>
<script>
    $(function(){
        $("#addButton").click(function () {
            location.href="/role/add/";
        });
        $(".content-list > div >table >tbody >tr").each(function(){
            var s= parseInt($(this).find("td[name=status]").attr("value"));
            if(s!=1){
                $(this).addClass("text-muted");
                $(this).find("a").addClass("text-muted");
            }
        });
    });
    function del(id){
        if(confirm("您确定要删除当前信息，该操作不可逆？")) {
            var formData = "id=" + id;
            $.ajax({
                type: 'POST',
                url: '/role/del',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        inc.showNotice("操作成功");
                        $("#i_"+id).parent().parent().remove();
                    }
                    else {
                        alertModel(json.data);
                    }
                },
                error: function (data) {
                    alertModel("操作失败！" + data.responseText);
                }
            });
        }
    }

</script>
