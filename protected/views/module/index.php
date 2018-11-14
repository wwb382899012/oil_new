<link href="/css/jquerytreetable/jquery.treetable.css" rel="stylesheet" type="text/css" />
<div class="box ">
    <div class="box-body">
        <div class="container-fluid">
            <div class="row" style="margin-bottom:10px">
                <div class="col-sm-6">
                    <div class="input-group">
                        <input id="key" type="text" class="form-control" placeholder="输入关键字查询">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button" onclick="search()">查询</button>
                        </span>
                    </div>
                </div>
                <div class="col-sm-4">
                    <button class="btn btn-success btn-sm" id="addButton" type="button"> 添加 </button>
                </div>
                <div class="col-sm-4">
                </div>
            </div>


        </div>
    </div>
</div>
<?php
//查询区域
$form_array = array('form_url'=>'/redemption/',
    'input_array'=>array(
        array('type'=>'text','key'=>'name*','text'=>'模块名称'),
        array('type'=>'text','key'=>'code','text'=>'权限码'),
        array('type'=>'space'),
        array('type'=>'select','key'=>'a.status','map_name'=>'redemption_status','text'=>'状态'),
        array('type'=>'select','key'=>'a.type','map_name'=>'redemption_type','text'=>'类别'),
        array('type'=>'select','key'=>'a.deal_status','map_name'=>'redemption_deal_status','text'=>'处理状态'),
    ),
    "buttonArray"=>array(
        array('text'=>'添加','buttonId'=>'addButton'),
        )

);
//$this->loadForm($form_array,$_GET);

function checkRowEditAction($row)
{
    return
        '<a href="/module/edit?id='.$row["id"].'" title="修改"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>'
        .'&emsp;<a id="i_'. $row["id"] .'" onclick="del('. $row["id"] .')" title="删除"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>';
}
?>
<div class="panel panel-default" style="min-width:900px;">
    <table class="table table-condensed table-hover table-bordered" id="dataTable">
                <tr data-tt-id="0">
                    <th style="width: 350px;">模块名</th>
                    <th style="text-align: center;width: 80px;">状态</th>
                    <th>操作</th>
                </tr>
<?php
foreach($data as $v)
{
    $cn="";
    if($v["status"]!=1)
        $cn="text-danger";
?>
    <tr class="item" data-tt-id="<?php echo $v["id"] ?>" data-tt-parent-id="<?php echo $v["parent_id"] ?>">
                    <td><?php echo $v["icon"] ?> <?php echo $v["name"] ?></td>
                    <td style="text-align: center;">
                        <span class="<?php echo $cn?>">
                            <?php echo $this->map["module_status"][$v["status"]] ?>
                        </span>
                    </td>
                    <td>
                        <a href="/module/detail?id=<?php echo $v["id"] ?>" title="查看详细"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></a>&emsp;
                        <?php echo checkRowEditAction($v); ?>
                    </td>
    </tr>
<?php
}
?>
</table>

</div>

<script src="/js/jquery.treetable.js"></script>

<script>
    $(function(){
        $("#addButton").click(function () {
            location.href="/module/add";
        });
        $("#dataTable").treetable({ expandable: true });
        $("#dataTable").treetable("expandAll");
    });

    function search(){
        var trs = $("#dataTable > tbody > tr.item");
        trs.each(function (index, row) {
            var found = false;
            var allCells = $(this).children('td').each(function () {
                var regExp = new RegExp($("#key").val(), 'i');
                if (regExp.test($(this).text())) {
                    found = true;
                    return false;
                }
            });
            if (found) $(this).show(); else $(this).hide();
        });
    }

    function del(id){
        if(confirm("您确定要删除当前信息，该操作不可逆？")) {
            var formData = "id=" + id;
            $.ajax({
                type: 'POST',
                url: '/module/del',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        inc.showNotice("操作成功");
                        $("#i_"+id).parent().parent().remove();
                    }
                    else {
						layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
					layer.alert("保存失败！" + data.responseText, {icon: 5});
                }
            });
        }
    }


</script>