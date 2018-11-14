<?php

/**
 * 附件上传模块
 *  调用方式：
 *      $this->renderPartial("/layouts/attachmentsEditMulti",
 *              array("baseId"=>1,"data"=>$data,"mapKey"=>$mapKey,"attachments"=>$attachments));
 *
 * @params $baseId
 * @params $data
 * @params $mapKey
 * @params $attachments
 * @params $idFieldName="id"
 * @params $controller=""
 *
 *
 */

//$mapKey    附件map文件中的key值
//$attachments          附件数组 格式：[
//                                      "1"=>[["id"=>12,"name"=>"附件1"],["id"=>12,"name"=>"附件1"]],
//                                      "2"=>[["id"=>12,"name"=>"附件1"]]
//                                      ]
//$idFieldName          附件Id字段名，默认id
//$baseId               ***关联的相关信息，必须
//会通过该方法$this->checkIsCanEdit($data["status"]) 判断是否可以操作

$moduleId= "attach_".time().Utility::getRandomKey();

?>
<table id="attachments_<?php echo $moduleId ?>" class="table table-striped table-hover ">
    <tbody>
    <tr>
        <th style="width: 40px" class="text-green">&nbsp;</th>
        <th style="width: 53px">序号</th>
        <th>文件名</th>
        <th style="width: 140px">
        </th>
    </tr>
    <?php
    if(empty($baseId))
    {
        echo "<p class='alert alert-danger alert-dismissible'>文件上传模块无效，缺少关联信息参数！！！</p>";
    }
    $index=1;
    $idFieldName=empty($idFieldName)?"id":$idFieldName;
    $controller=empty($controller)?$this->getId():$controller;
    if(is_array($this->map[$mapKey]))
    {
        $isCanDel= ($this->checkIsCanEdit($data["status"]) && UserService::checkActionRight($this->rightCode, "delFile"));

        foreach ($this->map[$mapKey] as $k => $v)
        {
            ?>
            <tr>
                <td style="font-size: 18px;">
                    <?php if (count($attachments[$k])>0)
                        echo '<span class="glyphicon glyphicon-ok text-green"></span>';
                    else
                        echo '<span class="glyphicon glyphicon glyphicon-remove text-red"></span>'; ?>
                </td>
                <td><?php echo $index ?>.</td>
                <td class="file-name" data-type="<?php echo $k ?>" data-name="<?php echo $v["name"] ?>"
                    data-maxSize="<?php echo $v["maxSize"] ?>" data-multi="<?php echo $v["multi"] ?>" data-fileType="<?php echo $v["fileType"] ?>">

                    <?php
                    $requiredIcon = "";
                    if(!empty($v['required'])) {
                        $requiredIcon = " <span class='text-red fa fa-asterisk'></span>";
                    }
                    echo "<span class=\"form-control-static\"><span class='file-title '>".$v["name"].$requiredIcon." </span>";
                    if(!empty($v["template"]))
                        echo "&emsp;&emsp;<a href='".$v["template"]."' target='_blank'>模板下载</a>";
                    echo  "</span><div class=\"file-list\">";
                    if(is_array($attachments[$k]) && count($attachments[$k])>0)
                    {
                        foreach ($attachments[$k] as $f)
                        {
                            echo '<br/><span id="i_'.$f[$idFieldName].'" class="form-control-static file">';
                            echo "<a  href='/".$this->getId()."/getFile/?id=".$f[$idFieldName]."&fileName=" . $f['name']."'  target='_blank' class='btn btn-primary btn-xs'>". $f['name']."</a>&emsp;&emsp;";
                            if($isCanDel)
                                echo '<a class="btn btn-danger btn-xs del-btn" onclick="'.$moduleId.'.del('.$f[$idFieldName].')">删除</a>';
                            echo '</span><br/>';
                        }
                    }
                    echo "</div>";
                    ?>
                </td>
                <td>
                    <?php if ($this->checkIsCanEdit($data["status"]))
                    { ?>
                        <span class="btn btn-success btn-xs fileinput-button">
                                        <?php
                                        if(!empty($v["multi"]) && $v["multi"]==1)
                                        {
                                            if (count($attachments[$k])>0)
                                                echo '<span class="btn-text">继续上传</span>';
                                            else
                                                echo '<span class="btn-text">上传</span>';
                                        }
                                        else
                                        {
                                            if (count($attachments[$k])>0)
                                                echo '<span class="btn-text">重新上传</span>';
                                            else
                                                echo '<span class="btn-text">上传</span>';
                                        }
                                        ?>
                            <input class="file-upload" type="file"/>
                     </span>
                    <?php } ?>

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

    var <?php echo $moduleId ?>;
    $(function()
    {
        <?php echo $moduleId ?>=new AttachModelNewUI('attachments_<?php echo $moduleId ?>','<?php echo $baseId ?>','<?php echo $controller ?>');

    });

</script>