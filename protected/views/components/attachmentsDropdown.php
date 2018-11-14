<label class="col-sm-2 control-label"><?php echo $this->map[$map_key][$attach_type]['name'] ?></label>
<div class="<?php echo $div_class ?? "col-sm-4"; ?>">
    <?php
    $attachments = AttachmentService::getAttachments($attachment_type,$id);
    ?>
    <?php if(empty($attachments[$attach_type])): ?>
        <p class='form-control-static'>
        无
        </p>
    <?php else:?>
        <?php if(1 == count($attachments[$attach_type])): ?>
            <p class='form-control-static'>
                <a href='/<?php echo $controller;?>/getFile/?id=<?php echo $attachments[$attach_type][0]["id"]; ?>&fileName=<?php echo $attachments[$attach_type][0]['name'];?>'
                   target='_blank' class='btn btn-primary btn-xs'>点击查看</a>
            </p>
        <?php else:?>
            <div class="btn-group " role="group">
                <button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    点击查看
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <?php foreach($attachments[$attach_type] as $item):?>
                        <li><a href='/<?php echo $controller;?>/getFile/?id=<?php echo $item["id"] ;?>&fileName=<?php echo $item['name'];?>' target='_blank' ><u style="color: #337ab7;" ><?php echo $item['name'];?></u></a></li>
                    <?php endforeach;?>
                </ul>
            </div>
        <?php endif;?>
    <?php endif;?>
</div>