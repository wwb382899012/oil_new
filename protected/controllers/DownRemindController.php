<?php

/**
 * Created by youyi000.
 * DateTime: 2016/7/4 11:20
 * Describe：
 */
class DownRemindController extends RemindController
{

    public function pageInit()
    {
        $this->filterActions= "getFile";
        $this->rightCode    = "downRemind";
        $this->type         = 2;
        $this->attachmentType=Attachment::C_DOWNREMIND;

    }

}