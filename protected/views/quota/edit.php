<link rel="stylesheet" type="text/css" href="/css/fengkongdetail.css?key=20180112">
<script type="text/javascript" src="/js/resize.js"></script>
<script type="text/javascript" src="/js/clipboard.js"></script>
<?php
if(empty($contract->relative)) {
    // 单边合同
    $upPartnerOnly=($contract->type==ConstantMap::BUY_TYPE)||($contract->type==ConstantMap::CONTRACT_CATEGORY_SUB_BUY);
}
$this->renderPartial("/quota/editElement", array('contract'=>$contract));
?>
<section class="content-header">
    <div class="content-header__des">
        <?php echo empty($this->pageTitle)?$this->moduleName:$this->pageTitle ?>
    </div>
</section>
<section class=" main-container" id="main-container">
    <section class="content sub-container">
          <!-- 详情综述 -->


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
                    </div>
                
                    <?php if(!empty($contract->relative)):

                        $buy_contract = ($contract->type==ConstantMap::BUY_TYPE)?$contract:$contract->relative;
                        $sell_contract = ($contract->type==ConstantMap::SALE_TYPE)?$contract:$contract->relative;
                    ?>
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
                    <div class="form-group pd-bottom-0">
                        <label for="type" class="col-lg-2 col-xl-1 control-label">合同状态：</label>
                        <div class="col-sm-4">
                            <p class="form-control-static form-control-static-custom">
                                <span class="label label-info"><?php
                                    echo $this->map["contract_status"][$contract->status];
                                    ?></span></p>
                        </div>
                    </div>
                    <?php if(!empty($buy_contract->agent)):?>
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
                        <div class="form-group pd-bottom-0">
                            <label for="type" class="col-lg-2 col-xl-1 control-label">合同状态：</label>
                            <div class="col-sm-4">
                                <p class="form-control-static form-control-static-custom"><span class="label label-info"><?php
                                        echo $this->map["contract_status"][$contract->status];
                                        ?></span></p>
                            </div>
                        </div>
                        <div class="form-group pd-bottom-0">
                            <label for="type" class="col-lg-2 col-xl-1 control-label">价格方式：</label>
                            <div class="col-sm-4">
                                <p class="form-control-static form-control-static-custom"><?php echo $this->map["price_type"][$contract['price_type']];?></p>
                            </div>
                        </div>
                        <?php if(!empty($contract->agent)):?>
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
            <form class="form-horizontal" role="form" id="mainForm">
            <?php
                if(!empty($contract->relative)) :
                    $this->renderPartial("/common/contractChannelInfo", array('contract'=>$contract));
                ?>
            <div class="box box-primary sub-container__box " style="position:relative">

                <div class="box-header with-border box-content-title">
                  <h3 class="box-title">&nbsp;&nbsp;&nbsp;占用额度</h3>
                </div>
                <div class="box-header box-content-custom">
                    <span class="box-content__company-style">
                    采购合同占用额度
                    </span>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                    <!-- ko component: {
                             name: "quota-items",
                             params: {
                                        quotas:upPartnerQuotas,
                                        managers:upManagers
                                    }
                         } -->
                    <!-- /ko -->
                    </div>
                </div>
            

                <div class="box-header box-content-custom">
                    <span class="box-content__company-style">
                    销售合同占用额度
                    </span>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                    <!-- ko component: {
                             name: "quota-items",
                             params: {
                                        quotas:downPartnerQuotas,
                                        managers:downManagers
                                    }
                         } -->
                    <!-- /ko -->
                    </div>
                </div>
                <div class="box-body form-horizontal form-horizontal-custom">
                </div>
            </div>

            <?php else:?>
            <?php
                if($upPartnerOnly) :
                    // 采购单边合同
                    $this->renderPartial("/common/contractInfo", array('contract'=>$contract));
                ?>
            <div class="box box-primary sub-container__box " style="position:relative">

                <div class="box-header with-border box-content-title">
                  <h3 class="box-title">&nbsp;&nbsp;&nbsp;占用额度</h3>
                </div>
                <div class="box-header box-content-custom">
                    <span class="box-content__company-style">
                    采购合同占用额度
                    </span>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                    <!-- ko component: {
                             name: "quota-items",
                             params: {
                                        quotas:upPartnerQuotas,
                                        managers:upManagers
                                    }
                         } -->
                    <!-- /ko -->
                    </div>
                </div>
                <div class="box-body form-horizontal form-horizontal-custom">
                </div>
            </div>
                <?php endif;?>
                <?php
                    if(!$upPartnerOnly) :
                        // 销售单边合同
                        $this->renderPartial("/common/contractInfo", array('contract'=>$contract));
                    ?>
            <div class="box box-primary sub-container__box " style="position:relative">

                <div class="box-header with-border box-content-title">
                  <h3 class="box-title">&nbsp;&nbsp;&nbsp;占用额度</h3>
                </div>
                <div class="box-header box-content-custom">
                    <span class="box-content__company-style">
                    销售合同占用额度
                    </span>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                    <!-- ko component: {
                             name: "quota-items",
                             params: {
                                        quotas:downPartnerQuotas,
                                        managers:downManagers
                                    }
                         } -->
                    <!-- /ko -->
                    </div>
                </div>
                <div class="box-body form-horizontal form-horizontal-custom">
                </div>
                <?php endif;?>
            </div>
            <?php endif;?>

            </form>

            <div class="box box-primary sub-container__box sub-container__fixed">
                <div class="box-body">
                    <div class="form-group form-group-custom-btn">
                        <div class="btn-contain-custom">
                        <button type="button" class="btn btn-contain__submit" data-bind="click:save">提交</button>
                        <a class="btn btn-contain__default" href="/<?php echo $this->getId();?>/">返回</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div><!--end box box-primary-->
