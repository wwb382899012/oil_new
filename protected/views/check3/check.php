<link rel="stylesheet" type="text/css" href="/css/fengkongdetail.css?key=20180112">
<script type="text/javascript" src="/js/resize.js"></script>
<script type="text/javascript" src="/js/clipboard.js"></script>
<?php
$contract = ProjectService::getContractDetailModel($data['obj_id']);
include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/checkItems.php";
$checkLogs = FlowService::getCheckLog($contract->contract_id,"2, 3");
$checkLogs = array_reverse($checkLogs);
$user = Utility::getNowUser();
// debug($checkLogs);die;
?>
<section class="content-header">
    <div class="content-header__des">
        <?php echo empty($this->pageTitle)?$this->moduleName:$this->pageTitle ?>
    </div>
</section>
<section class="content sub-container">
          <!-- 详情综述 -->
        <?php
        $this->renderPartial("/common/contractDetail", array('contract'=>$contract));
        ?>

        <!-- 流程 -->
        <div class="box box-primary sub-container__box " style="position:relative">

            <div class="box-header with-border box-content-title">
                <h3 class="box-title">&nbsp;&nbsp;&nbsp;流程进度</h3>
            </div>
            <div class="mod-review-tips" id="review-flow-comment-bot" style="display: none">
                <span class="review-tips__bot"></span>
                <span class="review-tips__top"></span>
                审核意见：<div style="display: inline" id="review-flow-comment"></div>
            </div>
            <div class="box-body box-body-overflow">
                <div class="mod-left-flow"><img src="/img/left-row.png"></div>
                <div class="review-flow-container">
                    <div class="review-flow-inner-container">
                    <?php foreach($checkLogs as $key => $log):?>
                        <?php if($key <= count($checkLogs) - 1):?>
                            <div class="review-flow__mod <?php echo ($log['check_status'] == 1)?'review-flow_pass':'review-flow_reject'?>">
                                <div class="review-flow__review-time"><?php echo $log['check_time'];?></div>
                                <div class="review-flow__circle">
                                  <span class="review-flow_comment" style="display: none">意见</span>
                                  <span class="review-flow_comment_status"><?php echo ($log['check_status']==1)?'通过':'驳回';?></span>
                                </div>
                                <div class="review-flow__line"></div>
                                <div class="review-flow__review-type">
                                  <?php echo $log['node_name'];?> <span class="review-type__review-name"><?php echo $log['name']?></span>
                                </div>
                                <div class="review-flow__comment"><?php echo $log['remark'];?></div>
                            </div>
                            <?php else:?>
                        <?php endif;?>
                    <?php endforeach;?>
                    <!-- 最后一个 -->
            
                    <div class="review-flow__mod mg-left-300 review-flow__not">
                      
                        <div class="review-flow__circle">未审核</div>
                        <div class="review-flow__review-type">
                        业务审核 <span class="review-type__review-name"><?php echo $user['name'];?></span>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="mod-right-flow"><img src="/img/right-row.png"></div>
            </div>
        </div>

        <!-- 流程 -->
        <!-- 提交保存 -->
        <div class="box box-primary sub-container__box">
            <div class="box-header with-border box-content-title">
                <h3 class="box-title">&nbsp;&nbsp;&nbsp;本次审核意见</h3>
            </div>
            <form class="form-horizontal" role="form" id="mainForm">

                <!-- ko component: {
                              name: "check-items",
                              params: {
                                          items: items

                                          }
                          } -->
                <!-- /ko -->
                <div class="box-body">
                    <div class="form-group form-group-custom">
                        <div class="col-sm-10">
                            <textarea class="form-control" rows="5" placeholder="请输入审核意见内容" data-bind="value:remark"></textarea>
                        </div>
                    </div>
                </div>

            </form>
        </div><!--end box-border-->

        <div class="box box-primary sub-container__box sub-container__fixed">
            <div class="box-body">
                <div class="form-group form-group-custom-btn">
                    <div class="btn-contain-custom">
                        <button type="button" class="btn btn btn-contain__submit"  data-bind="click:submit">通过</button>
                        <button type="button" class="btn btn-contain__danger"  data-bind="click:rollback">驳回</button>
                        <button type="button" class="btn btn-contain__default"  data-bind="click:back">返回</button>
                    </div>
                </div>
            </div>
        </div>
    </div><!--end box box-primary-->
