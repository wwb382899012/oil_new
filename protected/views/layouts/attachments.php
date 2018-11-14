<table id="attachments" class="table table-striped table-hover ">
    <tbody>
    <tr>
        <th style="width: 40px" class="text-green">&nbsp;</th>
        <th style="width: 50px">序号</th>
        <th>文件名</th>
    </tr>
    <?php
    //$attachmentTypeKey 附件map文件中的key值
    //$attachments 附件数组
    //$idFieldName
    $index=1;
    if(is_array($this->map[$attachmentTypeKey]))
    {
        $idFieldName=empty($idFieldName)?"id":$idFieldName;
        $controller=empty($controller)?$this->getId():$controller;
        foreach ($this->map[$attachmentTypeKey] as $k => $v)
        {
            ?>
            <tr>
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
                        echo "<a href='/".$controller."/getFile/?id=" . $attachments[$k][$idFieldName] . "&fileName=".$v['name']."'  target='_blank' class='btn btn-primary btn-xs'>" . $v["name"] . "</a>";
                    else
                        echo $v["name"]; ?>
                </td>


            </tr>
            <?php
            $index++;
        }
    }
    ?>

    </tbody>
</table>