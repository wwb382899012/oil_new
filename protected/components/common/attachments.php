<table id="attachments" class="table table-striped table-hover ">
    <tbody>
    <tr>
        <th style="width: 40px" class="text-green">&nbsp;</th>
        <th style="width: 50px">序号</th>
        <th>文件名</th>
        <th>合同编号</th>
    </tr>
    <?php
    $index=1;
    if(is_array($this->map[$mapKey]))
    {
        $idFieldName=empty($idFieldName)?"id":$idFieldName;
        $controllerName=empty($controllerName)?$this->getId():$controllerName;
        foreach ($this->map[$mapKey] as $k => $v)
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
                    <?php 
                    if (!empty($attachments[$k]["file_url"]))
                        echo "<a href='/".$controllerName."/getFile/?id=" . $attachments[$k][$idFieldName] . "&fileName=".$v['name']."'  target='_blank' class='btn btn-primary btn-xs'>" . $v["name"] . "</a>";
                    else
                        echo $v["name"]; 
                    
                    if($k==1 || $k==2)
                            echo '<span class="text-red fa fa-asterisk"></span>';

                    if (($k==1 && $data["check1"]=="on") || ($k==2 && $data["check2"]=="on"))
                        echo '<span class="text-red">&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;定制化</span>';
                    ?>

                </td>
                <td>
                    <?php
                    if(!empty($attachments[$k]["code"])) {
                        echo $attachments[$k]["code"];
                    } else {
                        echo "无";
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