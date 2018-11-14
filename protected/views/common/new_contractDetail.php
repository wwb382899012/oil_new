<!-- 进口代采 -->
<div class="content-wrap">
    <div class="content-wrap-title">
        <div>
            <p><?php
                    $typeDesc = $this->map["project_type"][$contract->project['type']];
                    if (!empty($contract->project['base']['buy_sell_type'])) {
                        $typeDesc .= '-' . $this->map["purchase_sale_order"][$contract->project['base']["buy_sell_type"]];
                    }
                    echo $typeDesc;
                ?>
                <span class="project-detail">
                    <a style="color:#3E8CF7!important;margin-left:10px;" href="/project/detail/?id=<?php echo $contract->project['project_id'] ?>&t=1" target="_blank">项目编号：<?php echo $contract->project['project_code']?></a>
                </span>
                <span style="color:#FF6E34;font-size:14px;margin-left:10px;" onclick="copy()" data-clipboard-text="<?php echo $contract->project['project_code']; ?>" class="copy-project-num">复制</span>
            </p>
        </div>
    </div>
    <ul class="item-com">
        <li>
            <label for="type">交易主体：</label>
            <div>
                <p><?php echo $contract->corporation['name'];?></p>
            </div>
        </li>
        <li>
            <label for="type">合同状态：</label>
            <div>
                <p>
                    <span style="color:#FF6E34;"><?php
                        if($contract->status == Contract::STATUS_BACK) {
                            $nodeName = FlowService::getNowCheckBackNode($contract->contract_id, ContractService::getContractBusinessIds());
                            echo $nodeName." - ";
                        }
                        echo $this->map["contract_status"][$contract->status];
                        ?>
                    </span>
                </p>
            </div>
        </li>
        <?php if(!empty($contract->project->storehouse)):?>
            <li class="form-group pd-bottom-0">
                <label for="type">仓库名称：</label>
                <div>
                    <p>
                        <?php
                            echo $contract->project->storehouse->name;
                        ?>
                    </p>
                </div>
            </li>
        <?php endif;?>
        <?php if(!empty($contract->relative)):
            $buy_contract = ($contract->type==ConstantMap::BUY_TYPE)?$contract:$contract->relative;
            $sell_contract = ($contract->type==ConstantMap::SALE_TYPE)?$contract:$contract->relative;
        ?>
        <?php if(!empty($contract["contract_code"])){ ?>
            <li class="form-group pd-bottom-0">
                <label for="type">采购合同编号：</label>
                <div>
                    <p><?php echo $buy_contract['contract_code'];?></p>
                </div>
            </li>
            <li>
                <label for="type">销售合同编号：</label>
                <div>
                    <pmust-logo><?php echo $sell_contract['contract_code'];?></p>
                </div>
            </li>
        <?php } ?>
        <li>
            <label for="type">采购合同类型：</label>
            <div>
                <pmust-logo><?php echo $this->map["contract_config"][$buy_contract["type"]][$buy_contract['category']]["name"];?></p>
            </div>
        </li>
        <li>
            <label for="type">销售合同类型：</label>
            <div>
                <pmust-logo><?php echo $this->map["contract_config"][$sell_contract["type"]][$sell_contract['category']]["name"];?></p>
            </div>
        </li>
        <?php if($buy_contract->category == ConstantMap::BUY_SALE_CONTRACT_TYPE_AGENT_IMPORT && !empty($buy_contract->agent)):?>
            <li class="form-group pd-bottom-0">
                <label for="type">采购代理商：</label>
                <div>
                    <pmust-logo>
                    <?php echo '<a href="/partner/detail/?id=' . $buy_contract->agent['partner_id'] . '&t=1" target="_blank">' . $buy_contract->agent['name'] . '</a>';?>
                    </p>
                </div>
            </li>
            <li>
                <label for="type">代理模式：</label>
                <div>
                    <pmust-logo>
                        <?php echo $this->map['buy_agent_type'][$buy_contract['agent_type']];?>
                    </p>
                </div>
            </li>
        <?php endif;?>
         <li class="form-group pd-bottom-0">
            <label for="type">采购价格方式：</label>
            <div>
                <pmust-logo><?php echo $this->map["price_type"][$buy_contract['price_type']];?></p>
            </div>
        </li>
        <li>
            <label for="type">销售价格方式：</label>
            <div>
                <pmust-logo><?php echo $this->map["price_type"][$sell_contract['price_type']];?></p>
            </div>
        </li>
        <?php else:?>
            <li class="form-group pd-bottom-0">
                <label for="buy_sell_type">购销信息：</label>
                <div>
                    <pmust-logo>
                    <?php echo $contract->getContractType();?>
                    </p>
                </div>
            </li>
            <li class="form-group pd-bottom-0">
                <label for="type">合同类型：</label>
                <div>
                    <pmust-logo>
                    <?php
                    echo $this->map["contract_config"][$contract["type"]][$contract['category']]["name"];
                    ?>
                    </p>
                </div>
            </li>
        <li>
            <label for="type">合同编号：</label>
            <div>
                <pmust-logo>
                    <?php echo $contract['contract_code'];?>
                    </p>
            </div>
        </li>
            <!-- <li class="form-group pd-bottom-0">
                <label for="type">合同状态：</label>
                <div>
                    <pmust-logo><span class="label label-info"><?php
                            echo $this->map["contract_status"][$contract->status];
                            ?></span></p>
                </div>
            </li> -->
            <li class="form-group pd-bottom-0">
                <label for="type">价格方式：</label>
                <div>
                    <pmust-logo><?php echo $this->map["price_type"][$contract['price_type']];?></p>
                </div>
            </li>
            <?php 

            if($contract->category == ConstantMap::BUY_SALE_CONTRACT_TYPE_AGENT_IMPORT && !empty($contract->agent)):?>
            <li class="form-group pd-bottom-0">
                <label for="type">采购代理商：</label>
                <div>
                    <pmust-logo>
                    <?php echo '<a href="/partner/detail/?id=' . $contract->agent['partner_id'] . '&t=1" target="_blank">' . $contract->agent['name'] . '</a>';?>
                    </p>
                </div>
            </li>
            <li>
                <label for="type">代理模式：</label>
                <div>
                    <pmust-logo>
                        <?php echo $this->map['buy_agent_type'][$contract['agent_type']];?>
                    </p>
                </div>
            </li>
            <?php endif;?>
        <?php endif;?>
    </ul>