</section><!--end content-->
<script type="text/javascript">
    var view;
    $(function () {
        view=new ViewModel(<?php echo json_encode(array('items'=>$this->map['transaction_checkitems_config'], 'check_id'=>$data['check_id']));?>);
        ko.applyBindings(view, $("#content")[0]);
        $("section.content").trigger('resize');
        var clipboard = new Clipboard('.copy-project-num');
        $('div.review-flow__circle').bind("mouseenter",function(event){
            $('div.review-flow__circle').trigger("mouseleave");
            var ele = $(event.target);
            var thiz = ele.parent();
            if(thiz.hasClass('review-flow__mod')) {
                var html = thiz.find('div.review-flow__comment').html();
                var left = thiz.position().left;
                var offset = $('div.review-flow-inner-container').position().left;
                $("#review-flow-comment").html(html);
                $("#review-flow-comment-bot").css('left', left + 40 + offset);
                $("#review-flow-comment-bot").show();
                thiz.find('span.review-flow_comment').show();
                thiz.find('span.review-flow_comment_status').hide();
            }
        });
        $('div.review-flow__circle').bind("mouseleave",function(event){
            var ele = $(event.target);
            var thiz = ele.parent();
            if(thiz.hasClass('review-flow__mod')) {
                $("#review-flow-comment").html('');
                $("#review-flow-comment-bot").hide();
                thiz.find('span.review-flow_comment').hide();
                thiz.find('span.review-flow_comment_status').show();
            }
        });
        
        $('div.mod-left-flow').bind("click", moveLeft);
        $('div.mod-right-flow').bind("click", moveRight);
    });

    function moveLeft(event) {
        $('div.mod-left-flow').unbind('click');
        var offset = $('div.review-flow-inner-container').position().left;
        if(offset < -250) {
            $("div.review-flow-inner-container").animate({left:'+=250px'}, function() {
                $('div.mod-left-flow').bind('click', moveLeft);
            });
            // $('div.review-flow-inner-container').css('left', offset + 100);
        } else if(offset < 0) {
            offset = offset * -1;
            $("div.review-flow-inner-container").animate({left:'+='+offset+'px'}, function() {
                $('div.mod-left-flow').bind('click', moveLeft);
            });
        }
        
    }

    function moveRight(event) {
        $('div.mod-right-flow').unbind('click');
        var realLength = $('div.review-flow__mod').length * 220;
        var containerLength = $('div.review-flow-container').width();
        var offset = $('div.review-flow-inner-container').position().left;
        if(realLength + offset > containerLength) {
            // $('div.review-flow-inner-container').css('left', offset - 100);
            $("div.review-flow-inner-container").animate({left:'-=250px'}, function() {
                $('div.mod-right-flow').bind('click', moveRight);
            });
        }
    }

    function ViewModel(option){
        var defaults={
            items:null,
            remark : '',
            status:1,
            check_id:'',
        };
        var o=$.extend(defaults,option);
        var self=this;
        self.items=ko.observableArray(o.items);
        self.status=ko.observable(o.status);
        self.check_id=o.check_id;
        self.remark=ko.observable(o.remark).extend({required:true,maxLength:512});
        self.errors = ko.validation.group(self);
        self.submitting = ko.observable(0);
        self.isValid = function () {
            return self.errors().length === 0;
        };
        self.submit = function() {
            self.status(1);
            self.save();
        }
        self.rollback = function() {
            self.status(-1);
            self.save();
        }
        self.save=function (checkStatus) {
            if(self.isValid() && self.submitting() == 0) {
                var confirmString = '';
                if(self.status()>0) {
                    confirmString = '通过业务审核？';
                } else {
                    confirmString = '驳回到商务确认?';
                }
                layer.confirm("是否确认"+confirmString, {icon: 3, title: '提示'}, function(){
                    /*var extraValues = {};
                    $(self.items()).each(function(ind, item) {
                        if(item.key())
                            extraValues[item.key()] = item.value();
                    });*/
                    var data ={
                        items:self.items.getValues(),
                        //items:extraValues,
                        obj:{
                            remark : self.remark(),
                            check_id : self.check_id,
                            checkStatus : self.status(),
                        }
                    }
                    self.submitting(1);
                    $.ajax({
                        type:"POST",
                        url:"/<?php echo $this->getId();?>/save",
                        data:data,
                        dataType:"json",
                        success:function (json) {
                            if(json.state==0){
                                layer.msg(json.data, {icon: 6, time:1000},function() {
                                    self.back();
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
        
        self.back= function () {
            window.location.href="/<?php echo $this->getId();?>/?search[checkStatus]=1";
        }
    }

    function copy() {
        layer.msg('复制成功', {icon: 6, time: 1000});
    }
</script>

