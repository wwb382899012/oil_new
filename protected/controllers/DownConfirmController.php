<?php

/**
 * Created by youyi000.
 * DateTime: 2016/7/4 11:20
 * Describe：
 */
class DownConfirmController extends SettleConfirmController
{
    public function pageInit()
    {
        $this->filterActions= "";
        $this->rightCode    = "downConfirm";
        $this->type         = 2;
    }

}