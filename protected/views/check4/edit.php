<section class="content">
    <div class="box box-primary">
        <!-- <div class="box-header">
            <h3 class="box-title">请在下面操作</h3>
        </div> -->
        <form class="form-horizontal" role="form" id="mainForm">
            <div class="box-body">
				<div class="form-group">
                    <label for="type" class="col-sm-1 control-label">项目编号</label>
                    <div class="col-sm-2">
                        <p class="form-control-static">
                            <a title="<?php echo $data["project_code"] ?>" href="/project/detail/?id=<?php echo $data["project_id"] ?>&t=1" target="_blank"><?php echo $data["project_code"] ?></a>
                        </p>
                    </div>
                    <label for="type" class="col-sm-1 control-label">项目类型</label>
                    <div class="col-sm-3">
                        <p class="form-control-static">
                            <?php echo $data["project_type_desc"]?>
                        </p>
                    </div>
                    <label for="type" class="col-sm-1 control-label">交易主体</label>
                    <div class="col-sm-3">
                        <p class="form-control-static">
                            <a href="/corporation/detail/?id=<?php echo $data["corporation_id"] ?>&t=1" target="_blank"><?php echo Corporation::getCorporationName($data["corporation_id"]) ?></a>
                        </p>
                    </div>
                </div>
                <table class="table table-bordered table-hover dataTable ">
                    <thead>
                    <tr>
                        <th style="width:200px; text-align: center;">合同名称</th>
                        <th style="width:150px; text-align: center;">我方合同编号</th>
                        <th style="width:150px; text-align: center;">对方合同编号</th>
                        <th style="width:80px; text-align: center;">商务上传</th>
                        <th style="text-align: center;">法务审核</th>
                    </tr>
                    </thead>
                </table>
                <?php
                    if (Utility::isNotEmpty($infoArr)) {
                        foreach ($infoArr as $key=>$value) {
                             foreach ($value as $k=>$val) {
                ?>
                            <table class="table table-bordered table-hover dataTable ">
                                    <thead>
                                    <tr>
                                        <th colspan="6">
                                            <?php
/*                                            echo $this->map["buy_sell_type"][$key]."&emsp;<a href='/contract/detail?id=".$val[0]['contract_id']."&t=1' target='_blank'>".$k.'</a>';
                                            */?>
                                            <div class="form-group" style="margin-bottom: 0px;">
                                                <div class="col-sm-3">
                                                    <p class="form-control-static">
                                                        <?php
                                                        $info = end($val);
                                                        echo $this->map["buy_sell_type"][$key]."&emsp;<a href='/contract/detail?id=".$info['contract_id']."&t=1' target='_blank'>".$k.'</a>';
                                                        ?>
                                                    </p>
                                                </div>
                                                <div class="col-sm-3" style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                                    <p class="form-control-static">
                                                        <a href="/partner/detail/?id=<?php echo $info['partner_id'] ?>" target="_blank" title="<?php echo $info['partner_name'] ?>"><?php echo $info['partner_name'] ?></a>
                                                    </p>
                                                </div>
                                                <div class="col-sm-2" style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                                    <p class="form-control-static">
                                                        <span title="<?php echo $info['amount'] ?>"><?php echo $info['amount'] ?></span>
                                                    </p>
                                                </div>
                                                <div class="col-sm-3" style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                                    <p class="form-control-static">
                                                        <span title="<?php echo $info['goods'] ?>"><?php echo $info['goods'] ?></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                <tbody>
                                <?php
                                    foreach($val as $v){
                                ?>
                                     <tr>
                                         <td style="width:110px;vertical-align:middle;">
                                            <?php
                                            echo $this->map['contract_file_categories'][$key][$v['category']]['name'];
                                            ?>
                                         </td>
                                         <td style="width:90px;vertical-align:middle;">
                                            <?php
                                            echo $this->map['contract_standard_type'][$v['version_type']]['name'];
                                            ?>  
                                         </td>
                                         <td style="width:150px;vertical-align:middle;"><?php echo !empty($v['code']) ? $v['code'] : '-'; ?></td>
                                         <td style="width:150px;vertical-align:middle;"><?php echo !empty($v['code_out']) ? $v['code_out'] : '-'; ?></td>
                                         <td style="width:80px;vertical-align:middle;">
                                             <?php
                                             if (!empty($v['file_id'])) {
                                                 echo '<a target="_blank" class="btn btn-primary btn-xs" title="' . $v['file_name'] . '" href="/contractUpload/getFile/?id=' . $v['file_id'] . '&fileName='.$v['file_name'].'">点击查看</a>';
                                             } else {
                                                 echo '无';
                                             }
                                             ?>
                                         </td>
                                         <td 
                                            <?php 
                                                if(($v['file_status']==ContractFile::STATUS_CHECKING && empty($v['status'])) || ($v['check_status']==1 && $v['count']<1 && empty($v['remark'])))
                                                    echo 'style="text-align: center;vertical-align:middle;"';
                                                else
                                                    echo 'style="text-align: left;vertical-align:middle;"';
                                            ?>
                                         >
                                             <?php 
                                                 if($v['file_status']==ContractFile::STATUS_CHECKING && empty($v['status'])) {
                                                     echo "<a href='/".$this->getId()."/check/?id=".$v['obj_id']."&type=1' class='btn btn-warning btn-sm'>审核</a>";
                                                     if($count>=2)
                                                            echo '&emsp;<button type="button" class="btn btn-sm btn-success" data-bind="click:function(){compareModal(' . $v["file_id"] . ', '.$key.', 2, \''.$k.'-'.$this->map['contract_file_categories'][$key][$v['category']]['name'].'\')}">对比审核</button>';
                                                 }else{
                                                    if($v['check_status']==1){
                                                        echo '通过';
                                                        if(is_array($v['content']) && count($v['content'])>0){
                                                            foreach ($v['content'] as $content) {
                                                                if(!empty($content['remark'])){
                                                                    echo "<br/><span>●".$content['name']."<br/>";
                                                                    echo "<span class='text-red'>备注：".$content['remark']."</span><br/>";    
                                                                }
                                                            }
                                                        }

                                                    }else{
                                                        if(is_array($v['content']) && count($v['content'])>0){
                                                            echo "<span class='text-red'>".$v['count']."项不通过：</span><br/>";
                                                            foreach ($v['content'] as $content) {
                                                                if(!empty($content['remark']) && empty($content['check_status'])){
                                                                    echo "<span>●".$content['name']."(不通过)<br/>";
                                                                    echo "<span class='text-red'>修改意见：".$content['remark']."</span><br/>";
                                                                }else if(!empty($content['remark'])){
                                                                    echo "<span>●".$content['name']."(通过)<br/>";
                                                                    echo "<span class='text-red'>备注：".$content['remark']."</span><br/>";
                                                                }
                                                            }
                                                        }
                                                    }

                                                    if(!empty($v['remark'])){
                                                        echo "<br/>备注：".$v['remark'];
                                                    }
                                                    
                                                 }
                                             ?>
                                         </td>
                                     </tr>
                                     <?php
                                    }
                                 }
                                 ?>
                                </tbody>
                            </table>
                            <?php
                        }
                    } 
                ?>
				<div class="box-footer">
					<!-- <button type="button"  class="btn btn-default btn-sm" onclick="back()">返回</button> -->
                    <button type="button"  class="btn btn-default" data-bind="click:back">返回</button>
				</div>
            </div>
        </form>
    </div>
