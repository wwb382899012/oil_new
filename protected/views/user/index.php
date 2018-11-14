<?php
/**
 * Created by PhpStorm.
 * User: youyi000
 * Date: 2015/10/22
 * Time: 15:39
 * Describe：
 */

function checkRowEditAction($row)
{
    return
        '<a href="/user/edit?id='.$row["user_id"].'" title="修改"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>
        &nbsp;<a href="/user/right?id='.$row["user_id"].'" title="授权"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span></a>
        &nbsp;<a id="i_'. $row["user_id"] .'" onclick="del('. $row["user_id"] .')" title="删除"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>';
}

//查询区域
$form_array = array('form_url'=>'/user/',
    'input_array'=>array(
        array('type'=>'text','key'=>'a.user_name*','text'=>'用户名'),
        array('type'=>'text','key'=>'a.name*','text'=>'姓名'),
        array('type'=>'corpName','key'=>'corp_id','text'=>'交易主体'),
        array('type'=>'mainRole','key'=>'a.main_role_id','text'=>'主角色'),
        array('type'=>'mainRole','key'=>'role_id','text'=>'角色'),
    ),
    'buttonArray'=>array(
        array('text'=>'添加','buttonId'=>'addButton'),
    ),
);

//列表显示
$array =array(
    array('key'=>'user_id','type'=>'href','style'=>'width:100px;text-align:center;','text'=>'操作','href_text'=>'checkRowEditAction'),
    array('key'=>'user_id','type'=>'','style'=>'width:60px;text-align:center','text'=>'编号'),
    array('key'=>'user_id,user_name','type'=>'href','style'=>'text-align:left;','text'=>'用户名','href_text'=>'<a id="t_{1}" title="查看详细" href="/user/detail/?id={1}" >{2}</a>'),
    array('key'=>'name','type'=>'','style'=>'width:150px;text-align:left','text'=>'姓名'),
    array('key'=>'role_name','type'=>'','style'=>'width:150px;text-align:left','text'=>'主角色'),
    array('key'=>'status','type'=>'map_val','text'=>'状态','map_name'=>'user_status','style'=>'width:80px;text-align:center'),
    array('key'=>'login_time','type'=>'','text'=>'最后登陆时间','style'=>'width:150px;text-align:center'),
);



$this->loadForm($form_array,$_GET);
$this->show_table($array,$_data_[data],"","min-width:900px;");


?>
<script>
    $(function(){
        $("#addButton").click(function () {
            location.href="/user/add/";
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
                url: '/user/del',
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
