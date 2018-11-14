<?php
$buy_contract = ($contract->type==ConstantMap::BUY_TYPE)?$contract:$contract->relative;
$sell_contract = ($contract->type==ConstantMap::SALE_TYPE)?$contract:$contract->relative;
?>

<div class="form-group">
    <label for="buy_sell_type" class="col-sm-2 control-label">购销信息</label>
    <div class="col-sm-2">
        <p class="form-control-static">
        <?php
        echo $contract->getContractType();
       ?>
        </p>
    </div>
    <label for="project_code" class="col-sm-2 control-label">项目编号</label>
    <div class="col-sm-2">
        <p class="form-control-static"><a href="/project/detail/?id=<?php echo $contract->project['project_id'] ?>&t=1" target="_blank"><?php echo $contract->project['project_code'];?></a></p>
    </div>
    <label for="type" class="col-sm-2 control-label">项目类型</label>
    <div class="col-sm-2">
        <p class="form-control-static">
        <?php
            $typeDesc = $this->map["project_type"][$contract->project['type']];
            if (!empty($contract->project['base']['buy_sell_type'])) {
                $typeDesc .= '-' . $this->map["purchase_sale_order"][$contract->project['base']["buy_sell_type"]];
            }
            echo $typeDesc;
        ?>
        </p>
    </div>
</div>
<?php if(!empty($contract["contract_code"])){ ?>
    <div class="form-group">
        <label for="type" class="col-sm-2 control-label">采购合同编号</label>
        <div class="col-sm-4">
            <p class="form-control-static"><?php echo $buy_contract['contract_code'];?></p>
        </div>
        <label for="type" class="col-sm-2 control-label">销售合同编号</label>
        <div class="col-sm-4">
            <p class="form-control-static"><?php echo $sell_contract['contract_code'];?></p>
        </div>
    </div>
<?php } ?>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">采购合同签订日期</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $buy_contract['contract_date'];?></p>
    </div>
    <label for="type" class="col-sm-2 control-label">销售合同签订日期</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $sell_contract['contract_date'];?></p>
    </div>
</div>
<div class="form-group">

    <label for="type" class="col-sm-2 control-label">采购合同类型</label>
    <div class="col-sm-2">
        <p class="form-control-static"><?php echo $this->map["contract_config"][$buy_contract["type"]][$buy_contract['category']]["name"];?></p>
    </div>
    <label for="type" class="col-sm-2 control-label">销售合同类型</label>
    <div class="col-sm-2">
        <p class="form-control-static"><?php echo $this->map["contract_config"][$sell_contract["type"]][$sell_contract['category']]["name"];?></p>
    </div>
    <label for="type" class="col-sm-2 control-label">合同状态</label>
    <div class="col-sm-2">
        <p class="form-control-static">
            <span class="label label-info"><?php
                if($contract->status == Contract::STATUS_BACK) {
                    $nodeName = FlowService::getNowCheckBackNode($contract->contract_id, ContractService::getContractBusinessIds());
                    echo $nodeName." - ";
                }
                echo $this->map["contract_status"][$contract->status];
                ?></span></p>
    </div>
</div>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">上游合作方</label>
    <div class="col-sm-10">
        <p class="form-control-static">
        <?php echo '<a href="/partner/detail/?id=' . $buy_contract->partner['partner_id'] . '&t=1" target="_blank">' . $buy_contract->partner['name'] . '</a>';?>
        </p>
    </div>
</div>
<?php if(!empty($buy_contract->agent)):?>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">采购代理商</label>
    <div class="col-sm-4">
        <p class="form-control-static">
        <?php echo '<a href="/partner/detail/?id=' . $buy_contract->agent['partner_id'] . '&t=1" target="_blank">' . $buy_contract->agent['name'] . '</a>';?>
        </p>
    </div>
    <label for="type" class="col-sm-2 control-label">代理模式</label>
    <div class="col-sm-4">
        <p class="form-control-static">
            <?php echo $this->map['buy_agent_type'][$buy_contract['agent_type']];?>
        </p>
    </div>
