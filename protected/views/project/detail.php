<?php

$notTonNum=0;//单位不为吨的数量
foreach($transactions as $good){
    if($good['unit']!=ConstantMap::CONTRACT_GOODS_UNIT_CONVERT_VALUE)
        $notTonNum++;
}

?>
<link href="/css/style/projectdetail.css?key=20180112" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/js/clipboard.js"></script>
<script type="text/javascript" src="/js/resize.js"></script>
<section class="content-header">
    <div class="content-header__des">
        <?php echo empty($this->pageTitle)?$this->moduleName:$this->pageTitle ?>
    </div>
</section>
<section  id="main-container">
        <section class="content sub-container">
            <?php if ($data["status"] == Project::STATUS_BACK) {
                $backLog = ProjectBackLog::model()->find(array("condition" => "project_id=" . $data["project_id"], "order" => "id desc"));
                if (!empty($backLog)) {

                    ?>
                    <div class="form-group text-danger" id="detail-back-title" style="">
                        <span class="fa fa-info-circle"></span>&nbsp;驳回备注:
                        <span class="form-control-static"><?php echo $backLog["remark"] ?></span>
                    </div>
                <?php }
            } ?>
            <!-- 详情综述 -->
            <div class="box box-primary sub-container__box">
                <div class="box-header with-border project-header">
                  <h3 class="box-title">
                      <span class="channel-type">
                        <?php
                        $typeDesc = $this->map["project_type"][$data["type"]];
                        if (!empty($data['buy_sell_type'])) {
                            $typeDesc .= '-' . $this->map["purchase_sale_order"][$data["buy_sell_type"]];
                        }
                        echo $typeDesc;
                        ?>
                      </span>
                      <span class="project-detail"> 项目编号：<?php echo $data["project_code"] ?></span>
                      <span onclick="copy()" data-clipboard-text="<?php echo $data['project_code']; ?>" class="copy-project-num">复制</span>
                  </h3>
                  <div class="box-body form-horizontal form-horizontal-custom">
                      <div class="form-group pd-bottom-0">
                          <label for="type" class="col-sm-2 control-label">交易主体：</label>
                          <div class="col-sm-4">
                              <p class="form-control-static"><?php echo $data["corporation_name"] ?></p>
                          </div>
                          <label for="type" class="col-sm-2 control-label">项目负责人：</label>
                          <div class="col-sm-4">
                              <p class="form-control-static"><?php echo $data["manager_name"] ?></p>
                          </div>
                      </div>
                      <div class="form-group pd-bottom-0">
                          <?php
                          if (!empty($data['agent_id'])) { ?>
                          <label for="type" class="col-sm-2 control-label">采购代理商：</label>
                          <div class="col-sm-4">
                              <p class="form-control-static">
                                  <?php
                                  echo '<a href="/partner/detail/?id=' . $data["agent_id"] . '&t=1" target="_blank">' . $data["agent_name"] . '</a>';
                                  ?>
                              </p>
                          </div>
                          <?php } ?>
                      </div>
                      <div class="form-group pd-bottom-0">
                          <label for="type" class="col-sm-2 control-label">价格方式：</label>
                          <div class="col-sm-4">
                              <p class="form-control-static form-control-static-custom"><?php echo $this->map["price_type"][$data['price_type']] ?></p>
                          </div>
                          <?php
                          if (!empty($data["storehouse_name"])) {
                              ?>
                              <label class="col-sm-2 control-label">仓库名称：</label>
                              <div class="col-sm-4">
                                  <p class="form-control-static"><?php echo $data["storehouse_name"] ?></p>
                              </div>
                          <?php } ?>
                      </div>
                      <?php if (!empty($data['buy_sell_type'])) { ?>
                      <div class="form-group pd-bottom-0">
                          <label for="type" class="col-sm-2 control-label">购销顺序：</label>
                          <div class="col-sm-4">
                              <p class="form-control-static form-control-static-custom"><?php echo $this->map["purchase_sale_order"][$data['buy_sell_type']] ?></p>
                          </div>
                      </div>
                      <?php } ?>
                  </div>
                </div>
            </div>
            <!-- 详情综述 -->

            <!-- 交易明细 -->
            
            <div class="box box-primary sub-container__box">
                <div class="box-header with-border box-content-title">
                  <h3 class="box-title">&nbsp;&nbsp;&nbsp;交易明细</h3>
                  <span class="box-title__hiden">
                  <i class="fa fa-angle-double-up"></i> 收起</span>
                </div>
                <?php
              if (!empty($data['up_partner_id']) || !empty($data['down_partner_id'])) {
                  ?>
                <?php
                  if (!empty($data['up_partner_id'])) {
                ?>
                <div class="box-header  box-content-custom pd-left-20">
                  <span class="box-content__company-style">上游合作方<span>
                  <span class="box-content__company-name"><?php
                    echo '<a href="/partner/detail/?id=' . $data["up_partner_id"] . '&t=1" target="_blank">' . $data["up_partner_name"] . '</a>';
                    ?></span>
                  <span class="box-content__buy-currency">采购币种:</span>
                  <span class="box-content__currency-type"><?php echo $this->map["currency_type"][$data["purchase_currency"]] ?></span>
                </div>
                <table class="table table-hover table-hover-custom mg-left-20">
                    <thead>
                      <tr>
                        <th style="width:150px;">采购品名
                          <span class="text-red fa fa-asterisk"></span>
                        </th>
                        <!-- <th style="width:120px; text-align: left;">规格</th> -->
                        <th style="width:120px; ">数量
                          <span class="text-red fa fa-asterisk"></span>
                        </th>
                         <?php if($notTonNum>0) :?>
                        <th style="width:120px; ">单位换算比
                              <span class="text-red fa fa-asterisk"></span>
                        </th>
                        <?php endif;?>

                        <!-- ko if: isShowPurchasePrice -->
                        <th style="width:155px; ">采购单价
                          <span class="text-red fa fa-asterisk"></span>
                        </th>
                        <!-- /ko -->
                        <!-- ko if: isShowSalePrice -->
                        <th style="width:170px;">采购总价
                          <span class="text-red fa fa-asterisk"></span>
                        </th>
                        <!-- /ko -->
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($transactions as $v) { ?>
                      <tr>
                        <td><?php echo $v["goods_name"] ?></td>
                        <td><?php echo $v["quantity"],$this->map["goods_unit"][$v["unit"]]['name']; ?></td>
                        <?php if($notTonNum>0) :?><td><?php echo $this->map["goods_unit"][$v["unit"]]['name'].'/'.ConstantMap::CONTRACT_GOODS_UNIT_CONVERT.'='.$v["unit_convert_rate"]; ?></td><?php endif;?>
                        <td><?php echo $this->map["currency"][$v["purchase_currency"]]['ico'] . Utility::numberFormatFen2Yuan($v["purchase_price"]) ?></td>
                        <td><?php echo $this->map["currency"][$v["purchase_currency"]]['ico'] . Utility::numberFormatFen2Yuan($v["purchase_amount"]) ?></td>
                      </tr>
                      <?php } ?>
                  </tbody>
                </table>
                <div class="line-dot" style="border-bottom: 1px solid #ccc"></div>
                <?php } ?>

                  <?php
                    if (!empty($data['down_partner_id'])) {
                  ?>
                  <div class="box-header  box-content-custom pd-left-20">
                    <span class="box-content__company-style">下游合作方<span>
                    <span class="box-content__company-name"><?php
                      echo '<a href="/partner/detail/?id=' . $data["down_partner_id"] . '&t=1" target="_blank">' . $data["down_partner_name"] . '</a>';
                      ?></span>
                    <span class="box-content__buy-currency">销售币种:</span>
                    <span class="box-content__currency-type"><?php echo $this->map["currency_type"][$data["sell_currency"]] ?></span>
                  </div>
                  <table class="table table-hover table-hover-custom  mg-left-20">
                      <thead>
                        <tr>
                          <th style="width:150px;">销售品名
                            <span class="text-red fa fa-asterisk"></span>
                          </th>
                          <th style="width:120px; ">数量
                            <span class="text-red fa fa-asterisk"></span>
                          </th>
                        <?php if($notTonNum>0) :?>
                            <th style="width:120px; ">单位换算比
                                <span class="text-red fa fa-asterisk"></span>
                            </th>
                        <?php endif;?>
                          <!-- ko if: isShowPurchasePrice -->
                          <th style="width:155px; ">销售单价
                            <span class="text-red fa fa-asterisk"></span>
                          </th>
                          <!-- /ko -->
                          <!-- ko if: isShowSalePrice -->
                          <th style="width:170px;">销售总价
                            <span class="text-red fa fa-asterisk"></span>
                          </th>
                          <!-- /ko -->
                        </tr>
                      </thead>
                      <tbody>
                          <?php foreach ($transactions as $v) { ?>
                          <tr>
                            <td><?php echo $v["goods_name"] ?></td>
                            <td><?php echo $v["quantity"],$this->map["goods_unit"][$v["unit"]]['name']; ?></td>
                            <?php if($notTonNum>0) :?><td><?php echo $this->map["goods_unit"][$v["unit"]]['name'].'/'.ConstantMap::CONTRACT_GOODS_UNIT_CONVERT.'='.$v["unit_convert_rate"]; ?></td><?php endif;?>
                            <td><?php echo $this->map["currency"][$v["sell_currency"]]['ico'] . Utility::numberFormatFen2Yuan($v["sale_price"]) ?></td>
                            <td><?php echo $this->map["currency"][$v["sell_currency"]]['ico'] . Utility::numberFormatFen2Yuan($v["sale_amount"]) ?></td>
                          </tr>
                          <?php } ?>
                      </tbody>
                  </table>

                <?php 
                    } 
                  }
                  if (empty($data['plan_describe'])) {
                ?>
                <div class="line-dot" style="border: none; display: block;"></div>
                <?php 
                  } else {
                ?>

                <div class="box-body pd-top-0 form-horizontal pd-left-0 form-horizontal-custom">
                    <div class="form-group pd-bottom-0">
                      <label for="type" class="col-sm-2 default-title control-label custom-width-12">*
                      市场分析及<?php echo $this->map['project_buy_sell_type'][$data['buy_sell_type']]; ?>计划：
                      </label>
                      <div class="col-sm-4">
                          <p class="form-control-static "><?php echo $data["plan_describe"] ?></p>
                      </div>
                    </div>
                </div>
                <?php } ?>
            </div>
            <!-- 交易明细 -->

            <!-- 附件信息 -->
            <div class="box box-primary sub-container__box padding-bottom-10">
                <div class="box-header with-border box-content-title">
                  <h3 class="box-title">&nbsp;&nbsp;&nbsp;附件信息</h3>
                  <span class="box-title__hiden">
                  <i class="fa fa-angle-double-up"></i> 收起</span>
                </div>

                <?php
                  $itemHead = '<div class="box-body box-body-custom box-content-custom" >
                                <div class="form-group form-group-custom  form-group-custom-upload">
                                    <div class="form-group">';
                  $itemEnd = '</div>
                                </div>
                            </div>';
                  $attachs = $this->map["project_launch_attachment_type"];
                  if (Utility::isNotEmpty($attachs)) {
                    $index = 0;
                    foreach ($attachs as $key => $row) {
                      if ($index % 2 == 0) echo $itemHead;
                ?>
                      <label class="col-sm-2 control-label">
                        <span class="glyphicon glyphicon-remove text-red" data-bind="css:{'glyphicon-ok text-green':fileUploadStatus,' glyphicon-remove text-red':!fileUploadStatus()}"></span>&emsp;
                        <span data-bind="html:model.name" class="upload-title upload-title-custom"><?php echo $row["name"] ?>：</span>
                        <span data-bind="visible:model.required" style="display: none;">
                          <span class="text-red fa fa-asterisk"></span>
                        </span>
                      </label>
                      <div class="col-sm-10">
                        <ul class="list-unstyled list-unstyled-custom" data-bind="foreach:files">
                            <?php if (Utility::isNotEmpty($attachments[$key])) {
                              foreach ($attachments[$key] as $val) {
                                if (!empty($val['file_url'])) { ?>
                            <li class="list-unstyled__upload-list">
                                <span class="glyphicon glyphicon-ok text-green" data-bind="visible:isDone"></span>
                                <a class="text-name-custom" target="_blank"  href="/<?php echo $this->getId() ?>/getFile/?id=<?php echo $val['id'] ?>&fileName=<?php echo $val['name'] ?>"><?php echo $val['name'] ?></a>
                            </li>
                            <?php
                                } else {
                                  echo '无';
                                }
                                }
                                } else {
                                  echo '<p class="form-control-static">无</p>';
                                }
                            ?>
                        </ul>
                      </div>
                <?php
                  if ($index % 2 != 0) echo $itemEnd;
                    ++$index;
                    }
                  }
                ?>
                <div class="box-body box-body-custom box-content-custom">
                  <div class="form-group form-group-custom  form-group-custom-upload">
                    <div class="form-group">    
                        <label class="col-sm-2 control-label">
                        <span class="glyphicon glyphicon-remove text-red" data-bind="css:{'glyphicon-ok text-green':fileUploadStatus,' glyphicon-remove text-red':!fileUploadStatus()}"></span> 
                        <span data-bind="html:model.name" class="upload-title upload-title-custom">备注：</span>
                      </label>
                      <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $data['remark'];?></p>             
                      </div>
                    </div>
                  </div>
                </div>
            </div>
            <!-- 附件信息 -->

            <!-- 采购合同信息 -->
            <?php if (Utility::isNotEmpty($purchaseData)) { ?>
            <div class="box box-primary sub-container__box">
                <div class="box-header with-border box-content-title">
                  <h3 class="box-title">&nbsp;&nbsp;&nbsp;采购合同信息</h3>
                  <span class="box-title__hiden">
                  <i class="fa fa-angle-double-up"></i> 收起</span>
                </div>
                <table class="table table-hover table-hover-custom mg-left-20">
                    <thead>
                      <tr>
                        <th style="width:250px;">采购合同编号
                          <span class="text-red fa fa-asterisk"></span>
                        </th>
                          <span class="text-red fa fa-asterisk"></span>
                        </th>
                        <th style="width:165px; ">外部合同编号
                          <span class="text-red fa fa-asterisk"></span>
                        </th>
                        <th style="width:260px;">上游合作方
                          <span class="text-red fa fa-asterisk"></span>
                        </th>
                        <th style="width:140px; ">状态</th>
                        <th style="width:160px; ">合同签订日期</th>
                        <th style="width:140px; ">合同文本</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($purchaseData as $v) { ?>
                      <tr>
                        <td><?php if (!empty($v['code'])) {
                                echo "<a href='/businessConfirm/detail?id=" . $v['contract_id'] . "'  target='_blank' class='btn btn-primary btn-xs'>" . $v['code'] . "</a>";
                            } else {
                                echo '无';
                            } ?></td>
                        <td><?php echo !empty($v["code_out"]) ? $v["code_out"] : '无' ?></td>
                        <td><a href="/partner/detail/?id=<?php echo $v['partner_id'] ?>&t=1" target="_blank"><?php echo $v['partner_name'] ?></a>
                        </td>
                        <td><?php echo $this->map["contract_status"][$v["status"]] ?></td>
                        <td><?php echo $v["contract_date"] ?></td>
                        <td>
                            <a href="/contractUpload/detail/?id=<?php echo $data['project_id'] ?>&t=1" target="_blank">合同文本</a>
                        </td>
                      </tr>
                      <?php } ?>
                  </tbody>
                </table>
                  <div class="box-body form-horizontal form-horizontal-custom">

                  </div>
            </div>
            <?php } ?>
            <!-- 采购合同信息 -->

            <!-- 销售合同信息 -->
            <?php if (Utility::isNotEmpty($saleData)) { ?>
            <div class="box box-primary sub-container__box">
                <div class="box-header with-border box-content-title">
                  <h3 class="box-title">&nbsp;&nbsp;&nbsp;销售合同信息</h3>
                  <span class="box-title__hiden">
                  <i class="fa fa-angle-double-up"></i> 收起</span>
                </div>
                <table class="table table-hover table-hover-custom mg-left-20">
                    <thead>
                      <tr>
                        <th style="width:250px;">销售合同编号
                          <span class="text-red fa fa-asterisk"></span>
                        </th>
                          <span class="text-red fa fa-asterisk"></span>
                        </th>
                        <th style="width:165px; ">外部合同编号
                          <span class="text-red fa fa-asterisk"></span>
                        </th>
                        <th style="width:260px;">下游合作方
                          <span class="text-red fa fa-asterisk"></span>
                        </th>
                        <th style="width:140px; ">状态</th>
                        <th style="width:160px; ">合同签订日期</th>
                        <th style="width:140px; ">合同文本</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($saleData as $v) { ?>
                      <tr>
                        <td><?php if (!empty($v['code'])) {
                              echo "<a href='/businessConfirm/detail?id=" . $v['contract_id'] . "'  target='_blank' class='btn btn-primary btn-xs'>" . $v['code'] . "</a>";
                          } else {
                              echo '无';
                          } ?></td>
                        <td><?php echo !empty($v["code_out"]) ? $v["code_out"] : '无' ?></td>
                        <td><a href="/partner/detail/?id=<?php echo $v['partner_id'] ?>&t=1" target="_blank"><?php echo $v['partner_name'] ?></a>
                        </td>
                        <td><?php echo $this->map["contract_status"][$v["status"]] ?></td>
                        <td><?php echo $v["contract_date"] ?></td>
                        <td>
                            <a href="/contractUpload/detail/?id=<?php echo $data['project_id'] ?>&t=1" target="_blank">合同文本</a>
                        </td>
                      </tr>
                      <?php } ?>
                  </tbody>
                </table>
                  <div class="box-body form-horizontal form-horizontal-custom">
                  </div>
            </div>
            <?php } ?>
            <!-- 销售合同信息 -->
            <div class="box box-primary sub-container__box">
                <div class="box-header with-border box-content-title">
                    <h3 class="box-title">&nbsp;&nbsp;&nbsp;创建人信息</h3>
                    <span class="box-title__hiden">
                <i class="fa fa-angle-double-up"></i> 收起</span>
                </div>
                <div class="box-body form-horizontal form-horizontal-custom">
                    <div class="form-group pd-bottom-0">
                        <label style="font-weight: 500" for="type" class="col-lg-3 col-xl-2 control-label">项目创建人/时间：</label>
                        <div class="col-lg-3 col-xl-4">
                            <p class="form-control-static form-control-static-custom">
                                <?php echo $data["create_name"] ?> /
                                <?php echo $data["create_time"] ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <br/>

            <!-- 提交保存 -->
            <?php if (empty($_GET['t']) || $_GET['t'] != 1) { ?>
          <div class="box box-primary sub-container__box sub-container__fixed">
              <div class="box-body box-body-custom">
                  <div class="form-group form-group-custom-btn">
                      <!-- 此处删除了类：col-sm-offset-2 col-sm-10  增加了类submit-btn-custom-->
                      <div class="btn-contain-custom">
                        <?php
                        if ($data["status"] < Project::STATUS_SUBMIT && $data["status"] > Project::STATUS_STOP) {
                            echo '<button type="button" id="saveButton" class="btn btn-contain__submit" onclick="submit()">提交</button>';
                        }
                        if ($this->checkIsCanEdit($data["status"])) {
                            echo '<button type="button" class="btn btn-contain__default" onclick="edit()">修改</button>';
                        }
                        if (empty($_GET['t']) || $_GET['t'] != 1) {
                            echo '<button type="button" class="btn btn-contain__default" onclick="back()">返回</button>';
                        }
                        ?>
                      </div>
                    </div>
              </div>
            </div>
            <?php } ?>
            <!-- 提交保存 -->

        </section>
 </section>
