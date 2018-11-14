<?php

/**
 * Created by youyi000.
 * DateTime: 2016/11/17 16:07
 * Describe：
 */
class ContractCommonController extends AttachmentController
{
    public function pageInit()
    {
        $this->attachmentType=Attachment::C_CONTRACT;
        $this->filterActions="getFile,pdf,getPdf";
        $this->rightCode="contractCommon";
        $this->isWordToPdf=1;
    }


}