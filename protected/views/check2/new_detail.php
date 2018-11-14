<!-- <link rel="stylesheet" type="text/css" href="/css/fengkongdetail.css?key=20180112"> -->
<script type="text/javascript" src="/js/resize.js"></script>
<script type="text/javascript" src="/js/clipboard.js"></script>
<?php
$contract = ProjectService::getContractDetailModel($data['obj_id']);
include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/checkItems.php";
//$checkLogs = FlowService::getCheckLog($contract->contract_id,"2, 3");
$checkLogs = FlowService::getCheckLogWithExtra($contract->contract_id, "2, 3");
$checkLogs = array_reverse($checkLogs);
$user = Utility::getNowUser();
?>
<?php
$menus = [
    ['text' => '项目管理'],
    ['text' => '风控审核', 'link' => '/check2/'],
    ['text' => '审核详情']
];
$buttons = [];
$this->loadHeaderWithNewUI($menus, $buttons, '/check2/');
?>
<section style="margin-bottom: 20px;">
    <!-- 流程 -->
    <div class="content-wrap" style="position:relative">
        <div class="content-wrap-title">
            <div>
                <p>流程进度</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
         <?php
        $this->renderPartial("/common/new_progressbar", array('contract'=>$contract,'checkLogs'=>$checkLogs));
        ?>
    </div>
    <!-- 流程 -->
    <!-- 详情综述 -->
    <?php
    $this->renderPartial("/common/new_contractDetail", array('contract'=>$contract));
    ?>
</section><!--end content-->

<!-- <div class="mod-review-tips" >
    <span class="review-tips__bot"></span>
    <span class="review-tips__top"></span>
</div> -->
<script type="text/javascript">
    var back = function() {
        history.go(-1);
    }
    var view;
    $(function () {
        view=new ViewModel();
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

        };
        var self = this;
        self.toggleBox = function(ele) {
            var toggle = $(ele).parents("div.sub-container__box").find("div.box-content-custom:visible");
            if(toggle.length > 0) {
                $(ele).parents("div.sub-container__box").find("div.box-content-custom:visible").hide();
            } else {
                $(ele).parents("div.sub-container__box").find("div.box-content-custom:visible").show();
            }
        }
    }

    function addQuota(contractId, isMain) {
        $.ajax({
            url:'/quota/ajaxEdit?contract_id='+contractId+'&is_main='+isMain,
            method:'get',
            success:function(data) {
                $("#childContent").html(data);
                $("#quotaModal").modal('show').on('hide.bs.modal', function(event) {
                    view.back();
                });
            }
        });
    }

    function copy() {
        inc.vueMessage('复制成功');
    }
</script>