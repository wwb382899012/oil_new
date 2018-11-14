<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="/newUI/css/contract-file/index.css">
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<?php
$menus = [['text' => '合同管理', 'link' => '/contractUpload/'], ['text' => $this->pageTitle]];
$this->loadHeaderWithNewUI($menus, [], true);
?>
<!--<div class="pull-right box-tools">-->
<!--    --><?php //if (ProjectService::checkIsCanContractUpload($projectInfo['project_id']) && UserService::checkActionRight($this->rightCode, "edit")) { ?>
<!--        <button type="button" id="uploadButton" class="btn btn-danger" placeholder="上传" data-bind="click:upload,text:buttonText">上传</button>-->
<!--    --><?php //} ?>
<!--</div>-->
<section class="el-container is-vertical" id="contract-upload-edit">
    <div class="card-wrapper">
        <div class="z-card">
            <h3 class="z-card-header">
                查看合同上传信息
            </h3>
            <div class="z-card-body">
                <div class="flex-grid form-group text-table-gap">
                    <label class="col col-count-3 field flex-grid">
                        <span class="line-h--text cell-title">项目编号:</span>
                        <span class="form-control-static line-h--text">
                                <a class="text-link" href="/project/detail/?id=<?php echo $project["project_id"] ?>&t=1"
                                   target="_blank"><?php echo $project["project_code"] ?></a>
                            </span>
                    </label>
                    <label class="col col-count-3 field flex-grid">
                        <span class="line-h--text cell-title">业务类型:</span>
                        <span class="form-control-static line-h--text">
                                <?php echo $project['project_type_desc'] ?>
                            </span>
                    </label>
                    <label class="col col-count-3 field flex-grid">
                        <span class="line-h--text cell-title">交易主体:</span>
                        <span class="form-control-static line-h--text">
                                <a class="text-link"
                                   href="/corporation/detail/?id=<?php echo $project["corporation_id"] ?>&t=1"
                                   target="_blank"><?php echo Corporation::getCorporationName($project["corporation_id"]) ?></a>
                            </span>
                    </label>
                </div>

                <div class="children-gap--top">
                    <?php
                    if (Utility::isNotEmpty($contracts)) {
                        foreach ($contracts as $contract) {
                            if (Utility::isNotEmpty($contract)) {
                                ?>
                                <div style="position: relative;">
                                    <div>
                                        <table class="table table-nowrap table-in-table table-fixed" autoscroll>
                                            <thead>
                                            <tr>
                                                <th style="width:137px">合同名称</th>
                                                <th style="width:80px">标准</th>
                                                <th style="width:210px; ">我方合同编号</th>
                                                <th style="width:210px; ">对方合同编号</th>
                                                <th style="width:73px; ">状态</th>
                                                <th style="width:90px; ">最终合同</th>
                                                <th style="width:110px;">电子双签合同</th>
                                                <th style="width: 122px">纸质双签合同</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td colspan="8">
                                                    <?php
                                                    echo $this->renderPartial("/contractFile/new_filesDetail", array('contract' => $contract));
                                                    ?>
                                                </td>
                                            </tr>
                                            </tbody>

                                        </table>
                                    </div>
                                </div>

                            <?php } ?>

                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
                        <div class="box-footer">
                            <?php if (ProjectService::checkIsCanContractUpload($projectInfo['project_id']) && UserService::checkActionRight($this->rightCode, "edit")) { ?>
                                <button type="button" id="uploadButton" class="btn btn-danger btn-xs" placeholder="上传"
                                        data-bind="click:upload,text:buttonText">上传
                                </button>
                            <?php } ?>

                        </div>
        </div>
    </div>
</section>

<script>
    function back() {
        if (document.referrer) {
            location.href = document.referrer;
        } else {
            location.href = '/<?php echo $this->getId(); ?>/';
        }
    }
    $(function () {
        page.positionInTable()
    })
</script>