<link href="/css/site.css" rel="stylesheet" type="text/css"/>
<div style="display: none" id="site_body">
    <!-- ko if:shortCuts().length > 0 -->
    <div class="box box-default">
        <div class="box-body">
            <div class="row">
            <!-- ko foreach:{ data: shortCuts(), as: 'shortCut' } -->
                <!-- ko if:!(shortCut.subMenu) -->
                <a class="short_cut_box text-center panel-title" data-bind="attr:{href:shortCut.url}, id:'shortCutLink'+$index()">
                    <i class="short_cut_ico" data-bind="css:shortCut.ico"></i>
                    <span class="short_cut_name" data-bind="text:shortCut.name"></span>
                </a>
                <!-- /ko -->
                <!-- ko if:shortCut.subMenu -->
                <a class="short_cut_box text-center panel-title" data-toggle="dropdown" data-bind="attr:{id:'shortCutLink'+$index()},click:function(){$parent.openMenu($index())}">
                    <i class="short_cut_ico" data-bind="css:shortCut.ico"></i>
                    <span class="short_cut_name" data-bind="text:shortCut.name"></span>
                </a>
                <!-- /ko -->
            <!-- /ko -->
            <ul class="dropdown-menu" id="shortCutMenu" >
                <!-- ko foreach:selectedSubMenu() -->
                <li>
                    <a data-bind="attr:{href:url}, text:name"></a>
                </li>
                <!-- /ko -->
            </ul>
            </div>
        </div>
    </div>
    <!-- /ko -->
    <div class="box box-default">
        <div class="row box-head">
            <div class="box-header with-border">
                <div class="col-sm-12 actions_head">
                    <strong ><i class="fa fa-circle text-red"></i> &nbsp;&nbsp;待办事项</strong>
                </div>
                <div class="col-sm-10" id="action_body">
                    <!-- ko if:actions().length==0 -->
                    <div class="col-sm-12 actions_nav">
                        <a>
                            <i class="fa fa-fw fa-check-square-o"></i>
                            <span>暂无待办</span>
                        </a>
                    </div>
                    <!-- /ko -->
                    <!-- ko foreach:{ data: actions(), as: 'action' } -->
                        <!-- ko if:$index()<=$parent.displayIndex() -->
                        <div class="pull-left actions_nav actions_achor" data-bind="click:function(){$parent.filterTasks(action.action_id)}, css:(($parent.selectedAction() == action.action_id)?'actived':''), attr:{title:action.action_name} ">
                            <span  data-bind="text:action.action_name"></span>&nbsp;( <a data-bind="text:action.n"></a> )
                        </div>
                        <!-- /ko -->
                        <!-- ko if:$index()>$parent.displayIndex() -->
                        <div class="pull-left actions_nav actions_achor" data-bind="style: { display:(( $parent.openStatus()) ? '' : 'none')}, click:function(){$parent.filterTasks(action.action_id)}, css:(($parent.selectedAction() == action.action_id)?'actived':''), attr:{title:action.action_name} ">
                            <span  data-bind="text:action.action_name"></span>&nbsp;( <a data-bind="text:action.n"></a> )
                        </div>
                        <!-- /ko -->
                    <!-- /ko -->
                </div>

                <div class="col-sm-2">
                    <!-- ko if:showOpenButton() -->
                    <div class="col-sm-12 actions_nav text-right">
                        <a href="javascript:void(0);" data-bind="html:openStatusStr,click:openAllActions">
                        </a>
                    </div>
                    <!-- /ko -->
                </div>
            </div>
        </div>
        <!-- ko if:selectedTasks().length > 0 -->
        <div class="row">
            <div class="col-sm-12">
                <div class="box-body no-padding">
                    <ul class="nav nav-stacked">
                        <!-- ko foreach:{ data: selectedTasks(), as: 'task' } -->
                        <li style='padding:16px 24px 16px 24px; '>
                            <span data-bind="text:($index() + 1) + '.&nbsp;&nbsp; 您有一个新任务 : ' + task.action_name + ' ( ' + task.title +' ) ，'"></span>
                            <a class="task_link no-padding" href="javascript:void(0);" data-bind="attr: { href: task.action_url}">点击这里确认操作</a>
                            <span class='pull-right' data-bind="text:task.create_time.substr(0, 10)"></span>
                        </li>
                        <!-- /ko -->
                    </ul>
                </div>
            </div>

        </div>
        <!-- /ko -->
    </div>
