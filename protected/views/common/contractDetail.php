
        <div class="box box-primary sub-container__box">
            <div class="box-header with-border project-header">
                <h3 class="box-title">
                    <span class="channel-type">
                     <?php
                        $typeDesc = $this->map["project_type"][$contract->project['type']];
                        if (!empty($contract->project['base']['buy_sell_type'])) {
                            $typeDesc .= '-' . $this->map["purchase_sale_order"][$contract->project['base']["buy_sell_type"]];
                        }
                        echo $typeDesc;
                    ?></span> 
                    <span class="project-detail">
                        <a href="/project/detail/?id=<?php echo $contract->project['project_id'] ?>&t=1" target="_blank">项目编号：<?php echo $contract->project['project_code']?></a></span>
                    <span onclick="copy()" data-clipboard-text="<?php echo $contract->project['project_code']; ?>" class="copy-project-num">复制</span>
                </h3>
                <div class="box-body form-horizontal form-horizontal-custom">
                    <div class="form-group pd-bottom-0">
                        <label for="type" class="col-lg-2 col-xl-1 control-label">交易主体：</label>
                        <div class="col-sm-4">
                            <p class="form-control-static form-control-static-custom"><?php echo $contract->corporation['name'];?></p>
                        </div>
                        <label for="type" class="col-lg-2 col-xl-1 control-label">合同状态：</label>
                        <div class="col-sm-4">
                            <p class="form-control-static form-control-static-custom">
                                <span class="label label-info"><?php
                                    if($contract->status == Contract::STATUS_BACK) {
                                        $nodeName = FlowService::getNowCheckBackNode($contract->contract_id, ContractService::getContractBusinessIds());
                                        echo $nodeName." - ";
                                    }
                                    echo $this->map["contract_status"][$contract->status];
                                    ?></span></p>
                        </div>
                    </div>
                    <?php if(!empty($contract->project->storehouse)):?>
                    <div class="form-group pd-bottom-0">
                        <label for="type" class="col-lg-2 col-xl-1 control-label">仓库名称 ：</label>
                        <div class="col-sm-4">
                            <p class="form-control-static form-control-static-custom">
                                <?php
                                    echo $contract->project->storehouse->name;
                                    ?></p>
                        </div>
                    </div>
                    <?php endif;?>
                
                    <?php if(!empty($contract->relative)):

                        $buy_contract = ($contract->type==ConstantMap::BUY_TYPE)?$contract:$contract->relative;
                        $sell_contract = ($contract->type==ConstantMap::SALE_TYPE)?$contract:$contract->relative;
                    ?>
                    <?php if(!empty($contract["contract_code"])){ ?>
                        <div class="form-group pd-bottom-0">
                            <label for="type" class="col-lg-2 col-xl-1 control-label">采购合同编号：</label>
                            <div class="col-sm-4">
                                <p class="form-control-static form-control-static-custom"><?php echo $buy_contract['contract_code'];?></p>
                            </div>
                            <label for="type" class="col-lg-2 col-xl-1 control-label">销售合同编号：</label>
                            <div class="col-sm-4">
                                <p class="form-control-static form-control-static-custom"><?php echo $sell_contract['contract_code'];?></p>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group pd-bottom-0">
                        <label for="type" class="col-lg-2 col-xl-1 control-label">采购合同类型：</label>
                        <div class="col-sm-4">
                            <p class="form-control-static form-control-static-custom"><?php echo $this->map["contract_config"][$buy_contract["type"]][$buy_contract['category']]["name"];?></p>
                        </div>
                        <label for="type" class="col-lg-2 col-xl-1 control-label">销售合同类型：</label>
                        <div class="col-sm-4">
                            <p class="form-control-static form-control-static-custom"><?php echo $this->map["contract_config"][$sell_contract["type"]][$sell_contract['category']]["name"];?></p>
                        </div>
                    </div>
                    <?php if($buy_contract->category == ConstantMap::BUY_SALE_CONTRACT_TYPE_AGENT_IMPORT && !empty($buy_contract->agent)):?>
                    <div class="form-group pd-bottom-0">
                        <label for="type" class="col-lg-2 col-xl-1 control-label">采购代理商：</label>
                        <div class="col-sm-4">
                            <p class="form-control-static form-control-static-custom">
                            <?php echo '<a href="/partner/detail/?id=' . $buy_contract->agent['partner_id'] . '&t=1" target="_blank">' . $buy_contract->agent['name'] . '</a>';?>
                            </p>
                        </div>
                        <label for="type" class="col-lg-2 col-xl-1 control-label">代理模式：</label>
                        <div class="col-sm-4">
                            <p class="form-control-static form-control-static-custom">
                                <?php echo $this->map['buy_agent_type'][$buy_contract['agent_type']];?>
                            </p>
                        </div>
                    </div>
                    <?php endif;?>
                    <div class="form-group pd-bottom-0">
                        <label for="type" class="col-lg-2 col-xl-1 control-label">采购价格方式：</label>
                        <div class="col-sm-4">
                            <p class="form-control-static form-control-static-custom"><?php echo $this->map["price_type"][$buy_contract['price_type']];?></p>
                        </div>
                        <label for="type" class="col-lg-2 col-xl-1 control-label">销售价格方式：</label>
                        <div class="col-sm-4">
                            <p class="form-control-static form-control-static-custom"><?php echo $this->map["price_type"][$sell_contract['price_type']];?></p>
                        </div>
                    </div>
                    <?php else:?>
                        <div class="form-group pd-bottom-0">
                            <label for="buy_sell_type" class="col-lg-2 col-xl-1 control-label">购销信息：</label>
                            <div class="col-sm-4">
                                <p class="form-control-static form-control-static-custom">
                                <?php echo $contract->getContractType();?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group pd-bottom-0">
                            <label for="type" class="col-lg-2 col-xl-1 control-label">合同类型：</label>
                            <div class="col-sm-4">
                                <p class="form-control-static form-control-static-custom">
                                <?php
                                echo $this->map["contract_config"][$contract["type"]][$contract['category']]["name"];
                                ?>
                                </p>
                            </div>
                            <label for="type" class="col-lg-2 col-xl-1 control-label">合同编号：</label>
                            <div class="col-sm-4">
                                <p class="form-control-static form-control-static-custom">
                                    <?php echo $contract['contract_code'];?>
                                </p>
                            </div>
                        </div>
                        <!-- <div class="form-group pd-bottom-0">
                            <label for="type" class="col-lg-2 col-xl-1 control-label">合同状态：</label>
                            <div class="col-sm-4">
                                <p class="form-control-static form-control-static-custom"><span class="label label-info"><?php
                                        echo $this->map["contract_status"][$contract->status];
                                        ?></span></p>
                            </div>
                        </div> -->
                        <div class="form-group pd-bottom-0">
                            <label for="type" class="col-lg-2 col-xl-1 control-label">价格方式：</label>
                            <div class="col-sm-4">
                                <p class="form-control-static form-control-static-custom"><?php echo $this->map["price_type"][$contract['price_type']];?></p>
                            </div>
                        </div>
                        <?php 

                        if($contract->category == ConstantMap::BUY_SALE_CONTRACT_TYPE_AGENT_IMPORT && !empty($contract->agent)):?>
                        <div class="form-group pd-bottom-0">
                            <label for="type" class="col-lg-2 col-xl-1 control-label">采购代理商：</label>
                            <div class="col-sm-4">
                                <p class="form-control-static form-control-static-custom">
                                <?php echo '<a href="/partner/detail/?id=' . $contract->agent['partner_id'] . '&t=1" target="_blank">' . $contract->agent['name'] . '</a>';?>
                                </p>
                            </div>
                            <label for="type" class="col-lg-2 col-xl-1 control-label">代理模式：</label>
                            <div class="col-sm-4">
                                <p class="form-control-static form-control-static-custom">
                                    <?php echo $this->map['buy_agent_type'][$contract['agent_type']];?>
                                </p>
                            </div>
                        </div>
                        <?php endif;?>
                    <?php endif;?>
                </div>
            </div>
        </div>

<?php
/**
 * Created by youyi000.
 * DateTime: 2017/9/18 17:09
 * Describe：
 */

if(!empty($contract->relative))
{
    $this->renderPartial("/common/contractChannelInfo", array('contract'=>$contract));
}
else
{
    $this->renderPartial("/common/contractInfo", array('contract'=>$contract));
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