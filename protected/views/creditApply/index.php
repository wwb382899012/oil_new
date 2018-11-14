<?php
/**
 * Created by youyi000.
 * DateTime: 2017/4/11 11:08
 * Describe：
 */
function checkRowEditAction($row,$self)
{
    $links=array();
    if($row["apply_status"]!=-2)
        $links[]='<a href="/'.$self->getId().'/detail?id='.$row["project_id"].'" title="查看额度申请详情">查看</a>';
    if($self->isCanApply($row["status"],$row["apply_status"]))
    {
        $links[]='<a href="/'.$self->getId().'/edit?id='.$row["project_id"].'" title="申请额度">申请</a>';
    }
    if($self->isCanTrash($row["status"],$row["apply_status"]))
        $links[]='<a id="trash_'.$row["project_id"].'" onclick="trash('.$row["project_id"].')"  title="作废额度申请">作废</a>';

    $s=implode("&nbsp;|&nbsp;",$links);
    return $s;
}


//查询区域
$form_array = array('form_url'=>'/'.$this->getId().'/',
    'input_array'=>array(
        array('type'=>'text','key'=>'a.project_id','text'=>'项目编号'),
        array('type'=>'text','key'=>'a.project_name*','text'=>'项目名称'),
        array('type'=>'managerUser','key'=>'a.manager_user_id','text'=>'项目经理'),
        array('type'=>'select','key'=>'a.status','map_name'=>'project_status','text'=>'项目状态'),
        array('type'=>'text','key'=>'u.name*','text'=>'上游合作方'),
        array('type'=>'text','key'=>'d.name*','text'=>'下游合作方'),
    ),
    'buttonArray'=>array(
        //array('text'=>'添加','buttonId'=>'addButton'),
    ),
);


//列表显示
$array =array(
    array('key'=>'project_id','type'=>'href','style'=>'width:100px;text-align:center;','text'=>'操作','href_text'=>'checkRowEditAction'),
    array('key'=>'project_id','type'=>'','style'=>'width:100px;text-align:center','text'=>'项目编号'),
    array('key'=>'project_id,project_name','type'=>'href','style'=>'text-align:left;','text'=>'项目名称','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/project/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'up_partner_id,up_name','type'=>'href','style'=>'text-align:left;','text'=>'上游合作方','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'down_partner_id,down_name','type'=>'href','style'=>'text-align:left;','text'=>'下游合作方','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key'=>'status','type'=>'map_val','text'=>'项目状态','map_name'=>'project_status','style'=>'width:140px;text-align:left'),
    array('key'=>'partner_used_amount','type'=>'amountWan','text'=>'占用企业额度','style'=>'width:120px;text-align:right'),
    array('key'=>'user_used_amount','type'=>'amountWan','text'=>'占用业务员额度','style'=>'width:120px;text-align:right'),
    //array('key'=>'other_amount','type'=>'amountWan','text'=>'其他业务员额度','style'=>'width:90px;text-align:right'),
);



$this->loadForm($form_array,$_data_);
$this->show_table($array,$_data_[data],"","min-width:1050px;","table-bordered table-layout");
?>
<script>
    function trash(id) {
        if(confirm("您确定要作废当前项目的额度占用申请吗？此操作不可逆！")){
            var formData="id="+id;
            $.ajax({
                type:"POST",
                url:"/creditApply/trash",
                data:formData,
                dataType:"json",
                success:function (json) {
                    if(json.state==0){
                        inc.showNotice("操作成功！");
                        var dom=$("#trash_"+id);
                        dom.after("<a href=\"/creditApply/edit?id="+id+"\" title=\"申请额度\">申请</a>");
                        dom.parent().siblings("td[name=partner_used_amount],td[name=user_used_amount]").html("￥0.00万元");
                        dom.remove();
                    }else{
                        alert(json.data);
                    }
                },
                error:function (data) {
                    alert("操作失败："+data.responseText);
                }
            });
        }
    }
</script>
