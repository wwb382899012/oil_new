<!-- <link rel="stylesheet" type="text/css" href="/css/fengkongdetail.css?key=20180112"> -->
<script type="text/javascript" src="/js/resize.js"></script>
<script type="text/javascript" src="/js/clipboard.js"></script>
<?php
$contract = ProjectService::getContractDetailModel($data['obj_id']);
include ROOT_DIR . DIRECTORY_SEPARATOR . "protected/views/components/new_checkItems.php";
//$checkLogs = FlowService::getCheckLog($contract->contract_id, "2, 3");
$checkLogs = FlowService::getCheckLogWithExtra($contract->contract_id, "2, 3");
$checkLogs = array_reverse($checkLogs);
$user = Utility::getNowUser();
?>
<?php
$menus = [
    ['text' => '项目管理'],
    ['text' => '风控审核', 'link' => '/check2/'],
    ['text' => '审核']
];
$buttons = [];
$buttons[] = ['text' => '通过', 'attr' => ['data-bind' => 'click:submit']];
$buttons[] = ['text' => '驳回', 'attr' => ['data-bind' => 'click:rollback', 'class_abbr' => 'action-default-base']];
$this->loadHeaderWithNewUI($menus, $buttons, '/check2/');
?>
<section>
    <!-- 流程 -->
    <div class="content-wrap" style="position:relative">
        <div class="content-wrap-title">
            <div>
                <p>流程进度</p>
                <p class="content-wrap-expand"><span>收起</span><i class="icon icon-shouqizhankai1"></i></p>
            </div>
        </div>
        <?php
        $lastCheckLog = [
            'check_status' => -101,
            'check_time' => '',
            'remark' => '',
            'name' => $user['name'],
            'node_name' => '风控审核',
            'item' => []
        ];
        $checkLogs[] = $lastCheckLog;
        ?>
        <?php
        $this->renderPartial("/common/new_progressbar", array('contract' => $contract, 'checkLogs' => $checkLogs));
        ?>
    </div>
    <!-- 流程 -->
    <!-- 详情综述 -->
    <?php
    $this->renderPartial("/common/new_contractDetail", array('contract' => $contract));
    ?>
    <!-- 提交保存 -->
    <div class="content-wrap">
        <div class="content-wrap-title">
            <div>
                <p>本次审核意见</p>
            </div>
        </div>

        <form class="form-horizontal" role="form" id="mainForm">

            <!-- ko component: {
                          name: "check-items",
                          params: {
                                      items: items

                                      }
                      } -->
            <!-- /ko -->
            <textarea style="width:100%;" class="form-control" rows="5" placeholder="请输入审核意见内容"
                      data-bind="value:remark"></textarea>

        </form>
    </div>
    </div><!--end box box-primary-->
</section><!--end content-->

<div class="modal fade draggable-modal" id="quotaModal" tabindex="-1" role="dialog" aria-labelledby="modal"
     data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="childContent">
        </div>
    </div>
