<label class="col col-count-2 field flex-grid">
    <span class="line-h--text w-fixed"><?php echo $this->map[$map_key][$attach_type]['name']; ?>:</span>
    <?php $attachments = AttachmentService::getAttachments($attachment_type,$id);?>
    <?php if(empty($attachments[$attach_type])): ?>
        <span class="form-control-static line-h--text">无</span>
    <?php else:?>
        <div class="dropdown link-more common-dropdown">
            <a href="javascript: void 0" data-toggle="dropdown">
                点击查看 <i class="icon icon-xiala icon--shrink"></i>
            </a>
            <ul class="dropdown-menu" aria-labelledby="drop1">
                <?php foreach($attachments[$attach_type] as $item):?>
                    <li>
                        <a href='/<?php echo $controller;?>/getFile/?id=<?php echo $item["id"] ;?>&fileName=<?php echo $item['name'];?>' target='_blank' class="text-link">
                            <span><?php echo $item['name'];?></span>
                        </a>
                    </li>
                <?php endforeach;?>
            </ul>
        </div>
    <?php endif;?>
</label>