</div>
<script>
    var view;
    $(function () {
        view=new ViewModel(<?php echo json_encode(array(
                'actions'=>$actions,
                'tasks'=>$tasks,
                'shortCuts'=>$shortCuts
            )
        )?>);
        ko.applyBindings(view);
        var actions = view.actions();
        if(actions.length > 0) {
            var firstAction = actions[0].action_id;
            view.filterTasks(firstAction);
        }
        $("#site_body").show();
        view.bodyWidth($("#action_body").width());
        $("#action_body").resize(function() {
            setTimeout(function() {
                view.bodyWidth($("#action_body").width());
            }, 10);
        });
    });

    function ViewModel(option){
        var defaults={
            actions:[],
            tasks:[],
            shortCuts:[],
            openAllActions:false,
            selectedAction:0,
        };


        var o = $.extend(defaults, option);
        var self = this;
        self.actions = ko.observable(o.actions);
        self.tasks = ko.observable(o.tasks);
        self.selectedTasks = ko.observable(o.tasks);
        // self.actions = ko.observable([]);
        // self.tasks = ko.observable([]);
        // self.selectedTasks = ko.observable([]);
        self.shortCuts = ko.observableArray(o.shortCuts);
        self.openAllActions = ko.observable(o.openAllActions);
        self.selectedAction = ko.observable(o.selectedAction);
        self.actionLength = ko.observable(o.selectedAction.length);
        self.openStatus = ko.observable(false);
        self.bodyWidth = ko.observable($("#action_body").width());
        self.openStatusStr = ko.computed(function() {
            return self.openStatus()?'<i class="fa fa-angle-double-up"></i>&nbsp;收起':'<i class="fa fa-angle-double-down"></i>&nbsp;展开';
        }, self);
        self.openAllActions = function() {
            self.openStatus(!self.openStatus());
        }
        self.filterTasks = function(actionId) {
            if(self.selectedAction() == actionId) {
                // self.selectedAction(0);
            } else {
                self.selectedAction(actionId);
            }
            self.selectedTasks([]);
            var tasks = [];
            for(var ind = 0; ind < self.tasks().length; ind ++) {
                if(self.selectedAction() == 0 || self.tasks()[ind].action_id==actionId) {
                    var thisTask = self.tasks()[ind];
                    thisTask.action_url = "/site/task?id=" + thisTask.task_id;
                    tasks.push(thisTask);
                }
            }
            self.selectedTasks(tasks);
        }
        self.filterTasks(0);
        self.timeLine = ko.computed(function() {
            var timeLine = [];
            var time = {
                name : '全部',
                height: 44,
                href:'#allTasks',
                showIcon:1
            }
            timeLine.push(time);
            var today = new Date();
            var todayCount = 0;
            var weekCount = 0;
            var fornightCount = 0;
            var extraCount = 0;
            for(var ind = 0; ind < self.selectedTasks().length; ind ++) {
                var thisTask = self.selectedTasks()[ind];
                var thisDate = new Date(thisTask.create_time),
                timeDiff = (today - thisDate) / (24 * 3600 * 1000);
                // 今天
                if(timeDiff < 1) {
                    todayCount ++;
                }
                // 一周
                if(timeDiff >= 1 && timeDiff < 7) {
                    weekCount ++;
                }
                // 两周
                if(timeDiff >= 7 && timeDiff < 14) {
                    fornightCount ++;
                }
                // 多于两周
                if(timeDiff >= 14) {
                    extraCount ++;
                }
            }
            if(todayCount > 0) {
                timeLine.push({
                    name : '今天',
                    height: 44 * todayCount,
                    href:'#todayTasks',
                    showIcon:1
                });
            }
            if(weekCount > 0) {
                timeLine.push({
                    name : '近一周',
                    height: 44 * weekCount,
                    href:'#weekTasks',
                    showIcon:1
                });
            }
            if(fornightCount > 0) {
                timeLine.push({
                    name : '近两周',
                    height: 44 * fornightCount,
                    href:'#weekTasks',
                    showIcon:1
                });
            }
            if(extraCount > 0) {
                timeLine.push({
                    name : '',
                    height: 44 * extraCount,
                    href:'#extraTasks',
                    showIcon:0
                });
            }
            return timeLine;
        }, self);
        self.selectedShortCut = ko.observable(-1);
        self.selectedSubMenu = ko.computed(function() {
            var subMenu = [];
            if(self.selectedShortCut() >= 0 && self.shortCuts()[self.selectedShortCut()].subMenu) {
                var main = self.shortCuts()[self.selectedShortCut()];
                var subMenus = main.subMenu;
                for(var ind = 0; ind < subMenus.length; ind ++) {
                    subMenu.push(subMenus[ind]);
                }
            }
            return subMenu;
        });
        self.openMenu = function(index) {
            if(self.selectedShortCut() != index && $("#shortCutMenu:visible").length > 0) {
                $("#shortCutMenu").dropdown('toggle');
            }
            $("#shortCutMenu").css('left', $("#shortCutLink" + index).position().left + 'px');
            self.selectedShortCut(index);
        }
        self.showOpenButton = ko.computed(function() {
            var actions = $('.actions_achor');
            var totalWidth = 0;
            for(var ind = 0; ind < actions.length; ind ++) {
                totalWidth += $(actions[ind]).width();
            }
            return totalWidth > self.bodyWidth();
        });
        self.displayIndex = ko.computed(function() {
            var actions = $('.actions_achor');
            var totalWidth = 0;
            for(var ind = 0; ind < actions.length; ind ++) {
                totalWidth += ($(actions[ind]).width() + 60);
                if(totalWidth >= self.bodyWidth()) {
                    return ind - 1;
                }
            }
            return ind;
        });
    }

