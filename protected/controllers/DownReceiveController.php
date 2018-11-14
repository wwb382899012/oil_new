<?php

/**
 * Created by youyi000.
 * DateTime: 2016/7/4 11:20
 * Describe：
 */
class DownReceiveController extends ReceiveController
{
    public function pageInit()
    {
        $this->filterActions= "getFile";
        $this->rightCode    = "downReceive";
        $this->type         = 2;
        $this->attachmentType=Attachment::C_DOWNRECEIVE;
    }
}