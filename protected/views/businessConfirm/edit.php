<link href="/css/businessconfirm.css?key=20180112" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/js/resize.js"></script>
<link href="/css/style/projectdetail.css?key=20180112" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/js/clipboard.js"></script>

<section class="content-header">
    <div class="content-header__des">
        <?php echo empty($this->pageTitle)?$this->moduleName:$this->pageTitle ?>
    </div>
</section>
<section class="content sub-container">
    <!-- 项目附件信息 -->
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
                    <span class="upload-title upload-title-custom"><?php echo $row["name"] ?>：</span>
                </label>
                <div class="col-sm-10">
                    <ul class="list-unstyled list-unstyled-custom">
                        <?php if (Utility::isNotEmpty($attachments[$key])) {
                            foreach ($attachments[$key] as $val) {
                                if (!empty($val['file_url'])) { ?>
                                    <li class="list-unstyled__upload-list">
                                        <a class="text-name-custom" target="_blank"  href="/project/getFile/?id=<?php echo $val['id'] ?>&fileName=<?php echo $val['name'] ?>"><?php echo $val['name'] ?></a>
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
                        <span class="upload-title upload-title-custom">备注：</span>
                    </label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $data['remark'];?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 项目附件信息 -->

        <!-- 合同信息 -->
        <div class="box box-primary sub-container__box">
            <div class="box-header with-border box-content-title">
                <h3 class="box-title">&nbsp;&nbsp;&nbsp;合同信息 (
                    <span class="project-num">
                        <a href="/project/detail/?id=<?php echo $data['project_id'] ?>&t=1" target="_blank">项目编号 : <?php echo $data["project_code"] ?></a></span> )
                </h3>
            </div>
            <form class="form-horizontal" role="form" id="mainForm">
                <div class="box-body box-body-custom">
                    <div class="form-group form-group-custom form-group ">
                        <label for="type" class="col-lg-2 col-xl-1  control-label control-label-custom">
                          <span class="label-custom__span-red"></span>项目类型 ：
                        </label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $data["project_type_desc"] ?></p>
                        </div>
                        <label for="type" class="col-lg-2 col-xl-1  control-label control-label-custom control-label-custom-right">
                            <span class="label-custom__span-red"></span>购销信息 ：
                        </label>
                        <div class="col-sm-4">
                            <p class="form-control-static"><?php echo $data['buy_sell_desc'] ?></p>
                        </div>
                    </div>
    
    
                    <div class="form-group form-group-custom ">
                        <span data-bind="visible: showBuyContractSelectType">
                            <label for="type" class="col-lg-2 col-xl-1  control-label control-label-custom">
                              <span class="label-custom__span-red">*</span>采购合同类型 ：
                            </label>
                            <div class="col-sm-4">
                                <select class="form-control selectpicker" title="请选择采购合同类型" id="buy_category" name="buy[category]" data-bind="optionsCaption: '请选择采购合同类型',value:buy_category, valueAllowUnset: true">
                                    <?php foreach ($this->map["buy_contract_type"] as $k => $v) {
                                        echo "<option value=" . $k . ">" . $v . "</option>";
                                    } ?>
                                </select>
                            </div>
                        </span>
                        <span data-bind="visible: showBuyContractStaticType">
                            <label for="type" class="col-lg-2 col-xl-1  control-label control-label-custom">
                                <span class="label-custom__span-red"></span>采购合同类型 ：
                            </label>
                            <div class="col-sm-4">
                                <p class="form-control-static">国内采购合同</p>
                                <input type="hidden" name="buy[category]" data-bind="value:buy_category" />
                            </div>
                        </span>
    
                        <span data-bind="visible: showSellContractStaticType">
                            <span data-bind="visible:isShowUpAndDown">
                                <label for="type" class="col-lg-2 col-xl-1  control-label control-label-custom control-label-custom-right">
                                    <span class="label-custom__span-red"></span>销售合同类型 ：
                                </label>
                            </span>
                            <span data-bind="visible:showOnlyDownPartner">
                                <label for="type" class="col-lg-2 col-xl-1  control-label control-label-custom">
                                    <span class="label-custom__span-red"></span>销售合同类型 ：
                                </label>
                            </span>
                            
                            <div class="col-sm-4">
                                <p class="form-control-static">国内销售合同</p>
                                <input type="hidden" name="sell[category]" data-bind="value:sell_category" />
                            </div>
                        </span>
                    </div>
    
                    <div class="form-group form-group-custom ">
                        <label for="type" class="col-lg-2 col-xl-1  control-label control-label-custom">
                          <span class="label-custom__span-red"></span>交易主体 ：
                        </label>
                        <div class="col-sm-4">
                            <select class="form-control selectpicker" title="请选择交易主体" id="corporation_id" name="obj[corporation_id]" data-live-search="true" data-bind="optionsCaption: '请选择交易主体',value:corporation_id,valueAllowUnset: true" disabled>
                                <?php
                                $cors = UserService::getUserSelectedCorporations();
                                foreach ($cors as $v) {
                                    echo "<option value='" . $v["corporation_id"] . "'>" . $v["name"] . "</option>";
                                } ?>
                            </select>
                        </div>
    
                        <span data-bind="visible: showStorehouse">
                            <label for="type" class="col-lg-2 col-xl-1  control-label control-label-custom control-label-custom-right">
                              <span class="label-custom__span-red">*</span>仓库名称 ：
                            </label>
                            <div class="col-sm-4">
                                <select class="form-control selectpicker" title="请选择仓库名称" id="storehouse_id" name="obj[storehouse_id]" data-live-search="true" data-bind="value: storehouse_id,valueAllowUnset: true">
                                    <option value='0'>请选择仓库名称</option>
                                    <?php
                                    $users = Storehouse::getAllActiveStorehouse();
                                    foreach ($users as $v) {
                                        echo "<option value='" . $v["store_id"] . "'>" . $v["name"] . "</option>";
                                    } ?>
                                </select>
                            </div>
                        </span>
                    </div>
    
                    <div class="form-group form-group-custom form-group " data-bind="visible: !showOnlyDownPartner()">
                        <label for="type" class="col-lg-2 col-xl-1  control-label control-label-custom">
                          <span class="label-custom__span-red">*</span>上游合作方 ：
                        </label>
                        <div class="col-sm-4">
                            <select class="form-control selectpicker" title="请选择上游合作方" id="up_partner_id" name="buy[up_partner_id]" data-live-search="true" data-bind="optionsCaption: '请选择上游合作方',value: up_partner_id,valueAllowUnset: true">
                                <?php
                                $downPartners = PartnerService::getUpPartners();
                                foreach ($downPartners as $v) {
                                    echo "<option value='" . $v["partner_id"] . "'>" . $v["name"] . "</option>";
                                } ?>
                            </select>
                        </div>
                    </div>
    
                    <div class="form-group form-group-custom " data-bind="visible: isShowAgent">
                        <label for="corporation_id" class="col-lg-2 col-xl-1  control-label control-label-custom">采购代理商 ：
                        </label>
                        <div class="col-sm-4">
                            <select class="form-control selectpicker" title="请选择采购代理商" id="agent_id" name="buy[agent_id]" data-live-search="true" data-bind="optionsCaption: '请选择采购代理商',value: agent_id,valueAllowUnset: true">
                                <option value=''>请选择采购代理商</option>
                                <?php
                                $downPartners = PartnerService::getAgentPartners();
                                foreach ($downPartners as $v) {
                                    echo "<option value='" . $v["partner_id"] . "'>" . $v["name"] . "</option>";
                                } ?>
                            </select>
                        </div>
                        <label for="manager_user_id" class="col-lg-2 col-xl-1  control-label control-label-custom control-label-custom-right"><span class="label-custom__span-red" data-bind="visible: showAgentDetail()">*</span>代理模式 ：
                        </label>
                        <div class="col-sm-4">
                            <select class="form-control selectpicker" title="请选择代理模式" id="agent_type" name="buy[agent_type]" data-bind="optionsCaption: '请选择代理模式',value:agent_type,valueAllowUnset: true">
                                <option value=''>请选择代理模式</option>
                                <?php foreach ($this->map["buy_agent_type"] as $k => $v) {
                                    echo "<option value='" . $k . "'>" . $v . "</option>";
                                } ?>
                            </select>
                        </div>
                    </div>
    
                    <div class="form-group form-group-custom form-group " data-bind="visible: !showOnlyUpPartner()">
                        <label for="type" class="col-lg-2 col-xl-1  control-label control-label-custom">
                          <span class="label-custom__span-red">*</span>下游合作方 ：
                        </label>
                        <div class="col-sm-4">
                            <select class="form-control selectpicker" title="请选择下游合作方" id="down_partner_id" name="sell[down_partner_id]" data-live-search="true" data-bind="optionsCaption: '请选择下游合作方',value: down_partner_id,valueAllowUnset: true">
                                <?php
                                $downPartners = PartnerService::getDownPartners();
                                foreach ($downPartners as $v) {
                                    echo "<option value='" . $v["partner_id"] . "'>" . $v["name"] . "</option>";
                                } ?>
                            </select>
                        </div>
                    </div>
    
                    <div class="form-group form-group-custom " data-bind="visible: !showOnlyDownPartner()">
                        <label for="corporation_id" class="col-lg-2 col-xl-1  control-label control-label-custom">
                        <span class="label-custom__span-red">*</span>采购币种 ：
                        </label>
                        <div class="col-sm-4">
                            <select class="form-control selectpicker" title="请选择采购币种" id="purchase_currency" name="buy[currency]" data-bind="optionsCaption: '请选择采购币种',value:purchase_currency,valueAllowUnset: true">
                                <?php foreach ($this->map["currency_type"] as $k => $v) {
                                    echo "<option value='" . $k . "'>" . $v . "</option>";
                                } ?>
                            </select>
                        </div>
                        <label class="col-lg-2 col-xl-1  control-label control-label-custom control-label-custom-right"><span class="label-custom__span-red">*</span>即期汇率 ：
                        </label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="buy_exchange_rate" name="buy[exchange_rate]" placeholder="采购币种即期汇率" data-bind="value:buy_exchange_rate">
                        </div>
                    </div>
    
                    <div class="form-group form-group-custom " data-bind="visible: !showOnlyUpPartner()">
                        <label for="corporation_id" class="col-lg-2 col-xl-1  control-label control-label-custom"><span class="label-custom__span-red">*</span>销售币种 ：
                          <span class="text-red fa fa-asterisk"></span>
                        </label>
                        <div class="col-sm-4">
                            <select class="form-control selectpicker" title="请选择销售币种" id="sell_currency" name="sell[currency]" data-bind="optionsCaption: '请选择销售币种',value:sell_currency,valueAllowUnset: true">
                                <?php foreach ($this->map["currency_type"] as $k => $v) {
                                    echo "<option value='" . $k . "'>" . $v . "</option>";
                                } ?>
                            </select>
                        </div>
                        <label for="manager_user_id" class="col-lg-2 col-xl-1  control-label control-label-custom control-label-custom-right"><span class="label-custom__span-red">*</span>即期汇率 ：
                          <span class="text-red fa fa-asterisk"></span>
                        </label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="sell_exchange_rate" name="sell[exchange_rate]" placeholder="销售币种即期汇率" data-bind="value:sell_exchange_rate">
                        </div>
                    </div>
    
                    <div class="form-group form-group-custom form-group " data-bind="visible: !showOnlyDownPartner()">
                        <label for="type" class="col-lg-2 col-xl-1  control-label control-label-custom">
                          <span class="label-custom__span-red">*</span>采购价格方式 ：
                        </label>
                        <div class="col-sm-4">
                            <select class="form-control selectpicker" title="请选择价格方式" id="buy_price_type" name="obj[buy_price_type]" data-bind="optionsCaption: '请选择采购价格方式',value:buy_price_type,valueAllowUnset: true">
                                <option value=''>请选择采购价格方式</option>
                                <?php foreach ($this->map["price_type"] as $k => $v) {
                                    echo "<option value='" . $k . "'>" . $v . "</option>";
                                } ?>
                            </select>
                        </div>
                    </div>
    
                    <div class="form-group form-group-custom form-group " data-bind="visible: !showOnlyUpPartner()">
                        <label for="type" class="col-lg-2 col-xl-1  control-label control-label-custom">
                          <span class="label-custom__span-red">*</span>销售价格方式 ：
                        </label>
                        <div class="col-sm-4">
                            <select class="form-control selectpicker" title="请选择价格方式" id="sell_price_type" name="obj[sell_price_type]" data-bind="optionsCaption: '请选择销售价格方式',value:sell_price_type,valueAllowUnset: true">
                                <option value=''>请选择销售价格方式</option>
                                <?php foreach ($this->map["price_type"] as $k => $v) {
                                    echo "<option value='" . $k . "'>" . $v . "</option>";
                                } ?>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- 项目信息 -->

        <!-- 交易明细 -->
        <div class="box box-primary sub-container__box">
            <div class="box-header with-border box-content-title">
                <h3 class="box-title">&nbsp;&nbsp;&nbsp;交易明细</h3>
            </div>
            <div class="box-header  box-content-custom" data-bind="visible: !showOnlyDownPartner()">
                <span class="box-content__company-style">&nbsp;&nbsp;采购合同</span>
                <div class="responser">
                    <label class="box-content__charger">
                      <span class="label-custom__span-red">*</span>负责人 ：
                    </label>
                    <select class="box-content__custom-select selectpicker" title="请选择负责人" id="buy_manager_user_id" data-live-search="true" name="buy[manager_user_id]" data-bind="optionsCaption: '请选择负责人',value: buy_manager_user_id,valueAllowUnset: true">
                         <option value=''>请选择负责人</option>
                         <?php
                         $users = UserService::getProjectManageUsers();
                         foreach ($users as $v) {
                             echo "<option value='" . $v["user_id"] . "'>" . $v["name"] . "</option>";
                         } ?>
                    </select>
                 </div>
            
                <!-- <span class="box-content__buy-amount">采购总价 : 24000.00万元</span> -->
                <div class="box-body  box-content-custom">
                <!-- ko component: {
                     name: "contract-goods",
                     params: {
                                exchange_type:buy_type,
                                price_type:buy_price_type,
                                items:buyItems,
                                allGoods: allGoods,
                                units:units,
                                currencies:currencies,
                                exchange_rate:buy_exchange_rate,
                                currency:purchase_currency
                            }
                } -->
                <!-- /ko -->
                </div>
            </div>

            <span data-bind="visible: !showOnlyDownPartner() &&  buy_formula_status()">
                <div class="line-dot"></div>
                <div class="box-body form-horizontal form-horizontal-custom">
                    <div class="form-group pd-bottom-0">
                        <label for="type" class="col-sm-2 control-label custom-width-10"><span class="label-custom__span-red">*</span>采购计价公式</label>
                        <div class="col-sm-8">
                            <textarea rows="3"  class="form-control" id="buy_formula" name="buy[buy_formula]" placeholder="采购计价公式" data-bind="value:buy_formula"></textarea>
                        </div>
                    </div>
                </div>
            </span>
            
            <div data-bind="visible:isShowUpAndDown"><div class="line-dot"></div></div>
            
            <div class="box-header  box-content-custom" data-bind="visible: !showOnlyUpPartner()">
                <span class="box-content__company-style">&nbsp;&nbsp;销售合同</span>
                <div class="responser">
                    <label class="box-content__charger">
                      <span class="label-custom__span-red">*</span>负责人 ：
                    </label>
                    <select class="box-content__custom-select selectpicker" title="请选择负责人" id="sell_manager_user_id" data-live-search="true" name="sell[manager_user_id]" data-bind="optionsCaption: '请选择负责人',value: sell_manager_user_id,valueAllowUnset: true"> <!--  -->
                        <option value=''>请选择负责人</option>
                        <?php
                        $users = UserService::getProjectManageUsers();
                        foreach ($users as $v) {
                            echo "<option value='" . $v["user_id"] . "'>" . $v["name"] . "</option>";
                        } ?>
                    </select>
                </div>
            
                <!-- <span class="box-content__buy-amount">采购总价 : 24000.00万元</span> -->
    
                <div class="box-body  box-content-custom">
                <!-- ko component: {
                    name: "contract-goods",
                    params: {
                               exchange_type:sell_type,
                               price_type:sell_price_type,
                               items:sellItems,
                               allGoods: allGoods,
                               units:units,
                               currencies:currencies,
                               exchange_rate:sell_exchange_rate,
                               currency:sell_currency
                           }
                } -->
                <!-- /ko -->
                </div>
            </div>
            
            <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/contractGoods.php"; ?>
            <span data-bind="visible: !showOnlyUpPartner() &&  sell_formula_status()">
                <div class="line-dot"></div>
            
                <div class="box-body form-horizontal form-horizontal-custom">
                    <div class="form-group pd-bottom-0">
                      <label for="type" class="col-sm-2 control-label custom-width-10"><span class="label-custom__span-red">*</span>销售计价公式</label>
                      <div class="col-sm-8">
                          <textarea rows="3"  class="form-control" id="sell_formula" name="sell[sell_formula]" placeholder="销售计价公式" data-bind="value:sell_formula"></textarea>
                      </div>
                    </div>
                </div>
            </span>
        </div>
        <!-- 交易明细 -->

        <!-- 代理费明细 -->
        <div class="box box-primary sub-container__box" data-bind="if: showAgentDetail()">
            <div class="box-header with-border box-content-title">
              <h3 class="box-title">&nbsp;&nbsp;&nbsp;手续费明细</h3>
            </div>
            <div class="box-header  box-content-custom">
              <span class="box-content__company-style">&nbsp;代理手续费</span>
            </div>
            <div class="box-body  box-content-custom">
                <table class="table table-hover table-hover-custom">
                    <thead>
                        <tr>
                            <th style="width:130px;text-align:center">品名</th>
                            <th style="width:150px;text-align:center"><span class="label-custom__span-red">*</span>计费方式</th>
                            <th style="width:150px;text-align:center"><span class="label-custom__span-red">*</span>计费单价</th>
                            <th style="width:100px;text-align:center"><span class="label-custom__span-red">*</span>计费单位</th>
                            <th style="width:130px;text-align:center"><span class="label-custom__span-red">*</span>代理手续费率</th>
                            <th style="width:130px;text-align:center">代理手续费</th>
                        </tr>
                    </thead>
                    <tbody data-bind="foreach: buyItems">
                        <tr>
                            <td style="text-align:left;vertical-align: middle!important;">
                                <span data-bind="text:goods_name"></span>
                                <input hidden name='agent_goods_id' data-bind="value:goods_id"/>
                            </td>
                            <td style="text-align:center">
                                <select class="form-control input-sm" title="请选择计费方式" name="type" data-bind="optionsCaption: '请选择计费方式',value:type,valueAllowUnset: true">
                                    <option value=''>请选择计费方式</option>
                                    <?php foreach($this->map["agent_fee_pay_type"] as $k=>$v)
                                    {
                                        echo "<option value='".$k."'>".$v."</option>";
                                    }?>
                                </select>
                            </td>
                            <td style="text-align:center">
                                <div class="input-group  input-group-sm">
                                    <span class="input-group-addon">￥</span>
                                    <input type="text" class="form-control input-sm" name= "agent_price" placeholder="计费单价" data-bind="money:agent_price, disable:isRateAgentPriceDisable">
                                </div>
                            </td>
                            <td style="text-align:center">
                                <select class="form-control input-sm" title="请选择计费单位" name="agnet_unit" data-bind="value:unit,valueAllowUnset: true, disable:isAgentunitDisable">
                                    <?php foreach($this->map["goods_unit"] as $k=>$v)
                                    {
                                        echo "<option value='".$v["id"]."'>".$v["name"]."</option>";
                                    }?>
                                </select>
                            </td>
                            <td style="">
                                <div class="input-group  input-group-sm">
                                    <input type="text" class="form-control"  name= "fee_rate" placeholder="代理手续费率" data-bind="percent:fee_rate, disable:isRateDisable" >
                                    <span class="input-group-addon">%</span>
                                </div>
                            </td>
                            <td style="text-align:right;vertical-align: middle!important;">
                                    ￥ <span data-bind="moneyText:agent_amount"></span>
                                    <input hidden name='agent_amount' data-bind="value:agent_amount"/>                                        
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- 代理费明细 -->

        <!-- 收付款明细 -->
        <div class="box box-primary sub-container__box">
            <div class="box-header with-border box-content-title">
                <h3 class="box-title">&nbsp;&nbsp;&nbsp;收付款明细</h3>
            </div>
                <span data-bind="if: !showOnlyDownPartner()">
                    <div class="box-header  box-content-custom">
                        <span class="box-content__company-style">&nbsp;&nbsp;上游付款计划</span>
                    </div>
                
                    <div class="box-body  box-content-custom">
                    <!-- ko component: {
                         name: "payments",
                         params: {
                                    type:buy_type,
                                    items:paymentItems,
                                    paymentTypes:paymentTypes,
                                    currencies:currencies,
                                    exchange_rate:buy_exchange_rate
                                }
                     } -->
                    <!-- /ko -->
                    </div>
            </span>
            <div class="line-dot"></div>
            <span data-bind="if: !showOnlyUpPartner()">
                <div class="box-header  box-content-custom">
                    <span class="box-content__company-style">&nbsp;下游收款计划</span>
                </div>
                <div class="box-body  box-content-custom">
                <!-- ko component: {
                     name: "payments",
                     params: {
                                type:sell_type,
                                items:proceedItems,
                                paymentTypes:proceedTypes,
                                currencies:currencies,
                                exchange_rate:sell_exchange_rate
                            }
                 } -->
                <!-- /ko -->
                </div>
                <div class="line-dot"></div>
            </span>
            <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/paymentItems.php"; ?>
        </div>
        <!-- 收付款明细 -->
        <!-- 上游交货日期与交票期限 -->
        <div class="box box-primary sub-container__box">
            <div class="box-header with-border box-content-title">
                <h3 class="box-title">&nbsp;&nbsp;&nbsp;最终交货/发货日期</h3>
            </div>
            <form class="form-horizontal" role="form" id="mainForm">
                <div class="box-body box-contract-extra">
                    <div class="col-md-6 bd-right-2"  data-bind="visible: !showOnlyDownPartner()">
                        <div class="contract-type-container">
                            <div class="contract-type__circle">上游</div>
                        </div>
                        <div class="form-group form-group-custom form-group ">
                            <label for="type" class="col-lg-2 col-xl-2  control-label control-label-custom">
                                <span class="label-custom__span-red">*</span>最终交货日期
                            </label>
                            <!-- 系统默认 -->
                            <div class="col-lg-6" data-bind="visible: upDeliveryTermDefault()">
                                <input type="text" class="form-control input-sm" id="up_delivery_term1" disabled name="buy[up_delivery_term]" placeholder="最终交货日期" data-bind="value:up_delivery_term">
                            </div>
                            <!-- 合同约定 -->
                            <div class="col-lg-6" data-bind="visible: !upDeliveryTermDefault()">
                                <input type="text" class="form-control input-sm date" id="up_delivery_term" name="buy[up_delivery_term]" placeholder="最终交货日期" data-bind="value:up_delivery_term">
                            </div>
                            <div class="col-lg-4">
                                <select class="form-control selectpicker" title="方式" id="purchase_currency" name="buy[delivery_mode]" data-bind="optionsCaption: '请选择',value:up_delivery_mode,valueAllowUnset: true">
                                    <?php foreach ($this->map["contract_delivery_mode"] as $k => $v) {
                                        echo "<option value='" . $k . "'>" . $v . "</option>";
                                    } ?>
                                </select>
                            </div>

                        </div>
                        <div class="form-group form-group-custom form-group ">
                            <label for="type" class="col-lg-2 col-xl-2  control-label control-label-custom">
                                <span class="label-custom__span-red">*</span>收票时间

                            </label>
                            <div class="col-lg-10">
                                <div class="input-group  input-group-sm">
                                    <input type="text" class="form-control" id="up_days" name="buy[up_days]" placeholder="收票时间" data-bind="value:up_days">
                                    <span class="input-group-addon">天</span>

                                </div>
                                <p style="font-size: 8px;">（根据入库单日期倒推）</p>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6 bd-right-2" data-bind="visible: !showOnlyUpPartner()">
                        <div class="contract-type-container">
                            <div class="contract-type__circle contract-type__circle-right">下游</div>
                        </div>
                        <div class="form-group form-group-custom form-group ">
                            <label for="type" class="col-lg-2 col-xl-2  control-label control-label-custom">
                                <span class="label-custom__span-red">*</span>最终发货日期
                            </label>
                            <!-- 系统默认 -->
                            <div class="col-lg-6" data-bind="visible: downDeliveryTermDefault()">
                                <input type="text" class="form-control input-sm" id="down_delivery_term" disabled name="buy[down_delivery_term]" placeholder="最终发货日期" data-bind="value:down_delivery_term">
                            </div>
                            <!-- 合同约定 -->
                            <div class="col-lg-6" data-bind="visible: !downDeliveryTermDefault()">
                                <input type="text" class="form-control input-sm date" id="down_delivery_term" name="buy[down_delivery_term]" placeholder="最终发货日期" data-bind="value:down_delivery_term">
                            </div>
                            <div class="col-lg-4">
                                <select class="form-control selectpicker" title="方式" id="purchase_currency" name="buy[delivery_mode]" data-bind="optionsCaption: '请选择',value:down_delivery_mode,valueAllowUnset: true">
                                    <?php foreach ($this->map["contract_delivery_mode"] as $k => $v) {
                                        echo "<option value='" . $k . "'>" . $v . "</option>";
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group form-group-custom form-group ">
                            <label for="type" class="col-lg-2 col-xl-2  control-label control-label-custom">
                                <span class="label-custom__span-red">*</span>开票时间
                            </label>
                            <div class="col-lg-10">
                                <div class="input-group  input-group-sm">
                                    <input type="text" class="form-control" id="down_days" name="buy[down_days]" placeholder="开票时间" data-bind="value:down_days">
                                    <span class="input-group-addon">天</span>
                                </div>
                                <p style="font-size: 8px;">（根据出库单日期倒推）</p>
                            </div>
                        </div>

                    </div>
                </div>
            </form>

        </div>
        <!-- 上游交货日期与交票期限 -->
        <!-- 合同条款 -->
        <div class="box box-primary sub-container__box">
            <div class="box-header with-border box-content-title">
              <h3 class="box-title">&nbsp;&nbsp;&nbsp;合同条款</h3>
            </div>
            <form class="form-horizontal" role="form" id="mainForm">
                <div class="box-body box-contract-extra">
                    <span data-bind="if:isShowUpAndDown()">
                        <div class="col-md-6 bd-right-2">
                            <div class="contract-type-container">
                              <div class="contract-type__circle">上游</div>
                            </div>
                            <div class="box-body box-body-custom">
                                <!-- ko component: {
                                            name: 'contract-items',
                                            params: {
                                                        type: buy_type,
                                                        category: buy_category,
                                                        config:upContractItems
                                                    }
                                        } -->
                                <!-- /ko -->
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="contract-type-container">
                                <div class="contract-type__circle contract-type__circle-right">下游</div>
                            </div>
                            <div class="box-body box-body-custom">
                                <!-- ko component: {
                                            name: 'contract-items',
                                            params: {
                                                        type: sell_type,
                                                        category: sell_category,
                                                        config:downContractItems
                                                    }
                                        } -->
                                <!-- /ko -->
                            </div>
                        </div>
                    </span>
                    <span data-bind="if:!isShowUpAndDown()">
                        <div class="col-md-6 bd-right-2">
                            <div class="contract-type-container">
                              <div class="contract-type__circle"><span data-bind="visible:showOnlyUpPartner">上游</span><span data-bind="visible:showOnlyDownPartner">下游</span></div>
                            </div>
                            <div class="box-body box-body-custom">
                                <!-- ko component: {
                                    name: 'contract-items',
                                            params: {
                                                        type:  contract_type,
                                                        category: contract_category,
                                                        config:contractItems,
                                                        labelWidth:2,
                                                        controlWidth:10
                                                    }
                                        } -->
                                <!-- /ko -->
                            </div>
                        </div>
                    </span>
                    <?php include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/contractItems.php";?>
                </div>
            </form>
        </div>
        <!-- 合同条款 -->

        <!-- 驳回合同 -->
        <div class="box box-primary sub-container__box" data-bind="visible:isBack">
            <div class="box-header with-border box-content-title">
              <h3 class="box-title">&nbsp;&nbsp;&nbsp;驳回明细</h3>
            </div>
            <form class="form-horizontal" role="form" id="mainForm">
                <div class="box-body form-horizontal form-horizontal-custom">
                    <div class="form-group pd-bottom-0">
                        <label for="type" class="col-sm-2 control-label custom-width-10"><span class="label-custom__span-red">*</span>驳回说明</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" id="back_remark" name= "obj[back_remark]" rows="3" placeholder="驳回说明" data-bind="value:back_remark"></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- 驳回合同 -->

        <!-- 提交保存 -->
        <div class="box box-primary sub-container__box sub-container__fixed">
            <div class="box-body">
                <div class="form-group form-group-custom-btn">
                  <!-- 此处删除了类：col-sm-offset-2 col-sm-10  增加了类submit-btn-custom-->
                  <div class="btn-contain-custom">
                    <button type="button" class="btn btn-contain__save" data-bind="click:startBack,visible:is_can_back()==1 && !isBack()">驳回</button>
                    <button type="button" class="btn btn-contain__submit" data-bind="click:tempSave,html:tempSaveBtnText,visible:!isBack()"></button>
                    <button type="button" class="btn btn-contain__save" data-bind="click:save, html:saveBtnText,visible:!isBack()"></button>
                    <!-- <button type="button" class="btn btn-warning btn-contain__default " >保存</button> -->
                    <button type="button" class="btn btn-contain__default history-back" data-bind="click:back">返回</button>
                    <span data-bind="visible:isBack">
                        <button type="button" class="btn btn-warning" data-bind="click:saveBack, html:saveBackBtnText"></button>
                        <button type="button" class="btn btn-primary" data-bind="click:cancelBack">取消驳回</button>
                    </span>
                    <!-- <button type="button" class="btn btn-warning" data-bind="click:startBack,visible:is_can_back()==1 && !isBack()">驳回</button>
                    <button type="button" id="tempSaveButton" class="btn btn-primary" data-bind="click:tempSave,html:tempSaveBtnText,visible:!isBack()"></button>
                    <button type="button" id="saveButton" class="btn btn-warning" data-bind="click:save, html:saveBtnText,visible:!isBack()"></button> -->
                    <!-- <button type="button" class="btn btn-default history-back" data-bind="click:back">返回</button> -->
                    <input type='hidden' name='obj[project_type]' data-bind="value:project_type"/>
                    <input type='hidden' name='obj[project_id]' data-bind="value:project_id"/>
                    <input type='hidden' name='obj[project_status]' data-bind="value:project_status"/>
                    <input type='hidden' name='obj[contract_status]' data-bind="value:contract_status"/>
                    <input type='hidden' name='obj[buy_sell_type]' data-bind="value:buy_sell_type"/>
                    <input type='hidden' name='obj[buy_contract_id]' data-bind="value:buy_contract_id"/>
                    <input type='hidden' name='obj[sell_contract_id]' data-bind="value:sell_contract_id"/>
                  </div>
                </div>
            </div>
        </div>
        <!-- 提交保存 -->

    </section>

<script>
    var view;
    $(function () {
        view = new ViewModel(<?php echo json_encode($data) ?>);
        view.formatGoods(<?php echo json_encode($goods) ?>);
        view.units = inc.objectToArray(<?php echo json_encode(array_values($this->map["goods_unit"])); ?>);
        view.formatCurrencies(<?php echo json_encode($this->map["currency"]); ?>);
        view.formatPaymentTypes(<?php echo json_encode($this->map["pay_type"]); ?>);
        view.formatProceedTypes(<?php echo json_encode($this->map["proceed_type"]); ?>);
        view.formatBuyItems(<?php echo json_encode($buyItems) ?>);
        view.formatSellItems(<?php echo json_encode($sellItems) ?>);
        view.formatPaymentItems(<?php echo json_encode($payments) ?>);
        view.formatProceedItems(<?php echo json_encode($proceeds) ?>);
        view.generatePayment();
        view.generateProceed();
        ko.applyBindings(view);
        $('.date').datetimepicker({format: 'yyyy-mm-dd', minView: 'month'});

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
    function ViewModel(option) {
        var defaults = {
            project_id: 0,
            project_type:'',
            buy_type: 1,
            sell_type: 2,
            buy_category: 0,
            sell_category: 4,
            agent_id: 0,
            agent_type: '',
            buy_sell_type: 0,
            up_partner_id: 0,
            down_partner_id: 0,
            corporation_id: 0,
            price_type: 0,
            purchase_currency: '',
            sell_currency: '',
            manager_user_id: 0,
            plan_describe: '',
            storehouse_id: 0,
            sell_exchange_rate: 1,
            buy_exchange_rate: 1,
            buy_formula: '',
            sell_formula: '',
            sell_price_type: '',
            buy_price_type: '',
            buy_manager_user_id: 1,
            sell_manager_user_id: 1,
            remark: '',
            project_status: '',
            contract_status: '',
            buy_contract_id: 0,
            sell_contract_id: 0,
            is_can_back:0,
            up_delivery_term:"<?php echo date("Y-m-d",strtotime("+".ConstantMap::CONTRACT_DEFAULT_DELIVERY_TERM." day")); ?>",
            down_delivery_term:"<?php echo date("Y-m-d",strtotime("+".ConstantMap::CONTRACT_DEFAULT_DELIVERY_TERM." day")); ?>",
        };
        var o = $.extend(defaults, option);
        var self = this;
        self.project_id = ko.observable(o.project_id);
        self.buy_contract_id = ko.observable(o.buy_contract_id);
        self.sell_contract_id = ko.observable(o.sell_contract_id);
        self.project_status = ko.observable(o.project_status);
        self.contract_status = ko.observable(o.contract_status);
        self.project_type = ko.observable(o.project_type);
        self.buy_sell_type = ko.observable(o.buy_sell_type);
        self.buy_type = ko.observable(o.buy_type);
        self.sell_type = ko.observable(o.sell_type);
        self.buy_category = ko.observable(o.buy_category);
        self.sell_category = ko.observable(o.sell_category);
        self.storehouse_id = ko.observable(o.storehouse_id);

        //仓库名称展示条件
        self.showStorehouse = ko.computed(function () {
            if ($.inArray(parseInt(self.project_type()), config.projectTypeWarehouseReceipt) >= 0)
                return true;
            return false;
        }, self);

        //采购合同类型下拉展示条件
        self.showBuyContractSelectType = ko.computed(function () {
            if ($.inArray(parseInt(self.project_type()), config.buyContractSelectType) >= 0 || (parseInt(self.project_type())==config.projectTypeSelfImport && parseInt(self.buy_sell_type())==config.firstBuyLastSale)){
                if(self.buy_category()==0)
                    self.buy_category(parseInt(config.buySaleContractTypeAgentImport));
                return true;
            }
            return false;
        }, self);
        //采购合同类型静态展示条件
        self.showBuyContractStaticType = ko.computed(function () {
            if ($.inArray(parseInt(self.project_type()), config.buyContractStaticType) >= 0 || (parseInt(self.project_type())==config.projectTypeSelfInternalTrade && parseInt(self.buy_sell_type())==config.firstBuyLastSale)){
                self.buy_category(config.buySaleContractTypeInternal);
                return true;
            }
            return false;
        }, self);

        //销售合同类型静态展示条件
        self.showSellContractStaticType = ko.computed(function () {
            if ($.inArray(parseInt(self.project_type()), config.buyContractSelectType.concat(config.buyContractStaticType)) >= 0 || ($.inArray(parseInt(self.project_type()), config.projectTypeSelfSupport) >= 0 && parseInt(self.buy_sell_type()) == config.firstSaleLastBuy))
                return true;
            return false;
        }, self);

        //仅展示上游合作方条件
        self.showOnlyUpPartner = ko.computed(function () {
            if ($.inArray(parseInt(self.project_type()), config.projectTypeSelfSupport) >= 0 && parseInt(self.buy_sell_type()) == config.firstBuyLastSale)
                return true;
            return false;
        }, self);


        //仅展示下游合作方条件
        self.showOnlyDownPartner = ko.computed(function () {
            if ($.inArray(parseInt(self.project_type()), config.projectTypeSelfSupport) >= 0 && parseInt(self.buy_sell_type()) == config.firstSaleLastBuy)
                return true;
            return false;
        }, self);



        //代理商展示条件
        self.isShowAgent = ko.observable(false);
        if(($.inArray(parseInt(self.project_type()), config.buyContractSelectType) >= 0 || (parseInt(self.project_type()) == config.projectTypeSelfImport && parseInt(self.buy_sell_type()) == config.firstBuyLastSale)) && parseInt(self.buy_category()) == config.buySaleContractTypeAgentImport){
            self.isShowAgent(true);
        }

        self.buy_category.subscribe(function(v){
            if(parseInt(v)==config.buySaleContractTypeAgentImport)
                self.isShowAgent(true);
            else
                self.isShowAgent(false);
        });
        self.agent_id = ko.observable(o.agent_id);

        self.agent_type = ko.observable(o.agent_type).extend({
            custom: {
                params: function (v) {
                    if (self.isShowAgent() && self.agent_id() > 0) {
                        if (v > 0 )
                            return true;
                        else
                            return false;
                    }
                    return true;
                },
                message: "请选择采购代理商"
            }
        });

        self.agent_id.subscribe(function(v){
            if(v=='')
                self.agent_type();
        });

        //代理商明细展示条件
        self.showAgentDetail = ko.computed(function () {
            if (self.isShowAgent() && self.agent_id()>0)
                return true;
            return false;
        }, self);


        self.up_partner_id = ko.observable(o.up_partner_id).extend({
            custom: {
                params: function (v) {
                    self.msg = '请选择上游合作方';
                    if (!self.showOnlyDownPartner()) {
                        if (v > 0) {
                            if (!self.showOnlyUpPartner() && v == ko.unwrap(self.down_partner_id)) {
                                self.msg = '上下游合作方不能重复'
                                return false;
                            } else {
                                return true;
                            }
                        }
                        else
                            return false;
                    }
                    return true;
                },
                message: function () {
                    return self.msg;
                }
            }
        });

        self.down_partner_id = ko.observable(o.down_partner_id).extend({
            custom: {
                params: function (v) {
                    self.msg = '请选择下游合作方';
                    if (!self.showOnlyUpPartner()) {
                        if (v > 0){
                            if (!self.showOnlyDownPartner() && v == ko.unwrap(self.up_partner_id)) {
                                self.msg = '上下游合作方不能重复'
                                return false;
                            } else {
                                return true;
                            }
                        }
                        else
                            return false;
                    }
                    return true;
                },
                message: function () {
                    return self.msg;
                }
            }
        });
        self.corporation_id = ko.observable(o.corporation_id).extend({
            custom: {
                params: function (v) {
                    if (v > 0)
                        return true;
                    else
                        return false;
                },
                message: "请选择交易主体"
            }
        });

        self.buy_price_type = ko.observable(o.buy_price_type).extend({
            custom: {
                params: function (v) {
                    if (!self.showOnlyDownPartner()) {
                        if (v > 0)
                            return true;
                        else
                            return false;
                    }
                    return true;
                },
                message: "请选择采购价格方式"
            }
        });

        self.sell_price_type = ko.observable(o.sell_price_type).extend({
            custom: {
                params: function (v) {
                    if (!self.showOnlyUpPartner()) {
                        if (v > 0)
                            return true;
                        else
                            return false;
                    }
                    return true;
                },
                message: "请选择销售价格方式"
            }
        });
        
        self.buy_manager_user_id = ko.observable(o.buy_manager_user_id).extend({
            custom: {
                params: function (v) {
                    if (!self.showOnlyDownPartner()) {
                        if (v > 0)
                            return true;
                        else
                            return false;
                    }
                    return true;
                },
                message: "请选择采购项目负责人"
            }
        });
        self.sell_manager_user_id = ko.observable(o.sell_manager_user_id).extend({
            custom: {
                params: function (v) {
                    if (!self.showOnlyUpPartner()) {
                        if (v > 0)
                            return true;
                        else
                            return false;
                    }
                    return true;
                },
                message: "请选择销售项目负责人"
            }
        });

        self.purchase_currency = ko.observable(o.purchase_currency);
        self.sell_currency = ko.observable(o.sell_currency);
        self.contractGoodsUnitConvert = ko.observable(o.contractGoodsUnitConvert);
        self.contractGoodsUnitConvertValue = ko.observable(o.contractGoodsUnitConvertValue);
        self.buy_exchange_rate = ko.observable(parseFloat(o.buy_exchange_rate)).extend({
            custom: {
                params: function (v) {
                    if (!self.showOnlyDownPartner()) {
                        if (v != '')
                            return true;
                        else
                            return false;
                    }
                    return true;
                },
                message: "采购币种即期汇率不得为空"
            }
        });
        self.sell_exchange_rate = ko.observable(parseFloat(o.sell_exchange_rate)).extend({
            custom: {
                params: function (v) {
                    if (!self.showOnlyUpPartner()) {
                        if (v != '')
                            return true;
                        else
                            return false;
                    }
                    return true;
                },
                message: "销售币种即期汇率不得为空"
            }
        });

        self.buy_formula = ko.observable(o.buy_formula).extend({
            custom: {
                params: function (v) {
                    if (!self.showOnlyDownPartner()) {
                        if (v != '' || parseInt(self.buy_price_type())==config.staticPrice)
                            return true;
                        else
                            return false;
                    }
                    return true;
                },
                message: "采购计价公式不得为空"
            }
        });

        self.sell_formula = ko.observable(o.sell_formula).extend({
            custom: {
                params: function (v) {
                    if (!self.showOnlyUpPartner()) {
                        if (v != '' || parseInt(self.sell_price_type())==config.staticPrice)
                            return true;
                        else
                            return false;
                    }
                    return true;
                },
                message: "销售计价公式不得为空"
            }
        });

        self.contract_default_delivery_term = ko.observable(o.contract_default_delivery_term);

        self.up_delivery_mode = ko.observable(o.up_delivery_mode);

        self.up_delivery_mode.subscribe(function (v) {
            if(v==0) {
                self.up_delivery_term((new Date(new Date().getTime() + self.contract_default_delivery_term()*24*60*60*1000)).Format("yyyy-MM-dd"));
            }
            else
                self.up_delivery_term('');
        });
        self.up_days = ko.observable(o.up_days).extend({
            custom: {
                params: function (v) {
                    if (v=='' || v==null || parseInt(v) <0 || isNaN(v))
                        return false;
                    else
                        return true;
                },
                message: "请输入一个不小于0的数字"
            }
        });

        self.up_delivery_term = ko.observable(o.up_delivery_term).extend({
            custom: {
                params: function (v) {
                    if (v=="" || v==null || v.match(/^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2})$/)==null)
                        return false;
                    else
                        return true;
                },
                message: "请填写正确的日期"
            }
        });

        self.down_delivery_mode = ko.observable(o.down_delivery_mode);
        self.down_delivery_mode.subscribe(function (v) {
            if(v==0) {
                self.down_delivery_term((new Date(new Date().getTime() + self.contract_default_delivery_term()*24*60*60*1000)).Format("yyyy-MM-dd"));
            }
            else
                self.down_delivery_term('');
        });
        self.down_delivery_term = ko.observable(o.down_delivery_term).extend({
            custom: {
                params: function (v) {
                    if (v=='' || v==null || v.match(/^(\d{1,4})(-|\/)(\d{1,2})\2(\d{1,2})$/)==null)
                        return false;
                    else
                        return true;
                },
                message: "请填写正确的日期"
            }
        });

        self.down_days = ko.observable(o.down_days).extend({
            custom: {
                params: function (v) {
                    if (v=='' || v==null || parseInt(v) <0 || isNaN(v))
                        return false;
                    else
                        return true;
                },
                message: "请输入一个不小于0的数字"
            }
        });

        //最终交货日期：方式
        self.upDeliveryTermDefault = ko.computed(function () {
            if (self.up_delivery_mode() != 1)//系统默认
                return true;
            return false;
        }, self);
        //最终发货日期：方式
        self.downDeliveryTermDefault = ko.computed(function () {
            if (self.down_delivery_mode() == 0)
                return true;
            return false;
        }, self);

        self.buy_formula_status = ko.observable(false);
        self.sell_formula_status= ko.observable(false);

        if(self.buy_price_type()==config.tempPrice)
            self.buy_formula_status(true);
        if(self.sell_price_type()==config.tempPrice)
            self.sell_formula_status(true);
        self.buy_price_type.subscribe(function(v){
            if(parseInt(v)==config.tempPrice)
                self.buy_formula_status(true);
            else
                self.buy_formula_status(false);
        });

        self.sell_price_type.subscribe(function(v){
            if(parseInt(v)==config.tempPrice)
                self.sell_formula_status(true);
            else
                self.sell_formula_status(false);
        });

        self.fileUploadStatus=ko.observable();
        self.allGoods = ko.observableArray();
        self.buyItems = ko.observableArray();
        self.sellItems = ko.observableArray();
        self.paymentItems = ko.observableArray();
        self.proceedItems = ko.observableArray();
        self.units = [];
        self.currencies = ko.observableArray();
        self.paymentTypes = ko.observableArray();
        self.proceedTypes = ko.observableArray();

        self.formatGoods = function (data) {
            if (data == null || data == undefined)
                return;

            for (var i = 0; i < data.length; i++) {
                self.allGoods().push(data[i]);
            }
        }

        self.formatBuyItems = function (data) {
            if (data == null || data == undefined)
                return;
            for (var i in data) {
                data[i]['currencies'] = self.currencies;
                data[i]['allGoods'] = self.allGoods;
                data[i]['exchange_type'] = self.buy_type();
                if (data[i]['type'] == config.agentFeeCalculateByAmount) {
                    data[i]['isRateDisable'] = true;
                    data[i]['isRateAgentPriceDisable'] = false;
                    data[i]['isAgentunitDisable'] = false;
                } else if (data[i]['type'] == config.agentFeeCalculateByPrice) {
                    data[i]['isRateDisable'] = false;
                    data[i]['isRateAgentPriceDisable'] = true;
                    data[i]['isAgentunitDisable'] = true;
                }
                var obj = new ContractGoods(data[i]);
                self.buyItems().push(obj);
            }
        }

        self.formatSellItems = function (data) {
            if (data == null || data == undefined)
                return;
            for (var i in data) {
                data[i]['currencies'] = self.currencies;
                data[i]['allGoods'] = self.allGoods;
                data[i]['exchange_type'] = self.sell_type();
                if (data[i]['type'] == config.agentFeeCalculateByAmount) {
                    data[i]['isRateDisable'] = true;
                    data[i]['isRateAgentPriceDisable'] = false;
                    data[i]['isAgentunitDisable'] = false;
                } else if (data[i]['type'] == config.agentFeeCalculateByPrice) {
                    data[i]['isRateDisable'] = false;
                    data[i]['isRateAgentPriceDisable'] = true;
                    data[i]['isAgentunitDisable'] = true;
                }

                var obj = new ContractGoods(data[i]);
                self.sellItems().push(obj);
            }
        }

        self.formatPaymentTypes = function (data) {
            if (data == null || data == undefined)
                return;
            for (var i in data) {
                self.paymentTypes.push(data[i]);
            }
        }

        self.formatPaymentItems = function (data) {
            if (data == null || data == undefined)
                return;
            for (var i in data) {
                data[i]['currencies'] = self.currencies();
                data[i]['paymentTypes'] = self.paymentTypes();
                var obj = new Payments(data[i]);
                self.paymentItems().push(obj);
            }
        }

        if(!self.showOnlyDownPartner()){
            self.generatePayment = function () {
                if (self.paymentItems().length == 0) {
                    self.paymentItems.push(new Payments({
                        currencies:self.currencies(),
                        paymentTypes:self.paymentTypes(),
                        exchange_rate: self.buy_exchange_rate(),
                        type:self.buy_type()
                    }));
                }
            }
        }else{
            // self.paymentItems=ko.observable();
            self.generatePayment = function(){}
        }


        self.formatCurrencies = function (data) {
            if (data == null || data == undefined)
                return;
            for (var i in data) {
                self.currencies.push(data[i]);
            }
        }

        

        self.formatProceedTypes = function (data) {
            if (data == null || data == undefined)
                return;
            for (var i in data) {
                self.proceedTypes.push(data[i]);
            }
        }
        

        self.formatProceedItems = function (data) {
            if (data == null || data == undefined)
                return;
            for (var i in data) {
                data[i]['currencies'] = self.currencies();
                data[i]['paymentTypes'] = self.proceedTypes();
                var obj = new Payments(data[i]);
                self.proceedItems().push(obj);
            }
        }

        if(!self.showOnlyUpPartner()){
            self.generateProceed = function () {
                if (self.proceedItems().length == 0) {
                    self.proceedItems.push(new Payments({
                        currencies:self.currencies(),
                        paymentTypes:self.proceedTypes(),
                        exchange_rate: self.sell_exchange_rate(),
                        type:self.sell_type()
                    }));
                }
            }
        }else{
            // self.proceedItems=ko.observable();
            self.generateProceed = function(){}
        }


        self.isShowUpAndDown = ko.observable(true);
        self.contract_type = ko.observable();
        self.contract_category = ko.observable();

        /*self.contractItems = [];
        self.upContractItems = [];
        self.downContractItems = [];*/


        // self.contractItemsMap = <?php echo json_encode($contractConfig) ?>;
        if($.inArray(parseInt(self.project_type()), config.projectTypeChannelBuy.concat(config.projectTypeWarehouseReceipt))>=0){
            self.upContractItems = <?php echo json_encode($upContractConfig) ?>;
            self.downContractItems = <?php echo json_encode($downContractConfig) ?>;
        }else{
            self.contractItems = <?php echo json_encode($contractConfig) ?>;
            if($.inArray(parseInt(self.project_type()) ,config.projectTypeSelfSupport)>=0 && parseInt(self.buy_sell_type())==config.firstBuyLastSale){
                self.contract_type(self.buy_type());
                self.contract_category(self.buy_category());
            }else{
                self.contract_type(self.sell_type());
                self.contract_category(self.sell_category());
            }

            self.isShowUpAndDown(false);
        }

        self.calculateBuyAmountCurrency = function () {
            var total = 0;
            if(self.buyItems()!=undefined && self.buyItems().length>0){
                ko.utils.arrayForEach(self.buyItems(), function (item) {
                    var value = parseFloat(item.amount());
                    if (!isNaN(value)) {
                        total += value;
                    }
                });
            }
            return total.toFixed(0);
        };

        self.calculateSellAmountCurrency = function () {
            var total = 0;
            if(self.sellItems()!=undefined && self.sellItems().length>0){
                ko.utils.arrayForEach(self.sellItems(), function (item) {
                    var value = parseFloat(item.amount());
                    if (!isNaN(value)) {
                        total += value;
                    }
                });
            }
            return total.toFixed(0);
        };

        self.calculateBuyAmount = function () {
            var total = 0;
            if(self.buyItems()!=undefined && self.buyItems().length>0){
                ko.utils.arrayForEach(self.buyItems(), function (item) {
                    var value = parseFloat(item.amount_cny());
                    if (!isNaN(value)) {
                        total += value;
                    }
                });
            }
            return total.toFixed(0);
        };

        self.calculateSellAmount = function () {
            var total = 0;
            if(self.sellItems()!=undefined && self.sellItems().length>0){
                ko.utils.arrayForEach(self.sellItems(), function (item) {
                    var value = parseFloat(item.amount_cny());
                    if (!isNaN(value)) {
                        total += value;
                    }
                });
            }
            return total.toFixed(0);
        };

        self.calculatePaymentAmount = function () {
            var total = 0;
            if(self.paymentItems()!=undefined && self.paymentItems().length>0){
                ko.utils.arrayForEach(self.paymentItems(), function (item) {
                    var value = parseFloat(item.amount_cny());
                    if (!isNaN(value)) {
                        total += value;
                    }
                });
            }
            return total.toFixed(0);
        }

        self.calculateProceedAmount = function () {
            var total = 0;
            if(self.proceedItems()!=undefined && self.proceedItems().length>0){
                ko.utils.arrayForEach(self.proceedItems(), function (item) {
                    var value = parseFloat(item.amount_cny());
                    if (!isNaN(value)) {
                        total += value;
                    }
                });
            }
            return total.toFixed(0);
        }

        self.buy_amount = ko.computed(function () {
            self.calculateBuyAmountCurrency();
        }, self);

        self.buy_amount_cny = ko.computed(function () {
            self.calculateBuyAmount();
        }, self);

        self.sell_amount = ko.computed(function () {
            self.calculateSellAmountCurrency();
        }, self);

        self.sell_amount_cny = ko.computed(function () {
            self.calculateSellAmount();
        }, self);

        self.tradeItems = function(data){
            if(Array.prototype.isPrototypeOf(data) && data.length>0){
                for(var k in data){
                    delete data[k]['currencies'];
                    delete data[k]['currency_ico'];
                    delete data[k]['allGoods'];
                    delete data[k]['isRateDisable'];
                    delete data[k]['isRateAgentPriceDisable'];
                    delete data[k]['isAgentunitDisable'];
                    delete data[k]['calculateAgentAmount'];
                }
            }
        }

        self.planItems = function(data){
            if(Array.prototype.isPrototypeOf(data) && data.length>0){
                for(var k in data){
                    delete data[k]['currencies'];
                    delete data[k]['currency_ico'];
                    delete data[k]['paymentTypes'];
                    delete data[k]['showExpenseNameInput'];
                    delete data[k]['expense_width'];
                }
            }
        }

        self.contractData = function(data, type, category){
            if(data!=undefined && Object.keys(data).length>0){
                var extra = data[type][category].extraItems;
                delete data[self.buy_type()];
                delete data[self.sell_type()];
                /*var item = {};
                data[type] = [];
                for(var i in extra) {
                    for(var j in extra[i]){
                        if(j=='type' && extra[i][j]== 'koMultipleSelect' && Array.isArray(extra[i]['value'])) {
                            extra[i]['value'] = extra[i]['value'].join(',');
                        }
                        if(j!='name' && j!='value' && j!='key')
                            delete extra[i][j];
                    }
                }*/
                data[type] = koForm.getValues(extra);
            }
        }

        self.postData  = function(){
            var data = inc.getPostData(self, ["isShowUpAndDown", "sell_formula_status", "buy_formula_status", "showAgentDetail", "isShowAgent", "showSellContractStaticType", "showBuyContractStaticType", "showBuyContractSelectType", "showOnlyDownPartner", "showOnlyUpPartner", "units", "currencies", "allGoods", "paymentTypes", "proceedTypes"]);
            self.tradeItems(data['buyItems']);
            self.tradeItems(data['sellItems']);
            self.planItems(data['paymentItems']);
            self.planItems(data['proceedItems']);
            self.contractData(data['upContractItems'], self.buy_type(), self.buy_category());
            self.contractData(data['downContractItems'], self.sell_type(), self.sell_category());
            self.contractData(data['contractItems'], self.contract_type(), self.contract_category());

            return data;
        }

        self.actionState = ko.observable(0);
        self.is_temp_save   = ko.observable(0);
        self.tempSaveBtnText= ko.observable("暂存");
        self.saveBtnText    = ko.observable("保存");

        self.is_can_back = ko.observable(o.is_can_back);
        self.isBack=ko.observable(false);
        self.back_remark = ko.observable("").extend({
            custom:{
                params:function(v){
                    return (!self.isBack() || (v!=null && v!=""))
                },
                message: "请填写驳回备注"
            }
        });


        self.errors = ko.validation.group(self);
        self.isValid = function () {
            return self.errors().length === 0;
        };

        //暂存
        self.tempSave = function () {
            self.is_temp_save(1);
            self.submit();
        }
        //保存
        self.save = function(){
            if (!self.isValid()) {
                console.log(self.errors.showAllMessages());
                self.errors.showAllMessages();
                return;
            }
            /*if(self.fileUploadStatus()!=1){
                layer.alert("请上传项目预算表！", {icon: 2});
                return;
            }*/

            self.fileUploadStatus(1);

            if (self.buy_amount() == undefined) {
                    self.buy_amount_cny = ko.observable(self.calculateBuyAmount());
                }
    
                if (self.buy_amount() == undefined) {
                    self.buy_amount = ko.observable(self.calculateBuyAmountCurrency());
                }
                
                var temp1 = 0;
                var temp2 = 0;
                if(parseFloat(self.buy_amount_cny()) < parseFloat(self.calculatePaymentAmount())){
                    temp1 = 1;
                }

                if (self.sell_amount_cny() == undefined) {
                    self.sell_amount_cny = ko.observable(self.calculateSellAmount());
                }
    
                if (self.sell_amount() == undefined) {
                    self.sell_amount = ko.observable(self.calculateSellAmountCurrency());
                }

                if(parseFloat(self.sell_amount_cny()) < parseFloat(self.calculateProceedAmount())){
                    temp2 = 1;
                }
                if(temp1==1){
                    layer.confirm("付款金额超过人民币采购总价,是否继续？", {icon: 3, title: '提示'}, function(index){
                        if(temp2==1){
                            layer.confirm("收款金额超过人民币销售总价,是否继续？", {icon: 3, title: '提示'}, function(index){
                                self.submit();
                                layer.close(index);
                            });
                        }else{
                            self.submit();
                            layer.close(index);
                        }
                    });
                }else if(temp2==1){
                    layer.confirm("收款金额超过人民币销售总价,是否继续？", {icon: 3, title: '提示'}, function(index){
                        self.submit();
                        layer.close(index);
                    });
                }else{
                    self.submit();
                }
        }

        self.submit = function () {
            var formData = {"data": self.postData()};
            if (self.actionState() == 1)
                return;
            if(self.is_temp_save() == 1)
                self.tempSaveBtnText("暂存中" + inc.loadingIco);
            else
                self.saveBtnText("保存中" + inc.loadingIco);

            self.actionState(1);
            $.ajax({
                type: 'POST',
                url: '/<?php echo $this->getId() ?>/save',
                data: formData,
                dataType: "json",
                success: function (json) {
                    if (json.state == 0) {
                        layer.msg("操作成功", {icon: 6, time:1000}, function(){
                            if(json.extra==1){
                                location.href = "/<?php echo $this->getId() ?>/edit/?id=" + json.data + "&project_id=<?php echo $data['project_id'] ?>";
                            }else{
                                location.href = "/<?php echo $this->getId() ?>/detail/?id=" + json.data;
                            }
                        });
                    } else {
                        layer.alert(json.data, {icon: 5});
                    }
                    self.tempSaveBtnText("暂存");
                    self.saveBtnText("保存");
					self.actionState(0);
                    self.is_temp_save(0);
                },
                error: function (data) {
                    self.tempSaveBtnText("暂存");
                    self.saveBtnText("保存");
					self.actionState(0);
                    self.is_temp_save(0);
                    layer.alert("保存失败！" + data.responseText, {icon: 5});
                }
            });
            //setTimeout("self.actionState(0)", 1000);
        }

        self.back = function () {
            // history.back();
            location.href="/<?php echo $this->getId()?>/";
        }



        self.cancelBack = function () {
            self.isBack(false);
        }
        self.startBack=function(){self.isBack(true);}

        self.saveBackBtnText=ko.observable("提交驳回");

        self.saveBack=function () {
            layer.msg("请在页面底部填写驳回备注");
            if(!self.back_remark.isValid())
            {
                self.back_remark.isModified(true);
                return;
            }

            var formData={
                id:self.project_id(),
                remark:self.back_remark()
            };
            formData={data:formData};
            if (self.actionState() == 1)
                return;
            self.saveBackBtnText("提交中。。。");
            self.actionState(1);
            $.ajax({
                type:"POST",
                url: '/<?php echo $this->getId() ?>/saveBack',
                data:formData,
                dataType:"json",
                success:function (json) {
                    self.actionState(0);
                    self.saveBackBtnText("提交驳回");
                    if(json.state==0){
                        layer.msg("操作成功", {icon: 6, time:1000}, function(){
                            location.href="/<?php echo $this->getId() ?>/";
                        });

                    }else{
                        layer.alert(json.data, {icon: 5});
                    }
                },
                error:function (data) {
                    self.saveBackBtnText("提交驳回");
                    self.actionState(0);
                    layer.alert("操作失败："+data.responseText, {icon: 5});
                }
            });
        }

    }



    Date.prototype.Format = function (fmt) {
        var o = {
            "M+": this.getMonth() + 1, //月份
            "d+": this.getDate(), //日
            "H+": this.getHours(), //小时
            "m+": this.getMinutes(), //分
            "s+": this.getSeconds(), //秒
            "q+": Math.floor((this.getMonth() + 3) / 3), //季度
            "S": this.getMilliseconds() //毫秒
        };
        if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
        for (var k in o)
            if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
        return fmt;
    }
</script>