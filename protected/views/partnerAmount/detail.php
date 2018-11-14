<script src="/js/bootstrap3-typeahead.min.js"></script>
<link href="/js/jqueryFileUpload/css/jquery.fileupload.css" rel="stylesheet" type="text/css" />
<script src="/js/jqueryFileUpload/vendor/jquery.ui.widget.js" type="text/javascript"></script>
<script type="text/javascript" src="/js/jqueryFileUpload/jquery.fileupload.js"></script>
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab1" data-toggle="tab">基本信息</a></li>
        <?php
        if(count($attachments)<=0)
            echo '<li ><a href="#tab2" data-toggle="tab" class="text-red fa fa-warning" >&nbsp;扩展信息</a></li>';
        else
            echo '<li><a href="#tab2" data-toggle="tab">附件信息</a></li>';

        if(count($amountInfo['rows'])>0)
            echo '<li ><a href="#tab4" data-toggle="tab" class="hide">额度信息</a></li>';

        if(count($logData['rows'])>0)
            echo '<li ><a href="#tab3" data-toggle="tab">操作日志</a></li>';

        

        if(Utility::isNotEmpty($checkLogs))
            echo '<li ><a href="#tab_check" data-toggle="tab">审核记录</a></li>';
        
        ?>
        <li class="pull-right"><button type="button" class="btn btn-default" onclick="back()">返回</button></li>
        <?php if($this->checkIsCanEdit($data['status']) && UserService::checkActionRight($this->rightCode,"save")) { ?>
            <!-- <li class="pull-right"><button type="button" class="btn btn-danger" onclick="submit()">提交</button></li> -->
            <li class="pull-right"><button type="button" class="btn btn-primary" onclick="edit()">调整</button></li>
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
                            <p class="form-control-static"><?php $owner=Ownership::model()->findByPk($data["ownership"]); echo $owner->name; ?></p>
                        </div>
                        <label class="col-sm-2 control-label">经营状态</label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $this->map["runs_state"][$data["runs_state"]] ?></p>
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
                        <label for="contact_person" class="col-sm-2 control-label">客户联系人 <span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $data["contact_person"] ?></p>
                        </div>
                        <label for="contact_phone" class="col-sm-2 control-label">联系方式 <span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $data["contact_phone"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="business_type" class="col-sm-2 control-label">企业类型 <span class="text-red fa fa-asterisk"></span></label>
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
                        <label class="col-sm-2 control-label">类型 <span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $this->map["partner_type"][$data["type"]] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="user_id" class="col-sm-2 control-label">业务员 <span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo UserService::getUsernameById($data['user_id']) ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="trade_info" class="col-sm-2 control-label">历史合作情况 <span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["trade_info"] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="goods_ids" class="col-sm-2 control-label">拟合作产品 <span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-10">
                            <p class="form-control-static">
                            <?php
                            $goods_info = GoodsService::getSpecialGoods($data['goods_ids']);
                            $html = '';
                            foreach ($goods_info as $row) {
                                $html[]= $row['name'];
                            }
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
                        <label for="custom_level" class="col-sm-2 control-label">商务强制分类</label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $this->map["partner_level"][$data["custom_level"]] ?></p>
                        </div>
                        <label for="custom_level" class="col-sm-2 control-label">风控评审分类</label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $this->map["partner_level"][$data["level"]] ?></p>
                        </div>
                    </div>
                    <div class="form-group ">
                        <label for="custom_level" class="col-sm-2 control-label">系统检测分类</label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $this->map["partner_level"][$data["auto_level"]] ?></p>
                        </div>
                    </div>
                    <div class="form-group ">
                        <label for="remark" class="col-sm-2 control-label">备注</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><?php echo $data["remark"] ?></p>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group ">
                        <label for="custom_level" class="col-sm-2 control-label">拟申请额度 <span class="text-red fa fa-asterisk"></span></label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><span class='text-red'>￥ <?php echo number_format($data['apply_amount']/100,2) ?></span></p>
                        </div>
                        <label for="custom_level" class="col-sm-2 control-label">原常规额度</label>
                        <div class="col-sm-4">
                            <p class="form-control-static">￥ <?php echo number_format($data['credit_amount']/100,2) ?></p>
                        </div>
                    </div>
                </div><!--end box-body form-horizontal-->
                <div class="box-footer">
                    <?php if(!$this->isExternal){ ?>
                        <button type="button"  class="btn btn-default" onclick="back()">返回</button>
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

        <div class="tab-pane hide" id="tab4">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">额度信息</h3>
                </div><!--end box-header-->
                <div class="box-body form-horizontal">
                    <div class="form-group">
                        <label for="type" class="col-sm-2 control-label">企业编号</label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $data["partner_id"] ?></p>
                        </div>
                        <label for="type" class="col-sm-2 control-label">常规额度</label>
                        <div class="col-sm-4">
                            <p class="form-control-static">￥ <?php echo number_format($amountInfo['rows'][0]["credit_amount"]/100,2) ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="type" class="col-sm-2 control-label">企业名称</label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $data["name"] ?></p>
                        </div>
                        <label for="type" class="col-sm-2 control-label">正占用额度</label>
                        <div class="col-sm-4">
                            <p class="form-control-static">￥ <?php echo number_format($amountInfo['rows'][0]["use_amount"]/100,2) ?></p>
                        </div>
                        
                    </div>
                    <div class="form-group">
                        <label for="type" class="col-sm-2 control-label"></label>
                        <div class="col-sm-4">
                            <p class="form-control-static"></p>
                        </div>
                        <label for="type" class="col-sm-2 control-label">剩余额度</label>
                        <div class="col-sm-4">
                            <p class="form-control-static">￥ <?php echo number_format($amountInfo['rows'][0]["balance_amount"]/100,2) ?></p>
                        </div>
                    </div>
                    <?php 
                        function showCell($row,$self){
                            $s = '';
                            $userAmount = ProjectCreditApplyDetail::model()->findAllToArray('project_id='.$row['project_id']." and status=".ProjectCreditApplyDetail::STATUS_CONFIRM);
                            if(count($userAmount)>0){
                                $s .= '<table width="180" border="0" align="center">';
                                foreach ($userAmount as $key => $value) {
                                    $username = UserService::getUsernameById($value['user_id']);
                                    $s .= '<tr>';
                                    $s .= '<td style="text-align:left;">'.$username.'</td>';
                                    $s .= '<td style="text-align:right;">￥ '.number_format($value['amount']/100,2).'</td>';
                                    $s .= '</tr>';
                                }
                                $s .= '</table>';
                            }
                            if(empty($s))
                                $s = '￥ 0.00';
                            return $s;
                        }
                        $table_array = array(
                            array('key' => 'rowno', 'type' => '', 'style' => 'width:60px;text-align:center;vertical-align:middle;', 'text' => '序号'),
                            array('key' => 'project_id', 'type' => '', 'style' => 'width:100px;text-align:center;vertical-align:middle;', 'text' => '项目编号'),
                            array('key' => 'project_id,project_name', 'type' => 'href', 'style' => 'text-align:left;vertical-align:middle;', 'text' => '项目名称','href_text'=>'<a id="t_{1}" title="{2}" target="_blank" href="/project/detail/?id={1}&t=1" >{2}</a>'),
                            array('key' => 'trade_type', 'type' => 'map_val', 'map_name'=>'trade_type', 'style' => 'width:80px;text-align:center;vertical-align:middle;', 'text' => '业务类型'),
                            array('key' => 'plan_amount', 'type' => 'amount', 'style' => 'width:120px;text-align:right;vertical-align:middle;', 'text' => '预计下游应收'),
                            array('key' => 'actual_amount', 'type' => 'amount', 'style' => 'width:120px;text-align:right;vertical-align:middle;', 'text' => '实际下游应收'),
                            array('key' => 'received_amount', 'type' => 'amount', 'style' => 'width:120px;text-align:right;vertical-align:middle;', 'text' => '下游已收'),
                            array('key' => 'unreceive_amount', 'type' => 'amount', 'style' => 'width:120px;text-align:right;vertical-align:middle;', 'text' => '下游未收'),
                            array('key' => 'unreceive_amount', 'type' => 'amount', 'style' => 'width:120px;text-align:right;vertical-align:middle;', 'text' => '下游占用额度'),
                            array('key' => 'project_id', 'type' => 'href', 'style' => 'width:200px;text-align:center;', 'text' => '个人额度','href_text'=>'showCell'),
                        );
                        $style = empty($_data_['amountInfo']) ? "min-width:900px;" : "min-width:1150px;";
                        $this->show_table($table_array, $_data_['amountInfo'], "", $style,"table-bordered table-layout");
                    ?>
                </div><!--end box-body no-padding-->
            </div><!--end box-->
        </div><!--end tab4-->

        <div class="tab-pane" id="tab3">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">操作日志</h3>
                </div><!--end box-header-->
                <div class="box-body no-padding">
                    <?php
                        $table_array = array(
                            array('key' => 'create_user_name', 'type' => '', 'style' => 'width:60px;text-align:center;', 'text' => '操作人'),
                            array('key' => 'create_time', 'type' => '', 'style' => 'width:100px;text-align:center;', 'text' => '时间'),
                            array('key' => 'operation_content', 'type' => '', 'style' => 'width:200px;text-align:center;', 'text' => '操作行为'),
                        );
                        $this->show_table($table_array, $_data_['logData'], "", "min-width:900px;");
                    ?>
                </div><!--end box-body no-padding-->
            </div><!--end box-->
        </div><!--end tab3-->

        <div class="tab-pane" id="tab_check">
            <div class="box">
                <div class="box-body no-padding">
                    <table class="table table-striped table-hover">
                        <tbody>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>审核意见</th>
                            <th style="width: 130px;">审核节点</th>
                            <th style="width: 100px;">审核人</th>
                            <th style="width: 60px;">结果</th>
                            <th style="width: 100px;">审核详情</th>
                            <th style="width: 150px;">审核时间</th>

                        </tr>
                        <?php
                        if(Utility::isNotEmpty($checkLogs))
                        {
                            $k=0;
                            foreach($checkLogs as $v)
                            {
                                $k++;
                                ?>
                                <tr>
                                    <td><?php echo $k ?>.</td>
                                    <td><?php echo $v["remark"] ?></td>
                                    <td><?php echo $v["node_name"] ?></td>
                                    <td><?php echo $v["name"] ?></td>
                                    <td><?php echo $this->map["check_status"][$v["check_status"]] ?></td>
                                    <td><?php echo '<a href="/check'.$v['business_id'].'/detail?t=1&check_id='.$v['check_id'].'&uid='.$v['user_id'].'&b='.$v['business_id'].'&id='.$v['obj_id'].'" target="_blank">' ?>点击查看</a></td>
                                    <td><?php echo $v["check_time"] ?></td>

                                </tr>
                                <?php
                            }
                        }

                        ?>

                        </tbody></table>
                </div>
            </div>
        </div>
    </div><!--end tab-content-->
</div><!--end nav-tabs-custom-->

<script>


    var requiredTypes = <?php
            $auto_level = PartnerApplyService::getPartnerLevel($data);
            $amountType = PartnerService::getAttachmentAmountType($data["business_type"], $auto_level, $data['apply_amount']/10000/100);
            echo json_encode($this->map["partner_required_attachment_config"][$data["business_type"]][$auto_level][$amountType]);
        ?>;
    if(requiredTypes != null && requiredTypes.length >0) {
		for (var i = 0; i < requiredTypes.length; i++) {
			$("td[data-type=" + requiredTypes[i] + "]").find(".file-title").append(" <span class='text-red fa fa-asterisk'></span>");
		}
    }

    function back() {
        location.href="/partnerAmount/";
    }

    function edit()
    {
        location.href="/partnerAmount/edit/?id=<?php echo $data['partner_id'] ?>";
    }
</script>