</section>

<div class="modal fade draggable-modal" id="contractModal" tabindex="-1" role="dialog" aria-labelledby="modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header"><h4 class="modal-title">请选择参考合同</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" role="form" id="contractModalForm">
                    <div class="form-group">
                        <label for="remark" class="col-sm-2 control-label">待审合同</label>
                        <div class="col-sm-6">
                            <p class="form-control-static">
                                <span data-bind="text: contract_name"></span>
                                <input type="hidden" name="obj[origin_file_id]" data-bind="value:origin_file_id"/>
                                <input type="hidden" name="obj[type]" data-bind="value:type"/>
                                <input type="hidden" name="obj[buy_sell_type]" data-bind="value:buy_sell_type"/>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="category" class="col-sm-2 control-label">参考合同</label>
                        <div class="col-sm-6">
                            <select class="form-control input-sm" title="请选择参考合同"  name="c[file_id]" id="file_id"
                                    data-bind="
                                    optionsText: 'contract_name',
                                    optionsValue: 'file_id',
                                    options:attachments,
                                    selectpicker:{value:file_id}">
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="confirmSubmitBtnText" class="btn btn-success" placeholder="确认" data-bind="click:confirm,html:confirmSubmitBtnText"></button>
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>

<script>
    var view;
    $(function () {
        view = new ViewModel();
        ko.applyBindings(view);
    })

    function ViewModel() {
        var self=this;
        self.origin_file_id = ko.observable(0);
        self.contract_name = ko.observable("");
        self.type = ko.observable(0);
        self.buy_sell_type = ko.observable(0);
        self.files = <?php $attachments = ContractService::getAllContractFile($data['project_id']); echo json_encode($attachments); ?>;
        self.attachments = ko.computed(function () {
            if(self.files.length > 0) {
                for (var i in self.files) {
                    if(self.files[i]['file_id'] == self.origin_file_id()){
                        self.files.splice(i, 1);
                    }
                }
                return self.files;
            }
        },self);

        self.file_id= ko.observable(0);
        self.init_file_id=function() {
            if(self.attachments().length>0){
                for (var i in self.attachments()){
                    if(self.buy_sell_type()>0 && self.attachments()[i]['type']!=self.buy_sell_type() && self.attachments()[i]['is_main']==1){
                        self.file_id(self.attachments()[i]['file_id']);
                        break;
                    }else{
                        self.file_id(0);
                    }
                }
            }
        };


        self.confirmSubmitBtnText = ko.observable('确认');
        self.actionState = 0;

        self.compareModal = function (file_id, buy_sell_type, type ,contract_name) {
            self.origin_file_id(file_id);
            self.buy_sell_type(buy_sell_type);
            self.type(type);
            self.contract_name(contract_name);
            self.init_file_id();
            $("#file_id").selectpicker('refresh');
            $("#contractModal").modal({
                backdrop: true,
                keyboard: false,
                show: true
            });
            $("#contractModal").on('hidden.bs.modal', function () {
                location.reload();
            });
        }


        self.confirm = function () {
            location.href = "/<?php echo $this->getId() ?>/check?id="+self.origin_file_id()+"&type="+self.type()+"&compare_id="+self.file_id();
        }

        self.back = function(){
            location.href='/<?php echo $this->getId() ?>';
        }
    }
</script>

