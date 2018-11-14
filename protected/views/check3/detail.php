<link rel="stylesheet" type="text/css" href="/css/fengkongdetail.css?key=20180112">
<script type="text/javascript" src="/js/resize.js"></script>
<script type="text/javascript" src="/js/clipboard.js"></script>
<?php
$contract = ProjectService::getContractDetailModel($data['obj_id']);
$checkLogs = FlowService::getCheckLog($contract->contract_id,"2, 3");
$checkLogs = array_reverse($checkLogs);
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
                            <div class="review-flow__mod <?php echo ($log['check_status'] == 1)?'review-flow_pass':'review-flow_reject'?>">
                                <div class="review-flow__review-time"><?php echo $log['check_time'];?></div>
                                <div class="review-flow__circle">
                                  <span class="review-flow_comment" style="display: none">意见</span>
                                  <span class="review-flow_comment_status"><?php echo ($log['check_status']==1)?'通过':'驳回';?></span>
                                </div>
                            <?php if($key < count($checkLogs) - 1):?>
                                <div class="review-flow__line"></div>
                            <?php endif;?>
                                <div class="review-flow__review-type">
                                  <?php echo $log['node_name'];?> <span class="review-type__review-name"><?php echo $log['name']?></span>
                                </div>
                                <div class="review-flow__comment"><?php echo $log['remark'];?></div>
                            </div>
                    <?php endforeach;?>
                    </div>
                </div>
                <div class="mod-right-flow"><img src="/img/right-row.png"></div>
            </div>
        </div>

        <div class="box box-primary sub-container__box sub-container__fixed">
            <div class="box-body">
                <div class="form-group form-group-custom-btn">
                    <div class="btn-contain-custom">
                        <a type="button" class="btn btn-contain__default" href="javascript:void(0);" onclick="back()">返回</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

<script type="text/javascript">
    var back = function() {
        history.go(-1);
    }
    $(document).ready(function() {
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

    function copy() {
        layer.msg('复制成功', {icon: 6, time: 1000});
    }
</script>