(function($, h, c) {
    var a = $([]),
    e = $.resize = $.extend($.resize, {}),
    i,
    k = "setTimeout",
    j = "resize",
    d = j + "-special-event",
    b = "delay",
    f = "throttleWindow";
    e[b] = 250;
    e[f] = true;
    $.event.special[j] = {
        setup: function() {
            if (!e[f] && this[k]) {
                return false;
            }
            var l = $(this);
            a = a.add(l);
            $.data(this, d, {
                w: l.width(),
                h: l.height()
            });
            if (a.length === 1) {
                g();
            }
        },
        teardown: function() {
            if (!e[f] && this[k]) {
                return false;
            }
            var l = $(this);
            a = a.not(l);
            l.removeData(d);
            if (!a.length) {
                clearTimeout(i);
            }
        },
        add: function(l) {
            if (!e[f] && this[k]) {
                return false;
            }
            var n;
            function m(s, o, p) {
                var q = $(this),
                r = $.data(this, d);
                r.w = o !== c ? o: q.width();
                r.h = p !== c ? p: q.height();
                n.apply(this, arguments);
            }
            if ($.isFunction(l)) {
                n = l;
                return m;
            } else {
                n = l.handler;
                l.handler = m;
            }
        }
    };
    function g() {
        i = h[k](function() {
            a.each(function() {
                var n = $(this),
                m = n.width(),
                l = n.height(),
                o = $.data(this, d);
                if (m !== o.w || l !== o.h) {
                    n.trigger(j, [o.w = m, o.h = l]);
                }
            });
            g();
        },
        e[b]);
    }
})(jQuery, this);
</script>
    