<script type="text/javascript">
  $(function() {
    var clipboard = new Clipboard('.copy-project-num');
    $('span.box-title__hiden').on('click',function(event) {
        var ele = $(this);
        $(ele).html('');
        var toggle1 = $(ele).parents("div.sub-container__box").find("div.box-content-custom:visible");
        var toggle2 = $(ele).parents("div.sub-container__box").find("div.form-horizontal:visible");
        var toggle3 = $(ele).parents("div.sub-container__box").find("div.box-body-overflow:visible");
        
        if(toggle1.length > 0 || toggle2.length > 0 || toggle3.length > 0) {
            $(ele).parents("div.sub-container__box").find("div.box-content-custom").hide('slow');
            $(ele).parents("div.sub-container__box").find("table").hide('slow');
            $(ele).parents("div.sub-container__box").find("div.form-horizontal").hide('slow');
            $(ele).parents("div.sub-container__box").find("div.box-body-overflow").hide('slow');
            $(ele).parents("div.sub-container__box").find("div.line-dot").hide('slow');

            var eleI = $('<i class="fa fa-angle-double-down"></i>');
              $(ele).html(' 展开');
            eleI.prependTo($(ele));
        } else {
            $(ele).parents("div.sub-container__box").find("div.box-content-custom").show('slow');
            $(ele).parents("div.sub-container__box").find("table").show('slow');
            $(ele).parents("div.sub-container__box").find("div.form-horizontal").show('slow');
            $(ele).parents("div.sub-container__box").find("div.box-body-overflow").show('slow');
            $(ele).parents("div.sub-container__box").find("div.line-dot").show('slow');

            
            var eleI = $('<i class="fa fa-angle-double-up"></i>');
            $(ele).html(' 收起');
            eleI.prependTo($(ele));
            
        }
    });
  });

	function back() {
		location.href = '/<?php echo $this->getId() ?>/';
	}

	function edit() {
		location.href = "/<?php echo $this->getId() ?>/edit/?t=<?php echo $this->isExternal ?>&id=<?php echo $data["project_id"] ?>";
	}

  function copy() {
      layer.msg('复制成功', {icon: 6, time: 1000});
  }

	function submit() {
		layer.confirm("您确定要提交当前项目信息吗，该操作不可逆？", {
			icon: 3,
			'title': '提示'
		}, function (index) {
			var formData = "id=<?php echo $data["project_id"] ?>";
			$.ajax({
				type: 'POST',
				url: '/<?php echo $this->getId() ?>/submit',
				data: formData,
				dataType: "json",
				success: function (json) {
					if (json.state == 0) {
						layer.msg(json.data, {icon: 6, time: 1000}, function () {
							location.href = '/<?php echo $this->getId() ?>/';
						});
					}
					else {
						layer.alert(json.data, {icon: 5});
					}
				},
				error: function (data) {
					layer.alert("操作失败！" + data.responseText, {icon: 5});
				}
			});
			layer.close(index);
		})
	}
</script>