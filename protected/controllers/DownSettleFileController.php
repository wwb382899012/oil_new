<?php

/**
 * Created by youyi000.
 * DateTime: 2016/11/11 14:29
 * Describe：
 */
class DownSettleFileController extends SettleFileController
{
    public function pageInit()
    {
        parent::pageInit();
        $this->filterActions = "getFile";
        $this->rightCode = "downSettleFile";
        $this->type=2;
    }
}