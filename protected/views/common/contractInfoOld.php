<?php
$contractType = $contract->type == ConstantMap::BUY_TYPE || $contract->type == ConstantMap::CONTRACT_CATEGORY_SUB_BUY;
$titleStr = ($contractType)?'采购':'销售';
$partnerStr = ($contractType)?'上游':'下游';
$paymentStr = ($contractType)?'付款':'收款';
//$mapStrPre = ($contractType)?'buy':'sell';
$paymentType = ($contractType)?'pay_type':'proceed_type';
$is_main = $contract->is_main;
?>

<div class="form-group">
    <label for="buy_sell_type" class="col-sm-2 control-label">购销信息</label>
    <div class="col-sm-2">
        <p class="form-control-static">
        <?php echo $contract->getContractType();?>
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
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">合同类型</label>
    <div class="col-sm-2">
        <p class="form-control-static">
        <?php
        echo $this->map["contract_config"][$contract["type"]][$contract['category']]["name"];
        ?>
        </p>
    </div>
    <label for="type" class="col-sm-2 control-label">合同编号</label>
    <div class="col-sm-2">
        <p class="form-control-static">
            <?php echo $contract['contract_code'];?>
        </p>
    </div>
    <label for="type" class="col-sm-2 control-label">合同状态</label>
    <div class="col-sm-2">
        <p class="form-control-static"><span class="label label-info"><?php
                if($contract->status == Contract::STATUS_BACK) {
                    $nodeName = FlowService::getNowCheckBackNode($contract->contract_id, ContractService::getContractBusinessIds());
                    echo $nodeName." - ";
                }
                echo $this->map["contract_status"][$contract->status];
                ?></span></p>
    </div>
</div>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label"><?php echo $partnerStr;?>合作方</label>
    <div class="col-sm-6">
        <p class="form-control-static">
            <?php echo '<a href="/partner/detail/?id=' . $contract->partner['partner_id'] . '&t=1" target="_blank">' . $contract->partner['name'] . '</a>';?>
        </p>
    </div>
    <label for="type" class="col-sm-2 control-label">合同签订日期</label>
    <div class="col-sm-2">
        <p class="form-control-static">
            <?php echo $contract['contract_date'];?>
        </p>
    </div>
</div>
<?php if(!empty($contract->agent)):?>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">采购代理商</label>
    <div class="col-sm-4">
        <p class="form-control-static">
        <?php echo '<a href="/partner/detail/?id=' . $contract->agent['partner_id'] . '&t=1" target="_blank">' . $contract->agent['name'] . '</a>';?>
        </p>
    </div>
    <label for="type" class="col-sm-2 control-label">代理模式</label>
    <div class="col-sm-4">
        <p class="form-control-static">
            <?php echo $this->map['buy_agent_type'][$contract['agent_type']];?>
        </p>
    </div>
</div>
<?php endif;?>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">交易主体</label>
    <div class="col-sm-10">
        <p class="form-control-static"><?php echo $contract->corporation['name'];?></p>
    </div>
</div>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label"><?php echo $titleStr;?>币种</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo $this->map["currency"][$contract['currency']]['name'];?></p>
    </div>
    <label for="type" class="col-sm-2 control-label"><?php echo $titleStr;?>币种即期汇率</label>
    <div class="col-sm-4">
        <p class="form-control-static"><?php echo number_format($contract['exchange_rate'], 2);?></p>
    </div>
</div>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">价格方式</label>
    <div class="col-sm-10">
        <p class="form-control-static"><?php echo $this->map["price_type"][$contract['price_type']];?></p>
    </div>
</div>
    <div class="form-group">
        <label for="type" class="col-sm-2 control-label">计价公式</label>
        <div class="col-sm-10">
            <p class="form-control-static"><?php echo $contract['formula'];?></p>
        </div>
    </div>
    <div class="form-group">
        <label for="type" class="col-sm-2 control-label">合同负责人</label>
        <div class="col-sm-10">
            <p class="form-control-static"><?php echo $contract->manager['name'];?></p>
        </div>
    </div>

<h4 class="section-title"><?php echo $titleStr ?>交易明细</h4>
<div class="form-group">
    <div class="col-sm-offset-1 col-sm-11">
    <?php $this->renderPartial("/common/goodDetailListOld", array("goodArr"=>$contract->goods, "title"=>$titleStr));?>
    </div>
</div>
<?php if(!empty($contract->agent)):?>
    <h4 class="section-title">代理手续费</h4>
    <div class="form-group">
        <div class="col-sm-offset-1 col-sm-11">
            <?php $this->renderPartial("/common/agentDetailsListOld", array("agentDetails"=>$contract->agentDetail));?>
        </div>
    </div>
<?php endif; ?>
<h4 class="section-title">最终交货/发货日期</h4>
<div class="form-group">
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="type" class="col-sm-4 control-label">最终<?php if($contract->type==ConstantMap::CONTRACT_CATEGORY_SUB_BUY):?>交货<?php else:?>发货<?php endif;?>日期：</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?php echo $contract->delivery_term; ?>&nbsp;&nbsp;
                        <?php if(!empty($contract->delivery_term)) echo $this->map['contract_delivery_mode'][$contract->delivery_mode];?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label for="type" class="col-sm-4 control-label"><?php if($contract->type==ConstantMap::CONTRACT_CATEGORY_SUB_BUY):?>收票<?php else:?>开票<?php endif;?>时间：</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?php
                        if($contract->type==ConstantMap::CONTRACT_CATEGORY_SUB_BUY)
                            echo empty($contract->days)&&$contract->days!=0?'':$contract->days.'天（根据入库单日期倒推）';
                        else
                            echo empty($contract->days)&&$contract->days!=0?'':$contract->days.'天（根据出库单日期倒推）';
                        ?>
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
<h4 class="section-title">合同条款</h4>
<div class="row">
    <div class="col-sm-12">
        <?php 
        if(!empty($this->map['contract_config'][$contract->type][$contract['category']]['extra']))
        $this->renderPartial("/common/contractExtraOld", array("mapValue"=>$this->map['contract_config'][$contract->type][$contract['category']]['extra'], "extraValue"=>$contract->extra, "label_width"=>2));?>
    </div>
</div>

<h4 class="section-title"><?php echo $partnerStr;?><?php echo $paymentStr;?>计划</h4>
<div class="form-group">
    <div class="col-sm-offset-1 col-sm-10">
    <?php $this->renderPartial("/common/paymentListOld", array("payments"=>$contract->payments, "showDates"=>$contractType, "title"=>$paymentStr, "paymentType"=>$paymentType));?>
    </div>
</div>
<?php
if(!empty($contract->quotas)):?>
    <h4 class="section-title"><?php echo $titleStr;?>合同占用额度</h4>
<div class="form-group">
    <div class="col-sm-offset-1 col-sm-10">
    <?php $this->renderPartial("/common/quotasListOld", array("quotas"=>$contract->quotas, "title"=>""));?>
    </div>
</div>
<hr>
<?php endif;?>
<div class="form-group">
    <label for="type" class="col-sm-2 control-label">合同创建人/时间</label>
    <div class="col-sm-4">
        <p class="form-control-static">
            <?php echo $contract->creator['name'];?> /
            <?php echo $contract['create_time'];?>
        </p>
    </div>
    <label for="type" class="col-sm-2 control-label">合同修改人/时间</label>
    <div class="col-sm-4">
        <p class="form-control-static">
            <?php $user = SystemUser::getUser($contract["update_user_id"]); echo $user['name'] ?> /
            <?php echo $contract['update_time'];?></p>
    </div>
</div>