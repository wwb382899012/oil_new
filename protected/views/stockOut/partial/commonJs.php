<script>
    <?php if(isset($isShowBackButton) && $isShowBackButton):?>
    function back() {
        <?php if(isset($_GET["url"]) && !empty($_GET["url"])):?>
        location.href = "<?php echo $this->getBackPageUrl();?>";
        <?php else:?>
        history.back();
        <?php endif;?>
    }
    <?php endif;?>

    function editOut(id) {
        location.href = "/stockOut/edit?out_order_id=" + id;

        event.stopPropagation();
    }

    function submitOut(id) {
        layer.confirm("您确定要提交当前出库单信息吗，该操作不可逆？", {icon: 3, 'title': '提示'}, function (index) {
            var formData = "id=" + id;
            $.ajax({
                type: "POST",
                url: "/stockOut/submit",
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        layer.msg(json.data, {icon: 6, time: 1000}, function () {
                            location.href = "/<?php echo $this->getId() ?>/";
                        });
                    } else {
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error: function (data) {
                    layer.alert("操作失败！" + data.responseText, {icon: 5});
                }
            });
            layer.close(index);
        });

        event.stopPropagation();
    }
</script>