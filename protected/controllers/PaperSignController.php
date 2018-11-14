<?php

/**
 * Created by youyi000.
 * DateTime: 2016/11/15 17:25
 * Describe：
 */
class PaperSignController extends ContractFileBaseController {
    public function pageInit() {
        parent::pageInit();
        $this->filterActions = '';
        $this->rightCode = 'paperSign';
        $this->moduleType = ConstantMap::PAPER_DOUBLE_SIGN_CONTRACT_MODULE;
    }
}