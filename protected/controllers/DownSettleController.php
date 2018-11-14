<?php

/**
 * Created by youyi000.
 * DateTime: 2016/11/10 16:14
 * Describe：
 */
class DownSettleController  extends SettleBaseController
{

    public function pageInit()
    {
        parent::pageInit();
        $this->filterActions = "getFile";
        $this->rightCode = "downSettlement";
        $this->type=2;
    }


}