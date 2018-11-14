<table id="attachments" class="table table-striped table-hover ">
    <tbody>
    <tr>
        <th style="width: 40px" class="text-green">&nbsp;</th>
        <th style="width: 50px">序号</th>
        <th>文件名</th>
        <th style="width: 140px">
        </th>
    </tr>
    <?php
    $index=1;
    $idFieldName=empty($idFieldName)?"id":$idFieldName;
    $controllerName=empty($controllerName)?$this->getId():$controllerName;
    if(is_array($this->map[$mapKey]))
    {

        foreach ($this->map[$mapKey] as $k => $v)
        {
            ?>
            <tr data-file-id="<?php echo $attachments[$k][$idFieldName] ?>">
                <td style="font-size: 18px;">
                    <?php if (!empty($attachments[$k]["file_url"]))
                        echo '<span class="glyphicon glyphicon-ok text-green"></span>';
                    else
                        echo '<span class="glyphicon glyphicon glyphicon-remove text-red"></span>'; ?>
                </td>
                <td><?php echo $index ?>.</td>
                <td class="file-name" type="<?php echo $k ?>" name="<?php echo $v["name"] ?>"
                    maxSize="<?php echo $v["maxSize"] ?>" fileType="<?php echo $v["fileType"] ?>">
                    <?php if (!empty($attachments[$k]["file_url"]))
                        echo "<a href='/" . $controllerName . "/getFile/?id=" . $attachments[$k][$idFieldName] . "&fileName=".$v['name']."'  target='_blank' class='btn btn-primary btn-xs'>" . $v["name"] . "</a>";
                    else
                        echo $v["name"]; ?>
                </td>

                <td>
                    <?php if ($this->checkIsCanEdit($data["status"]))
                    { ?>
                        <span class="btn btn-success btn-xs fileinput-button">
                                        <?php
                                        if (!empty($attachments[$k]["file_url"]))
                                            echo '<span class="btn-text">重新上传</span>';
                                        else
                                            echo '<span class="btn-text">上传</span>';
                                        ?>
                            <input class="file-upload" type="file"/>
                                    </span>
                    <?php } ?>
                    <?php
                    if (UserService::checkActionRight($this->rightCode, "delFile"))
                    {
                        $cccc = "hide1";
                        if (!empty($attachments[$k]["file_url"]))
                        {
                            $cccc = "";
                        }
                        echo '<a class="btn btn-danger btn-xs del-btn ' . $cccc . '" onclick="delFile(this)">删除</a>';
                    }
                    ?>
                </td>
            </tr>
            <?php
            $index++;
        }
    }
    ?>

    </tbody>
</table>
<script>
    $(function(){

        $(".file-upload").each(function () {
            var self=$(this);
            var btnText=self.prev();
            var tr=self.parent().parent().parent();
            var delBtn=self.parent().parent().find(".del-btn");
            var fileNameTd=tr.find("td.file-name");
            var maxSize=parseInt(fileNameTd.attr("maxSize"))*1024*1024;
            var permitFileType=fileNameTd.attr("fileType");
            self.fileupload({
                url: '/<?php echo $controllerName ?>/saveFile/',
                dataType: 'json',
                autoUpload: true,
                add: function (e, data) {
                    if (!inc.checkFileType(data.files[0].name, permitFileType)) {
                        layer.alert("只能上传指定类型的文件："+permitFileType, {icon: 5});
                        return;
                    }
                    if(data.files[0].size>maxSize)
                    {
                        layer.alert("文件大小超出最大限制："+fileNameTd.attr("maxSize")+"M", {icon: 5});
                        return;
                    }
                    btnText.html("正在上传文件。。。");

                    data.formData={id:<?php echo $data['project_id'] ?>,type:fileNameTd.attr("type")};
                    data.submit();
                },
                done: function (e, data) {
                    if (data.result.state == 0) {
                        fileNameTd.prev().prev().html('<span class="glyphicon glyphicon-ok text-green"></span>');
                        fileNameTd.html($("<a target='_blank' class='btn btn-primary btn-xs'></a>").attr("href","/<?php echo $controllerName ?>/getFile/?id="+data.result.data+"&fileName="+fileNameTd.attr("name")).html(fileNameTd.attr("name")));
                        btnText.html("重新上传");
                        tr.attr("data-file-id",data.result.data);
                        delBtn.show();
                    }
                    else {
                        layer.alert(data.result.data, {icon: 5});
                        btnText.html("上传");
                    }
                },
                fail:function(){
                    layer.alert("上传出错，请稍后重试！", {icon: 5});
                    btnText.html("上传");
                }
            });
        });
    });

    function delFile(target)
    {
        layer.confirm("您确定删除当前已经上传的文件吗，该操作不可逆？", {icon: 3, title: "提示"}, function(index){
            var tr=$(target).parent().parent();
            var id=tr.attr("data-file-id");
            var formData = "id="+id;
            $.ajax({
                type: 'POST',
                url: '/<?php echo $controllerName ?>/delFile',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        inc.showNotice("操作成功");
                        var btnText=tr.find(".btn-text");
                        var fileNameTd=tr.find("td.file-name");
                        fileNameTd.prev().prev().html('<span class="glyphicon glyphicon glyphicon-remove text-red"></span>');
                        fileNameTd.html(fileNameTd.attr("name"));
                        btnText.html("上传");
                        $(target).hide();
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
        });

    }
</script>