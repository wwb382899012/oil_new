<style>
    /* .datetimepicker{
        width:332px;
        left:unset !important;
        right:40px;
    }
    .datetimepicker-dropdown-bottom-right:before,.datetimepicker-dropdown-bottom-right:after{
        left:unset!important;
        right:20px;
    } */
</style>
<?php
/**
 * Created by youyi000.
 * DateTime: 2016/11/7 15:15
 * Describe：
 */
function checkRowEditAction($row, $self) {
    $links = array();
    if ($self->getId() == ConstantMap::MODUAL_PROJECT_ID) {
        if (!empty($row['project_id'])) {
            $links[] = '<a href="/' . $self->getId() . '/detail?id=' . $row["project_id"] . '" title="查看详情">详情</a>';
        }

        if ($self->checkIsCanEdit($row["status"])) {
            $links[] = '<a href="/' . $self->getId() . '/edit?id=' . $row["project_id"] . '" title="修改">修改</a>';
            $links[] = '<a title="删除" onclick="del(' . $row["project_id"] . ')" href="#">删除</a>';
        }
    } elseif ($self->getId() == ConstantMap::MODUAL_SUB_CONTRACT_ID) {
        if (ProjectService::checkIsCanAddSubContract($row['project_id'])) {
             $links[] = '<a href="/' . $self->getId() . '/add?id=' . $row["project_id"] . '&type=' . ConstantMap::CONTRACT_CATEGORY_SUB_BUY . '" title="增加采购">增加采购</a>';
             $links[] = '<a href="/' . $self->getId() . '/add?id=' . $row["project_id"] . '&type=' . ConstantMap::CONTRACT_CATEGORY_SUB_SALE . '" title="增加销售">增加销售</a>';
        }
    }

    $s = Utility::isNotEmpty($links) ? implode("&nbsp;&nbsp;", $links) : '';

    return $s;
}


//查询区域
$form_array = array(
    'form_url' => '/' . $this->getId() . '/',
    'items' => array(
        array('type' => 'text', 'key' => 'a.project_code', 'text' => '项目编号'),
        array('type' => 'text', 'key' => 'd.name', 'text' => '交易主体'),
        array('type' => 'text', 'key' => 'up.name*', 'text' => '上游合作方'),
        array('type' => 'text', 'key' => 'dp.name*', 'text' => '下游合作方'),
        array('type' => 'select', 'key' => 'a.status', 'map_name' => 'project_status', 'text' => '状态'),
        array('type' => 'datetime', 'id'=>'createStartTime', 'key'=>'a.create_time>','text'=>'申请开始时间'),
        array('type' => 'datetime', 'id'=>'createEndTime', 'key'=>'a.create_time<','text'=>'申请结束时间'),
        array('type' => 'select', 'key' => 'project_type', 'map_name' => 'project_detail_type', 'text' => '项目类型'),
        array('type' => 'managerUser', 'key' => 'a.manager_user_id', 'text' => '项目负责人'),
        array('type'=>'text', 'key' => 'c.goods_name*', 'text' => '品名'),
    )
);
$buttonArray = array();
if ($this->isCanAdd == 1 && $this->getId() == ConstantMap::MODUAL_PROJECT_ID) {
    $addBtn = array(
            'text' => '发起项目',
            'attr' => [
                'id' => 'addButton',
                'onclick' => "location.href='/".$this->getId()."/add'",
            ]
    );
    array_push($buttonArray, $addBtn);
}
if ($this->getId() == ConstantMap::MODUAL_SUB_CONTRACT_ID) {
    $is_show_back_bread = '/businessConfirm';
    /*$backBtn = array(
            'text' => '返回',
            'attr' => [
                'id' => 'backButton',
                'onclick' => "location.href='/businessConfirm'"
            ],
    );
    array_push($buttonArray, $backBtn);*/
}

function getRowGoodsName($row,$self,$key)
{
    $arr=explode("|",$row[$key]);
    return join(" | ",$arr);
}

//列表显示
$array = array(
    array('key' => 'project_id,project_code', 'type' => 'href', 'style' => 'width:140px;text-align:left', 'text' => '项目编号', 'href_text'=>'<a target="_blank" id="t_{1}" title="{2}" href="/project/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'up_partner_id,up_partner_name', 'type' => 'href', 'style' => 'width:200px;text-align:left', 'text' => '上游合作方', 'href_text'=>'<a id="t_{1}" target="_blank" title="{2}" href="/partner/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'corporation_id,corp_name', 'type' => 'href', 'style' => 'width:200px;text-align:left', 'text' => '交易主体', 'href_text'=>'<a id="t_{1}" target="_blank" title="{2}" href="/corporation/detail/?id={1}&t=1" >{2}</a>'),
    array('key' => 'down_partner_id,down_partner_name', 'type' => 'href', 'style' => 'width:200px;text-align:left', 'text' => '下游合作方', 'href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/partner/detail/?id={1}&t=1">{2}</a>'),
    array('key' => 'project_type_desc', 'type' => '', 'map_name' => '', 'style' => 'width:140px;text-align:left', 'text' => '项目类型'),
    array('key' => 'goods_name', 'type' => 'href','style' => 'width:100px;text-align:center;', 'text' => '品名','href_text'=>'getRowGoodsName','params'=>'goods_name'),
    array('key' => 'name', 'type' => '', 'style' => 'width:80px;text-align:left', 'text' => '项目负责人'),
    array('key' => 'creater_name', 'type' => '', 'style' => 'width:60px;text-align:left', 'text' => '创建人'),
    array('key' => 'status', 'type' => 'map_val', 'map_name' => 'project_status', 'style' => 'width:60px;text-align:left', 'text' => '状态'),
    array('key' => 'create_time', 'type' => '', 'style' => 'width:140px;text-align:left', 'text' => '申请时间'),
    array('key' => 'status_time', 'type' => '', 'style' => 'width:140px;text-align:left', 'text' => '状态时间'),
    array('key' => 'project_id', 'type' => 'href', 'style' => 'text-align:left;', 'text' => '操作', 'href_text' => 'checkRowEditAction'),
);

$headerArray = ['button_config' => $buttonArray, 'is_show_back_bread' => $is_show_back_bread];
$searchArray = ['search_config' => $form_array, 'search_lines' => 2];
$tableArray = ['column_config' => $array];
$this->showIndexViewWithNewUI($_data_, $headerArray, $searchArray, $tableArray);
?>
<script>
    function del(project_id) {
    	inc.vueConfirm({
            content: '您确定要删除当前项目信息吗，该操作不可逆？',
            onConfirm: function () {
				$.ajax({
					type: 'GET',
					url: '/<?php echo $this->getId() ?>/del',
					data: {id: project_id},
					dataType: "json",
					success: function (json) {
						if (json.state == 0) {
                            inc.vueMessage('删除成功');
							location.reload();
						}
						else {
							inc.vueAlert({
                                content: json.data
                            });
						}
					},
					error: function (data) {
						inc.vueAlert({
							content: "操作失败！" + data.responseText
						})
					}
				});
			}
        })
	}
</script>
