<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#detail" data-toggle="tab">合同详情</a></li>
            <li><a href="#flow" data-toggle="tab">审核记录</a></li>
            <?php
            $contractFiles = ContractService::getContractFiles($contract->contract_id);
            if(Utility::isNotEmpty($contractFiles)) { ?>
            <li><a href="#contract-file" data-toggle="tab">合同文本</a></li>
            <?php } ?>
            <?php if (!$this->isExternal) { ?>
                <li class="pull-right">
                    <button type="button" class="btn btn-sm btn-default" onclick="back()">返回</button>
                </li>
            <?php } ?>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="detail">
                <div class="box box-primary">
                    <form class="form-horizontal" role="form" id="mainForm">
                        <div class="box-body">
                            <?php
                            $this->renderPartial("/common/contractDetailOld", array('contract'=>$contract));
                            ?>
                        </div>
                    </form>
                    <div class="box-footer">
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <?php if(!$this->isExternal){ ?>
                                    <button type="button"  class="btn btn-default" onclick="back()">返回</button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="flow">
                <?php
                $checkLogs = FlowService::getCheckLogModel($contract->contract_id, ContractService::getContractBusinessIds());
                if (Utility::isEmpty($checkLogs)) {
                    $checkLogs = FlowService::getCheckLogModel($contract->relation_contract_id, ContractService::getContractBusinessIds());
                }
                $this->renderPartial("/check/checkLogs", array('checkLogs' => $checkLogs));
                ?>
            </div>
            <?php if(Utility::isNotEmpty($contractFiles)) { ?>
            <div class="tab-pane" id="contract-file">
                <?php
                $this->renderPartial("/contractFile/contractFiles", array('contractFiles' => $contractFiles, 'contract' => $contract));
                ?>
            </div>
            <?php } ?>
        </div>
    </div>
</section>
<script>
    function back() {
        location.href = "<?php echo $this->getBackPageUrl() ?>";
    }


</script>