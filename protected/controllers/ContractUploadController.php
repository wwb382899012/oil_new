<?php

/**
 * Created by youyi000.
 * DateTime: 2016/11/15 17:25
 * Describe：
 */
class ContractUploadController extends ContractFileBaseController {
    public function pageInit() {
        parent::pageInit();
        $this->filterActions = 'getFileId,getPdf,getFile';
        $this->rightCode = 'contractUpload';
        $this->isWordToPdf = 1;
        $this->moduleType = ConstantMap::FINAL_CONTRACT_MODULE;
    }
}