</section><!--end content-->
<script type="text/javascript">
    var view;

    (function() {
        setTimeout(function() {
            $("section.content").trigger('resize');
        });
        var clipboard = new Clipboard('.copy-project-num');
        view = new ViewModel();
        view.upManagers = <?php echo json_encode($upManagers)?>;
        view.downManagers = <?php echo json_encode($downManagers)?>;
        ko.applyBindings(view, $("#content")[0]);
        view.contract_id=<?php echo $contract['contract_id'];?>;
        view.project_id=<?php echo $contract['project_id'];?>;
        view.is_main=<?php echo $contract['is_main'];?>;
    })();

    function ViewModel(option) {
        var defaults = {
            upPartnerQuotas:[],
            downPartnerQuotas:[],
            upManagers:[],
            downManagers:[],
            contract_id:'',
            project_id:'',
            is_main:'',
        }
        var o = $.extend(defaults, option);
        var self = this;
        self.upPartnerQuotas = ko.observableArray(o.upPartnerQuotas);
        self.downPartnerQuotas = ko.observableArray(o.downPartnerQuotas);
        self.contract_id = o.contract_id;
        self.project_id = o.project_id;
        self.upManagers = o.upManagers;
        self.downManagers = o.downManagers;
        self.errors = ko.validation.group(self);
        self.submitting = ko.observable(0);
        self.isValid=function () {
            return self.errors().length===0;
        }

        self.save = function() {
            if(self.isValid() && self.submitting() == 0) {
                var upQuotaItems = self.getQuotasValue(self.upPartnerQuotas());
                var downQuotaItems = self.getQuotasValue(self.downPartnerQuotas());
                var confirmString = "是否确认提交额度占用，该操作不可逆？";
                if(upQuotaItems.length == 0 && downQuotaItems.length == 0) {
                    confirmString = "额度占用信息尚未填写,确认无额度占用信息，该操作不可逆？";
                }
                layer.confirm(confirmString, {icon: 3, title: '提示'}, function(){
                    self.submitting(1);
                    $.ajax({
                        type:"POST",
                        url:"/quota/save",
                        data: {
                            contract_id:self.contract_id,
                            project_id:self.project_id,
                            is_main:self.is_main,
                            upQuotaItems:upQuotaItems,
                            downQuotaItems:downQuotaItems,
                        },
                        dataType:"json",
                        success:function (json) {
                            if(json.state==0){
                                layer.msg(json.data, {icon: 6, time:1000}, function() {
                                    window.location.href = '/quota/';
                                });
                            }else{
                                layer.alert(json.data);
                                self.submitting(0);
                            }
                        },
                        error:function (data) {
                            layer.alert("保存失败！"+data.responseText);
                            self.submitting(0);
                        }
                    });
                });
            } else {
                self.errors.showAllMessages();
            }
        }
        self.getQuotasValue=function(quotas) {
            var quotaItems = [];
            $(quotas).each(function(ind, item) {
                quotaItems.push(item.getValue());
            });
            return quotaItems;
        }
    }

    $(document).delegate('span.box-title__hiden','click',function(event) {
        var ele = event.target;
        var toggle1 = $(ele).parents("div.sub-container__box").find("div.box-content-custom:visible");
        var toggle2 = $(ele).parents("div.sub-container__box").find("div.form-horizontal:visible");
        if(toggle1.length > 0 || toggle2.length > 0) {
            $(ele).parents("div.sub-container__box").find("div.box-content-custom").hide('slow');
            $(ele).parents("div.sub-container__box").find("table").hide('slow');
            $(ele).parents("div.sub-container__box").find("div.form-horizontal").hide('slow');
            var eleI = $('<i class="fa fa-angle-double-down"></i>');
            $(ele).html(' 展开');
            eleI.prependTo($(ele));
        } else {
            $(ele).parents("div.sub-container__box").find("div.box-content-custom").show('slow');
            $(ele).parents("div.sub-container__box").find("table").show('slow');
            $(ele).parents("div.sub-container__box").find("div.form-horizontal").show('slow');
            var eleI = $('<i class="fa fa-angle-double-up"></i>');
            $(ele).html(' 收起');
            eleI.prependTo($(ele));
        }
    });

    function copy() {
        layer.msg('复制成功', {icon: 6, time: 1000});
    }
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