</div>
<?php endif;?>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">下游合作方</label>
    <div class="col-sm-10">
        <p class="form-control-static">
        <?php echo '<a href="/partner/detail/?id=' . $sell_contract->partner['partner_id'] . '&t=1" target="_blank">' . $sell_contract->partner['name'] . '</a>';?>
        </p>
    </div>
</div>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">交易主体</label>
    <div class="col-sm-10">
        <p class="form-control-static"><?php echo $contract->corporation['name'];?></p>
    </div>
</div>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">采购币种</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $this->map["currency"][$buy_contract['currency']]['name'];?></p>
    </div>
    <label for="type" class="col-sm-2 control-label">采购币种即期汇率</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo number_format($buy_contract['exchange_rate'], 2);?></p>
    </div>
</div>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">销售币种</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $this->map["currency"][$sell_contract['currency']]['name'];?></p>
    </div>
    <label for="type" class="col-sm-2 control-label">销售币种即期汇率</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo number_format($sell_contract['exchange_rate'], 2);?></p>
    </div>
</div>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">采购价格方式</label>
    <div class="col-sm-10">
        <p class="form-control-static"><?php echo $this->map["price_type"][$buy_contract['price_type']];?></p>
    </div>
</div>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">销售价格方式</label>
    <div class="col-sm-10">
        <p class="form-control-static"><?php echo $this->map["price_type"][$sell_contract['price_type']];?></p>
    </div>
</div>
<h4 class="section-title">采购交易明细</h4>
<div class="form-group">
    <div class="col-sm-offset-1 col-sm-11">
    <?php $this->renderPartial("/common/goodDetailListOld", array("goodArr"=>$buy_contract->goods, "title"=>"采购"));?>
    </div>
</div>
    <h4 class="section-title">销售交易明细</h4>
<div class="form-group">

    <div class="col-sm-offset-1 col-sm-11">
    <?php $this->renderPartial("/common/goodDetailListOld", array("goodArr"=>$sell_contract->goods, "title"=>"销售"));?>
    </div>
</div>
<hr>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">采购计价公式</label>
    <div class="col-sm-10">
        <p class="form-control-static"><?php echo $buy_contract['formula'];?></p>
    </div>
</div>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">销售计价公式</label>
    <div class="col-sm-10">
        <p class="form-control-static"><?php echo $sell_contract['formula'];?></p>
    </div>
</div>
<?php if(!empty($buy_contract->agent)):?>
<hr>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">代理手续费</label>
    <div class="col-sm-offset-1 col-sm-10">
    <?php $this->renderPartial("/common/agentDetailsListOld", array("agentDetails"=>$buy_contract->agentDetail));?>
    </div>
</div>
<?php endif; ?>
<hr>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">采购合同负责人</label>
    <div class="col-sm-10">
        <p class="form-control-static"><?php echo $buy_contract->manager['name'];?></p>
    </div>
</div>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">销售合同负责人</label>
    <div class="col-sm-10">
        <p class="form-control-static"><?php echo $sell_contract->manager['name'];?></p>
    </div>
</div>

<h4 class="section-title">最终交货/发货日期</h4>
<div class="form-group">
    <label for="remark" class="col-sm-4 control-label"><h4>上游</h4></label>
    <div class="col-sm-2"></div>
    <label for="remark" class="col-sm-4 control-label"><h4>下游</h4></label>
