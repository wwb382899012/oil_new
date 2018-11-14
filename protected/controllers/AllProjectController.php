<?php

/**
 * Created by youyi000.
 * DateTime: 2016/11/7 17:44
 * Describe：
 */
class AllProjectController extends ProjectBaseController
{
    public function pageInit()
    {
        parent::pageInit();
        $this->rightCode = "allProject";
    } 

}