</div>

<?php if(false && $contract['status']>=Contract::STATUS_BUSINESS_CHECKED && !empty($contract['block_hash'])){ ?>
<div class="content-wrap">
    <div class="content-wrap-title">
        <div>
            <p>区块链信息</p>
            <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
        </div>
    </div>
    <?php 
        $contract_relative = !empty($contract->relative) ? $contract->relative : '';
        $buyContract  = ($contract->type==ConstantMap::BUY_TYPE) ? $contract:$contract_relative;
        $sellContract = ($contract->type==ConstantMap::SALE_TYPE)? $contract:$contract_relative;
    ?>
    <div style="display:flex;">
        <?php if(!empty($buyContract)){ ?>
        <div style="flex:1;">
            <div class="contract-type-container" style="margin-bottom:14px;">
                <div style="font-size:16px;font-weight:500;margin-bottom:14px;">采购合同</div>
            </div>
            <ul class="item-com item-com-1 ul-contract-item">
                <li>
                    <label for="type">区块哈希值：</label>
                    <span><?php echo $buyContract['block_hash'];?></span>
                </li>
                <li>
                    <label for="type">交易哈希值：</label>
                    <span><?php echo $buyContract['tx_hash'];?></span>
                </li>
            </ul>
        </div>
        <?php } ?>
        <?php if(!empty($sellContract)){ ?>
        <div style="flex:1;">
            <div class="contract-type-container" style="margin-bottom:14px;">
                <div style="font-size:16px;font-weight:500;margin-bottom:14px;">销售合同</div>
            </div>
            <ul class="item-com item-com-1 ul-contract-item">
                <li>
                    <label for="type">区块哈希值：</label>
                    <span><?php echo $sellContract['block_hash'];?></span>
                </li>
                <li>
                    <label for="type">交易哈希值：</label>
                    <span><?php echo $sellContract['tx_hash'];?></span>
                </li>
            </ul>
        </div>
        <?php } ?>
    </div>

    <!-- <ul class="item-com item-com-2">
        <?php if(!empty($buyContract)){ ?>
        <li>
            <label style="width:unset;">采购合同区块哈希值：</label>
            <p class="form-control-static form-control-static-custom">
                <?php echo $buyContract['block_hash'];?>
            </p>
        </li>
        <li>
            <label style="width:unset;">采购合同交易哈希值：</label>
            <p class="form-control-static form-control-static-custom">
                <?php echo $buyContract['tx_hash'];?>
            </p>
        </li>
        <?php } ?>
        <?php if(!empty($sellContract)){ ?>
        <li>
            <label style="width:unset;">销售合同区块哈希值：</label>
            <p class="form-control-static form-control-static-custom">
                <?php echo $sellContract['block_hash'];?>
            </p>
        </li>
        <li>
            <label style="width:unset;">销售合同交易哈希值：</label>
            <p class="form-control-static form-control-static-custom">
                <?php echo $sellContract['tx_hash'];?>
            </p>
        </li>
        <?php } ?>
    </ul> -->
</div>
<?php } ?>

<?php
/**
 * Created by youyi000.
 * DateTime: 2017/9/18 17:09
 * Describe：
 */

if(!empty($contract->relative))
{
    $this->renderPartial("/common/new_contractChannelInfo", array('contract'=>$contract));
}
else
{
    $this->renderPartial("/common/new_contractInfo", array('contract'=>$contract));
}
?>

<script type="text/javascript">
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
</script>
