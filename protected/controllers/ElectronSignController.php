<?php

/**
 * Created by youyi000.
 * DateTime: 2016/11/15 17:25
 * Describe：
 */
class ElectronSignController extends ContractFileBaseController {
    public function pageInit() {
        parent::pageInit();
        $this->filterActions = '';
        $this->rightCode = 'electronSign';
        $this->moduleType = ConstantMap::ELECTRON_DOUBLE_SIGN_CONTRACT_MODULE;
    }
}