</div>
<script type="text/javascript">
    var view;
    $(function () {
        view = new ViewModel(<?php echo json_encode(array('items' => $this->map['riskmanagement_checkitems_config'], 'check_id' => $data['check_id'], 'contract_id' => $contract->contract_id, 'is_main' => ($contract->is_main) ? '1' : '0'));?>);
        ko.applyBindings(view, $("#main-container")[0]);
        $("section.content").trigger('resize');
        var clipboard = new Clipboard('.copy-project-num');
        $('div.review-flow__circle').bind("mouseenter", function (event) {
            $('div.review-flow__circle').trigger("mouseleave");
            var ele = $(event.target);
            var thiz = ele.parent();
            var offset = $('div.review-flow-inner-container').position().left;
            if (thiz.hasClass('review-flow__mod')) {
                var html = thiz.find('div.review-flow__comment').html();
                var left = thiz.position().left;
                $("#review-flow-comment").html(html);
                $("#review-flow-comment-bot").css('left', left + 40 + offset);
                $("#review-flow-comment-bot").show();
                thiz.find('span.review-flow_comment').show();
                thiz.find('span.review-flow_comment_status').hide();
            }
        });
        $('div.review-flow__circle').bind("mouseleave", function (event) {
            var ele = $(event.target);
            var thiz = ele.parent();
            if (thiz.hasClass('review-flow__mod')) {
                $("#review-flow-comment").html('');
                $("#review-flow-comment-bot").hide();
                thiz.find('span.review-flow_comment').hide();
                thiz.find('span.review-flow_comment_status').show();
            }
        });

        $('div.mod-left-flow').bind("click", moveLeft);
        $('div.mod-right-flow').bind("click", moveRight);
    });

    function ViewModel(option) {
        var defaults = {
            items: null,
            remark: '',
            status: 1,
            check_id: '',
        };
        var o = $.extend(defaults, option);
        var self = this;
        self.items = ko.observableArray(o.items);
        self.status = ko.observable(o.status);
        self.check_id = o.check_id;
        self.contract_id = o.contract_id;
        self.is_main = o.is_main;
        self.remark = ko.observable(o.remark).extend({required: true, maxLength: 512});
        self.errors = ko.validation.group(self);
        self.submitting = ko.observable(0);
        self.isValid = function () {
            return self.errors().length === 0;
        };
        self.submit = function () {
            self.status(1);
            self.save();
        }
        self.rollback = function () {
            self.status(-1);
            self.save();
        }
        self.save = function (checkStatus) {
            if (self.isValid() && self.submitting() == 0) {
                var confirmString = '';
                if (self.status() > 0) {
                    confirmString = '通过风控审核';
                } else {
                    confirmString = '驳回到商务确认';
                }
                inc.vueConfirm({
                    content:"是否确认" + confirmString + "，该操作不可逆？",
                    onConfirm:function () {
                        /*var extraValues = {};
                        $(self.items()).each(function(ind, item) {
                            if(item.key())
                                extraValues[item.key()] = item.value();
                        });*/
                        var data = {
                            items: self.items.getValues(),
                            //items:extraValues,
                            obj: {
                                remark: self.remark(),
                                check_id: self.check_id,
                                checkStatus: self.status(),
                            }
                        }
                        self.submitting(1);
                        $.ajax({
                            type: "POST",
                            url: "/<?php echo $this->getId() ?>/save",
                            data: data,
                            dataType: "json",
                            success: function (json) {
                                if (json.state == 0) {
                                    if (self.status() == 1) {
                                        inc.vueMessage({duration: 500,message: json.data, onClose: function () {
                                                addQuota(self.contract_id, self.is_main);
                                            }
                                        });
                                    } else {
                                        inc.vueMessage({duration: 500,message: json.data, onClose: function () {
                                                self.back();
                                            }
                                        });
                                    }
                                } else {
                                    inc.vueAlert(json.data);
                                    self.submitting(0);
                                }
                            },
                            error: function (data) {
                                var resErr = data.responseText;
                                if(!navigator.onLine)
                                    resErr = '您当前无网络！';
                                inc.vueAlert({content: "保存失败！" + resErr});
                                self.submitting(0);
                            }
                        });
                    }
                });

            } else {
                self.errors.showAllMessages();
            }
        }

        self.back = function () {
            window.location.href = "/<?php echo $this->getId() ?>/?search[checkStatus]=1";
        }
    }

    function moveLeft(event) {
        $('div.mod-left-flow').unbind('click');
        var offset = $('div.review-flow-inner-container').position().left;
        if (offset < -250) {
            $("div.review-flow-inner-container").animate({left: '+=250px'}, function () {
                $('div.mod-left-flow').bind('click', moveLeft);
            });
            // $('div.review-flow-inner-container').css('left', offset + 100);
        } else if (offset < 0) {
            offset = offset * -1;
            $("div.review-flow-inner-container").animate({left: '+=' + offset + 'px'}, function () {
                $('div.mod-left-flow').bind('click', moveLeft);
            });
        }

    }

    function moveRight(event) {
        $('div.mod-right-flow').unbind('click');
        var realLength = $('div.review-flow__mod').length * 220;
        var containerLength = $('div.review-flow-container').width();
        var offset = $('div.review-flow-inner-container').position().left;
        if (realLength + offset > containerLength) {
            // $('div.review-flow-inner-container').css('left', offset - 100);
            $("div.review-flow-inner-container").animate({left: '-=250px'}, function () {
                $('div.mod-right-flow').bind('click', moveRight);
            });
        }
    }

    function addQuota(contractId, isMain) {
        $.ajax({
            url: '/quota/ajaxEdit?contract_id=' + contractId + '&is_main=' + isMain,
            method: 'get',
            success: function (data) {
                $("#childContent").html(data);
                $("#quotaModal").modal('show').on('hide.bs.modal', function (event) {
                    view.back();
                });
            }
        });
    }

    function copy() {
        inc.vueMessage('复制成功');
    }
</script>

