<?php

/**
 * Created by youyi000.
 * DateTime: 2017/12/5 10:32
 * Describe：
 */
class ImportCommand extends CConsoleCommand {

    public function actionImport() {
        $this->importManager();
        $this->importPartner();
        $this->importStorehouse();
        $this->importProject();
        $this->importContract();
        $this->importStockIn();
        $this->importStockOut();
        $this->importPay();
    }

    public function actionImportBase() {
        $this->importManager();
        $this->importPartner();
        $this->importStorehouse();
    }

    public function actionImportProjectAndContract() {
        $this->importProject();
        $this->importContract();
    }

    public function actionImportStockInOut() {
        $this->importStockIn();
        $this->importStockOut();
    }

    public function actionImportPay() {
        $this->importPay();
    }

    public function actionGenerateContractFile() {
        ImportService::generateContractFile();
    }

    public function actionRollback() {
        ImportService::rollback();
        StorageMigrateService::rollback();
        PayRecMigrateService::rollback();
    }

    /**
     * @desc 历史项目导入
     */
    protected function importProject() {
        ImportService::importHistoryProject();
    }

    /**
     * @desc 历史合同导入
     */
    protected function importContract() {
        ImportService::importHistoryContracts();
    }

    /**
     * @desc 历史合作方导入
     */
    protected function importPartner() {
        ImportService::importHistoryPartners();
    }

    /**
     * @desc 历史业务员导入
     */
    protected function importManager() {
        ImportService::importHistoryManagerIds();
    }

    /**
     * @desc 历史仓库导入
     */
    protected function importStorehouse() {
        ImportService::importHistoryStorehouses();
    }

    /**
     * @desc 入库单相关导入
     */
    protected function importStockIn() {
        StorageMigrateService::importStockIn();
    }

    protected function importStockOut() {
        ImportService::importHistoryStockOuts();
    }

    /**
     * @desc 导入支付相关数据
     */
    protected function importPay() {
        PayRecMigrateService::run();
    }
}