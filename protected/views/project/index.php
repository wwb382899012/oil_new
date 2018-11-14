<?php
/**
 * Created by youyi000.
 * DateTime: 2016/11/7 15:15
 * Describe：
 */
function checkRowEditAction($row, $self) {
    $links = array();
    if ($self->getId() == ConstantMap::MODUAL_PROJECT_ID) {
        /*if ($self->isShowAllLink == 1) {
        $links[] = '<a href="/' . $self->getId() . '/all?t=1&id=' . $row["project_id"] . '" target="_blank" title="信息全览">全览</a>';
    }*/

        if (!empty($row['project_id'])) {
            $links[] = '<a href="/' . $self->getId() . '/detail?id=' . $row["project_id"] . '" title="查看详情">详情</a>';
        }

        if ($self->checkIsCanEdit($row["status"])) {
            $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["project_id"] . '" title="修改">修改</a>';
            $links[] = '<a title="删除" onclick="del(' . $row["project_id"] . ')">删除</a>';
        }
    } elseif ($self->getId() == ConstantMap::MODUAL_SUB_CONTRACT_ID) {
        if (ProjectService::checkIsCanAddSubContract($row['project_id'])) {
             $links[] = '<a href="/' . $self->getId() . '/add?id=' . $row["project_id"] . '&type=' . ConstantMap::CONTRACT_CATEGORY_SUB_BUY . '" title="增加采购">增加采购</a>';
             $links[] = '<a href="/' . $self->getId() . '/add?id=' . $row["project_id"] . '&type=' . ConstantMap::CONTRACT_CATEGORY_SUB_SALE . '" title="增加销售">增加销售</a>';
        }
    }

    $s = Utility::isNotEmpty($links) ? implode("&nbsp;|&nbsp;", $links) : '';

    return $s;
}


//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'input_array' => array(
//        array('type' => 'text', 'key' => 'a.project_id', 'text' => '项目ID'),

        array('type' => 'text', 'key' => 'a.project_code', 'text' => '项目编号'),
        array('type' => 'select', 'key' => 'project_type', 'map_name' => 'project_detail_type', 'text' => '项目类型'),
        array('type' => 'text', 'key' => 'd.name', 'text' => '交易主体'),
        array('type' => 'select', 'key' => 'a.status', 'map_name' => 'project_status', 'text' => '状态&emsp;'),

        array('type' => 'text', 'key' => 'b.name', 'text' => '项目负责人'),
        array('type' => 'text', 'key' => 'up.name*', 'text' => '上游合作方'),
        array('type' => 'text', 'key' => 'dp.name*', 'text' => '下游合作方'),
        array('type' => 'select', 'key' => 'a.status', 'map_name' => 'project_status', 'text' => '状态&emsp;&emsp;&emsp;'),
        array('type' => 'datetime', 'id'=>'createStartTime', 'key'=>'a.create_time>','text'=>'申请时间&emsp;'),
        array('type' => 'datetime', 'id'=>'createEndTime', 'key'=>'a.create_time<','text'=>'到&emsp;&emsp;&emsp;&emsp;'),
        array('type'=>'text', 'key' => 'c.goods_name*', 'text' => '品名&emsp;&emsp;&emsp;'),
    )
);
if ($this->isCanAdd == 1 && $this->getId() == ConstantMap::MODUAL_PROJECT_ID) {
    $form_array["buttonArray"][] = array('text' => '添加', 'buttonId' => 'addButton');
}
if ($this->getId() == ConstantMap::MODUAL_SUB_CONTRACT_ID) {
    $form_array["buttonArray"][] = array('text' => '返回', 'buttonId' => 'backButton', 'class' => 'btn btn-default btn-sm');
}

//列表显示
$array = array(
    array('key' => 'project_id', 'type' => 'href', 'style' => 'width:120px;text-align:center;', 'text' => '操作', 'href_text' => 'checkRowEditAction'),
//    array('key' => 'project_id', 'type' => '', 'style' => 'width:140px;text-align:center', 'text' => '项目ID'),
    array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '项目编号', 'href_text'=>'<a target="_blank" id="t_{1}" title="{2}" href="/project/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'up_partner_id,up_partner_name', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '上游合作方', 'href_text'=>'<a id="t_{1}" target="_blank" title="{2}" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'corporation_id,corp_name', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '交易主体', 'href_text'=>'<a id="t_{1}" target="_blank" title="{2}" href="/corporation/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'down_partner_id,down_partner_name', 'type' => 'href', 'style' => 'width:140px;text-align:center', 'text' => '下游合作方', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/partner/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'goods_name', 'type' => '', 'map_name' => '', 'style' => 'width:140px;text-align:left', 'text' => '品名'),
    array('key' => 'project_type_desc', 'type' => '', 'map_name' => '', 'style' => 'width:140px;text-align:center', 'text' => '项目类型'),
    array('key' => 'name', 'type' => '', 'style' => 'width:80px;text-align:center', 'text' => '项目负责人'),
    array('key' => 'creater_name', 'type' => '', 'style' => 'width:80px;text-align:center', 'text' => '创建人'),
    array('key' => 'status', 'type' => 'map_val', 'map_name' => 'project_status', 'style' => 'width:80px;text-align:center', 'text' => '状态'),
    array('key' => 'create_time', 'type' => '', 'style' => 'width:140px;text-align:center', 'text' => '申请时间'),
    array('key' => 'status_time', 'type' => '', 'style' => 'width:140px;text-align:center', 'text' => '状态时间'),
);


$this->loadForm($form_array, $_data_);
$this->show_table($array, $_data_[data], "", "min-width:1650px;", "table-bordered table-layout");
?>
<script>
	$(function () {
		$("#addButton").click(function () {
			location.href = "/<?php echo $this->getId() ?>/add/";
		});
		$("#backButton").click(function () {
            location.href = '/businessConfirm/';
		})
	});

	function del(project_id) {
		layer.confirm("您确定要删除当前项目信息吗，该操作不可逆？", {
			icon: 3,
			'title': '提示'
		}, function (index) {
			$.ajax({
				type: 'GET',
				url: '/<?php echo $this->getId() ?>/del',
				data: {id: project_id},
				dataType: "json",
				success: function (json) {
					if (json.state == 0) {
						layer.msg(json.data, {icon: 6, time: 1000}, function () {
							location.reload();
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
		})
	}
</script>
