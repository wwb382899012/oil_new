<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css"/>
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">查看合同上传信息</h3>
            <div class="pull-right box-tools">
                <?php if (ProjectService::checkIsCanContractUpload($projectInfo['project_id']) && UserService::checkActionRight($this->rightCode, "edit")) { ?>
                    <button type="button" id="uploadButton" class="btn btn-danger" placeholder="上传" data-bind="click:upload,text:buttonText">上传</button>
                <?php } ?>
                <?php if (!$this->isExternal) { ?>
                    <button type="button" class="btn btn-default" onclick="back()">返回</button>
                <?php } ?>
            </div>
        </div>
        <div class="box-body form-horizontal">
            <div class="form-group">
                <div class="form-group">
                    <label class="col-sm-1 control-label">项目编号</label>
                    <div class="col-sm-2">
                        <p class="form-control-static">
                            <a href="/project/detail/?id=<?php echo $project["project_id"] ?>&t=1" target="_blank"><?php echo $project["project_code"] ?></a>
                        </p>
                    </div>
                    <label class="col-sm-2 control-label">业务类型</label>
                    <div class="col-sm-2">
                        <p class="form-control-static"><?php echo $project['project_type_desc'] ?></p>
                    </div>
                    <label class="col-sm-2 control-label">交易主体</label>
                    <div class="col-sm-3 titleholder">
                        <p class="form-control-static">
                            <a href="/corporation/detail/?id=<?php echo $project["corporation_id"] ?>&t=1" target="_blank"><?php echo Corporation::getCorporationName($project["corporation_id"]) ?></a>
                        </p>
                    </div>
                </div>
            </div>

            <?php
            if (Utility::isNotEmpty($contracts)) {
                foreach ($contracts as $contract) {
                    if (Utility::isNotEmpty($contract)) {
                        ?>
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th style="width:300px; text-align: left;">合同名称</th>
                                <th style="width:150px; text-align: left;">我方合同编号</th>
                                <th style="width:150px; text-align: left;">对方合同编号</th>
                                <th style="width:80px; text-align: center;">状态</th>
                                <th style="width:150px; text-align: center;">最终合同</th>
                                <th style="width:150px; text-align: center;">电子双签合同</th>
                                <th style="width:150px; text-align: center;">纸质双签合同</th>
                            </tr>
                            </thead>
                        </table>
                        <?php
                        foreach ($contract as $row) {
                            if (Utility::isNotEmpty($row['files'])) {
                                ?>
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th colspan="7">
                                            <?php
/*                                            $map = array(1 => '采购合同', 2 => '销售合同');
                                            echo $map[$row['type']];
                                            echo '&emsp;';
                                            echo '<a href="/businessConfirm/detail?id=' . $row['contract_id'] . '&t=1" title="合同详情" target="_blank">' . $row['contract_code'] . '</a> &nbsp;&nbsp;';
                                            */?>
                                            <form class="form-horizontal">
                                                <div class="form-group" style="margin-bottom: 0px;">
                                                    <div class="col-sm-3">
                                                        <p class="form-control-static">
                                                            <?php
                                                            $map = array(1 => '采购合同', 2 => '销售合同');
                                                            echo $map[$row['type']];
                                                            echo '&emsp;';
                                                            echo '<a href="/businessConfirm/detail?id=' . $row['contract_id'] . '&t=1" title="合同详情" target="_blank">' . $row['contract_code'] . '</a> &nbsp;&nbsp;';
                                                            ?>
                                                        </p>
                                                    </div>
                                                    <div class="col-sm-3" style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                                        <p class="form-control-static">
                                                            <?php
                                                                echo '<a href="/partner/detail/?id='.$row['partner_id'].'" title="'.$row['partner_name'].'" target="_blank">'.$row['partner_name'].'</a>';
                                                            ?>
                                                        </p>
                                                    </div>
                                                    <div class="col-sm-2" style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                                        <p class="form-control-static">
                                                            <?php
                                                            echo '<span title="'.$row['amount'].'">'.$row['amount'].'</span>';
                                                            ?>
                                                        </p>
                                                    </div>
                                                    <div class="col-sm-3" style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                                        <p class="form-control-static">
                                                            <?php
                                                            echo '<span title="'.$row['goods'].'">'.$row['goods'].'</span>';
                                                            ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </form>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach ($row['files'] as $val) {
                                        ?>
                                        <tr>
                                            <td style="width:300px; text-align: left;">
                                                <?php
                                                echo Map::$v['contract_file_categories'][$row['type']][$val['category']]['name'];
                                                ?>
                                                &nbsp;&nbsp;&nbsp;
                                                <span class="text-red" style="font-size: 12px">
                                        <?php
                                        echo Map::$v['contract_standard_type'][$val['version_type']]['name'];
                                        ?>
                                        </span>
                                            </td>
                                            <td style="width:150px; text-align: left;"><?php echo !empty($val['code']) ? $val['code'] : '无'; ?></td>
                                            <td style="width:150px; text-align: left;"><?php echo !empty($val['code_out']) ? $val['code_out'] : '无'; ?></td>
                                            <td style="width:80px; text-align: center;"><?php echo Map::$v['contract_upload_status'][$val['status']] ?></td>
                                            <td style="width:150px; text-align: center;">
                                                <?php
                                                if (!empty($val['file_url'])) {
                                                    echo '<a target="_blank" class="btn btn-primary btn-xs" title="' . $val['name'] . '" href="/' . $this->getId() . '/getFile/?id=' . $val['file_id'] . '&fileName='.$val['name'].'">点击查看</a>';
                                                } else {
                                                    echo '无';
                                                }
                                                ?>
                                            </td>
                                            <td style="width:150px; text-align: center;">
                                                <?php
                                                if (!empty($val['esign_file_url'])) {
                                                    echo '<a target="_blank" class="btn btn-primary btn-xs" title="' . $val['esign_file_name'] . '" href="/' . $this->getId() . '/getFile/?id=' . $val['esign_file_id'] . '&fileName='.$val['esign_file_name'].'">点击查看</a>';
                                                } else {
                                                    echo '无';
                                                }
                                                ?>
                                            </td>
                                            <td style="width:150px; text-align: center;">
                                                <?php
                                                if (!empty($val['psign_file_url'])) {
                                                    echo '<a target="_blank" class="btn btn-primary btn-xs" title="' . $val['psign_file_name'] . '" href="/' . $this->getId() . '/getFile/?id=' . $val['psign_file_id'] . '&fileName='.$val['psign_file_name'].'">点击查看</a>';
                                                } else {
                                                    echo '无';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                                <?php
                            }
                        }
                    }
                    ?>
                    <hr>
                    <?php
                }
            }
            ?>
        </div>
        <div class="box-footer">
            <?php if (ProjectService::checkIsCanContractUpload($projectInfo['project_id']) && UserService::checkActionRight($this->rightCode, "edit")) { ?>
                <button type="button" id="uploadButton" class="btn btn-danger btn-xs" placeholder="上传" data-bind="click:upload,text:buttonText">上传</button>
            <?php }
            if (!$this->isExternal) { ?>
                <button type="button" class="btn btn-default" onclick="back()">返回</button>
            <?php } ?>
        </div>

    </div>
</section>

<script>
	function back() {
		if(document.referrer){
			location.href=document.referrer;
		} else {
			location.href = '/<?php echo $this->getId(); ?>/';
		}
	}
</script>