</div>
<div class="form-group">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="type" class="col-sm-4 control-label">最终交货日期：</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?php echo $buy_contract->delivery_term; ?> &nbsp;&nbsp;
                        <?php if(!empty($buy_contract->delivery_term)) echo $this->map['contract_delivery_mode'][$buy_contract->delivery_mode];?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label for="type" class="col-sm-4 control-label">收票时间：</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?php
                        echo empty($buy_contract->days)&&$buy_contract->days!=0?'':$buy_contract->days.'天（根据入库单日期倒推）';
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="type" class="col-sm-4 control-label">最终发货日期：</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?php echo $sell_contract->delivery_term; ?>&nbsp;&nbsp;
                        <?php if(!empty($sell_contract->delivery_term)) echo $this->map['contract_delivery_mode'][$sell_contract->delivery_mode];?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label for="type" class="col-sm-4 control-label">开票期限：</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?php
                        echo empty($sell_contract->days)&&$sell_contract->days!=0?'':$sell_contract->days.'天（根据出库单日期倒推）';
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="form-group">
    <label for="remark" class="col-sm-4 control-label"><h4>上游条款</h4></label>
    <div class="col-sm-2"></div>
    <label for="remark" class="col-sm-4 control-label"><h4>下游条款</h4></label>
</div>
<div class="row">
    <div class="col-sm-6">
        <?php
        $this->renderPartial("/common/contractExtraOld", array("mapValue"=>$this->map['contract_config'][1][$buy_contract['category']]['extra'], "extraValue"=>$buy_contract->extra));?>
    </div>
    <div class="col-sm-6">
        <?php
        $this->renderPartial("/common/contractExtraOld", array("mapValue"=>$this->map['contract_config'][2][$sell_contract['category']]['extra'], "extraValue"=>$sell_contract->extra));?>
    </div>
</div>
    <h4 class="section-title">上游付款计划</h4>
<div class="form-group">
    <div class="col-sm-offset-1 col-sm-11">
    <?php $this->renderPartial("/common/paymentListOld", array("payments"=>$buy_contract->payments, "showDates"=>1, "title"=>"付款", "paymentType"=>"pay_type"));?>
    </div>
</div>
    <h4 class="section-title">下游收款计划</h4>
<div class="form-group">

    <div class="col-sm-offset-1 col-sm-11">
    <?php $this->renderPartial("/common/paymentListOld", array("payments"=>$sell_contract->payments, "showDates"=>0, "title"=>"收款", "paymentType"=>"proceed_type"));?>
    </div>
</div>
<hr>
<?php if(!empty($buy_contract->quotas)):?>
    <h4 class="section-title">采购合同占用额度</h4>
<div class="form-group">

    <div class="col-sm-offset-1 col-sm-10">
    <?php $this->renderPartial("/common/quotasListOld", array("quotas"=>$buy_contract->quotas, "title"=>""));?>
    </div>
</div>
<hr>
<?php endif;?>
<?php if(!empty($sell_contract->quotas)):?>
    <h4 class="section-title">销售合同占用额度</h4>
<div class="form-group">

    <div class="col-sm-offset-1 col-sm-10">
    <?php $this->renderPartial("/common/quotasListOld", array("quotas"=>$sell_contract->quotas, "title"=>""));?>
    </div>
</div>
<hr>
<?php endif;?>

<div class="form-group">
    <label for="type" class="col-sm-2 control-label">采购合同创建人/时间</label>
    <div class="col-sm-4">
        <p class="form-control-static">
            <?php echo $buy_contract->creator['name'];?> /
            <?php echo $buy_contract['create_time'];?>
        </p>
    </div>
    <label for="type" class="col-sm-2 control-label">采购合同修改人/时间</label>
    <div class="col-sm-4">
        <p class="form-control-static">
            <?php $user = SystemUser::getUser($buy_contract["update_user_id"]); echo $user['name'] ?> /
            <?php echo $buy_contract['update_time'];?></p>
    </div>
</div>
<hr>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">销售合同创建人/时间</label>
    <div class="col-sm-4">
        <p class="form-control-static">
            <?php echo $sell_contract->creator['name'];?> /
            <?php echo $sell_contract['create_time'];?>
        </p>
    </div>
    <label for="type" class="col-sm-2 control-label">销售合同修改人/时间</label>
    <div class="col-sm-4">
        <p class="form-control-static">
            <?php $user = SystemUser::getUser($sell_contract["update_user_id"]); echo $user['name'] ?> /
            <?php echo $sell_contract['update_time'];?></p>
    </div>
</div>
