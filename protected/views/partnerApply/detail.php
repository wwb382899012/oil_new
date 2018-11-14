<script src="/js/bootstrap3-typeahead.min.js"></script>
<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css" />
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab1" data-toggle="tab">基本信息</a></li>
        <?php
        if (count($attachments) <= 0) {
	        echo '<li ><a href="#tab2" data-toggle="tab" class="text-red fa fa-warning" >&nbsp;附件信息</a></li>';
        } else {
	        echo '<li><a href="#tab2" data-toggle="tab">附件信息</a></li>';
        }
        if (count($logData['rows']) > 0) {
	        echo '<li ><a href="#tab3" data-toggle="tab">操作日志</a></li>';
        }
        ?>

        <li class="pull-right">
            <?php if(!$this->isExternal){ ?>
                <button type="button" class="btn btn-sm btn-default history-back" onclick="back()">返回</button>
            <?php } ?>
            </li>
        <?php if($this->checkIsCanEdit($data['status']) && UserService::checkActionRight($this->rightCode,"save")) { ?>
            <li class="pull-right"><button type="button" class="btn btn-sm btn-danger" onclick="submit()">提交</button></li>
            <li class="pull-right"><button type="button" class="btn btn-sm btn-primary" onclick="edit()">修改</button></li>
        <?php } ?>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab1">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">合作方详细信息</h3>
                </div>
                <div class="box-body form-horizontal">
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">企业名称（全称）<span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["name"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="business_scope" class="col-sm-2 control-label">当前状态</label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $this->map["partner_status"][$data["status"]] ?></p>
                        </div>
                        <label for="business_scope" class="col-sm-2 control-label">状态时间</label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $data["status_time"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="credit_code" class="col-sm-2 control-label">统一社会信用代码</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["credit_code"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="registration_code" class="col-sm-2 control-label">工商注册号</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["registration_code"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="corporate" class="col-sm-2 control-label">法定代表人</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["corporate"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="start_date" class="col-sm-2 control-label">成立日期</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["start_date"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address" class="col-sm-2 control-label">注册地址</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["address"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="registration_authority" class="col-sm-2 control-label">登记机关</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["registration_authority"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="registered_capital" class="col-sm-2 control-label">注册资本（万元）</label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $data["registered_capital"] ?></p>
                        </div>
                        <label for="paid_up_capital" class="col-sm-2 control-label">实收（万元）</label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $data["paid_up_capital"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="business_scope" class="col-sm-2 control-label">经营范围</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["business_scope"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">企业所有制</label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php if(!empty($data["ownership"])){ $owner=Ownership::model()->findByPk($data["ownership"]); echo $owner->name;} ?></p>
                        </div>
                        <label class="col-sm-2 control-label">经营状态</label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $this->map["runs_state"][$data["runs_state"]] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="describe" class="col-sm-2 control-label">公司简介 <span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["describe"] ?></p>
                        </div>
                    </div>
                    <hr/>
                    <div class="form-group">
                        <label for="is_stock" class="col-sm-2 control-label">是否上市</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">
                            <?php
                                if($data['is_stock']) {
                                    echo "是";
                                } else {
                                    echo "否";
                                }
                            ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="stock_code" class="col-sm-2 control-label">上市编号</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["stock_code"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="stock_name" class="col-sm-2 control-label">上市名称</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["stock_name"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="stock_type" class="col-sm-2 control-label">上市板块</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["stock_type"] ?></p>
                        </div>
                    </div>

                    <hr/>
                    <div class="form-group">
                        <label for="contact_person" class="col-sm-2 control-label">客户联系人<span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $data["contact_person"] ?></p>
                        </div>
                        <label for="contact_phone" class="col-sm-2 control-label">联系方式<span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $data["contact_phone"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="business_type" class="col-sm-2 control-label">企业类型<span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $this->map["business_type"][$data["business_type"]] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="product" class="col-sm-2 control-label">
                            <?php
                            if($data['business_type'] == 1) {
                                echo "生产产品";
                            }elseif($data['business_type'] == 2) {
                                echo "主营产品";
                            }
                            ?>
                        </label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["product"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="equipment" class="col-sm-2 control-label">
	                        <?php
	                        if($data['business_type'] == 1) {
		                        echo "生产装置";
	                        }elseif($data['business_type'] == 2) {
		                        echo "贸易规模";
	                        }
	                        ?>
                        </label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["equipment"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="production_scale" class="col-sm-2 control-label">
	                        <?php
	                        if($data['business_type'] == 1) {
		                        echo "生产规模";
	                        }elseif($data['business_type'] == 2) {
		                        echo "行业口碑";
	                        }
	                        ?>
                        </label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["production_scale"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">类型<span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-4">
                            <p class="form-control-static">
                                <?php echo PartnerApplyService::getPartnerType($data['type']); ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="user_id" class="col-sm-2 control-label">业务员<span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-10">
                            <p class="form-control-static">
                                <?php
                                $users = UserService::getBusinessDirectors();
                                foreach ($users as $key => $row) {
                                    if($data['user_id'] == $row['user_id']) {
                                        echo $row['name'];
                                        break;
                                    }
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="trade_info" class="col-sm-2 control-label">历史合作情况<span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["trade_info"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="goods_ids" class="col-sm-2 control-label">拟合作产品<span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-10">
                            <p class="form-control-static">
                            <?php
                            $goods_info = GoodsService::getSpecialGoods($data['goods_ids']);
                            $html = array();
                            foreach ($goods_info as $row) {
                                $html[]= $row['name'];
                            }
                            if(is_array($html) && !empty($html))
                                echo implode($html,'&nbsp;|&nbsp;');
                            ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="bank_name" class="col-sm-2 control-label">银行名称</label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $data["bank_name"] ?></p>
                        </div>
                        <label for="bank_account" class="col-sm-2 control-label">银行账号</label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo preg_replace("/(\d{4})(?=\d)/", "$1 ", $data['bank_account']) ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tax_code" class="col-sm-2 control-label">纳税识别号</label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $data["tax_code"] ?></p>
                        </div>
                        <label for="phone" class="col-sm-2 control-label">电话</label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $data["phone"] ?></p>
                        </div>
                    </div>
                    <div class="form-group ">
                        <label for="remark" class="col-sm-2 control-label">备注</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["remark"] ?></p>
                        </div>
                    </div>
                    <div class="form-group ">
                        <label for="custom_level" class="col-sm-2 control-label">商务强制分类</label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $this->map["partner_level"][$data["custom_level"]] ?></p>
                        </div>
                        <label for="custom_level" class="col-sm-2 control-label">系统检测分类</label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $this->map["partner_level"][$data["auto_level"]] ?></p>
                        </div>
                    </div>
                    <?php 
                        $type = $data["type"];
                        $tArr = explode(',', $data['type']);
                        if(in_array(2, $tArr)){ 
                    ?>
                    <hr/>
                    <div class="form-group ">
                        <label for="custom_level" class="col-sm-2 control-label">拟申请额度 <span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><span class='text-red'>￥ <?php echo number_format($data['apply_amount']/10000/100,2) ?> 万元</span></p>
                        </div>
                        <label for="custom_level" class="col-sm-2 control-label">确认额度</label>
                        <div class="col-sm-4">
                            <p class="form-control-static">￥ <?php echo number_format($data['credit_amount']/10000/100,2) ?>  万元</p>
                        </div>
                    </div>
                    <?php } ?>
                </div><!--end box-body form-horizontal-->
                <div class="box-footer">
                    <?php if(!$this->isExternal){ ?>
                        <button type="button"  class="btn btn-default history-back" onclick="back()">返回</button>
                    <?php } ?>
                </div><!--end box-footer-->
            </div><!--end box-->
        </div><!--end tab1-->

        <div class="tab-pane" id="tab2">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">附件信息（注：<span class='text-red fa fa-asterisk'></span>标注资料必须上传）</h3>
                </div><!--end box-header-->
                <div class="box-body no-padding">
	                <?php
	                if (empty($attachments)) {
		                $attachments = PartnerApplyService::getAttachment($data["partner_id"]);
	                }
	                $attachmentTypeKey = "partner_apply_attachment_type";
	                $this->showAttachmentsEditMulti($data["partner_id"], $data, $attachmentTypeKey, $attachments);
	                ?>
                </div><!--end box-body no-padding-->
            </div><!--end box-->
        </div><!--end tab2-->

		<!--start tab3-->
        <div class="tab-pane" id="tab3">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">操作日志</h3>
                </div><!--end box-header-->
                <div class="box-body no-padding">
                    <table id="operationLogs" class="table table-condensed table-hover table-bordered table-layout">
                        <thead>
                        <tr>
                            <th style='width: 10%;text-align:center;'>序号</th>
                            <th style='width: 20%;text-align:center;'>操作人</th>
                            <th style='width: 30%; text-align:center'>时间</th>
                            <th style='width: 30%; text-align:center;'>操作</th>
                            <th style='text-align:center;'>操作详情</th>
                        </tr>
                        </thead>

                        <tbody id="partnerBody" data-bind="foreach: operationLogs">
                        <tr class="item">
                            <td style='text-align:center;' data-bind="text:id"></td>
                            <td style='text-align:center;' data-bind="text:create_user_name"></td>
                            <td style='text-align:center' data-bind="text:create_time"></td>
                            <td style='text-align:center;' data-bind="text:remark"></td>
                            <td style='text-align:center;'>
                                <a data-bind="visible:content!=null,click:function(){$parent.showLogDetailModal($index())}">变更详情</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div><!--end box-body no-padding-->
            </div><!--end box-->
        </div><!--end tab3-->

        <!-- log detail modal -->
        <div class="modal fade draggable-modal" id="logModel" tabindex="-1" role="dialog" aria-labelledby="modal">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">×</span></button>
                        <h4 class="modal-title">变更详情</h4>
                    </div>
                    <div class="modal-body">
                        <div class="box box-primary">
                            <div class="box-body">
                                <table id="changeDetails" class="table table-condensed table-hover table-bordered table-layout">
                                    <thead>
                                    <tr>
                                        <th style='width: 20%;text-align:center;'>字段</th>
                                        <th style='width: 20%; text-align:center'>字段名</th>
                                        <th style='width: 30%; text-align:center;'>旧值</th>
                                        <th style='text-align:center;'>新值</th>
                                    </tr>
                                    </thead>

                                    <tbody id="partnerBody" data-bind="foreach: changeDetails">
                                    <tr class="item">
                                        <td style='text-align:center;' data-bind="text:field"></td>
                                        <td style='text-align:center' data-bind="text:field_name"></td>
                                        <td style='text-align:center;' data-bind="text:oldValue"></td>
                                        <td style='text-align:center;' data-bind="text:newValue"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div><!--end tab-content-->
</div><!--end nav-tabs-custom-->

<script>
	var requiredTypes = <?php
		$level = !empty($data['custom_level']) ? $data['custom_level'] : (!empty($data['auto_level']) ? $data['auto_level'] : 0);
		$amountType = PartnerService::getAttachmentAmountType($data["business_type"], $level, $data['apply_amount']/10000/100);
		echo json_encode($this->map["partner_required_attachment_config"][$data["business_type"]][$level][$amountType]);
		?>;
	if(requiredTypes != null && requiredTypes.length > 0) {
		for (var i = 0; i < requiredTypes.length; i++) {
			$("td[data-type=" + requiredTypes[i] + "]").find(".file-title").append(" <span class='text-red fa fa-asterisk'></span>");
		}
    }

	function edit() {
		location.href = "/partnerApply/edit/?partner_id=<?php echo $data["partner_id"] ?>"
	}

	function submit() {
        layer.confirm("您确定要提交当前合作方准入信息，该操作不可逆？", {icon: 3, title: '提示'},function(index){
          var formData="data[partner_id]=<?php echo $data['partner_id'] ?>";
          $.ajax({
            type: "POST",
            url: "/partnerApply/save",
            data: formData,
            dataType: "json",
            success:function (json) {
                if (json.state == 0) {
                    inc.showNotice("操作成功");
                    location.href="/partnerApply/detail/?id=<?php echo $data['partner_id'] ?>";
                }
                else {
                    layer.alert(json.data, {icon: 5});
                }
            },
            error:function (data) {
                layer.alert("操作失败！" + data.responseText, {icon: 5});
            }
          });

          layer.close(index);
        });
	}

    function back() {
        <?php
            if(!empty($_GET["url"]))
                echo 'location.href="'.$this->getBackPageUrl().'";';
            else
                echo "history.back();";
        ?>
    }

	var view;
	$(function() {
		view = new OperationLogsModel(<?php echo json_encode($logData['rows']) ?>);
		ko.applyBindings(view);
	})
    function OperationLogsModel(option) {
		var self = this;
		self.operationLogs = ko.observableArray(option);
		self.changeDetails = ko.observableArray();

		//变更详情
        self.showLogDetailModal = function (index) {
            if(index >= self.operationLogs().length || index < 0) {
            	layer.alert("选择有误，请重新选择", {icon: 5});
            }
            self.changeDetails(self.operationLogs()[index]['content']);
			$("#logModel").modal({
				backdrop: true,
				keyboard: false,
				show: true
			});
		}
    }
</script>